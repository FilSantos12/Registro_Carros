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

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $senha = trim($_POST['senha']);

    // Consulta segura usando prepared statements
    $sql = "SELECT id, usuario, senha FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Erro na preparação da consulta: " . $conn->error);
    }
    
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        
        // VERIFICAÇÃO CORRETA DA SENHA COM HASH
        if (password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario'] = $usuario['usuario'];
            $_SESSION['logado'] = true;
            
            header("Location: index.php");
            exit();
        } else {
            $mensagem = "<div class='alert alert-danger'>Senha incorreta!</div>";
        }
    } else {
        $mensagem = "<div class='alert alert-danger'>Usuário não encontrado!</div>";
    }
    
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    /* Estilo de fundo */
    body {
      background: linear-gradient(135deg, #4e54c8, #8f94fb);
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
      background: rgba(255, 255, 255, 0.9);
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      padding: 30px;
      transition: transform 0.3s ease-in-out;
    }

    .card:hover {
      transform: translateY(-5px);
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
      border-color: #4e54c8;
      box-shadow: 0 0 5px rgba(78, 84, 200, 0.8);
    }

    .btn {
      background-color: #4e54c8;
      border-radius: 10px;
      padding: 12px;
      font-size: 1rem;
      transition: background-color 0.3s ease;
      width: 100%;
    }

    .btn:hover {
      background-color: #8f94fb;
    }

    .alert {
      margin-top: 20px;
    }
  </style>
</head>
<body>

  <div class="card">
    <h3><i class="bi bi-box-arrow-in-right"></i> Login</h3>

    <?php if (!empty($mensagem)) { echo $mensagem; } ?>

    <form action="login.php" method="POST">
      <div class="mb-3">
        <label class="form-label"><i class="bi bi-person-fill"></i> Usuário</label>
        <input type="text" class="form-control" name="usuario" required>
      </div>
      <div class="mb-3">
        <label class="form-label"><i class="bi bi-key-fill"></i> Senha</label>
        <input type="password" class="form-control" name="senha" required>
      </div>
      <button type="submit" class="btn">Entrar</button>
    </form>
  </div>

</body>
</html>