<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
  header('Location: index.php');
  exit;
}

$id = $_GET['id'];

// Buscar dados do veículo  {
  header('Location: index.php');
  exit;
}

$id = $_GET['id'];

// Buscar dados do veículo
$stmt = $conn->prepare("SELECT * FROM veiculos WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$veiculo = $stmt->fetch();

if (!$veiculo) {
  header('Location: index.php');
  exit;
}

// Buscar veículos similares (mesma marca ou modelo)
$stmt = $conn->prepare("
  SELECT * FROM veiculos 
  WHERE (marca = :marca OR modelo = :modelo) 
  AND id != :id 
  ORDER BY RANDOM() 
  LIMIT 3
");
$stmt->bindParam(':marca', $veiculo['marca']);
$stmt->bindParam(':modelo', $veiculo['modelo']);
$stmt->bindParam(':id', $id);
$stmt->execute();
$veiculos_similares = $stmt->fetchAll();

// URL base para imagens do Supabase
$supabase_image_url = $supabase_url . '/storage/v1/object/public/veiculos/';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $veiculo['marca'] . ' ' . $veiculo['modelo']; ?> - HS Motors</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <?php include 'includes/public/header.php'; ?>
  
  <div class="container mt-5 mb-5">
      <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="index.php">Início</a></li>
              <li class="breadcrumb-item"><a href="index.php#veiculos">Veículos</a></li>
              <li class="breadcrumb-item active" aria-current="page"><?php echo $veiculo['marca'] . ' ' . $veiculo['modelo']; ?></li>
          </ol>
      </nav>
      
      <div class="row">
          <div class="col-lg-8">
              <div class="card mb-4">
                  <div class="vehicle-gallery">
                      <?php if (!empty($veiculo['imagem'])): ?>
                          <img src="<?php echo $supabase_image_url . $veiculo['imagem']; ?>" class="img-fluid" alt="<?php echo $veiculo['marca'] . ' ' . $veiculo['modelo']; ?>">
                      <?php else: ?>
                          <img src="assets/images/car-placeholder-large.jpg" class="img-fluid" alt="<?php echo $veiculo['marca'] . ' ' . $veiculo['modelo']; ?>">
                      <?php endif; ?>
                  </div>
              </div>
              
              <div class="card mb-4">
                  <div class="card-header bg-primary text-white">
                      <h5 class="mb-0"><i class="fas fa-info-circle"></i> Descrição do Veículo</h5>
                  </div>
                  <div class="card-body">
                      <?php if (!empty($veiculo['descricao'])): ?>
                          <p><?php echo nl2br($veiculo['descricao']); ?></p>
                      <?php else: ?>
                          <p class="text-muted">Veículo <?php echo $veiculo['marca'] . ' ' . $veiculo['modelo']; ?> em excelente estado. Entre em contato para mais informações.</p>
                      <?php endif; ?>
                  </div>
              </div>
              
              <div class="card mb-4">
                  <div class="card-header bg-primary text-white">
                      <h5 class="mb-0"><i class="fas fa-car-side"></i> Características</h5>
                  </div>
                  <div class="card-body">
                      <div class="row">
                          <div class="col-md-6">
                              <ul class="list-group list-group-flush">
                                  <li class="list-group-item d-flex justify-content-between align-items-center">
                                      <span><i class="fas fa-calendar-alt text-primary me-2"></i> Ano</span>
                                      <span class="fw-bold"><?php echo $veiculo['ano']; ?></span>
                                  </li>
                                  <li class="list-group-item d-flex justify-content-between align-items-center">
                                      <span><i class="fas fa-palette text-primary me-2"></i> Cor</span>
                                      <span class="fw-bold"><?php echo $veiculo['cor']; ?></span>
                                  </li>
                                  <li class="list-group-item d-flex justify-content-between align-items-center">
                                      <span><i class="fas fa-id-card text-primary me-2"></i> Placa</span>
                                      <span class="fw-bold"><?php echo $veiculo['placa']; ?></span>
                                  </li>
                              </ul>
                          </div>
                          <div class="col-md-6">
                              <ul class="list-group list-group-flush">
                                  <li class="list-group-item d-flex justify-content-between align-items-center">
                                      <span><i class="fas fa-car text-primary me-2"></i> Marca</span>
                                      <span class="fw-bold"><?php echo $veiculo['marca']; ?></span>
                                  </li>
                                  <li class="list-group-item d-flex justify-content-between align-items-center">
                                      <span><i class="fas fa-car-alt text-primary me-2"></i> Modelo</span>
                                      <span class="fw-bold"><?php echo $veiculo['modelo']; ?></span>
                                  </li>
                                  <li class="list-group-item d-flex justify-content-between align-items-center">
                                      <span><i class="fas fa-fingerprint text-primary me-2"></i> Chassi</span>
                                      <span class="fw-bold"><?php echo substr($veiculo['chassi'], 0, 5) . '********'; ?></span>
                                  </li>
                              </ul>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          
          <div class="col-lg-4">
              <div class="card mb-4 sticky-top" style="top: 20px; z-index: 1;">
                  <div class="card-header bg-primary text-white">
                      <h5 class="mb-0"><?php echo $veiculo['marca'] . ' ' . $veiculo['modelo']; ?></h5>
                  </div>
                  <div class="card-body">
                      <div class="price-tag mb-4">
                          <h2 class="text-primary mb-0">R$ <?php echo number_format($veiculo['valor'], 2, ',', '.'); ?></h2>
                          <small class="text-muted">À vista</small>
                      </div>
                      
                      <div class="d-grid gap-2 mb-4">
                          <a href="#formInteresse" class="btn btn-primary btn-lg">Tenho Interesse</a>
                          <a href="https://wa.me/5511987654321?text=Olá, tenho interesse no veículo <?php echo $veiculo['marca'] . ' ' . $veiculo['modelo']; ?>" class="btn btn-success btn-lg" target="_blank">
                              <i class="fab fa-whatsapp"></i> Falar pelo WhatsApp
                          </a>
                      </div>
                      
                      <div class="contact-info">
                          <h5>Mais Informações</h5>
                          <ul class="list-unstyled">
                              <li class="mb-2"><i class="fas fa-phone text-primary me-2"></i> (11) 1234-5678</li>
                              <li class="mb-2"><i class="fas fa-envelope text-primary me-2"></i> contato@hsmotors.com.br</li>
                              <li><i class="fas fa-map-marker-alt text-primary me-2"></i> Av. Principal, 1234 - Centro</li>
                          </ul>
                      </div>
                  </div>
              </div>
          </div>
      </div>
      
      <!-- Formulário de Interesse -->
      <div class="card mt-4 mb-4" id="formInteresse">
          <div class="card-header bg-primary text-white">
              <h5 class="mb-0"><i class="fas fa-envelope"></i> Tenho Interesse neste Veículo</h5>
          </div>
          <div class="card-body">
              <form id="formVeiculoInteresse">
                  <input type="hidden" name="veiculo_id" value="<?php echo $veiculo['id']; ?>">
                  <div class="row">
                      <div class="col-md-6 mb-3">
                          <label for="nome" class="form-label">Nome Completo</label>
                          <input type="text" class="form-control" id="nome" name="nome" required>
                      </div>
                      <div class="col-md-6 mb-3">
                          <label for="email" class="form-label">Email</label>
                          <input type="email" class="form-control" id="email" name="email" required>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-md-6 mb-3">
                          <label for="telefone" class="form-label">Telefone</label>
                          <input type="tel" class="form-control" id="telefone" name="telefone" required>
                      </div>
                      <div class="col-md-6 mb-3">
                          <label for="melhor_horario" class="form-label">Melhor Horário para Contato</label>
                          <select class="form-select" id="melhor_horario" name="melhor_horario">
                              <option value="Manhã">Manhã</option>
                              <option value="Tarde">Tarde</option>
                              <option value="Noite">Noite</option>
                              <option value="Qualquer horário">Qualquer horário</option>
                          </select>
                      </div>
                  </div>
                  <div class="mb-3">
                      <label for="mensagem" class="form-label">Mensagem</label>
                      <textarea class="form-control" id="mensagem" name="mensagem" rows="3" required>Olá, tenho interesse no veículo <?php echo $veiculo['marca'] . ' ' . $veiculo['modelo']; ?>.</textarea>
                  </div>
                  <div class="d-grid">
                      <button type="submit" class="btn btn-primary">Enviar Mensagem</button>
                  </div>
              </form>
          </div>
      </div>
      
      <!-- Veículos Similares -->
      <?php if (count($veiculos_similares) > 0): ?>
      <div class="mt-5">
          <h3 class="mb-4">Veículos Similares</h3>
          <div class="row">
              <?php foreach ($veiculos_similares as $similar): ?>
                  <div class="col-md-4 mb-4">
                      <div class="card h-100 veiculo-card">
                          <div class="card-img-container">
                              <?php if (!empty($similar['imagem'])): ?>
                                  <img src="<?php echo $supabase_image_url . $similar['imagem']; ?>" class="card-img-top" alt="<?php echo $similar['marca'] . ' ' . $similar['modelo']; ?>">
                              <?php else: ?>
                                  <img src="assets/images/car-placeholder.jpg" class="card-img-top" alt="<?php echo $similar['marca'] . ' ' . $similar['modelo']; ?>">
                              <?php endif; ?>
                              <div class="card-img-overlay">
                                  <span class="badge bg-primary"><?php echo $similar['ano']; ?></span>
                              </div>
                          </div>
                          <div class="card-body">
                              <h5 class="card-title"><?php echo $similar['marca'] . ' ' . $similar['modelo']; ?></h5>
                              <p class="card-text text-muted"><?php echo $similar['cor']; ?> | <?php echo $similar['placa']; ?></p>
                              <p class="card-text fw-bold text-primary">R$ <?php echo number_format($similar['valor'], 2, ',', '.'); ?></p>
                              <a href="veiculo.php?id=<?php echo $similar['id']; ?>" class="btn btn-outline-primary w-100">Ver Detalhes</a>
                          </div>
                      </div>
                  </div>
              <?php endforeach; ?>
          </div>
      </div>
      <?php endif; ?>
  </div>
  
  <?php include 'includes/public/footer.php'; ?>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="assets/js/script.js"></script>
</body>
</html>

