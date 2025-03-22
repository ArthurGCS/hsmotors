<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../includes/upload_supabase.php';

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
  $destaque = isset($_POST['destaque']) ? true : false;
  
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
  
  // Verificar se placa ou chassi já existem
  $stmt = $conn->prepare("SELECT COUNT(*) as total FROM veiculos WHERE placa = :placa");
  $stmt->bindParam(':placa', $placa);
  $stmt->execute();
  $result = $stmt->fetch();
  
  if ($result['total'] > 0) {
      $errors[] = "Esta placa já está cadastrada.";
  }
  
  $stmt = $conn->prepare("SELECT COUNT(*) as total FROM veiculos WHERE chassi = :chassi");
  $stmt->bindParam(':chassi', $chassi);
  $stmt->execute();
  $result = $stmt->fetch();
  
  if ($result['total'] > 0) {
      $errors[] = "Este chassi já está cadastrado.";
  }
  
  // Upload de imagem para o Supabase
  $imagem = '';
  if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
      $upload_result = upload_to_supabase($_FILES['imagem']);
      
      if ($upload_result['success']) {
          $imagem = $upload_result['filename'];
      } else {
          $errors[] = $upload_result['error'];
      }
  }
  
  // Se não houver erros, inserir no banco de dados
  if (empty($errors)) {
      try {
          $data = [
              'marca' => $marca,
              'modelo' => $modelo,
              'ano' => $ano,
              'placa' => $placa,
              'cor' => $cor,
              'chassi' => $chassi,
              'valor' => $valor,
              'descricao' => $descricao,
              'imagem' => $imagem,
              'destaque' => $destaque
          ];
          
          $id = db_insert('veiculos', $data);
          
          if ($id) {
              redirectWithAlert('../veiculos/listar.php', 'Veículo cadastrado com sucesso!');
          } else {
              $errors[] = "Erro ao cadastrar veículo.";
          }
      } catch(PDOException $e) {
          $errors[] = "Erro ao cadastrar veículo: " . $e->getMessage();
      }
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastrar Veículo - HS Motors</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
  <?php include '../../includes/admin/header.php'; ?>
  
  <div class="container mt-5">
      <div class="row mb-4">
          <div class="col-md-8">
              <h2><i class="fas fa-car-side"></i> Cadastrar Novo Veículo</h2>
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
          <div class="card-header bg-success text-white">
              <h5><i class="fas fa-car-side"></i> Formulário de Cadastro</h5>
          </div>
          <div class="card-body">
              <form method="POST" action="" enctype="multipart/form-data">
                  <div class="row mb-3">
                      <div class="col-md-4">
                          <label for="marca" class="form-label">Marca</label>
                          <input type="text" class="form-control" id="marca" name="marca" value="<?php echo isset($_POST['marca']) ? $_POST['marca'] : ''; ?>" required>
                      </div>
                      <div class="col-md-4">
                          <label for="modelo" class="form-label">Modelo</label>
                          <input type="text" class="form-control" id="modelo" name="modelo" value="<?php echo isset($_POST['modelo']) ? $_POST['modelo'] : ''; ?>" required>
                      </div>
                      <div class="col-md-4">
                          <label for="ano" class="form-label">Ano</label>
                          <input type="number" class="form-control" id="ano" name="ano" min="1900" max="<?php echo date('Y') + 1; ?>" value="<?php echo isset($_POST['ano']) ? $_POST['ano'] : date('Y'); ?>" required>
                      </div>
                  </div>
                  <div class="row mb-3">
                      <div class="col-md-4">
                          <label for="placa" class="form-label">Placa</label>
                          <input type="text" class="form-control" id="placa" name="placa" value="<?php echo isset($_POST['placa']) ? $_POST['placa'] : ''; ?>" required>
                      </div>
                      <div class="col-md-4">
                          <label for="cor" class="form-label">Cor</label>
                          <input type="text" class="form-control" id="cor" name="cor" value="<?php echo isset($_POST['cor']) ? $_POST['cor'] : ''; ?>" required>
                      </div>
                      <div class="col-md-4">
                          <label for="valor" class="form-label">Valor (R$)</label>
                          <input type="text" class="form-control" id="valor" name="valor" value="<?php echo isset($_POST['valor']) ? $_POST['valor'] : ''; ?>" required>
                      </div>
                  </div>
                  <div class="mb-3">
                      <label for="chassi" class="form-label">Chassi</label>
                      <input type="text" class="form-control" id="chassi" name="chassi" value="<?php echo isset($_POST['chassi']) ? $_POST['chassi'] : ''; ?>" required>
                  </div>
                  <div class="mb-3">
                      <label for="imagem" class="form-label">Imagem do Veículo</label>
                      <input type="file" class="form-control" id="imagem" name="imagem">
                      <div class="form-text">Formatos aceitos: JPG, JPEG, PNG, WEBP. Tamanho máximo: 5MB.</div>
                  </div>
                  <div class="mb-3">
                      <label for="descricao" class="form-label">Descrição</label>
                      <textarea class="form-control" id="descricao" name="descricao" rows="3"><?php echo isset($_POST['descricao']) ? $_POST['descricao'] : ''; ?></textarea>
                  </div>
                  <div class="mb-3 form-check">
                      <input type="checkbox" class="form-check-input" id="destaque" name="destaque" <?php echo (isset($_POST['destaque']) && $_POST['destaque']) ? 'checked' : ''; ?>>
                      <label class="form-check-label" for="destaque">Destacar na página inicial</label>
                  </div>
                  <div class="d-grid gap-2">
                      <button type="submit" class="btn btn-success">
                          <i class="fas fa-save"></i> Cadastrar Veículo
                      </button>
                  </div>
              </form>
          </div>
      </div>
  </div>
  
  <?php include '../../includes/admin/footer.php'; ?>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="../../assets/js/admin.js"></script>
</body>
</html>

