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

// Variável para mensagens
$mensagem = "";

// Deletar usuário se o botão for pressionado
if (isset($_GET['delete'])) {
    $usuario_id = $_GET['delete'];

    // Prepara a consulta de exclusão
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);

    if ($stmt->execute()) {
        $mensagem = "<div class='alert alert-success'>Usuário excluído com sucesso.</div>";
    } else {
        $mensagem = "<div class='alert alert-danger'>Erro ao excluir usuário.</div>";
    }
}

// Consulta para listar usuários
$sql = "SELECT * FROM usuarios";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Visualizar Usuários</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
 <link rel="stylesheet" href="styles.css">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .container-fluid {
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      padding: 30px;
    }
    h2 {
      font-size: 2rem;
      font-weight: bold;
      color: #343a40;
    }
    .table thead {
      background-color: #007bff;
      color: white;
    }
    .table tbody tr {
      transition: background-color 0.3s;
    }
    .table tbody tr:hover {
      background-color: #f1f1f1;
    }
    .btn-danger {
      background-color: #dc3545;
      border: none;
      border-radius: 5px;
    }
    .btn-danger:hover {
      background-color: #c82333;
    }
  </style>
</head>
<body>
  <div class="d-flex">
    <?php include 'menu.php'; ?>

    <div class="container-fluid p-4">
      <h2 class="mb-4">Users Cadastrados</h2>

      <?= $mensagem ?>

      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Usuário</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['usuario'] . "</td>";
                    echo "<td>
                            <a href='ver_usuarios.php?delete=" . $row['id'] . "' class='btn btn-danger btn-sm'>Excluir</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3' class='text-center'>Nenhum usuário cadastrado.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
