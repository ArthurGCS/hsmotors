<?php
// Função para contar registros em uma tabela
function countRecords($conn, $table) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM $table");
    $stmt->execute();
    $result = $stmt->fetch();
    return $result['total'];
}

// Função para validar CPF
function validarCPF($cpf) {
    // Remove caracteres especiais
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    // Verifica se o CPF tem 11 dígitos
    if (strlen($cpf) != 11) {
        return false;
    }
    
    // Verifica se todos os dígitos são iguais
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    
    // Calcula o primeiro dígito verificador
    $soma = 0;
    for ($i = 0; $i < 9; $i++) {
        $soma += $cpf[$i] * (10 - $i);
    }
    $resto = $soma % 11;
    $dv1 = ($resto < 2) ? 0 : 11 - $resto;
    
    // Calcula o segundo dígito verificador
    $soma = 0;
    for ($i = 0; $i < 9; $i++) {
        $soma += $cpf[$i] * (11 - $i);
    }
    $soma += $dv1 * 2;
    $resto = $soma % 11;
    $dv2 = ($resto < 2) ? 0 : 11 - $resto;
    
    // Verifica se os dígitos verificadores estão corretos
    if ($cpf[9] == $dv1 && $cpf[10] == $dv2) {
        return true;
    } else {
        return false;
    }
}

// Função para formatar CPF
function formatarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
}

// Função para validar placa de veículo (padrão Mercosul)
function validarPlaca($placa) {
    // Padrão Mercosul: 3 letras, 1 número, 1 letra, 2 números
    // ou padrão antigo: 3 letras, 4 números
    return (preg_match('/^[A-Z]{3}[0-9][A-Z][0-9]{2}$/', $placa) || preg_match('/^[A-Z]{3}[0-9]{4}$/', $placa));
}

// Função para validar chassi
function validarChassi($chassi) {
    // Chassi tem 17 caracteres alfanuméricos
    return preg_match('/^[A-HJ-NPR-Z0-9]{17}$/', $chassi);
}

// Função para exibir mensagens de alerta
function showAlert($message, $type = 'success') {
    $_SESSION['alert'] = [
        'message' => $message,
        'type' => $type
    ];
}

// Função para exibir alerta e redirecionar
function redirectWithAlert($url, $message, $type = 'success') {
    showAlert($message, $type);
    header("Location: $url");
    exit;
}

// Função para verificar se um veículo já está vinculado a um cliente
function isVeiculoVinculado($conn, $veiculo_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM cliente_veiculo WHERE veiculo_id = :veiculo_id");
    $stmt->bindParam(':veiculo_id', $veiculo_id);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result['total'] > 0;
}

// Função para obter o nome do cliente pelo ID
function getClienteNome($conn, $cliente_id) {
    $stmt = $conn->prepare("SELECT nome FROM clientes WHERE id = :id");
    $stmt->bindParam(':id', $cliente_id);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result ? $result['nome'] : 'Cliente não encontrado';
}

// Função para obter informações do veículo pelo ID
function getVeiculoInfo($conn, $veiculo_id) {
    $stmt = $conn->prepare("SELECT marca, modelo, placa FROM veiculos WHERE id = :id");
    $stmt->bindParam(':id', $veiculo_id);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result ? $result['marca'] . ' ' . $result['modelo'] . ' (' . $result['placa'] . ')' : 'Veículo não encontrado';
}
?>

