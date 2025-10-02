<?php require('partials/header.php'); ?>

<div class="container py-5">
    <div class="row">
        <!-- Column 1: Dashboard Sidebar Navigation -->
        <div class="col-lg-3">
            <?php 
            // We now load the reusable sidebar
            require('partials/dashboard_sidebar.php'); 
            ?>
        </div>

        <!-- Column 2: Main Content -->
        <div class="col-lg-9">
            <div class="dashboard-content">
                <!-- Overview Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Account Overview</h4>
                    </div>
                    <div class="card-body">
                        <p>From your account dashboard you can view your recent orders, manage your shipping and billing addresses, and edit your password and account details.</p>
                    </div>
                </div>

                <!-- Recent Orders Card -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Recent Orders</h4>
                        <a href="/orders" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($orders)): ?>
                            <p class="text-center">You have not placed any orders yet.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td>#<?= htmlspecialchars($order['id']) ?></td>
                                                <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                                                <td>৳<?= htmlspecialchars(number_format($order['total_amount'])) ?></td>
                                                <td>
                                                    <span class="badge bg-info text-dark">
                                                        <?= ucfirst(htmlspecialchars($order['status'])) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require('partials/footer.php'); ?>