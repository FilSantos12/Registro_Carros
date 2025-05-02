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

// Configurações - CORREÇÃO CRÍTICA AQUI
$pastaUpload = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
$resultado = ['sucesso' => 0, 'erros' => []];
$tamanhoMaximo = 10 * 1024 * 1024; // 10MB

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_FILES['pdfs']['name'][0])) {
    
    // Verificação e criação do diretório com tratamento de erros
    if (!is_dir($pastaUpload)) {
        if (!mkdir($pastaUpload, 0777, true)) {
            die("ERRO: Não foi possível criar o diretório de uploads em: " . $pastaUpload);
        }
    }

    // Verifica se o diretório é gravável - CORREÇÃO IMPORTANTE
    if (!is_writable($pastaUpload)) {
        die("ERRO: O diretório de uploads não tem permissão de escrita: " . $pastaUpload);
    }

    foreach ($_FILES['pdfs']['tmp_name'] as $key => $tmpName) {
        $nomeOriginal = $_FILES['pdfs']['name'][$key];
        $erro = $_FILES['pdfs']['error'][$key];
        $tamanho = $_FILES['pdfs']['size'][$key];

        if ($erro !== UPLOAD_ERR_OK) {
            $resultado['erros'][] = "Erro no upload do arquivo {$nomeOriginal} (Código: $erro)";
            continue;
        }

        if ($tamanho > $tamanhoMaximo) {
            $resultado['erros'][] = "O arquivo {$nomeOriginal} excede o limite de 10MB";
            continue;
        }

        $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
        if ($extensao !== 'pdf') {
            $resultado['erros'][] = "{$nomeOriginal} não é um PDF válido!";
            continue;
        }

        $nomeBase = pathinfo($nomeOriginal, PATHINFO_FILENAME);
        $partes = explode('_', $nomeBase, 2);
        $placa = strtoupper(str_replace(['-', ' '], '', $partes[0] ?? ''));
        $tipoDoc = $partes[1] ?? 'documento';

        if (!placaValida($placa)) {
            $resultado['erros'][] = "Placa inválida no arquivo {$nomeOriginal}";
            continue;
        }

        // Gera nome único para o arquivo
        $novoNome = uniqid('doc_') . '.pdf';
        $caminhoFinal = $pastaUpload . $novoNome;

        // DEBUG: Mostra o caminho completo onde está tentando salvar
        error_log("Tentando salvar arquivo em: " . $caminhoFinal);

        // Move o arquivo para a pasta de uploads com verificação explícita
        if (move_uploaded_file($tmpName, $caminhoFinal)) {
            // DEBUG: Verifica se o arquivo realmente foi criado
            if (!file_exists($caminhoFinal)) {
                $resultado['erros'][] = "ERRO CRÍTICO: Arquivo {$nomeOriginal} não foi criado em {$caminhoFinal}";
                continue;
            }
            
            try {
                $observacao = date('') ;
                $renavam = '';
                $crv = '';
                $codigo = '';
                
                // Verifica se a placa já existe
                $checkStmt = $conn->prepare("SELECT id FROM carros WHERE placa = ?");
                $checkStmt->bind_param("s", $placa);
                $checkStmt->execute();
                $result = $checkStmt->get_result();
                
                if ($result->num_rows > 0) {
                    // Atualiza registro existente
                    $sql = "UPDATE carros SET 
                            pdf_path = ?, 
                            observacoes = CONCAT(IFNULL(observacoes, ''), '\n', ?) 
                            WHERE placa = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sss", $novoNome, $observacao, $placa);
                } else {
                    // Insere novo registro
                    $sql = "INSERT INTO carros 
                            (placa, renavam, crv, codigo_seguranca, pdf_path, observacoes) 
                            VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssss", $placa, $renavam, $crv, $codigo, $novoNome, $observacao);
                }
                
                if ($stmt->execute()) {
                    $resultado['sucesso']++;
                } else {
                    $resultado['erros'][] = "Erro ao salvar no banco de dados: " . $stmt->error;
                    @unlink($caminhoFinal); // Remove o arquivo se falhou no banco
                }
            } catch (Exception $e) {
                $resultado['erros'][] = "Erro ao salvar {$nomeOriginal}: " . $e->getMessage();
                @unlink($caminhoFinal); // Remove o arquivo em caso de erro
            }
        } else {
            $error = error_get_last();
            $resultado['erros'][] = "Falha ao mover {$nomeOriginal} para {$caminhoFinal}. Erro: " . ($error['message'] ?? 'Desconhecido');
        }
    }

    $_SESSION['resultado_upload'] = $resultado;
    header("Location: resultado_upload.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Upload em Massa de PDFs</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">

</head>
<body>
  <div class="d-flex">
    <?php include 'menu.php'; ?>

    <div class="main-content">
      <div class="container-fluid p-4">
        <h2 class="mb-4"><i class="bi bi-upload me-2"></i>Upload em Massa de PDFs</h2>

        <div class="card">
          <div class="card-body">
            <form method="POST" enctype="multipart/form-data" id="uploadForm">
              <div class="mb-3">
                <label class="form-label"><i class="bi bi-file-pdf-fill me-2"></i>Selecione os arquivos PDF</label>
                <input type="file" class="form-control" name="pdfs[]" multiple accept=".pdf" required id="fileInput">
                <small class="text-muted">
                  O sistema ira criar o cadastro do veiculo, conforme o nome do arquivo em PDF!
                </small>
                <div class="file-info" id="fileInfo">Tamanho máximo por arquivo: 10MB +ou- 50 arquivos</div>
              </div>
              
              <button type="submit" class="btn btn-success">
                <i class="bi bi-upload me-2"></i>Enviar Tudo
              </button>
              <a href="lista_carros.php" class="btn btn-secondary ms-2">
                <i class="bi bi-arrow-left me-2"></i>Voltar
              </a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
      const files = document.getElementById('fileInput').files;
      const maxSize = 10 * 1024 * 1024;
      let hasError = false;
      
      for (let i = 0; i < files.length; i++) {
        if (files[i].size > maxSize) {
          alert(`O arquivo "${files[i].name}" excede o limite de 10MB!`);
          hasError = true;
        }
      }
      
      if (hasError) {
        e.preventDefault();
      }
    });

    document.getElementById('fileInput').addEventListener('change', function() {
      const files = this.files;
      const infoDiv = document.getElementById('fileInfo');
      let totalSize = 0;
      
      if (files.length > 0) {
        for (let i = 0; i < files.length; i++) {
          totalSize += files[i].size;
        }
        
        const totalMB = (totalSize / (1024 * 1024)).toFixed(2);
        infoDiv.textContent = `${files.length} arquivo(s) selecionado(s) - Total: ${totalMB} MB`;
      } else {
        infoDiv.textContent = 'Tamanho máximo por arquivo: 10MB';
      }
    });
  </script>
</body>
</html>