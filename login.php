<?php
session_start();

// Conexão com o banco
$host = "127.0.0.1:3306";
$user = "root";
$password = "admin";
$dbname = 'cadastro_carros';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Inicializar a variável para evitar erro de variável indefinida
$mensagem = "";

// Quando o formulário for enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND senha = '$senha'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $_SESSION['usuario'] = $usuario;
        header("Location: index.php");
        exit();
    } else {
        $mensagem = "<div class='alert alert-danger'>Usuário ou senha inválidos.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* Estilo de fundo */
    body {
      background: linear-gradient(135deg, #4e54c8, #8f94fb); /* Gradiente moderno */
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
    }

    /* Card de login */
    .card {
      width: 100%;
      max-width: 400px;
      background: rgba(255, 255, 255, 0.9); /* Fundo mais claro e suave */
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); /* Sombra mais suave */
      padding: 30px;
      transition: transform 0.3s ease-in-out;
    }

    .card:hover {
      transform: translateY(-5px); /* Efeito de hover para o card */
    }

    h3 {
      font-size: 2rem;
      color: #333;
      font-weight: 700;
      text-align: center;
      margin-bottom: 30px;
    }

    .form-label {
      font-weight: bold;
    }

    .form-control {
      border-radius: 10px;
      padding: 10px;
      transition: border 0.3s ease;
    }

    .form-control:focus {
      border-color: #4e54c8; /* Cor de foco personalizada */
      box-shadow: 0 0 5px rgba(78, 84, 200, 0.8); /* Efeito de foco */
    }

    .btn {
      background-color: #4e54c8; /* Cor do botão */
      border-radius: 10px;
      padding: 12px;
      font-size: 1rem;
      transition: background-color 0.3s ease;
      width: 100%;
    }

    .btn:hover {
      background-color: #8f94fb; /* Efeito de hover no botão */
    }

    .alert {
      margin-top: 20px;
    }
  </style>
</head>
<body>

  <div class="card">
    <h3>Login</h3>

    <?php if (!empty($mensagem)) { echo $mensagem; } ?>

    <form action="login.php" method="POST">
      <div class="mb-3">
        <label class="form-label">Usuário</label>
        <input type="text" class="form-control" name="usuario" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Senha</label>
        <input type="password" class="form-control" name="senha" required>
      </div>
      <button type="submit" class="btn">Entrar</button>
    </form>
  </div>

</body>
</html>
