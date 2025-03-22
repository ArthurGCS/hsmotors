<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirectWithAlert('../clientes/listar.php', 'ID do cliente não fornecido.', 'danger');
}

$id = $_GET['id'];

// Buscar dados do cliente
$stmt = $conn->prepare("SELECT * FROM clientes WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$cliente = $stmt->fetch();

if (!$cliente) {
    redirectWithAlert('../clientes/listar.php', 'Cliente não encontrado.', 'danger');
}

// Buscar veículos vinculados ao cliente
$stmt = $conn->prepare("
    SELECT v.* 
    FROM veiculos v
    JOIN cliente_veiculo cv ON v.id = cv.veiculo_id
    WHERE cv.cliente_id = :cliente_id
");
$stmt->bindParam(':cliente_id', $id);
$stmt->execute();
$veiculos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Cliente - HS Motors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2><i class="fas fa-user"></i> Detalhes do Cliente</h2>
            </div>
            <div class="col-md-4 text-end">
                <a href="listar.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="editar.php?id=<?php echo $cliente['id']; ?>" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5><i class="fas fa-user-circle"></i> Informações Pessoais</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4 detail-label">Nome:</div>
                            <div class="col-md-8"><?php echo $cliente['nome']; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 detail-label">CPF:</div>
                            <div class="col-md-8"><?php echo $cliente['cpf']; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 detail-label">Data de Nascimento:</div>
                            <div class="col-md-8"><?php echo date('d/m/Y', strtotime($cliente['data_nascimento'])); ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 detail-label">Data de Cadastro:</div>
                            <div class="col-md-8"><?php echo date('d/m/Y H:i', strtotime($cliente['data_cadastro'])); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5><i class="fas fa-address-card"></i> Informações de Contato</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4 detail-label">Email:</div>
                            <div class="col-md-8"><?php echo $cliente['email']; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 detail-label">Telefone:</div>
                            <div class="col-md-8"><?php echo $cliente['telefone']; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 detail-label">Endereço:</div>
                            <div class="col-md-8"><?php echo $cliente['endereco']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header bg-success text-white">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-car"></i> Veículos Vinculados</h5>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="../vinculos/cadastrar.php?cliente_id=<?php echo $cliente['id']; ?>" class="btn btn-light btn-sm">
                            <i class="fas fa-plus"></i> Vincular Novo Veículo
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php if (count($veiculos) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Marca/Modelo</th>
                                    <th>Ano</th>
                                    <th>Placa</th>
                                    <th>Cor</th>
                                    <th>Valor</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($veiculos as $veiculo): ?>
                                    <tr>
                                        <td><?php echo $veiculo['id']; ?></td>
                                        <td><?php echo $veiculo['marca'] . ' ' . $veiculo['modelo']; ?></td>
                                        <td><?php echo $veiculo['ano']; ?></td>
                                        <td><?php echo $veiculo['placa']; ?></td>
                                        <td><?php echo $veiculo['cor']; ?></td>
                                        <td>R$ <?php echo number_format($veiculo['valor'], 2, ',', '.'); ?></td>
                                        <td>
                                            <a href="../veiculos/visualizar.php?id=<?php echo $veiculo['id']; ?>" class="btn btn-sm btn-info btn-action" data-bs-toggle="tooltip" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="../vinculos/desvincular.php?cliente_id=<?php echo $cliente['id']; ?>&veiculo_id=<?php echo $veiculo['id']; ?>" class="btn btn-sm btn-danger btn-action btn-delete" data-bs-toggle="tooltip" title="Desvincular">
                                                <i class="fas fa-unlink"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Este cliente não possui veículos vinculados.
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                        Total de veículos: <strong><?php echo count($veiculos); ?></strong>
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

