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

// Conectar no banco
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Pegar o ID pela URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Primeiro busca o PDF para excluir do servidor (se quiser)
    $query = "SELECT pdf_path FROM carros WHERE id = $id";
    $result = $conn->query($query);
    $carro = $result->fetch_assoc();

    if ($carro && !empty($carro['pdf_path'])) {
        $arquivo = 'uploads/' . $carro['pdf_path'];
        if (file_exists($arquivo)) {
            unlink($arquivo); // Deleta o arquivo
        }
    }

    // Agora deleta o registro
    $sql = "DELETE FROM carros WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header("Location: lista_carros.php?msg=Carro excluído com sucesso!");
        exit();
    } else {
        echo "Erro ao excluir: " . $conn->error;
    }
} else {
    header("Location: lista_carros.php");
    exit();
}
?>
