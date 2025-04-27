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
    $sql = "SELECT * FROM carros WHERE id = $id";
    $result = $conn->query($sql);

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

    $novo_pdf = $carro['pdf_path']; // Mantém o mesmo PDF por padrão

    // Se enviou novo PDF
    if (isset($_FILES['documento']) && $_FILES['documento']['error'] == 0) {
        $arquivoTmp = $_FILES['documento']['tmp_name'];
        $nomeArquivo = basename($_FILES['documento']['name']);
        $extensao = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));

        if ($extensao == 'pdf') {
            // Criar pasta se não existir
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

    $update = "UPDATE carros SET 
                placa = '$placa', 
                renavam = '$renavam', 
                crv = '$crv', 
                codigo_seguranca = '$codigo_seguranca',
                pdf_path = '$novo_pdf'
               WHERE id = $id";

    if ($conn->query($update) === TRUE) {
        header("Location: lista_carros.php?msg=Carro atualizado com sucesso!");
        exit();
    } else {
        echo "Erro ao atualizar: " . $conn->error;
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
      <h2 class="mb-4">Editar Carro</h2>

      <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label">Placa</label>
          <input type="text" class="form-control" name="placa" value="<?= htmlspecialchars($carro['placa']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Renavam</label>
          <input type="text" class="form-control" name="renavam" value="<?= htmlspecialchars($carro['renavam']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">CRV</label>
          <input type="text" class="form-control" name="crv" value="<?= htmlspecialchars($carro['crv']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Código de Segurança</label>
          <input type="text" class="form-control" name="codigo_seguranca" value="<?= htmlspecialchars($carro['codigo_seguranca']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Documento (PDF)</label>
            <input type="file" class="form-control" name="documento" accept="application/pdf">
            <?php if (!empty($carro['pdf_path'])): ?>
                <p class="mt-2">
                Documento atual: 
                <a href="uploads/<?= htmlspecialchars($carro['pdf_path']) ?>" target="_blank" class="btn btn-sm btn-success">Visualizar PDF</a>
                </p>
            <?php endif; ?>
            </div>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="lista_carros.php" class="btn btn-secondary ms-2">Cancelar</a>
      </form>
    </div>
  </div>
</body>
</html>
