<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !hasRole('Reader')) {
    redirect('../login.php');
}

// Manejar solicitud de préstamo
if (isset($_GET['borrow']) && is_numeric($_GET['borrow'])) {
    $book_id = (int)$_GET['borrow'];
    $user_id = $_SESSION['user_id'];
    
    // Verificar disponibilidad
    $book = getBookById($conn, $book_id);
    
    if ($book && $book['quantity'] > 0) {
        // Registrar transacción
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, book_id, date_of_issue) VALUES (?, ?, CURDATE())");
        if ($stmt->execute([$user_id, $book_id])) {
            // Reducir cantidad disponible
            $conn->prepare("UPDATE books SET quantity = quantity - 1 WHERE id = ?")->execute([$book_id]);
            
            $_SESSION['success'] = "Libro prestado exitosamente";
            redirect('catalog.php');
        }
    } else {
        $error = "El libro no está disponible";
    }
}

// Manejar devolución de libro
if (isset($_GET['return']) && is_numeric($_GET['return'])) {
    $transaction_id = (int)$_GET['return'];
    
    // Obtener información de la transacción
    $stmt = $conn->prepare("SELECT book_id FROM transactions WHERE id = ? AND user_id = ? AND date_of_return IS NULL");
    $stmt->execute([$transaction_id, $_SESSION['user_id']]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($transaction) {
        // Registrar devolución
        $stmt = $conn->prepare("UPDATE transactions SET date_of_return = CURDATE() WHERE id = ?");
        if ($stmt->execute([$transaction_id])) {
            // Aumentar cantidad disponible
            $conn->prepare("UPDATE books SET quantity = quantity + 1 WHERE id = ?")->execute([$transaction['book_id']]);
            
            $_SESSION['success'] = "Libro devuelto exitosamente";
            redirect('catalog.php');
        }
    } else {
        $error = "No se pudo procesar la devolución";
    }
}

// Obtener todos los libros
$books = displayBooks($conn);

// Obtener libros prestados por el usuario actual
$borrowed_books = $conn->prepare("
    SELECT t.id as transaction_id, b.* 
    FROM transactions t 
    JOIN books b ON t.book_id = b.id 
    WHERE t.user_id = ? AND t.date_of_return IS NULL
");
$borrowed_books->execute([$_SESSION['user_id']]);
$borrowed_books = $borrowed_books->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container mt-4">
    <h2>Catálogo de Libros</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="row mt-4">
        <?php foreach ($books as $book): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                    <p class="card-text">
                        <strong>Autor:</strong> <?php echo htmlspecialchars($book['author']); ?><br>
                        <strong>Año:</strong> <?php echo $book['year']; ?><br>
                        <strong>Género:</strong> <?php echo htmlspecialchars($book['genre']); ?><br>
                        <strong>Disponibles:</strong> <?php echo $book['quantity']; ?>
                    </p>
                </div>
                <div class="card-footer">
                    <?php if ($book['quantity'] > 0): ?>
                        <a href="catalog.php?borrow=<?php echo $book['id']; ?>" class="btn btn-primary">Solicitar Préstamo</a>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled>No disponible</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <?php if (!empty($borrowed_books)): ?>
    <div class="card mt-4">
        <div class="card-header">
            <h4>Mis Libros Prestados</h4>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Fecha de Préstamo</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($borrowed_books as $book): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                        <td><?php echo $book['date_of_issue']; ?></td>
                        <td>
                            <a href="catalog.php?return=<?php echo $book['transaction_id']; ?>" class="btn btn-warning">Devolver</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>