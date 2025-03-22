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

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $endereco = trim($_POST['endereco']);
    $data_nascimento = $_POST['data_nascimento'];
    
    // Remover formatação do CPF
    $cpf_limpo = preg_replace('/[^0-9]/', '', $cpf);
    
    // Validações
    $errors = [];
    
    if (empty($nome)) {
        $errors[] = "O nome é obrigatório.";
    }
    
    if (empty($cpf)) {
        $errors[] = "O CPF é obrigatório.";
    } elseif (!validarCPF($cpf_limpo)) {
        $errors[] = "CPF inválido.";
    }
    
    if (empty($email)) {
        $errors[] = "O email é obrigatório.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email inválido.";
    }
    
    if (empty($telefone)) {
        $errors[] = "O telefone é obrigatório.";
    }
    
    if (empty($endereco)) {
        $errors[] = "O endereço é obrigatório.";
    }
    
    if (empty($data_nascimento)) {
        $errors[] = "A data de nascimento é obrigatória.";
    }
    
    // Verificar se CPF ou email já existem (excluindo o cliente atual)
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM clientes WHERE cpf = :cpf AND id != :id");
    $stmt->bindParam(':cpf', $cpf);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result['total'] > 0) {
        $errors[] = "Este CPF já está cadastrado para outro cliente.";
    }
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM clientes WHERE email = :email AND id != :id");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result['total'] > 0) {
        $errors[] = "Este email já está cadastrado para outro cliente.";
    }
    
    // Se não houver erros, atualizar no banco de dados
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("UPDATE clientes SET nome = :nome, cpf = :cpf, email = :email, telefone = :telefone, endereco = :endereco, data_nascimento = :data_nascimento WHERE id = :id");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':cpf', $cpf);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':endereco', $endereco);
            $stmt->bindParam(':data_nascimento', $data_nascimento);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            redirectWithAlert('../clientes/listar.php', 'Cliente atualizado com sucesso!');
        } catch(PDOException $e) {
            $errors[] = "Erro ao atualizar cliente: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente - HS Motors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2><i class="fas fa-user-edit"></i> Editar Cliente</h2>
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
                <h5><i class="fas fa-user-edit"></i> Formulário de Edição</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo $cliente['nome']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="cpf" class="form-label">CPF</label>
                            <input type="text" class="form-control cpf-mask" id="cpf" name="cpf" value="<?php echo $cliente['cpf']; ?>" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $cliente['email']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control telefone-mask" id="telefone" name="telefone" value="<?php echo $cliente['telefone']; ?>" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                            <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" value="<?php echo $cliente['data_nascimento']; ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereço Completo</label>
                        <textarea class="form-control" id="endereco" name="endereco" rows="3" required><?php echo $cliente['endereco']; ?></textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Atualizar Cliente
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

