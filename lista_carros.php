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
      <h2 class="mb-4"><i class="bi bi-card-list me-2"></i> Lista de Veículos</h2>

      <div class="card mb-4">
        <div class="card-body">
          <form method="GET" class="row">
            <div class="col-md-4 mb-2 mb-md-0">
              <input type="text" name="busca" class="form-control" placeholder="Buscar por placa ou renavam" value="<?= htmlspecialchars($busca) ?>">
            </div>
            <div class="col-md-2 mb-2 mb-md-0">
              <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Buscar
              </button>
            </div>
            <div class="col-md-2">
              <a href="lista_carros.php" class="btn btn-secondary w-100">
                <i class="bi bi-x-circle"></i> Limpar
              </a>
            </div>
            <div class="col-md-2 ms-auto">
              <a href="add_car.php" class="btn btn-success w-100">
                <i class="bi bi-plus-circle"></i> Novo Veículo
              </a>
            </div>
          </form>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Placa</th>
              <th>Renavam</th>
              <th>CRV</th>
              <th>Código Segurança</th>
              <th>Observações</th>
              <th>Documento</th>
              <th class="text-end">Ações</th>
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
                  <td class="observacao-cell" title="<?= htmlspecialchars($row['observacoes']) ?>">
                    <?= htmlspecialchars($row['observacoes']) ?>
                  </td>
                  <td class="text-nowrap">
                    <?php if (!empty($row['pdf_path'])): ?>
                      <div class="d-flex gap-2">
                        <a href="uploads/<?= htmlspecialchars($row['pdf_path']) ?>" class="btn btn-sm btn-success" target="_blank">
                          <i class="bi bi-eye"></i>
                        </a>
                        <a href="uploads/<?= htmlspecialchars($row['pdf_path']) ?>" download class="btn btn-sm btn-primary">
                          <i class="bi bi-download"></i>
                        </a>
                      </div>
                    <?php else: ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                  <td class="action-buttons text-end">
                    <div class="d-flex gap-2 justify-content-end">
                      <a href="edit_car.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning" title="Editar">
                        <i class="bi bi-pencil"></i>
                      </a>
                      <a href="delete_car.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" 
                         onclick="return confirm('Tem certeza que deseja excluir este veículo?');" title="Excluir">
                        <i class="bi bi-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="8" class="text-center py-4 text-muted">
                  <i class="bi bi-info-circle me-2"></i> Nenhum veículo encontrado
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
