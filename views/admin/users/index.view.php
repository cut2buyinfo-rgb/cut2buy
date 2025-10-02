<?php require(ROOT_PATH . '/views/partials/header.php'); ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <?php require(ROOT_PATH . '/views/partials/admin_sidebar.php'); ?>
        </div>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Users</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" id="addUserBtn">
                    <i class="bi bi-plus-circle"></i> Add New User
                </button>
            </div>

            <!-- Success/Error Messages -->
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Joined On</th><th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr><td colspan="8" class="text-center">No other users found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($users as $u): ?>
                                        <tr>
                                            <td><?= $u['id'] ?></td>
                                            <td><?= htmlspecialchars($u['name']) ?></td>
                                            <td><?= htmlspecialchars($u['email']) ?></td>
                                            <td><?= htmlspecialchars($u['phone'] ?? 'N/A') ?></td>
                                            <td>
                                                <?php
                                                    $roleClass = 'bg-secondary';
                                                    if ($u['role'] === 'admin') $roleClass = 'bg-info';
                                                    if ($u['role'] === 'saller') $roleClass = 'bg-primary';
                                                ?>
                                                <span class="badge <?= $roleClass ?>"><?= ucfirst($u['role']) ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                    $statusClass = 'bg-success';
                                                    if ($u['status'] === 'inactive') $statusClass = 'bg-warning text-dark';
                                                    if ($u['status'] === 'banned') $statusClass = 'bg-danger';
                                                ?>
                                                <span class="badge <?= $statusClass ?>"><?= ucfirst($u['status']) ?></span>
                                            </td>
                                            <td><?= date('d M, Y', strtotime($u['created_at'])) ?></td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-outline-primary edit-btn" 
                                                        data-bs-toggle="modal" data-bs-target="#userModal"
                                                        data-user='<?= json_encode($u) ?>'>
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <form action="/admin/users/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="userForm" action="/admin/users/store" method="POST">
                <input type="hidden" name="id" id="userId">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label for="userName" class="form-label">Full Name*</label><input type="text" class="form-control" id="userName" name="name" required></div>
                        <div class="col-md-6 mb-3"><label for="userEmail" class="form-label">Email*</label><input type="email" class="form-control" id="userEmail" name="email" required></div>
                        <div class="col-md-6 mb-3"><label for="userPhone" class="form-label">Phone</label><input type="text" class="form-control" id="userPhone" name="phone"></div>
                        <div class="col-md-6 mb-3"><label for="userPassword" class="form-label">Password</label><input type="password" class="form-control" id="userPassword" name="password" placeholder="Leave blank to keep unchanged"></div>
                        <div class="col-md-6 mb-3"><label for="userRole" class="form-label">Role*</label><select class="form-select" id="userRole" name="role" required><option value="user">User</option><option value="saller">Seller</option><option value="admin">Admin</option></select></div>
                        <div class="col-md-6 mb-3"><label for="userStatus" class="form-label">Status*</label><select class="form-select" id="userStatus" name="status" required><option value="active">Active</option><option value="inactive">Inactive</option><option value="banned">Banned</option></select></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const userModal = document.getElementById('userModal');
    const userForm = document.getElementById('userForm');
    const modalTitle = document.getElementById('userModalLabel');
    
    // Logic to switch modal between "Add" and "Edit" mode
    userModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const userData = button.dataset.user ? JSON.parse(button.dataset.user) : null;

        if (userData) { // Edit mode
            modalTitle.textContent = 'Edit User: ' + userData.name;
            userForm.action = '/admin/users/update';
            document.getElementById('userId').value = userData.id;
            document.getElementById('userName').value = userData.name;
            document.getElementById('userEmail').value = userData.email;
            document.getElementById('userPhone').value = userData.phone || '';
            document.getElementById('userRole').value = userData.role;
            document.getElementById('userStatus').value = userData.status;
            document.getElementById('userPassword').setAttribute('placeholder', 'Leave blank to keep unchanged');
            document.getElementById('userPassword').required = false;
        } else { // Add mode
            modalTitle.textContent = 'Add New User';
            userForm.action = '/admin/users/store';
            userForm.reset();
            document.getElementById('userId').value = '';
            document.getElementById('userPassword').setAttribute('placeholder', 'Required');
            document.getElementById('userPassword').required = true;
        }
    });
});
</script>

<?php require(ROOT_PATH . '/views/partials/footer.php'); ?>