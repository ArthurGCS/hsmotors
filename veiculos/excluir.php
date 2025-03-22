<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirectWithAlert('../veiculos/listar.php', 'ID do veículo não fornecido.', 'danger');
}

$id = $_GET['id'];

// Verificar se o veículo existe
$stmt = $conn->prepare("SELECT * FROM veiculos WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$veiculo = $stmt->fetch();

if (!$veiculo) {
    redirectWithAlert('../veiculos/listar.php', 'Veículo não encontrado.', 'danger');
}

// Verificar se o veículo está vinculado a algum cliente
if (isVeiculoVinculado($conn, $id)) {
    redirectWithAlert('../veiculos/listar.php', 'Não é possível excluir um veículo vinculado a um cliente. Desvincule-o primeiro.', 'danger');
}

// Excluir o veículo
try {
    $stmt = $conn->prepare("DELETE FROM veiculos WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    redirectWithAlert('../veiculos/listar.php', 'Veículo excluído com sucesso!');
} catch(PDOException $e) {
    redirectWithAlert('../veiculos/listar.php', 'Erro ao excluir veículo: ' . $e->getMessage(), 'danger');
}
?>

