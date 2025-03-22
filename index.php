<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Consulta para listar todos os veículos disponíveis para exibição pública
$stmt = $conn->prepare("SELECT * FROM veiculos ORDER BY marca, modelo");
$stmt->execute();
$veiculos = $stmt->fetchAll();

// Consulta para obter marcas para o filtro
$stmt = $conn->prepare("SELECT DISTINCT marca FROM veiculos ORDER BY marca");
$stmt->execute();
$marcas = $stmt->fetchAll();

// Consulta para veículos em destaque
$stmt = $conn->prepare("SELECT * FROM veiculos WHERE destaque = TRUE ORDER BY RANDOM() LIMIT 3");
$stmt->execute();
$veiculos_destaque = $stmt->fetchAll();

// URL base para imagens do Supabase
$supabase_image_url = $supabase_url . '/storage/v1/object/public/veiculos/';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HS Motors - Concessionária de Veículos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <?php include 'includes/public/header.php'; ?>
  
  <!-- Hero Section -->
  <section class="hero-section">
      <div class="container">
          <div class="row align-items-center">
              <div class="col-lg-6">
                  <h1 class="display-4 fw-bold">Encontre o carro dos seus sonhos</h1>
                  <p class="lead">Na HS Motors, você encontra os melhores veículos com as melhores condições do mercado.</p>
                  <div class="mt-4">
                      <a href="#veiculos" class="btn btn-primary btn-lg hero-btn">Ver Veículos</a>
                      <a href="#contato" class="btn btn-outline-light btn-lg ms-2 hero-btn">Contato</a>
                  </div>
              </div>
              <div class="col-lg-6">
                  <img src="assets/images/hero-car.jpg" alt="HS Motors" class="img-fluid rounded shadow-lg">
              </div>
          </div>
      </div>
  </section>
  
  <!-- Veículos em Destaque -->
  <?php if (count($veiculos_destaque) > 0): ?>
  <section class="py-5 bg-white">
      <div class="container">
          <h2 class="section-title text-center mb-5">Veículos em Destaque</h2>
          <div class="row">
              <?php foreach ($veiculos_destaque as $destaque): ?>
                  <div class="col-lg-4 col-md-6 mb-4">
                      <div class="card h-100 veiculo-card">
                          <div class="card-img-container">
                              <?php if (!empty($destaque['imagem'])): ?>
                                  <img src="<?php echo $supabase_image_url . $destaque['imagem']; ?>" class="card-img-top" alt="<?php echo $destaque['marca'] . ' ' . $destaque['modelo']; ?>">
                              <?php else: ?>
                                  <img src="assets/images/car-placeholder.jpg" class="card-img-top" alt="<?php echo $destaque['marca'] . ' ' . $destaque['modelo']; ?>">
                              <?php endif; ?>
                              <div class="card-img-overlay">
                                  <span class="badge bg-primary"><?php echo $destaque['ano']; ?></span>
                              </div>
                          </div>
                          <div class="card-body">
                              <h5 class="card-title"><?php echo $destaque['marca'] . ' ' . $destaque['modelo']; ?></h5>
                              <p class="card-text text-muted"><?php echo $destaque['cor']; ?> | <?php echo $destaque['placa']; ?></p>
                              <p class="card-text fw-bold text-primary">R$ <?php echo number_format($destaque['valor'], 2, ',', '.'); ?></p>
                              <a href="veiculo.php?id=<?php echo $destaque['id']; ?>" class="btn btn-outline-primary w-100">Ver Detalhes</a>
                          </div>
                      </div>
                  </div>
              <?php endforeach; ?>
          </div>
      </div>
  </section>
  <?php endif; ?>
  
  <!-- Filtro de Veículos -->
  <section class="veiculos-section" id="veiculos">
      <div class="container">
          <h2 class="section-title mb-5">Nossos Veículos</h2>
          
          <div class="filtro-container">
              <div class="row">
                  <div class="col-md-8">
                      <p class="lead mb-0">Confira nossa seleção de veículos disponíveis</p>
                  </div>
                  <div class="col-md-4">
                      <div class="input-group">
                          <select id="filtroMarca" class="form-select">
                              <option value="">Todas as Marcas</option>
                              <?php foreach ($marcas as $marca): ?>
                                  <option value="<?php echo $marca['marca']; ?>"><?php echo $marca['marca']; ?></option>
                              <?php endforeach; ?>
                          </select>
                          <button class="btn btn-primary" type="button" id="btnFiltrar">Filtrar</button>
                      </div>
                  </div>
              </div>
          </div>
          
          <div class="row" id="listaVeiculos">
              <?php if (count($veiculos) > 0): ?>
                  <?php foreach ($veiculos as $veiculo): ?>
                      <div class="col-lg-4 col-md-6 mb-4 veiculo-item" data-marca="<?php echo $veiculo['marca']; ?>">
                          <div class="card h-100 veiculo-card">
                              <div class="card-img-container">
                                  <?php if (!empty($veiculo['imagem'])): ?>
                                      <img src="<?php echo $supabase_image_url . $veiculo['imagem']; ?>" class="card-img-top" alt="<?php echo $veiculo['marca'] . ' ' . $veiculo['modelo']; ?>">
                                  <?php else: ?>
                                      <img src="assets/images/car-placeholder.jpg" class="card-img-top" alt="<?php echo $veiculo['marca'] . ' ' . $veiculo['modelo']; ?>">
                                  <?php endif; ?>
                                  <div class="card-img-overlay">
                                      <span class="badge bg-primary"><?php echo $veiculo['ano']; ?></span>
                                  </div>
                              </div>
                              <div class="card-body">
                                  <h5 class="card-title"><?php echo $veiculo['marca'] . ' ' . $veiculo['modelo']; ?></h5>
                                  <p class="card-text text-muted"><?php echo $veiculo['cor']; ?> | <?php echo $veiculo['placa']; ?></p>
                                  <p class="card-text fw-bold text-primary">R$ <?php echo number_format($veiculo['valor'], 2, ',', '.'); ?></p>
                                  <a href="veiculo.php?id=<?php echo $veiculo['id']; ?>" class="btn btn-outline-primary w-100">Ver Detalhes</a>
                              </div>
                          </div>
                      </div>
                  <?php endforeach; ?>
              <?php else: ?>
                  <div class="col-12">
                      <div class="alert alert-info">
                          <i class="fas fa-info-circle"></i> Nenhum veículo disponível no momento.
                      </div>
                  </div>
              <?php endif; ?>
          </div>
      </div>
  </section>
  
  <!-- Resto do código permanece o mesmo -->
  
  <?php include 'includes/public/footer.php'; ?>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="assets/js/script.js"></script>
</body>
</html>

