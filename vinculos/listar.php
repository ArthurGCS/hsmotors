<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Consulta para listar todos os vínculos
$stmt = $conn->prepare("
    SELECT cv.id, cv.cliente_id, cv.veiculo_id, cv.data_vinculo,
           c.nome as cliente_nome, c.cpf as cliente_cpf,
           v.marca, v.modelo, v.placa, v.ano, v.cor
    FROM cliente_veiculo cv
    JOIN clientes c ON cv.cliente_id = c.id
    JOIN veiculos v ON cv.veiculo_id = v.id
    ORDER BY cv.data_vinculo DESC
");
$stmt->execute();
$vinculos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Vínculos - HS Motors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2><i class="fas fa-link"></i> Gerenciamento de Vínculos</h2>
            </div>
            <div class="col-md-4 text-end">
                <a href="cadastrar.php" class="btn btn-warning">
                    <i class="fas fa-plus"></i> Novo Vínculo
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-warning text-white">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-list"></i> Lista de Vínculos</h5>
                    </div>
                    <div class="col-md-6">
                        <input type="text" id="searchInput" class="form-control" placeholder="Pesquisar vínculo...">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="dataTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Veículo</th>
                                <th>Data do Vínculo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($vinculos) > 0): ?>
                                <?php foreach ($vinculos as $vinculo): ?>
                                    <tr>
                                        <td><?php echo $vinculo['id']; ?></td>
                                        <td><?php echo $vinculo['cliente_nome'] . ' (' . $vinculo['cliente_cpf'] . ')'; ?></td>
                                        <td><?php echo $vinculo['marca'] . ' ' . $vinculo['modelo'] . ' - ' . $vinculo['placa']; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($vinculo['data_vinculo'])); ?></td>
                                        <td>
                                            <a href="../clientes/visualizar.php?id=<?php echo $vinculo['cliente_id']; ?>" class="btn btn-sm btn-primary btn-action" data-bs-toggle="tooltip" title="Ver Cliente">
                                                <i class="fas fa-user"></i>
                                            </a>
                                            <a href="../veiculos/visualizar.php?id=<?php echo $vinculo['veiculo_id']; ?>" class="btn btn-sm btn-success btn-action" data-bs-toggle="tooltip" title="Ver Veículo">
                                                <i class="fas fa-car"></i>
                                            </a>
                                            <a href="desvincular.php?cliente_id=<?php echo $vinculo['cliente_id']; ?>&veiculo_id=<?php echo $vinculo['veiculo_id']; ?>" class="btn btn-sm btn-danger btn-action btn-delete" data-bs-toggle="tooltip" title="Desvincular">
                                                <i class="fas fa-unlink"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">Nenhum vínculo cadastrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                        Total de vínculos: <strong><?php echo count($vinculos); ?></strong>
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

