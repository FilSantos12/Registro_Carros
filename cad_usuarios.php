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

    // Verifica se o usuário já existe no banco de dados
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $mensagem = "<div class='alert alert-danger'>Usuário já existe. Escolha outro nome de usuário.</div>";
    } else {
        // Criptografando a senha antes de salvar no banco
        $hashed_password = password_hash($senha, PASSWORD_DEFAULT);

        // Insere o novo usuário no banco de dados
        $stmt = $conn->prepare("INSERT INTO usuarios (usuario, senha) VALUES (?, ?)");
        $stmt->bind_param("ss", $usuario, $hashed_password);
        if ($stmt->execute()) {
            $mensagem = "<div class='alert alert-success'>Cadastro realizado com sucesso. Você pode fazer login agora.</div>";
        } else {
            $mensagem = "<div class='alert alert-danger'>Erro ao cadastrar usuário.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastro de Usuário</title>
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
      <h2 class="mb-4">Cadastro de Usuário</h2>

      <?= $mensagem ?>

      <form action="cad_usuarios.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label"><i class="bi bi-person-fill"></i> Usuário</label>
          <input type="text" class="form-control" name="usuario" required>
        </div>
        <div class="mb-3">
          <label class="form-label"><i class="bi bi-key-fill"></i> Senha</label>
          <input type="password" class="form-control" name="senha" required>
        </div>

        <button type="submit" class="btn btn-primary"><i class="bi bi-check"></i> Cadastrar</button>
      </form>
    </div>
  </div>
</body>
</html>
