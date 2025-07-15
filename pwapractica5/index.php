<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirectBasedOnRole();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Online</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS Personalizado -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Barra de Navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-book-open me-2"></i>Biblioteca Online
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Características</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">Acerca de</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Iniciar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light ms-2" href="register.php">Registrarse</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section bg-light py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Explora nuestro catálogo de libros</h1>
                    <p class="lead mb-4">Accede a miles de títulos desde cualquier lugar y gestiona tus préstamos de forma sencilla.</p>
                    <div class="d-flex gap-3">
                        <a href="register.php" class="btn btn-primary btn-lg px-4">Regístrate Gratis</a>
                        <a href="login.php" class="btn btn-outline-primary btn-lg px-4">Iniciar Sesión</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="assets/img/library-hero.png" alt="Biblioteca" class="img-fluid d-none d-lg-block">
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Nuestras Características</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 mx-auto" style="width: 60px; height: 60px;">
                                <i class="fas fa-book fa-lg"></i>
                            </div>
                            <h5 class="card-title">Catálogo Digital</h5>
                            <p class="card-text">Accede a nuestra amplia colección de libros digitales desde cualquier dispositivo.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 mx-auto" style="width: 60px; height: 60px;">
                                <i class="fas fa-user-shield fa-lg"></i>
                            </div>
                            <h5 class="card-title">Roles Personalizados</h5>
                            <p class="card-text">Sistema de roles con permisos diferenciados para administradores, bibliotecarios y lectores.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 mx-auto" style="width: 60px; height: 60px;">
                                <i class="fas fa-chart-line fa-lg"></i>
                            </div>
                            <h5 class="card-title">Gestión de Préstamos</h5>
                            <p class="card-text">Sistema completo para solicitar, aprobar y registrar devoluciones de libros.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <img src="assets/img/about-library.png" alt="Acerca de" class="img-fluid rounded shadow">
                </div>
                <div class="col-lg-6">
                    <h2 class="mb-4">Acerca de Nuestra Biblioteca</h2>
                    <p>Nuestra plataforma de biblioteca online fue desarrollada para facilitar el acceso a los recursos literarios y optimizar la gestión de préstamos.</p>
                    <p>El sistema fue creado utilizando tecnologías modernas como PHP, MySQL, Bootstrap 5 y JavaScript, garantizando un rendimiento óptimo y una experiencia de usuario intuitiva.</p>
                    <div class="mt-4">
                        <a href="register.php" class="btn btn-primary me-2">Únete Ahora</a>
                        <a href="#features" class="btn btn-outline-primary">Más Características</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3">Biblioteca Online</h5>
                    <p>Sistema de gestión de biblioteca desarrollado como proyecto educativo.</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3">Enlaces Rápidos</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php" class="text-white text-decoration-none">Inicio</a></li>
                        <li class="mb-2"><a href="login.php" class="text-white text-decoration-none">Iniciar Sesión</a></li>
                        <li class="mb-2"><a href="register.php" class="text-white text-decoration-none">Registrarse</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Contacto</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i> contacto@bibliotecaonline.com</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i> +123 456 7890</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 bg-light">
            <div class="text-center">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> Biblioteca Online. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JS Personalizado -->
    <script src="assets/js/script.js"></script>
</body>
</html>