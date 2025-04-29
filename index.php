<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Sistema de Registro de Carros</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">

  <style>
    body {
      background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                  url('images/laptop.jpg') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      color: #fff; /* Deixa o texto branco pra destacar no fundo */
    }

    .container-fluid {
      background: rgba(255, 255, 255, 0.1); /* Fundo semi-transparente */
      backdrop-filter: blur(10px); /* Efeito de vidro (glassmorphism) */
      border-radius: 20px;
      padding: 40px;
      margin-top: 40px;
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37); /* Sombra moderna */
      color: #ffffff;
    }

    h2 {
      font-weight: bold;
      font-size: 2.5rem;
      letter-spacing: 1px;
    }

  </style>
</head>

<body>
  <div class="d-flex">
    <?php include 'menu.php'; ?>

    <div class="container-fluid p-4 mt-4">
      <h2>Bem-vindo!</h2>
      <p class="mt-3">Sistema moderno de controle de veículos.</p>
      
    </div>
  </div>
</body>
</html>
