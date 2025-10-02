<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <!-- সাইডবার -->
        <div class="col-md-3 col-lg-2">
             <?php require __DIR__ . '/../../partials/admin_sidebar.php'; ?>
        </div>

        <!-- মূল কন্টেন্ট -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Orders</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Order ID</th>
                                    <th scope="col">Customer Name</th>
                                    <th scope="col">Total Amount</th>
                                    <th scope="col">Order Date</th>
                                    <th scope="col">Order Status</th>
                                    <th scope="col">Payment Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No orders found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>#<?= htmlspecialchars($order['id']) ?></td>
                                            <td><?= htmlspecialchars($order['user_name']) ?></td>
                                            <td>৳<?= number_format($order['total_amount'], 2) ?></td>
                                            <td><?= date('d M, Y', strtotime($order['created_at'])) ?></td>
                                            <td>
                                                <?php
                                                    $statusClass = 'bg-info text-dark';
                                                    if ($order['status'] === 'processing') $statusClass = 'bg-primary';
                                                    if ($order['status'] === 'shipped') $statusClass = 'bg-warning text-dark';
                                                    if ($order['status'] === 'delivered') $statusClass = 'bg-success';
                                                    if ($order['status'] === 'cancelled') $statusClass = 'bg-danger';
                                                ?>
                                                <span class="badge <?= $statusClass ?>"><?= htmlspecialchars(ucfirst($order['status'])) ?></span>
                                            </td>
                                            <td>
                                                <span class="badge <?= $order['payment_status'] === 'paid' ? 'bg-success' : 'bg-danger' ?>">
                                                    <?= htmlspecialchars(ucfirst($order['payment_status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                               <a href="/admin/orders/view/<?= $order['id'] ?>" class="btn btn-sm btn-outline-info" title="View Details">
        <i class="bi bi-eye"></i> View
    </a>
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

<?php require __DIR__ . '/../../partials/footer.php'; ?>