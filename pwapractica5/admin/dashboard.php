<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !hasRole('Administrator')) {
    redirect('../login.php');
}

$total_users = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_books = $conn->query("SELECT COUNT(*) FROM books")->fetchColumn();
$total_transactions = $conn->query("SELECT COUNT(*) FROM transactions")->fetchColumn();

include '../includes/header.php';
?>

<div class="container mt-4">
    <h2>Panel de Administrador</h2>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Usuarios</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $total_users; ?></h5>
                    <p class="card-text">Usuarios registrados</p>
                    <a href="users.php" class="btn btn-light">Gestionar</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Libros</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $total_books; ?></h5>
                    <p class="card-text">Libros en el catálogo</p>
                    <a href="../librarian/books.php" class="btn btn-light">Ver catálogo</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Transacciones</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $total_transactions; ?></h5>
                    <p class="card-text">Préstamos registrados</p>
                    <a href="transactions.php" class="btn btn-light">Ver transacciones</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>