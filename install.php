<?php
// Verificar se o PHP atende aos requisitos
$php_version = phpversion();
$php_ok = version_compare($php_version, '7.4.0', '>=');

// Verificar extensões necessárias
$extensions = [
    'pdo' => extension_loaded('pdo'),
    'pdo_mysql' => extension_loaded('pdo_mysql'),
    'curl' => extension_loaded('curl'),
    'gd' => extension_loaded('gd')
];

// Verificar permissões de diretórios
$directories = [
    'uploads' => is_writable('uploads') || mkdir('uploads', 0755, true),
    'uploads/veiculos' => is_writable('uploads/veiculos') || mkdir('uploads/veiculos', 0755, true)
];

// Verificar conexão com o banco de dados
$db_connection = false;
$db_error = '';

if (isset($_POST['test_connection'])) {
    $host = $_POST['db_host'];
    $dbname = $_POST['db_name'];
    $username = $_POST['db_user'];
    $password = $_POST['db_pass'];
    
    try {
        $conn = new PDO("mysql:host=$host", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Tentar criar o banco de dados se não existir
        $conn->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
        $conn->exec("USE `$dbname`");
        
        $db_connection = true;
        
        // Salvar configurações
        $config_content = "<?php
// Definir modo de teste (true = usar banco de dados local, false = usar Supabase)
\$test_mode = true;

if (\$test_mode) {
    // Configuração local para testes
    \$host = '$host';
    \$dbname = '$dbname';
    \$username = '$username';
    \$password = '$password';
    
    try {
        \$conn = new PDO(\"mysql:host=\$host;dbname=\$dbname\", \$username, \$password);
        \$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        \$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch(PDOException \$e) {
        die(\"Erro na conexão com o banco de dados: \" . \$e->getMessage());
    }
    
    // Resto do arquivo...
";
        
        // Salvar apenas se o diretório config existir e for gravável
        if (is_dir('config') && is_writable('config')) {
            file_put_contents('config/database_new.php', $config_content);
        }
        
    } catch(PDOException $e) {
        $db_error = $e->getMessage();
    }
}

// Verificar se a instalação está completa
$all_requirements_met = $php_ok && !in_array(false, $extensions) && !in_array(false, $directories);
$ready_to_install = $all_requirements_met && $db_connection;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - HS Motors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .install-container {
            max-width: 800px;
            margin: 50px auto;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo i {
            font-size: 3rem;
            color: #e63946;
        }
        .logo h1 {
            font-weight: 700;
            color: #1d3557;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #1d3557;
            color: white;
            font-weight: 600;
        }
        .requirement-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .requirement-item:last-child {
            border-bottom: none;
        }
        .status-icon {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }
        .status-ok {
            color: #28a745;
        }
        .status-error {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container install-container">
        <div class="logo">
            <i class="fas fa-car-side"></i>
            <h1>HS Motors</h1>
            <p class="lead">Assistente de Instalação</p>
        </div>
        
        <div class="card">
            <div class="card-header">
                <i class="fas fa-check-circle me-2"></i> Verificação de Requisitos
            </div>
            <div class="card-body">
                <div class="requirement-item">
                    <span class="status-icon <?php echo $php_ok ? 'status-ok' : 'status-error'; ?>">
                        <i class="fas <?php echo $php_ok ? 'fa-check' : 'fa-times'; ?>"></i>
                    </span>
                    <span>PHP versão <?php echo $php_version; ?> <?php echo $php_ok ? '(OK)' : '(Requer PHP 7.4 ou superior)'; ?></span>
                </div>
                
                <?php foreach ($extensions as $extension => $loaded): ?>
                <div class="requirement-item">
                    <span class="status-icon <?php echo $loaded ? 'status-ok' : 'status-error'; ?>">
                        <i class="fas <?php echo $loaded ? 'fa-check' : 'fa-times'; ?>"></i>
                    </span>
                    <span>Extensão <?php echo $extension; ?> <?php echo $loaded ? '(OK)' : '(Não carregada)'; ?></span>
                </div>
                <?php endforeach; ?>
                
                <?php foreach ($directories as $directory => $writable): ?>
                <div class="requirement-item">
                    <span class="status-icon <?php echo $writable ? 'status-ok' : 'status-error'; ?>">
                        <i class="fas <?php echo $writable ? 'fa-check' : 'fa-times'; ?>"></i>
                    </span>
                    <span>Diretório <?php echo $directory; ?> <?php echo $writable ? '(Gravável)' : '(Não gravável)'; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <i class="fas fa-database me-2"></i> Configuração do Banco de Dados
            </div>
            <div class="card-body">
                <?php if ($db_error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i> Erro: <?php echo $db_error; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($db_connection): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i> Conexão com o banco de dados estabelecida com sucesso!
                </div>
                <?php endif; ?>
                
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="db_host" class="form-label">Host do Banco de Dados</label>
                        <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                    </div>
                    <div class="mb-3">
                        <label for="db_name" class="form-label">Nome do Banco de Dados</label>
                        <input type="text" class="form-control" id="db_name" name="db_name" value="hs_motors" required>
                    </div>
                    <div class="mb-3">
                        <label for="db_user" class="form-label">Usuário do Banco de Dados</label>
                        <input type="text" class="form-control" id="db_user" name="db_user" value="root" required>
                    </div>
                    <div class="mb-3">
                        <label for="db_pass" class="form-label">Senha do Banco de Dados</label>
                        <input type="password" class="form-control" id="db_pass" name="db_pass">
                    </div>
                    <button type="submit" name="test_connection" class="btn btn-primary">
                        <i class="fas fa-plug me-2"></i> Testar Conexão
                    </button>
                </form>
            </div>
        </div>
        
        <?php if ($ready_to_install): ?>
        <div class="card">
            <div class="card-header">
                <i class="fas fa-cog me-2"></i> Finalizar Instalação
            </div>
            <div class="card-body">
                <p>Todos os requisitos foram atendidos e a conexão com o banco de dados foi estabelecida. Você está pronto para finalizar a instalação.</p>
                <a href="index.php" class="btn btn-success">
                    <i class="fas fa-check-circle me-2"></i> Concluir Instalação
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

