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
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-image: url('images/logo.jpg'); /* Caminho da imagem de fundo */
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card {
      width: 100%;
      max-width: 400px;
      background: rgba(255, 255, 255, 0.9); /* Fundo branco semi-transparente */
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2); /* Sombra para destacar o card */
      padding: 20px;
    }
  </style>
</head>
<body>

  <div class="card">
    <h3 class="text-center mb-3">Login</h3>

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
      <button type="submit" class="btn btn-primary w-100">Entrar</button>
    </form>
  </div>

</body>
</html>
