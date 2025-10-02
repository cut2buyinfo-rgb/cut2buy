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
                    <h4>My Orders</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($orders)): ?>
                        <div class="text-center p-4">
                            <p class="mb-0">You have not placed any orders yet.</p>
                            <a href="/" class="btn btn-primary mt-3">Start Shopping</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Payment</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><strong>#<?= htmlspecialchars($order['id']) ?></strong></td>
                                            <td><?= date('d M, Y', strtotime($order['created_at'])) ?></td>
                                            <td>
                                                <span class="badge <?= $order['payment_status'] === 'paid' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                                    <?= ucfirst(htmlspecialchars($order['payment_status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info text-dark">
                                                    <?= ucfirst(htmlspecialchars($order['status'])) ?>
                                                </span>
                                            </td>
                                            <td><strong>৳<?= htmlspecialchars(number_format($order['total_amount'])) ?></strong></td>
                                            <td>
                                               <!-- ★★★ এই লিঙ্কটি খুবই গুরুত্বপূর্ণ ★★★ -->
                                               <a href="/orders/view/<?= $order['id'] ?>" class="btn btn-sm btn-outline-dark">
                                                    View Details
                                               </a>
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

<?php require(ROOT_PATH . '/views/partials/footer.php'); ?>