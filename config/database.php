<?php
// Definir modo de teste (true = usar banco de dados local, false = usar Supabase)
$test_mode = true;

// Incluir configuração do Supabase
require_once 'supabase.php';

if ($test_mode) {
    // Configuração local para testes
    $host = 'localhost';
    $dbname = 'hs_motors';
    $username = 'root';
    $password = '';
    
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        die("Erro na conexão com o banco de dados: " . $e->getMessage());
    }
    
    // Criar tabelas se não existirem
    $sql = [
        "CREATE TABLE IF NOT EXISTS clientes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            cpf VARCHAR(14) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            telefone VARCHAR(15) NOT NULL,
            endereco TEXT NOT NULL,
            data_nascimento DATE NOT NULL,
            data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS veiculos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            marca VARCHAR(50) NOT NULL,
            modelo VARCHAR(50) NOT NULL,
            ano INT NOT NULL,
            placa VARCHAR(10) UNIQUE NOT NULL,
            cor VARCHAR(30) NOT NULL,
            chassi VARCHAR(17) UNIQUE NOT NULL,
            valor DECIMAL(10,2) NOT NULL,
            descricao TEXT,
            imagem VARCHAR(255),
            destaque TINYINT(1) DEFAULT 0,
            data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS cliente_veiculo (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cliente_id INT NOT NULL,
            veiculo_id INT NOT NULL,
            data_vinculo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
            FOREIGN KEY (veiculo_id) REFERENCES veiculos(id) ON DELETE CASCADE,
            UNIQUE KEY (cliente_id, veiculo_id)
        )",
        
        "CREATE TABLE IF NOT EXISTS contatos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            telefone VARCHAR(15) NOT NULL,
            mensagem TEXT NOT NULL,
            veiculo_id INT,
            lido TINYINT(1) DEFAULT 0,
            data_contato TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (veiculo_id) REFERENCES veiculos(id) ON DELETE SET NULL
        )"
    ];
    
    foreach ($sql as $query) {
        try {
            $conn->exec($query);
        } catch(PDOException $e) {
            echo "Erro ao criar tabela: " . $e->getMessage();
        }
    }
    
    // Verificar se a coluna 'imagem' existe na tabela 'veiculos'
    try {
        $result = $conn->query("SHOW COLUMNS FROM veiculos LIKE 'imagem'");
        if ($result->rowCount() == 0) {
            $conn->exec("ALTER TABLE veiculos ADD COLUMN imagem VARCHAR(255) AFTER descricao");
        }
        
        $result = $conn->query("SHOW COLUMNS FROM veiculos LIKE 'destaque'");
        if ($result->rowCount() == 0) {
            $conn->exec("ALTER TABLE veiculos ADD COLUMN destaque TINYINT(1) DEFAULT 0 AFTER imagem");
        }
    } catch(PDOException $e) {
        echo "Erro ao verificar/adicionar coluna: " . $e->getMessage();
    }
    
    // Inserir dados de exemplo se não existirem
    try {
        $result = $conn->query("SELECT COUNT(*) as total FROM veiculos");
        $count = $result->fetch()['total'];
        
        if ($count == 0) {
            // Inserir veículos de exemplo
            $veiculos = [
                [
                    'marca' => 'Toyota', 
                    'modelo' => 'Corolla', 
                    'ano' => 2022, 
                    'placa' => 'ABC1234', 
                    'cor' => 'Prata', 
                    'chassi' => '9BRBL3HE6J0123456', 
                    'valor' => 120000.00,
                    'descricao' => 'Toyota Corolla XEi 2.0 Flex, completo, único dono, revisões em concessionária.',
                    'destaque' => 1
                ],
                [
                    'marca' => 'Honda', 
                    'modelo' => 'Civic', 
                    'ano' => 2021, 
                    'placa' => 'DEF5678', 
                    'cor' => 'Preto', 
                    'chassi' => '93HFC2630MZ123456', 
                    'valor' => 115000.00,
                    'descricao' => 'Honda Civic EXL 2.0, teto solar, bancos em couro, multimídia completa.',
                    'destaque' => 1
                ],
                [
                    'marca' => 'Jeep', 
                    'modelo' => 'Compass', 
                    'ano' => 2023, 
                    'placa' => 'GHI9012', 
                    'cor' => 'Branco', 
                    'chassi' => '98AAJ22PONE123456', 
                    'valor' => 180000.00,
                    'descricao' => 'Jeep Compass Limited 2.0 Diesel 4x4, pacote premium, teto panorâmico.',
                    'destaque' => 1
                ],
                [
                    'marca' => 'Volkswagen', 
                    'modelo' => 'T-Cross', 
                    'ano' => 2022, 
                    'placa' => 'JKL3456', 
                    'cor' => 'Vermelho', 
                    'chassi' => '9BWBJ6BF8M8123456', 
                    'valor' => 130000.00,
                    'descricao' => 'Volkswagen T-Cross Highline 1.4 TSI, completo, baixa quilometragem.',
                    'destaque' => 0
                ],
                [
                    'marca' => 'Hyundai', 
                    'modelo' => 'HB20', 
                    'ano' => 2021, 
                    'placa' => 'MNO7890', 
                    'cor' => 'Azul', 
                    'chassi' => '9BHBG51CBNP123456', 
                    'valor' => 75000.00,
                    'descricao' => 'Hyundai HB20 Diamond Plus 1.0 Turbo, completo, multimídia, câmera de ré.',
                    'destaque' => 0
                ],
                [
                    'marca' => 'Chevrolet', 
                    'modelo' => 'Onix', 
                    'ano' => 2022, 
                    'placa' => 'PQR1234', 
                    'cor' => 'Cinza', 
                    'chassi' => '9BGEA48A0NG123456', 
                    'valor' => 78000.00,
                    'descricao' => 'Chevrolet Onix Premier 1.0 Turbo, completo, MyLink, OnStar.',
                    'destaque' => 0
                ]
            ];
            
            $stmt = $conn->prepare("INSERT INTO veiculos (marca, modelo, ano, placa, cor, chassi, valor, descricao, destaque) VALUES (:marca, :modelo, :ano, :placa, :cor, :chassi, :valor, :descricao, :destaque)");
            
            foreach ($veiculos as $veiculo) {
                $stmt->bindParam(':marca', $veiculo['marca']);
                $stmt->bindParam(':modelo', $veiculo['modelo']);
                $stmt->bindParam(':ano', $veiculo['ano']);
                $stmt->bindParam(':placa', $veiculo['placa']);
                $stmt->bindParam(':cor', $veiculo['cor']);
                $stmt->bindParam(':chassi', $veiculo['chassi']);
                $stmt->bindParam(':valor', $veiculo['valor']);
                $stmt->bindParam(':descricao', $veiculo['descricao']);
                $stmt->bindParam(':destaque', $veiculo['destaque']);
                $stmt->execute();
            }
            
            echo "<div class='alert alert-success'>Dados de exemplo inseridos com sucesso!</div>";
        }
    } catch(PDOException $e) {
        echo "Erro ao inserir dados de exemplo: " . $e->getMessage();
    }
    
} else {
    // Usar Supabase
    
    
    try {
        // Conexão com PostgreSQL do Supabase
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password";
        $conn = new PDO($dsn);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // Aqui você pode adicionar código para criar as tabelas no PostgreSQL se necessário
        // Nota: a sintaxe SQL pode ser ligeiramente diferente do MySQL
        
    } catch(PDOException $e) {
        die("Erro na conexão com o Supabase: " . $e->getMessage());
    }
}

// Funções de acesso ao banco de dados que funcionam com ambos MySQL e PostgreSQL
function db_select($table, $conditions = [], $order = null, $limit = null) {
    global $conn, $test_mode;
    
    if ($test_mode) {
        // Usando MySQL
        $sql = "SELECT * FROM $table";
        
        if (!empty($conditions)) {
            $sql .= " WHERE ";
            $clauses = [];
            foreach ($conditions as $key => $value) {
                $clauses[] = "$key = :$key";
            }
            $sql .= implode(" AND ", $clauses);
        }
        
        if ($order) {
            $sql .= " ORDER BY $order";
        }
        
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        
        $stmt = $conn->prepare($sql);
        
        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    } else {
        // Usando Supabase API
        $endpoint = "/rest/v1/$table";
        $params = [];
        
        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                $params[] = "$key=eq.$value";
            }
        }
        
        if ($order) {
            $params[] = "order=$order";
        }
        
        if ($limit) {
            $params[] = "limit=$limit";
        }
        
        if (!empty($params)) {
            $endpoint .= "?" . implode("&", $params);
        }
        
        $response = supabase_request($endpoint);
        
        if ($response['status'] == 200) {
            return $response['data'];
        }
        
        return [];
    }
}

function db_insert($table, $data) {
    global $conn, $test_mode;
    
    if ($test_mode) {
        // Usando MySQL
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $conn->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        $stmt->execute();
        return $conn->lastInsertId();
    } else {
        // Usando Supabase API
        $endpoint = "/rest/v1/$table";
        $response = supabase_request($endpoint, 'POST', $data);
        
        if ($response['status'] == 201) {
            return $response['data'][0]['id'] ?? null;
        }
        
        return null;
    }
}

function db_update($table, $data, $conditions) {
    global $conn, $test_mode;
    
    if ($test_mode) {
        // Usando MySQL
        $sql = "UPDATE $table SET ";
        $updates = [];
        
        foreach ($data as $key => $value) {
            $updates[] = "$key = :$key";
        }
        
        $sql .= implode(", ", $updates);
        $sql .= " WHERE ";
        
        $clauses = [];
        foreach ($conditions as $key => $value) {
            $clauses[] = "$key = :condition_$key";
        }
        
        $sql .= implode(" AND ", $clauses);
        
        $stmt = $conn->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":condition_$key", $value);
        }
        
        return $stmt->execute();
    } else {
        // Usando Supabase API
        $endpoint = "/rest/v1/$table";
        $params = [];
        
        foreach ($conditions as $key => $value) {
            $params[] = "$key=eq.$value";
        }
        
        if (!empty($params)) {
            $endpoint .= "?" . implode("&", $params);
        }
        
        $response = supabase_request($endpoint, 'PATCH', $data);
        
        return $response['status'] == 200;
    }
}

function db_delete($table, $conditions) {
    global $conn, $test_mode;
    
    if ($test_mode) {
        // Usando MySQL
        $sql = "DELETE FROM $table WHERE ";
        $clauses = [];
        
        foreach ($conditions as $key => $value) {
            $clauses[] = "$key = :$key";
        }
        
        $sql .= implode(" AND ", $clauses);
        
        $stmt = $conn->prepare($sql);
        
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        return $stmt->execute();
    } else {
        // Usando Supabase API
        $endpoint = "/rest/v1/$table";
        $params = [];
        
        foreach ($conditions as $key => $value) {
            $params[] = "$key=eq.$value";
        }
        
        if (!empty($params)) {
            $endpoint .= "?" . implode("&", $params);
        }
        
        $response = supabase_request($endpoint, 'DELETE');
        
        return $response['status'] == 200;
    }
}
?>

