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

// Parte nova: busca
$busca = "";
if (isset($_GET['busca'])) {
    $busca = $conn->real_escape_string($_GET['busca']);
    $sql = "SELECT * FROM carros 
            WHERE placa LIKE '%$busca%' 
               OR renavam LIKE '%$busca%'
            ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM carros ORDER BY id DESC";
}

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Lista de Veículos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="d-flex">
    <?php include 'menu.php'; ?>

    <div class="container-fluid p-4">
      <h2 class="mb-4">Lista de Veículos</h2>

      <form method="GET" class="row mb-4">
        <div class="col-md-4">
          <input type="text" name="busca" class="form-control" placeholder="Buscar por placa ou renavam" value="<?= htmlspecialchars($busca) ?>">
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-search"></i> Buscar
          </button>
        </div>
        <div class="col-md-2">
          <a href="lista_carros.php" class="btn btn-secondary w-100">
            <i class="bi bi-x-circle"></i> Limpar
          </a>
        </div>
      </form>

      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Placa</th>
            <th>Renavam</th>
            <th>CRV</th>
            <th>Código Segurança</th>
            <th>Documento (PDF)</th>
            <th>Ações</th> <!-- Nova coluna para os botões -->
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['placa']) ?></td>
                <td><?= htmlspecialchars($row['renavam']) ?></td>
                <td><?= htmlspecialchars($row['crv']) ?></td>
                <td><?= htmlspecialchars($row['codigo_seguranca']) ?></td>
                <td>
                  <?php if (!empty($row['pdf_path'])): ?>
                    <a href="uploads/<?= htmlspecialchars($row['pdf_path']) ?>" class="btn btn-sm btn-success" target="_blank">
                      Visualizar
                    </a>
                    <a href="uploads/<?= htmlspecialchars($row['pdf_path']) ?>" download class="btn btn-sm btn-primary ms-2">
                      Baixar
                    </a>
                  <?php else: ?>
                    <span class="text-muted">Nenhum PDF</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="edit_car.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                  <a href="delete_car.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este carro?');">Excluir</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center">Nenhum carro cadastrado.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

    </div>
  </div>
</body>
</html>
