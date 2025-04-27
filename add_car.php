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

function placaValida($placa) {
    $placa = strtoupper(str_replace(['-', ' '], '', $placa));
    return preg_match('/^[A-Z]{3}[0-9]{4}$/', $placa) || preg_match('/^[A-Z]{3}[0-9]{1}[A-Z]{1}[0-9]{2}$/', $placa);
}

$mensagem = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $placa = strtoupper(str_replace(['-', ' '], '', $_POST['placa']));
    $renavam = $_POST['renavam'];
    $crv = $_POST['crv'];
    $codigo = $_POST['codigo_seguranca'];

    if (!placaValida($placa)) {
        $mensagem = "<div class='alert alert-warning'>Placa inválida. Use o formato ABC-1234 ou ABC1D23.</div>";
    } else {
        $pdf_path = NULL; // Assume que pode não ter PDF

        // Se o usuário enviou arquivo e não deu erro
        if (isset($_FILES['documento']) && $_FILES['documento']['error'] == 0) {
            $arquivoTmp = $_FILES['documento']['tmp_name'];
            $nomeArquivo = basename($_FILES['documento']['name']);
            $extensao = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));

            if ($extensao != 'pdf') {
                $mensagem = "<div class='alert alert-warning'>Envie apenas arquivos PDF!</div>";
            } else {
                if (!is_dir('uploads')) {
                    mkdir('uploads', 0777, true);
                }

                $novoNome = uniqid('doc_') . '.pdf';
                $destino = 'uploads/' . $novoNome;

                if (move_uploaded_file($arquivoTmp, $destino)) {
                    $pdf_path = $novoNome; // Salva o nome para o banco
                } else {
                    $mensagem = "<div class='alert alert-danger'>Falha ao enviar o arquivo.</div>";
                }
            }
        }

        // Só insere no banco se não teve problema com o upload
        if (empty($mensagem)) {
            $sql = "INSERT INTO carros (placa, renavam, crv, codigo_seguranca, pdf_path)
                    VALUES ('$placa', '$renavam', '$crv', '$codigo', " . 
                    ($pdf_path ? "'$pdf_path'" : "NULL") . ")";

            if ($conn->query($sql) === TRUE) {
                $mensagem = "<div class='alert alert-success'>Carro cadastrado com sucesso!</div>";
            } else {
                $mensagem = "<div class='alert alert-danger'>Erro ao cadastrar: " . $conn->error . "</div>";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastro de Veiculos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/jquery.inputmask.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
  <div class="d-flex">
    <?php include 'inc/menu.php'; ?>

    <div class="container-fluid p-4">
      <h2 class="mb-4">Cadastro de Veiculos</h2>

      <?= $mensagem ?>

      <form action="add_car.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="placa" class="form-label">Placa</label>
          <input type="text" class="form-control" id="placa" name="placa" required>
        </div>
        <div class="mb-3">
          <label for="renavam" class="form-label">Codigo do Renavam</label>
          <input type="text" class="form-control" id="renavam" name="renavam" required>
        </div>
        <div class="mb-3">
          <label for="crv" class="form-label">Numero do CRV</label>
          <input type="text" class="form-control" id="crv" name="crv" required>
        </div>
        <div class="mb-3">
          <label for="codigo" class="form-label">Numero de segurança do CRV</label>
          <input type="text" class="form-control" id="codigo" name="codigo_seguranca" required>
        </div>
        <div class="mb-3">
          <label for="documento" class="form-label">Documento (PDF)</label>
          <input type="file" class="form-control" id="documento" name="documento" accept="application/pdf">
        </div>
        <button type="submit" class="btn btn-primary">Cadastrar</button>
      </form>
    </div>
  </div>

<script>
  $(document).ready(function(){
    $("#placa").inputmask({
      mask: [
        "AAA-9999", 
        "AAA9A99"
      ],
      keepStatic: true
    });
  });
</script>

</body>
</html>
