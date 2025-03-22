<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Consulta para listar todos os clientes
$stmt = $conn->prepare("SELECT * FROM clientes ORDER BY nome");
$stmt->execute();
$clientes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Clientes - HS Motors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2><i class="fas fa-users"></i> Gerenciamento de Clientes</h2>
            </div>
            <div class="col-md-4 text-end">
                <a href="cadastrar.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Cliente
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-list"></i> Lista de Clientes</h5>
                    </div>
                    <div class="col-md-6">
                        <input type="text" id="searchInput" class="form-control" placeholder="Pesquisar cliente...">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="dataTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th>Email</th>
                                <th>Telefone</th>
                                <th>Data de Cadastro</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($clientes) > 0): ?>
                                <?php foreach ($clientes as $cliente): ?>
                                    <tr>
                                        <td><?php echo $cliente['id']; ?></td>
                                        <td><?php echo $cliente['nome']; ?></td>
                                        <td><?php echo $cliente['cpf']; ?></td>
                                        <td><?php echo $cliente['email']; ?></td>
                                        <td><?php echo $cliente['telefone']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($cliente['data_cadastro'])); ?></td>
                                        <td>
                                            <a href="visualizar.php?id=<?php echo $cliente['id']; ?>" class="btn btn-sm btn-info btn-action" data-bs-toggle="tooltip" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="editar.php?id=<?php echo $cliente['id']; ?>" class="btn btn-sm btn-warning btn-action" data-bs-toggle="tooltip" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="excluir.php?id=<?php echo $cliente['id']; ?>" class="btn btn-sm btn-danger btn-action btn-delete" data-bs-toggle="tooltip" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Nenhum cliente cadastrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                        Total de clientes: <strong><?php echo count($clientes); ?></strong>
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

