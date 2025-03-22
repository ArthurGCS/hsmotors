<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar se foi fornecido cliente_id ou veiculo_id na URL
$cliente_id = isset($_GET['cliente_id']) ? $_GET['cliente_id'] : '';
$veiculo_id = isset($_GET['veiculo_id']) ? $_GET['veiculo_id'] : '';

// Buscar todos os clientes
$stmt = $conn->prepare("SELECT id, nome, cpf FROM clientes ORDER BY nome");
$stmt->execute();
$clientes = $stmt->fetchAll();

// Buscar todos os veículos disponíveis (não vinculados)
$stmt = $conn->prepare("
    SELECT v.id, v.marca, v.modelo, v.placa, v.ano, v.cor
    FROM veiculos v
    LEFT JOIN cliente_veiculo cv ON v.id = cv.veiculo_id
    WHERE cv.id IS NULL
    ORDER BY v.marca, v.modelo
");
$stmt->execute();
$veiculos_disponiveis = $stmt->fetchAll();

// Se um cliente específico foi selecionado, buscar seus dados
$cliente_selecionado = null;
if (!empty($cliente_id)) {
    $stmt = $conn->prepare("SELECT id, nome, cpf FROM clientes WHERE id = :id");
    $stmt->bindParam(':id', $cliente_id);
    $stmt->execute();
    $cliente_selecionado = $stmt->fetch();
}

// Se um veículo específico foi selecionado, buscar seus dados
$veiculo_selecionado = null;
if (!empty($veiculo_id)) {
    $stmt = $conn->prepare("SELECT id, marca, modelo, placa, ano, cor FROM veiculos WHERE id = :id");
    $stmt->bindParam(':id', $veiculo_id);
    $stmt->execute();
    $veiculo_selecionado = $stmt->fetch();
    
    // Verificar se o veículo já está vinculado
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM cliente_veiculo WHERE veiculo_id = :veiculo_id");
    $stmt->bindParam(':veiculo_id', $veiculo_id);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result['total'] > 0) {
        redirectWithAlert('../vinculos/listar.php', 'Este veículo já está vinculado a um cliente.', 'danger');
    }
}

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $veiculo_id = $_POST['veiculo_id'];
    
    // Validações
    $errors = [];
    
    if (empty($cliente_id)) {
        $errors[] = "Selecione um cliente.";
    }
    
    if (empty($veiculo_id)) {
        $errors[] = "Selecione um veículo.";
    }
    
    // Verificar se o veículo já está vinculado
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM cliente_veiculo WHERE veiculo_id = :veiculo_id");
    $stmt->bindParam(':veiculo_id', $veiculo_id);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result['total'] > 0) {
        $errors[] = "Este veículo já está vinculado a um cliente.";
    }
    
    // Se não houver erros, inserir no banco de dados
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO cliente_veiculo (cliente_id, veiculo_id) VALUES (:cliente_id, :veiculo_id)");
            $stmt->bindParam(':cliente_id', $cliente_id);
            $stmt->bindParam(':veiculo_id', $veiculo_id);
            $stmt->execute();
            
            redirectWithAlert('../vinculos/listar.php', 'Vínculo cadastrado com sucesso!');
        } catch(PDOException $e) {
            $errors[] = "Erro ao cadastrar vínculo: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Vínculo - HS Motors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2><i class="fas fa-link"></i> Cadastrar Novo Vínculo</h2>
            </div>
            <div class="col-md-4 text-end">
                <a href="listar.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5><i class="fas fa-link"></i> Formulário de Vínculo</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="cliente_id" class="form-label">Cliente</label>
                            <select class="form-select" id="cliente_id" name="cliente_id" required <?php echo $cliente_selecionado ? 'disabled' : ''; ?>>
                                <option value="">Selecione um cliente</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?php echo $cliente['id']; ?>" <?php echo ($cliente_selecionado && $cliente['id'] == $cliente_selecionado['id']) ? 'selected' : ''; ?>>
                                        <?php echo $cliente['nome'] . ' (' . $cliente['cpf'] . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($cliente_selecionado): ?>
                                <input type="hidden" name="cliente_id" value="<?php echo $cliente_selecionado['id']; ?>">
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="veiculo_id" class="form-label">Veículo</label>
                            <select class="form-select" id="veiculo_id" name="veiculo_id" required <?php echo $veiculo_selecionado ? 'disabled' : ''; ?>>
                                <option value="">Selecione um veículo</option>
                                <?php if ($veiculo_selecionado): ?>
                                    <option value="<?php echo $veiculo_selecionado['id']; ?>" selected>
                                        <?php echo $veiculo_selecionado['marca'] . ' ' . $veiculo_selecionado['modelo'] . ' - ' . $veiculo_selecionado['placa']; ?>
                                    </option>
                                <?php else: ?>
                                    <?php foreach ($veiculos_disponiveis as $veiculo): ?>
                                        <option value="<?php echo $veiculo['id']; ?>">
                                            <?php echo $veiculo['marca'] . ' ' . $veiculo['modelo'] . ' - ' . $veiculo['placa']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if ($veiculo_selecionado): ?>
                                <input type="hidden" name="veiculo_id" value="<?php echo $veiculo_selecionado['id']; ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if (empty($veiculos_disponiveis) && !$veiculo_selecionado): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Não há veículos disponíveis para vínculo. Todos os veículos já estão vinculados a clientes.
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning" <?php echo (empty($veiculos_disponiveis) && !$veiculo_selecionado) ? 'disabled' : ''; ?>>
                            <i class="fas fa-save"></i> Cadastrar Vínculo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>

