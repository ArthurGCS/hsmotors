<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirectWithAlert('../clientes/listar.php', 'ID do cliente não fornecido.', 'danger');
}

$id = $_GET['id'];

// Verificar se o cliente existe
$stmt = $conn->prepare("SELECT * FROM clientes WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$cliente = $stmt->fetch();

if (!$cliente) {
    redirectWithAlert('../clientes/listar.php', 'Cliente não encontrado.', 'danger');
}

// Excluir o cliente (as relações serão excluídas automaticamente devido à restrição ON DELETE CASCADE)
try {
    $stmt = $conn->prepare("DELETE FROM clientes WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    redirectWithAlert('../clientes/listar.php', 'Cliente excluído com sucesso!');
} catch(PDOException $e) {
    redirectWithAlert('../clientes/listar.php', 'Erro ao excluir cliente: ' . $e->getMessage(), 'danger');
}
?>

