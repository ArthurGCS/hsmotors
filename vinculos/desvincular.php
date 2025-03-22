<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar se os IDs foram fornecidos
if (!isset($_GET['cliente_id']) || empty($_GET['cliente_id']) || !isset($_GET['veiculo_id']) || empty($_GET['veiculo_id'])) {
    redirectWithAlert('../vinculos/listar.php', 'IDs do cliente e veículo não fornecidos.', 'danger');
}

$cliente_id = $_GET['cliente_id'];
$veiculo_id = $_GET['veiculo_id'];

// Verificar se o vínculo existe
$stmt = $conn->prepare("SELECT * FROM cliente_veiculo WHERE cliente_id = :cliente_id AND veiculo_id = :veiculo_id");
$stmt->bindParam(':cliente_id', $cliente_id);
$stmt->bindParam(':veiculo_id', $veiculo_id);
$stmt->execute();
$vinculo = $stmt->fetch();

if (!$vinculo) {
    redirectWithAlert('../vinculos/listar.php', 'Vínculo não encontrado.', 'danger');
}

// Excluir o vínculo
try {
    $stmt = $conn->prepare("DELETE FROM cliente_veiculo WHERE cliente_id = :cliente_id AND veiculo_id = :veiculo_id");
    $stmt->bindParam(':cliente_id', $cliente_id);
    $stmt->bindParam(':veiculo_id', $veiculo_id);
    $stmt->execute();
    
    redirectWithAlert('../vinculos/listar.php', 'Vínculo removido com sucesso!');
} catch(PDOException $e) {
    redirectWithAlert('../vinculos/listar.php', 'Erro ao remover vínculo: ' . $e->getMessage(), 'danger');
}
?>

