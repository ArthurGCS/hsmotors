<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirectWithAlert('../veiculos/listar.php', 'ID do veículo não fornecido.', 'danger');
}

$id = $_GET['id'];

// Buscar dados do veículo
$stmt = $conn->prepare("SELECT * FROM veiculos WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$veiculo = $stmt->fetch();

if (!$veiculo) {
    redirectWithAlert('../veiculos/listar.php', 'Veículo não encontrado.', 'danger');
}

// Buscar cliente vinculado ao veículo
$stmt = $conn->prepare("
    SELECT c.* 
    FROM clientes c
    JOIN cliente_veiculo cv ON c.id = cv.cliente_id
    WHERE cv.veiculo_id = :veiculo_id
");
$stmt->bindParam(':veiculo_id', $id);
$stmt->execute();
$cliente = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Veículo - HS Motors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2><i class="fas fa-car"></i> Detalhes do Veículo</h2>
            </div>
            <div class="col-md-4 text-end">
                <a href="listar.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="editar.php?id=<?php echo $veiculo['id']; ?>" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-car-side"></i> Informações do Veículo</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4 detail-label">Marca:</div>
                            <div class="col-md-8"><?php echo $veiculo['marca']; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 detail-label">Modelo:</div>
                            <div class="col-md-8"><?php echo $veiculo['modelo']; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 detail-label">Ano:</div>
                            <div class="col-md-8"><?php echo $veiculo['ano']; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 detail-label">Placa:</div>
                            <div class="col-md-8"><?php echo $veiculo['placa']; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 detail-label">Cor:</div>
                            <div class="col-md-8"><?php echo $veiculo['cor']; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 detail-label">Chassi:</div>
                            <div class="col-md-8"><?php echo $veiculo['chassi']; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 detail-label">Valor:</div>
                            <div class="col-md-8">R$ <?php echo number_format($veiculo['valor'], 2, ',', '.'); ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 detail-label">Data de Cadastro:</div>
                            <div class="col-md-8"><?php echo date('d/m/Y H:i', strtotime($veiculo['data_cadastro'])); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-info-circle"></i> Descrição</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($veiculo['descricao'])): ?>
                            <p><?php echo nl2br($veiculo['descricao']); ?></p>
                        <?php else: ?>
                            <p class="text-muted">Nenhuma descrição disponível.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5><i class="fas fa-user"></i> Cliente Vinculado</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($cliente): ?>
                            <div class="cliente-info">
                                <div class="row mb-2">
                                    <div class="col-md-4 detail-label">Nome:</div>
                                    <div class="col-md-8"><?php echo $cliente['nome']; ?></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 detail-label">CPF:</div>
                                    <div class="col-md-8"><?php echo $cliente['cpf']; ?></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 detail-label">Telefone:</div>
                                    <div class="col-md-8"><?php echo $cliente['telefone']; ?></div>
                                </div>
                                <div class="mt-3">
                                    <a href="../clientes/visualizar.php?id=<?php echo $cliente['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> Ver Detalhes do Cliente
                                    </a>
                                    <a href="../vinculos/desvincular.php?cliente_id=<?php echo $cliente['id']; ?>&veiculo_id=<?php echo $veiculo['id']; ?>" class="btn btn-sm btn-danger btn-delete">
                                        <i class="fas fa-unlink"></i> Desvincular
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Este veículo não está vinculado a nenhum cliente.
                            </div>
                            <a href="../vinculos/cadastrar.php?veiculo_id=<?php echo $veiculo['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-link"></i> Vincular a um Cliente
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>

