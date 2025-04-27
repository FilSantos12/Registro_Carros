
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Sistema de Registro de Carros</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background-image: url('images/logo.jpg'); /* Caminho da imagem */
      background-size: cover;       /* Faz a imagem cobrir toda a tela */
      background-position: center;  /* Centraliza a imagem */
      background-repeat: no-repeat; /* Não repete a imagem */
      min-height: 100vh;             /* Garante altura total da tela */
    }

    .container-fluid {
     
      border-radius: 10px;
      padding: 30px;
      margin-top: 30px; /* Espaçamento extra acima */
    }
  </style>
</head>
<body>
  <div class="d-flex">
    <?php include 'inc/menu.php'; ?>

    <div class="container-fluid p-4 mt-4">
      <h2>Bem-vindo!</h2>
    </div>
  </div>
</body>
</html>
