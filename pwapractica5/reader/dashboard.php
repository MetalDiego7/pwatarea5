<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !hasRole('Reader')) {
    redirect('../login.php');
}

// Obtener información del usuario
$user_id = $_SESSION['user_id'];
$user_stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

// Obtener libros prestados actualmente
$borrowed_books = $conn->prepare("
    SELECT b.title, b.author, t.date_of_issue 
    FROM transactions t
    JOIN books b ON t.book_id = b.id
    WHERE t.user_id = ? AND t.date_of_return IS NULL
    ORDER BY t.date_of_issue DESC
    LIMIT 5
");
$borrowed_books->execute([$user_id]);
$borrowed_books = $borrowed_books->fetchAll(PDO::FETCH_ASSOC);

// Obtener libros recientemente añadidos
$recent_books = $conn->query("
    SELECT * FROM books 
    ORDER BY id DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Lector - Biblioteca Online</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS Personalizado -->
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .dashboard-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            border: none;
            margin-bottom: 20px;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .card-header-custom {
            background-color: #4e73df;
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
        }
        .user-profile-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 3px solid #4e73df;
        }
        .book-cover {
            height: 120px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- Barra de Navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-book-open me-2"></i>Biblioteca Online
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">Bienvenido, <?php echo htmlspecialchars($user['username']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="catalog.php"><i class="fas fa-book me-1"></i> Catálogo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-1"></i> Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container py-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="dashboard-card card">
                    <div class="card-body text-center">
                        <img src="../assets/img/user-profile.png" alt="Perfil" class="user-profile-img rounded-circle mb-3">
                        <h5><?php echo htmlspecialchars($user['username']); ?></h5>
                        <p class="text-muted mb-1"><?php echo htmlspecialchars($user['email']); ?></p>
                        <span class="badge bg-primary">Lector</span>
                        
                        <hr>
                        
                        <div class="d-grid gap-2">
                            <a href="catalog.php" class="btn btn-outline-primary">
                                <i class="fas fa-search me-1"></i> Buscar Libros
                            </a>
                            <a href="#" class="btn btn-outline-secondary">
                                <i class="fas fa-history me-1"></i> Historial
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="dashboard-card card">
                    <div class="card-header card-header-custom">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información</h5>
                    </div>
                    <div class="card-body">
                        <p class="small">Como lector, puedes:</p>
                        <ul class="small ps-3">
                            <li>Explorar nuestro catálogo completo</li>
                            <li>Solicitar préstamos de libros</li>
                            <li>Gestionar tus préstamos activos</li>
                            <li>Ver tu historial de lectura</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="dashboard-card card mb-4">
                    <div class="card-header card-header-custom">
                        <h5 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Resumen</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <div class="card bg-primary text-white h-100">
                                    <div class="card-body text-center">
                                        <h1 class="display-5">
                                            <?php echo count($borrowed_books); ?>
                                        </h1>
                                        <p class="mb-0">Libros Prestados</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card bg-success text-white h-100">
                                    <div class="card-body text-center">
                                        <h1 class="display-5">
                                            <?php 
                                            $total_books = $conn->query("SELECT COUNT(*) FROM books")->fetchColumn();
                                            echo $total_books;
                                            ?>
                                        </h1>
                                        <p class="mb-0">Libros Disponibles</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card bg-info text-white h-100">
                                    <div class="card-body text-center">
                                        <h1 class="display-5">
                                            <?php 
                                            $total_loans = $conn->prepare("SELECT COUNT(*) FROM transactions WHERE user_id = ?")->execute([$user_id]);
                                            echo $total_loans;
                                            ?>
                                        </h1>
                                        <p class="mb-0">Préstamos Totales</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="dashboard-card card h-100">
                            <div class="card-header card-header-custom">
                                <h5 class="mb-0"><i class="fas fa-bookmark me-2"></i>Mis Préstamos Activos</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($borrowed_books)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No tienes libros prestados actualmente</p>
                                        <a href="catalog.php" class="btn btn-primary">Explorar Catálogo</a>
                                    </div>
                                <?php else: ?>
                                    <div class="list-group">
                                        <?php foreach ($borrowed_books as $book): ?>
                                        <div class="list-group-item border-0">
                                            <div class="d-flex align-items-center">
                                                <img src="../assets/img/book-cover.jpg" alt="Portada" class="book-cover me-3">
                                                <div>
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($book['title']); ?></h6>
                                                    <small class="text-muted"><?php echo htmlspecialchars($book['author']); ?></small>
                                                    <div class="mt-2">
                                                        <span class="badge bg-light text-dark">
                                                            <i class="fas fa-calendar-alt me-1"></i>
                                                            <?php echo date('d/m/Y', strtotime($book['date_of_issue'])); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="text-end mt-3">
                                        <a href="catalog.php?filter=borrowed" class="btn btn-sm btn-outline-primary">Ver todos</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="dashboard-card card h-100">
                            <div class="card-header card-header-custom">
                                <h5 class="mb-0"><i class="fas fa-star me-2"></i>Novedades</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recent_books)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No hay libros recientes</p>
                                    </div>
                                <?php else: ?>
                                    <div class="list-group">
                                        <?php foreach ($recent_books as $book): ?>
                                        <a href="catalog.php?book=<?php echo $book['id']; ?>" class="list-group-item list-group-item-action border-0">
                                            <div class="d-flex align-items-center">
                                                <img src="../assets/img/book-cover.jpg" alt="Portada" class="book-cover me-3">
                                                <div>
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($book['title']); ?></h6>
                                                    <small class="text-muted"><?php echo htmlspecialchars($book['author']); ?></small>
                                                    <div class="mt-2">
                                                        <?php if ($book['quantity'] > 0): ?>
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check me-1"></i> Disponible
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">
                                                                <i class="fas fa-times me-1"></i> Agotado
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="text-end mt-3">
                                        <a href="catalog.php" class="btn btn-sm btn-outline-primary">Ver catálogo completo</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-book-open me-2"></i> Biblioteca Online</h5>
                    <p class="mb-0">Sistema de gestión de biblioteca para lectores.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Biblioteca Online. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JS Personalizado -->
    <script src="../assets/js/script.js"></script>
</body>
</html>