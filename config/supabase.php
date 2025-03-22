<?php
// Configuração do Supabase
$supabase_url = 'https://hqfviyubjptxakwoxypt.supabase.co';
$supabase_key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImhxZnZpeXVianB0eGFrd294eXB0Iiwicm9sZSI6ImFub24iLCJpYXQiOjE2MTY1OTMwNzMsImV4cCI6MTkzMjE2OTA3M30.YOUR_JWT_SECRET'; // Substitua pelo seu anon key real

// String de conexão PDO para PostgreSQL
$database_url = 'postgresql://postgres:[YOUR-PASSWORD]@db.hqfviyubjptxakwoxypt.supabase.co:5432/postgres';
// Extrair informações da URL
$db_parts = parse_url($database_url);
$host = $db_parts['host'];
$port = $db_parts['port'];
$dbname = ltrim($db_parts['path'], '/');
$user = $db_parts['user'];
$password = $db_parts['pass'];

try {
    // Conexão com PostgreSQL do Supabase
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password";
    $conn = new PDO($dsn);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erro na conexão com o Supabase: " . $e->getMessage());
}

// Função para fazer requisições à API do Supabase
function supabase_request($endpoint, $method = 'GET', $data = null) {
    global $supabase_url, $supabase_key;
    
    $url = $supabase_url . $endpoint;
    $ch = curl_init($url);
    
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $supabase_key,
        'apikey: ' . $supabase_key
    ];
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($method !== 'GET') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $status_code,
        'data' => json_decode($response, true)
    ];
}

// Funções de acesso ao banco de dados
function db_select($table, $conditions = [], $order = null, $limit = null) {
    global $conn;
    
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
}

function db_insert($table, $data) {
    global $conn;
    
    $columns = implode(", ", array_keys($data));
    $placeholders = ":" . implode(", :", array_keys($data));
    
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders) RETURNING id";
    $stmt = $conn->prepare($sql);
    
    foreach ($data as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    
    $stmt->execute();
    $result = $stmt->fetch();
    return $result['id'] ?? null;
}

function db_update($table, $data, $conditions) {
    global $conn;
    
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
}

function db_delete($table, $conditions) {
    global $conn;
    
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
}

// Funções específicas para o sistema
function countRecords($conn, $table) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM $table");
    $stmt->execute();
    $result = $stmt->fetch();
    return $result['total'];
}

function isVeiculoVinculado($conn, $veiculo_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM cliente_veiculo WHERE veiculo_id = :veiculo_id");
    $stmt->bindParam(':veiculo_id', $veiculo_id);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result['total'] > 0;
}

function getClienteNome($conn, $cliente_id) {
    $stmt = $conn->prepare("SELECT nome FROM clientes WHERE id = :id");
    $stmt->bindParam(':id', $cliente_id);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result ? $result['nome'] : 'Cliente não encontrado';
}

function getVeiculoInfo($conn, $veiculo_id) {
    $stmt = $conn->prepare("SELECT marca, modelo, placa FROM veiculos WHERE id = :id");
    $stmt->bindParam(':id', $veiculo_id);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result ? $result['marca'] . ' ' . $result['modelo'] . ' (' . $result['placa'] . ')' : 'Veículo não encontrado';
}
?>

