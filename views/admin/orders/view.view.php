<?php require(ROOT_PATH . '/views/partials/header.php'); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2"><?php require(ROOT_PATH . '/views/partials/admin_sidebar.php'); ?></div>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Order Details: #<?= $order['id'] ?></h1>
                <a href="/admin/orders" class="btn btn-secondary btn-sm">Back to Orders</a>
            </div>

            <?php if (isset($_GET['status']) && $_GET['status'] === 'updated'): ?>
                <div class="alert alert-success">Order status has been updated successfully.</div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Left Column: Order & Customer Details -->
                <div class="col-lg-7">
                    <div class="card mb-4">
                        <div class="card-header"><strong>Order Summary</strong></div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Order Date:</strong> <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></li>
                                <li class="list-group-item"><strong>Current Status:</strong> <span class="badge bg-primary"><?= ucfirst($order['status']) ?></span></li>
                                <li class="list-group-item"><strong>Payment Method:</strong> <?= $order['payment_method'] ?></li>
                                <li class="list-group-item"><strong>Payment Status:</strong> <span class="badge <?= $order['payment_status'] === 'paid' ? 'bg-success' : 'bg-danger' ?>"><?= ucfirst($order['payment_status']) ?></span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><strong>Customer Details</strong></div>
                        <div class="card-body">
                            <p><strong>Name:</strong> <?= htmlspecialchars($order['user_name']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($order['user_email']) ?></p>
                            <p><strong>Phone:</strong> <?= htmlspecialchars($order['user_phone']) ?></p>
                            <p><strong>Shipping Address:</strong><br><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Status Update & Payment Proof -->
                <div class="col-lg-5">
                    <div class="card mb-4">
                        <div class="card-header"><strong>Update Order Status</strong></div>
                        <div class="card-body">
                            <form action="/admin/orders/update-status" method="POST">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <div class="mb-3">
                                    <label class="form-label">New Status</label>
                                    <select name="status" id="status-select" class="form-select" required>
                                        <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                                        <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                        <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                        <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                </div>
                                <!-- Tracking Info Fields (initially hidden) -->
                                <div id="tracking-info" class="d-none">
                                    <div class="mb-3"><label class="form-label">Tracking Number</label><input type="text" name="tracking_number" class="form-control" value="<?= htmlspecialchars($order['tracking_number'] ?? '') ?>"></div>
                                    <div class="mb-3"><label class="form-label">Tracking URL</label><input type="text" name="tracking_url" class="form-control" value="<?= htmlspecialchars($order['tracking_url'] ?? '') ?>"></div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Update Status</button>
                            </form>
                        </div>
                    </div>

                    <?php if ($order['transaction_id'] || $order['payment_screenshot']): ?>
                    <div class="card">
                        <div class="card-header"><strong>Payment Proof</strong></div>
                        <div class="card-body">
                            <?php if ($order['transaction_id']): ?>
                                <p><strong>Transaction ID:</strong> <?= htmlspecialchars($order['transaction_id']) ?></p>
                            <?php endif; ?>
                            <?php if ($order['payment_screenshot']): ?>
                                <p><strong>Screenshot:</strong></p>
                                <a href="/assets/images/screenshots/<?= $order['payment_screenshot'] ?>" target="_blank">
                                    <img src="/assets/images/screenshots/<?= $order['payment_screenshot'] ?>" class="img-fluid rounded border" alt="Payment Screenshot">
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

                <!-- Order Items Table (MODIFIED) -->
            <div class="card mt-4">
                <div class="card-header"><strong>Order Items</strong></div>
                <div class="card-body table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Product Details</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td>
                                    <a href="/product/<?= $item['product_slug'] ?>" target="_blank" class="fw-bold text-dark d-block">
                                        <?= htmlspecialchars($item['product_name']) ?>
                                    </a>
                                    <?php if (!empty($item['variation_details'])): ?>
                                        <small class="text-muted"><?= htmlspecialchars($item['variation_details']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><?= $item['quantity'] ?></td>
                                <td class="text-end">৳<?= number_format($item['price'], 2) ?></td>
                                <td class="text-end">৳<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-group-divider">
                            <tr><td colspan="3" class="text-end"><strong>Subtotal:</strong></td><td class="text-end">৳<?= number_format($order['total_amount'] - $order['shipping_fee'], 2) ?></td></tr>
                            <tr><td colspan="3" class="text-end"><strong>Shipping Fee:</strong></td><td class="text-end">৳<?= number_format($order['shipping_fee'], 2) ?></td></tr>
                            <tr><td colspan="3" class="text-end"><strong>Grand Total:</strong></td><td class="text-end"><strong>৳<?= number_format($order['total_amount'], 2) ?></strong></td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>
</main>

    </div>
</div>

<script>
// Simple script to show/hide tracking fields based on status selection
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status-select');
    const trackingInfoDiv = document.getElementById('tracking-info');

    function toggleTrackingInfo() {
        if (statusSelect.value === 'shipped') {
            trackingInfoDiv.classList.remove('d-none');
        } else {
            trackingInfoDiv.classList.add('d-none');
        }
    }
    // Check on page load
    toggleTrackingInfo();
    // Check on change
    statusSelect.addEventListener('change', toggleTrackingInfo);
});
</script>

<?php require(ROOT_PATH . '/views/partials/footer.php'); ?>