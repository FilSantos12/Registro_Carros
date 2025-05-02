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

if (!isset($_SESSION['resultado_upload'])) {
    header("Location: upload_em_massa.php");
    exit();
}

$resultado = $_SESSION['resultado_upload'];
unset($_SESSION['resultado_upload']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Resultado do Upload</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
  <style>
    .main-content {
      margin-left: 250px; /* Ajuste conforme a largura do seu menu */
      padding: 20px;
      width: calc(100% - 250px);
    }
    @media (max-width: 992px) {
      .main-content {
        margin-left: 0;
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="d-flex">
    <?php include 'menu.php'; ?>

    <div class="main-content">
      <div class="container-fluid p-4">
        <h2 class="mb-4"><i class="bi bi-list-check me-2"></i>Resultado do Upload</h2>

        <div class="card">
          <div class="card-body">
            <?php if ($resultado['sucesso'] > 0): ?>
              <div class="alert alert-success">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong><?= $resultado['sucesso'] ?> arquivo(s)</strong> processado(s) com sucesso!
              </div>
            <?php endif; ?>

            <?php if (!empty($resultado['erros'])): ?>
              <div class="alert alert-danger">
                <h5><i class="bi bi-exclamation-triangle-fill me-2"></i>Erros encontrados:</h5>
                <ul class="mb-0">
                  <?php foreach ($resultado['erros'] as $erro): ?>
                    <li><?= htmlspecialchars($erro) ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>

            <div class="mt-3">
              <a href="upload_em_massa.php" class="btn btn-primary">
                <i class="bi bi-arrow-left me-2"></i>Voltar
              </a>
              <a href="lista_carros.php" class="btn btn-secondary ms-2">
                <i class="bi bi-list-ul me-2"></i>Ver Veículos
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>