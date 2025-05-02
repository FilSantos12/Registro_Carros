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

function placaValida($placa) {
    $placa = strtoupper(str_replace(['-', ' '], '', $placa));
    return preg_match('/^[A-Z]{3}[0-9]{4}$/', $placa) || preg_match('/^[A-Z]{3}[0-9]{1}[A-Z]{1}[0-9]{2}$/', $placa);
}

// Configurações
$pastaUpload = __DIR__ . '/uploads/';
$mensagem = "";
$tamanhoMaximo = 10 * 1024 * 1024; // 10MB

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Processamento do formulário de cadastro individual
    $placa = strtoupper(str_replace(['-', ' '], '', $_POST['placa']));
    $renavam = $_POST['renavam'];
    $crv = $_POST['crv'];
    $codigo = $_POST['codigo_seguranca'];
    $observacoes = $_POST['observacoes'] ?? null;

    if (!placaValida($placa)) {
        $mensagem = "<div class='alert alert-danger'>Placa inválida. Use o formato ABC-1234 ou ABC1D23.</div>";
    } else {
        $pdf_path = null;

        // Processamento do arquivo PDF se foi enviado
        if (isset($_FILES['documento']) && $_FILES['documento']['error'] == UPLOAD_ERR_OK) {
            $arquivoTmp = $_FILES['documento']['tmp_name'];
            $nomeArquivo = $_FILES['documento']['name'];
            $extensao = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));

            if ($extensao !== 'pdf') {
                $mensagem = "<div class='alert alert-danger'>Apenas arquivos PDF são permitidos!</div>";
            } elseif ($_FILES['documento']['size'] > $tamanhoMaximo) {
                $mensagem = "<div class='alert alert-danger'>O arquivo excede o limite de 10MB!</div>";
            } else {
                // Garantir que a pasta de uploads existe
                if (!is_dir($pastaUpload)) {
                    mkdir($pastaUpload, 0777, true);
                }

                $novoNome = uniqid('doc_') . '.pdf';
                $caminhoFinal = $pastaUpload . $novoNome;

                if (move_uploaded_file($arquivoTmp, $caminhoFinal)) {
                    $pdf_path = $novoNome;
                } else {
                    $mensagem = "<div class='alert alert-danger'>Falha ao salvar o arquivo PDF.</div>";
                }
            }
        }

        // Só prossegue se não houve erro com o arquivo
        if (empty($mensagem)) {
            try {
                // Verifica se a placa já existe
                $checkStmt = $conn->prepare("SELECT id FROM carros WHERE placa = ?");
                $checkStmt->bind_param("s", $placa);
                $checkStmt->execute();
                $result = $checkStmt->get_result();

                if ($result->num_rows > 0) {
                    // Atualiza veículo existente
                    $sql = "UPDATE carros SET 
                            renavam = ?, 
                            crv = ?, 
                            codigo_seguranca = ?, 
                            observacoes = CONCAT(IFNULL(observacoes, ''), '\n', ?)" .
                            ($pdf_path ? ", pdf_path = ?" : "") . 
                            " WHERE placa = ?";
                    
                    $stmt = $conn->prepare($sql);
                    if ($pdf_path) {
                        $stmt->bind_param("ssssss", $renavam, $crv, $codigo, $observacoes, $pdf_path, $placa);
                    } else {
                        $stmt->bind_param("sssss", $renavam, $crv, $codigo, $observacoes, $placa);
                    }
                } else {
                    // Insere novo veículo
                    $sql = "INSERT INTO carros 
                            (placa, renavam, crv, codigo_seguranca, observacoes" . 
                            ($pdf_path ? ", pdf_path" : "") . ") 
                            VALUES (?, ?, ?, ?, ?" . ($pdf_path ? ", ?" : "") . ")";
                    
                    $stmt = $conn->prepare($sql);
                    if ($pdf_path) {
                        $stmt->bind_param("ssssss", $placa, $renavam, $crv, $codigo, $observacoes, $pdf_path);
                    } else {
                        $stmt->bind_param("sssss", $placa, $renavam, $crv, $codigo, $observacoes);
                    }
                }

                if ($stmt->execute()) {
                    $mensagem = "<div class='alert alert-success'>Veículo cadastrado com sucesso!</div>";
                    // Limpa os campos do formulário após cadastro bem-sucedido
                    $_POST = array();
                } else {
                    $mensagem = "<div class='alert alert-danger'>Erro ao cadastrar: " . $conn->error . "</div>";
                    // Remove o arquivo PDF se houve erro no banco
                    if ($pdf_path) {
                        @unlink($pastaUpload . $pdf_path);
                    }
                }
            } catch (Exception $e) {
                $mensagem = "<div class='alert alert-danger'>Erro: " . $e->getMessage() . "</div>";
                if ($pdf_path) {
                    @unlink($pastaUpload . $pdf_path);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastro de Veículos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/jquery.inputmask.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="d-flex">
    <?php include 'menu.php'; ?>

    <div class="container-fluid p-4">
      <h2 class="mb-4"><i class="bi bi-car-front-fill"></i> Cadastro de Veículos</h2>
      
      <?php if (!empty($mensagem)) echo $mensagem; ?>

      <form method="POST" enctype="multipart/form-data">
        <!-- Placa -->
        <div class="row">    
          <div class="col-md-6 mb-3">
            <label for="placa" class="form-label"><i class="bi bi-credit-card-2-front"></i> Placa</label>
            <input type="text" class="form-control" id="placa" name="placa" required 
                   value="<?= htmlspecialchars($_POST['placa'] ?? '') ?>">
          </div>
          <!-- Código do Renavam -->
          <div class="col-md-6 mb-3">
            <label for="renavam" class="form-label"><i class="bi bi-upc"></i> Código do Renavam</label>
            <input type="text" class="form-control" id="renavam" name="renavam" required 
                   value="<?= htmlspecialchars($_POST['renavam'] ?? '') ?>">
          </div>
        </div>
        <!-- Número do CRV -->
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="crv" class="form-label"><i class="bi bi-123"></i> Número do CRV</label>
            <input type="text" class="form-control" id="crv" name="crv" required 
                   value="<?= htmlspecialchars($_POST['crv'] ?? '') ?>">
          </div>
          <!-- Número de segurança do CRV -->    
          <div class="col-md-6 mb-3">
            <label for="codigo" class="form-label"><i class="bi bi-lock-fill"></i> Número de segurança do CRV</label>
            <input type="text" class="form-control" id="codigo" name="codigo_seguranca" required 
                   value="<?= htmlspecialchars($_POST['codigo_seguranca'] ?? '') ?>">
          </div>
        </div>
        <!-- Observações -->
        <div class="mb-3">
          <label for="editObservacoes" class="form-label"><i class="bi bi-clipboard2-fill"></i> Observações</label>
          <input type="text" name="observacoes" class="form-control" id="editObservacoes" 
                 value="<?= htmlspecialchars($_POST['observacoes'] ?? '') ?>">
        </div>
        <!-- PDF -->
        <div class="mb-3">
          <label for="documento" class="form-label"><i class="bi bi-file-pdf-fill"></i> PDF</label>
          <input type="file" class="form-control" id="documento" name="documento" accept="application/pdf">
          <small class="text-muted">Tamanho máximo: 10MB</small>
        </div>
        <button type="submit" class="btn btn-success"><i class="bi bi-check"></i> Cadastrar</button>
      </form>
    </div>
  </div>

  <script>
    // Máscara para placa de veículo
    $(document).ready(function() {
      $('#placa').inputmask('AAA-9999');  // A máscara para a placa, ex: ABC-1234
    });
  </script>
</body>
</html>