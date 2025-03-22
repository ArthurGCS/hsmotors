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

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marca = trim($_POST['marca']);
    $modelo = trim($_POST['modelo']);
    $ano = trim($_POST['ano']);
    $placa = strtoupper(trim($_POST['placa']));
    $cor = trim($_POST['cor']);
    $chassi = strtoupper(trim($_POST['chassi']));
    $valor = str_replace(',', '.', str_replace('.', '', $_POST['valor']));
    $descricao = trim($_POST['descricao']);
    
    // Validações
    $errors = [];
    
    if (empty($marca)) {
        $errors[] = "A marca é obrigatória.";
    }
    
    if (empty($modelo)) {
        $errors[] = "O modelo é obrigatório.";
    }
    
    if (empty($ano)) {
        $errors[] = "O ano é obrigatório.";
    } elseif (!is_numeric($ano) || $ano < 1900 || $ano > date('Y') + 1) {
        $errors[] = "Ano inválido.";
    }
    
    if (empty($placa)) {
        $errors[] = "A placa é obrigatória.";
    } elseif (!validarPlaca($placa)) {
        $errors[] = "Placa inválida. Use o formato AAA1234 ou AAA1A23.";
    }
    
    if (empty($cor)) {
        $errors[] = "A cor é obrigatória.";
    }
    
    if (empty($chassi)) {
        $errors[] = "O chassi é obrigatório.";
    } elseif (!validarChassi($chassi)) {
        $errors[] = "Chassi inválido. Deve conter 17 caracteres alfanuméricos.";
    }
    
    if (empty($valor)) {
        $errors[] = "O valor é obrigatório.";
    } elseif (!is_numeric($valor) || $valor <= 0) {
        $errors[] = "Valor inválido.";
    }
    
    // Verificar se placa ou chassi já existem (excluindo o veículo atual)
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM veiculos WHERE placa = :placa AND id != :id");
    $stmt->bindParam(':placa', $placa);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result['total'] > 0) {
        $errors[] = "Esta placa já está cadastrada para outro veículo.";
    }
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM veiculos WHERE chassi = :chassi AND id != :id");
    $stmt->bindParam(':chassi', $chassi);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result['total'] > 0) {
        $errors[] = "Este chassi já está cadastrado para outro veículo.";
    }
    
    // Se não houver erros, atualizar no banco de dados
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("UPDATE veiculos SET marca = :marca, modelo = :modelo, ano = :ano, placa = :placa, cor = :cor, chassi = :chassi, valor = :valor, descricao = :descricao WHERE id = :id");
            $stmt->bindParam(':marca', $marca);
            $stmt->bindParam(':modelo', $modelo);
            $stmt->bindParam(':ano', $ano);
            $stmt->bindParam(':placa', $placa);
            $stmt->bindParam(':cor', $cor);
            $stmt->bindParam(':chassi', $chassi);
            $stmt->bindParam(':valor', $valor);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            redirectWithAlert('../veiculos/listar.php', 'Veículo atualizado com sucesso!');
        } catch(PDOException $e) {
            $errors[] = "Erro ao atualizar veículo: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Veículo - HS Motors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2><i class="fas fa-car-side"></i> Editar Veículo</h2>
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
                <h5><i class="fas fa-car-side"></i> Formulário de Edição</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="marca" class="form-label">Marca</label>
                            <input type="text" class="form-control" id="marca" name="marca" value="<?php echo $veiculo['marca']; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="modelo" class="form-label">Modelo</label>
                            <input type="text" class="form-control" id="modelo" name="modelo" value="<?php echo $veiculo['modelo']; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="ano" class="form-label">Ano</label>
                            <input type="number" class="form-control" id="ano" name="ano" min="1900" max="<?php echo date('Y') + 1; ?>" value="<?php echo $veiculo['ano']; ?>" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="placa" class="form-label">Placa</label>
                            <input type="text" class="form-control" id="placa" name="placa" value="<?php echo $veiculo['placa']; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="cor" class="form-label">Cor</label>
                            <input type="text" class="form-control" id="cor" name="cor" value="<?php echo $veiculo['cor']; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="valor" class="form-label">Valor (R$)</label>
                            <input type="text" class="form-control" id="valor" name="valor" value="<?php echo number_format($veiculo['valor'], 2, ',', '.'); ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="chassi" class="form-label">Chassi</label>
                        <input type="text" class="form-control" id="chassi" name="chassi" value="<?php echo $veiculo['chassi']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3"><?php echo $veiculo['descricao']; ?></textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Atualizar Veículo
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

