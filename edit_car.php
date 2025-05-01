<?php
$host = "127.0.0.1:3306";
$user = "root";
$password = "admin";
$dbname = 'cadastro_carros';

session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Buscar o carro
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM carros WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows != 1) {
        echo "Carro não encontrado.";
        exit();
    }

    $carro = $result->fetch_assoc();
} else {
    header("Location: lista_carros.php");
    exit();
}

// Atualizar os dados se enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $placa = $_POST['placa'];
    $renavam = $_POST['renavam'];
    $crv = $_POST['crv'];
    $codigo_seguranca = $_POST['codigo_seguranca'];
    $observacoes = !empty($_POST['observacao']) ? $_POST['observacao'] : NULL;

    $novo_pdf = $carro['pdf_path']; // Mantém o mesmo PDF por padrão

    // Se enviou novo PDF
    if (isset($_FILES['documento']) && $_FILES['documento']['error'] == UPLOAD_ERR_OK) {
        $arquivoTmp = $_FILES['documento']['tmp_name'];
        $nomeArquivo = basename($_FILES['documento']['name']);
        $extensao = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));

        if ($extensao == 'pdf') {
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

            $novoNome = uniqid('doc_') . '.pdf';
            $destino = 'uploads/' . $novoNome;

            if (move_uploaded_file($arquivoTmp, $destino)) {
                // Deleta o PDF antigo, se existir
                if (!empty($carro['pdf_path']) && file_exists('uploads/' . $carro['pdf_path'])) {
                    unlink('uploads/' . $carro['pdf_path']);
                }
                $novo_pdf = $novoNome;
            }
        } else {
            echo "<div class='alert alert-warning'>Apenas arquivos PDF são permitidos!</div>";
            exit();
        }
    }

    // Usando prepared statement para evitar SQL injection
    $stmt = $conn->prepare("UPDATE carros SET 
                            placa = ?, 
                            renavam = ?, 
                            crv = ?, 
                            codigo_seguranca = ?,
                            observacoes = ?,
                            pdf_path = ?
                           WHERE id = ?");
    
    $stmt->bind_param("ssssssi", $placa, $renavam, $crv, $codigo_seguranca, $observacoes, $novo_pdf, $id);

    if ($stmt->execute()) {
        header("Location: lista_carros.php?msg=Carro+atualizado+com+sucesso!");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Erro ao atualizar: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Editar Carro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="d-flex">
    <?php include 'menu.php'; ?>

    <div class="container-fluid p-4">
      <h2 class="mb-4"><i class="bi bi-car-front-fill"></i> Editar Veículo</h2> 
      <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label"><i class="bi bi-credit-card-2-front"></i> Placa</label>
              <input type="text" class="form-control" name="placa" value="<?= htmlspecialchars($carro['placa']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label"><i class="bi bi-upc"></i> Código do Renavam</label>
              <input type="text" class="form-control" name="renavam" value="<?= htmlspecialchars($carro['renavam']) ?>" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label"><i class="bi bi-123"></i> Número do CRV</label>
              <input type="text" class="form-control" name="crv" value="<?= htmlspecialchars($carro['crv']) ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label"><i class="bi bi-lock-fill"></i> Número de segurança do CRV</label>
              <input type="text" class="form-control" name="codigo_seguranca" value="<?= htmlspecialchars($carro['codigo_seguranca']) ?>">
            </div>
        </div>
        <div class="mb-3">
          <label class="form-label"><i class="bi bi-clipboard2-fill"></i> Observações</label>
          <input type="text" class="form-control" name="observacao" value="<?= htmlspecialchars($carro['observacoes'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label"><i class="bi bi-file-pdf-fill"></i> PDF</label>
            <input type="file" class="form-control" name="documento" accept="application/pdf">
            <?php if (!empty($carro['pdf_path'])): ?>
                <p class="mt-2">
                Documento atual: 
                <a href="uploads/<?= htmlspecialchars($carro['pdf_path']) ?>" target="_blank" class="btn btn-sm btn-success">
                    <i class="bi bi-file-pdf-fill"></i> Visualizar
                </a>
                </p>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary"><i class="bi bi-floppy-fill"></i> Salvar Alterações</button>
        <a href="lista_carros.php" class="btn btn-secondary ms-2"><i class="bi bi-x-circle"></i> Cancelar</a>
      </form>
    </div>
  </div>
</body>
</html>