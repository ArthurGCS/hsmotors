<?php
// Função para fazer upload de imagens para o Supabase Storage
function upload_to_supabase($file, $bucket = 'veiculos') {
    global $supabase_url, $supabase_key;
    
    // Verificar se o arquivo é válido
    if (!isset($file) || $file['error'] != 0) {
        return [
            'success' => false,
            'error' => 'Arquivo inválido'
        ];
    }
    
    // Verificar tipo de arquivo
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        return [
            'success' => false,
            'error' => 'Tipo de arquivo não permitido. Use JPG, PNG ou WEBP.'
        ];
    }
    
    // Verificar tamanho do arquivo (máximo 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return [
            'success' => false,
            'error' => 'Tamanho máximo permitido: 5MB'
        ];
    }
    
    // Gerar nome único para o arquivo
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = md5(time() . $file['name']) . '.' . $extension;
    
    // Preparar URL para upload
    $url = $supabase_url . '/storage/v1/object/' . $bucket . '/' . $filename;
    
    // Preparar headers
    $headers = [
        'Authorization: Bearer ' . $supabase_key,
        'apikey: ' . $supabase_key,
        'Content-Type: ' . $file['type']
    ];
    
    // Iniciar cURL
    $ch = curl_init($url);
    
    // Configurar cURL
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($file['tmp_name']));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    // Executar cURL
    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Verificar resposta
    if ($status_code == 200) {
        return [
            'success' => true,
            'filename' => $filename,
            'url' => $supabase_url . '/storage/v1/object/public/' . $bucket . '/' . $filename
        ];
    } else {
        return [
            'success' => false,
            'error' => 'Erro ao fazer upload: ' . $response
        ];
    }
}

// Função para excluir imagem do Supabase Storage
function delete_from_supabase($filename, $bucket = 'veiculos') {
    global $supabase_url, $supabase_key;
    
    // Preparar URL para exclusão
    $url = $supabase_url . '/storage/v1/object/' . $bucket . '/' . $filename;
    
    // Preparar headers
    $headers = [
        'Authorization: Bearer ' . $supabase_key,
        'apikey: ' . $supabase_key
    ];
    
    // Iniciar cURL
    $ch = curl_init($url);
    
    // Configurar cURL
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    // Executar cURL
    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Verificar resposta
    if ($status_code == 200) {
        return [
            'success' => true
        ];
    } else {
        return [
            'success' => false,
            'error' => 'Erro ao excluir imagem: ' . $response
        ];
    }
}
?>

