<?php require(ROOT_PATH . '/views/partials/header.php'); ?>

<div class="container py-5">
    <div class="row">
        <!-- Column 1: Dashboard Sidebar -->
        <div class="col-lg-3">
            <?php require(ROOT_PATH . '/views/partials/dashboard_sidebar.php'); ?>
        </div>

        <!-- Column 2: Main Content -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header">
                    <h4>Address Book & Profile Details</h4>
                </div>
                <div class="card-body">

                    <?php // Display success or error messages (if any)
                    if (isset($message)): ?>
                        <div class="alert alert-<?= htmlspecialchars($message['type']) ?>" role="alert">
                            <?= htmlspecialchars($message['text']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Profile Update Form -->
                    <form action="/profile" method="POST">
                        <div class="row">
                            <!-- Full Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= htmlspecialchars($current_user['name'] ?? '') ?>" required>
                            </div>

                            <!-- Email Address -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= htmlspecialchars($current_user['email'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="row">
                             <!-- Phone Number -->
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                       value="<?= htmlspecialchars($current_user['phone'] ?? '') ?>" placeholder="e.g., 01700000000">
                            </div>
                        </div>
                        
                        <hr class="my-3">
                        <h5 class="mb-3">Shipping Address</h5>

                        <!-- Shipping Address -->
                        <div class="mb-3">
                            <label for="address" class="form-label">Full Shipping Address</label>
                            <textarea class="form-control" id="address" name="address" rows="4" 
                                      placeholder="Enter your full street address, area, city, and postal code"><?= htmlspecialchars($current_user['address'] ?? '') ?></textarea>
                            <div class="form-text">This will be used as your default shipping address.</div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                    
                </div>
            </div>

            <!-- Future Feature: Change Password (Optional) -->
            <!-- 
            <div class="card mt-4">
                <div class="card-header">
                    <h4>Change Password</h4>
                </div>
                <div class="card-body">
                    <form action="/profile/password" method="POST">
                        ... (form fields for current_password, new_password, confirm_password) ...
                    </form>
                </div>
            </div>
            -->

        </div>
    </div>
</div>

<?php require(ROOT_PATH . '/views/partials/footer.php'); ?>