<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !hasRole('Administrator')) {
    redirect('../login.php');
}

// Obtener todos los usuarios con sus roles
$users = $conn->query("
    SELECT u.id, u.username, u.email, r.name as role 
    FROM users u 
    JOIN roles r ON u.role_id = r.id
")->fetchAll(PDO::FETCH_ASSOC);

// Obtener todos los roles para el formulario
$roles = $conn->query("SELECT * FROM roles")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container mt-4">
    <h2>Gestión de Usuarios</h2>
    
    <div class="card mt-4">
        <div class="card-header">
            <h4>Lista de Usuarios</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre de usuario</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-user" data-id="<?php echo $user['id']; ?>">Editar</button>
                            <button class="btn btn-sm btn-danger delete-user" data-id="<?php echo $user['id']; ?>">Eliminar</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para editar usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId" name="id">
                    <div class="mb-3">
                        <label for="editUsername" class="form-label">Nombre de usuario</label>
                        <input type="text" class="form-control" id="editUsername" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="editRole" class="form-label">Rol</label>
                        <select class="form-select" id="editRole" name="role_id" required>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveUserChanges">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Editar usuario
    $('.edit-user').click(function() {
        const userId = $(this).data('id');
        
        $.ajax({
            url: '../api/get_user.php',
            type: 'GET',
            data: { id: userId },
            success: function(response) {
                if (response.success) {
                    $('#editUserId').val(response.data.id);
                    $('#editUsername').val(response.data.username);
                    $('#editEmail').val(response.data.email);
                    $('#editRole').val(response.data.role_id);
                    
                    $('#editUserModal').modal('show');
                }
            }
        });
    });
    
    // Guardar cambios del usuario
    $('#saveUserChanges').click(function() {
        const formData = $('#editUserForm').serialize();
        
        $.ajax({
            url: '../api/update_user.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
    });
    
    // Eliminar usuario
    $('.delete-user').click(function() {
        if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
            const userId = $(this).data('id');
            
            $.ajax({
                url: '../api/delete_user.php',
                type: 'POST',
                data: { id: userId },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>