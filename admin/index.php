<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HS Motors - Sistema de Cadastro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <?php include '../includes/admin/header.php'; ?>
    
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12 text-center mb-5">
                <h1 class="display-4">Bem-vindo ao HS Motors</h1>
                <p class="lead">Sistema de Gerenciamento de Clientes e Veículos</p>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-4x mb-3 text-primary"></i>
                        <h3 class="card-title">Clientes</h3>
                        <p class="card-text">Gerencie o cadastro de clientes</p>
                        <a href="clientes/listar.php" class="btn btn-primary">Acessar</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <i class="fas fa-car fa-4x mb-3 text-success"></i>
                        <h3 class="card-title">Veículos</h3>
                        <p class="card-text">Gerencie o cadastro de veículos</p>
                        <a href="veiculos/listar.php" class="btn btn-success">Acessar</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <i class="fas fa-link fa-4x mb-3 text-warning"></i>
                        <h3 class="card-title">Vínculos</h3>
                        <p class="card-text">Gerencie os vínculos entre clientes e veículos</p>
                        <a href="vinculos/listar.php" class="btn btn-warning">Acessar</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h4>Estatísticas do Sistema</h4>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h5>Total de Clientes</h5>
                                <h2 class="text-primary"><?php echo countRecords($conn, 'clientes'); ?></h2>
                            </div>
                            <div class="col-md-4">
                                <h5>Total de Veículos</h5>
                                <h2 class="text-success"><?php echo countRecords($conn, 'veiculos'); ?></h2>
                            </div>
                            <div class="col-md-4">
                                <h5>Total de Vínculos</h5>
                                <h2 class="text-warning"><?php echo countRecords($conn, 'cliente_veiculo'); ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/admin/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/admin.js"></script>
</body>
</html>

