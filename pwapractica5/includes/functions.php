<?php
// includes/functions.php
function redirectBasedOnRole() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
    
    switch ($_SESSION['role']) {
        case 'Administrator':
            redirect('admin/dashboard.php');
            break;
        case 'Librarian':
            redirect('librarian/dashboard.php');
            break;
        case 'Reader':
            redirect('reader/dashboard.php');
            break;
        default:
            redirect('login.php');
    }
}

function displayBooks($conn, $limit = null) {
    $sql = "SELECT * FROM books";
    if ($limit) {
        $sql .= " LIMIT $limit";
    }
    
    $stmt = $conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getBookById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>