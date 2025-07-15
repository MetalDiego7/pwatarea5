<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !hasRole('Librarian')) {
    redirect('../login.php');
}

// Manejar agregar nuevo libro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book'])) {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $year = trim($_POST['year']);
    $genre = trim($_POST['genre']);
    $quantity = (int)$_POST['quantity'];
    
    $stmt = $conn->prepare("INSERT INTO books (title, author, year, genre, quantity) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$title, $author, $year, $genre, $quantity])) {
        $_SESSION['success'] = "Libro agregado exitosamente";
        redirect('books.php');
    } else {
        $error = "Error al agregar el libro";
    }
}

// Manejar actualización de libro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_book'])) {
    $id = (int)$_POST['id'];
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $year = trim($_POST['year']);
    $genre = trim($_POST['genre']);
    $quantity = (int)$_POST['quantity'];
    
    $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, year = ?, genre = ?, quantity = ? WHERE id = ?");
    if ($stmt->execute([$title, $author, $year, $genre, $quantity, $id])) {
        $_SESSION['success'] = "Libro actualizado exitosamente";
        redirect('books.php');
    } else {
        $error = "Error al actualizar el libro";
    }
}

// Manejar eliminación de libro
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['success'] = "Libro eliminado exitosamente";
        redirect('books.php');
    } else {
        $error = "Error al eliminar el libro";
    }
}

$books = $conn->query("SELECT * FROM books")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container mt-4">
    <h2>Gestión de Libros</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="card mt-4">
        <div class="card-header">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookModal">
                Agregar Nuevo Libro
            </button>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Año</th>
                        <th>Género</th>
                        <th>Cantidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?php echo $book['id']; ?></td>
                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                        <td><?php echo $book['year']; ?></td>
                        <td><?php echo htmlspecialchars($book['genre']); ?></td>
                        <td><?php echo $book['quantity']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-book" 
                                    data-id="<?php echo $book['id']; ?>"
                                    data-title="<?php echo htmlspecialchars($book['title']); ?>"
                                    data-author="<?php echo htmlspecialchars($book['author']); ?>"
                                    data-year="<?php echo $book['year']; ?>"
                                    data-genre="<?php echo htmlspecialchars($book['genre']); ?>"
                                    data-quantity="<?php echo $book['quantity']; ?>">
                                Editar
                            </button>
                            <a href="books.php?delete=<?php echo $book['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para agregar libro -->
<div class="modal fade" id="addBookModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Nuevo Libro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="add_book" value="1">
                    <div class="mb-3">
                        <label for="title" class="form-label">Título</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="author" class="form-label">Autor</label>
                        <input type="text" class="form-control" id="author" name="author" required>
                    </div>
                    <div class="mb-3">
                        <label for="year" class="form-label">Año</label>
                        <input type="number" class="form-control" id="year" name="year">
                    </div>
                    <div class="mb-3">
                        <label for="genre" class="form-label">Género</label>
                        <input type="text" class="form-control" id="genre" name="genre">
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Cantidad</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required min="1">
                    </div>
                    <button type="submit" class="btn btn-primary">Agregar Libro</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar libro -->
<div class="modal fade" id="editBookModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Libro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="update_book" value="1">
                    <input type="hidden" id="editId" name="id">
                    <div class="mb-3">
                        <label for="editTitle" class="form-label">Título</label>
                        <input type="text" class="form-control" id="editTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="editAuthor" class="form-label">Autor</label>
                        <input type="text" class="form-control" id="editAuthor" name="author" required>
                    </div>
                    <div class="mb-3">
                        <label for="editYear" class="form-label">Año</label>
                        <input type="number" class="form-control" id="editYear" name="year">
                    </div>
                    <div class="mb-3">
                        <label for="editGenre" class="form-label">Género</label>
                        <input type="text" class="form-control" id="editGenre" name="genre">
                    </div>
                    <div class="mb-3">
                        <label for="editQuantity" class="form-label">Cantidad</label>
                        <input type="number" class="form-control" id="editQuantity" name="quantity" required min="1">
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.edit-book').click(function() {
        $('#editId').val($(this).data('id'));
        $('#editTitle').val($(this).data('title'));
        $('#editAuthor').val($(this).data('author'));
        $('#editYear').val($(this).data('year'));
        $('#editGenre').val($(this).data('genre'));
        $('#editQuantity').val($(this).data('quantity'));
        
        $('#editBookModal').modal('show');
    });
});
</script>

<?php include '../includes/footer.php'; ?>