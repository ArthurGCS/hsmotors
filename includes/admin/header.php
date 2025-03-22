<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-car-side"></i> HS Motors
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fas fa-home"></i> Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="clientes/listar.php"><i class="fas fa-users"></i> Clientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="veiculos/listar.php"><i class="fas fa-car"></i> Veículos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="vinculos/listar.php"><i class="fas fa-link"></i> Vínculos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php"><i class="fas fa-globe"></i> Ver Site</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <?php if (isset($_SESSION['alert'])): ?>
    <div class="alert alert-<?php echo $_SESSION['alert']['type']; ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['alert']['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['alert']); endif; ?>
</header>

