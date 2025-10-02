<?php require(ROOT_PATH . '/views/partials/header.php'); ?>
<style>
    /* Modern Order Tracking Timeline CSS */
    .tracking-timeline { list-style: none; padding: 0; position: relative; }
    .tracking-timeline:before { content: ''; position: absolute; top: 0; bottom: 0; left: 6px; width: 2px; background-color: #e9ecef; }
    .tracking-timeline-item { position: relative; margin-bottom: 30px; }
    .tracking-timeline-item .marker { position: absolute; top: 2px; left: 0; width: 14px; height: 14px; border-radius: 50%; background-color: #e9ecef; border: 2px solid #fff; }
    .tracking-timeline-item .content { padding-left: 30px; }
    .tracking-timeline-item.active .marker { background-color: #0d6efd; }
    .tracking-timeline-item.completed .marker { background-color: #198754; }
</style>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <?php require(ROOT_PATH . '/views/partials/dashboard_sidebar.php'); ?>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Order Details <span class="text-muted">#<?= htmlspecialchars($order['id']) ?></span></h4>
                    <a href="/orders" class="btn btn-sm btn-outline-secondary">Back to My Orders</a>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Left side: Order Status Timeline -->
                        <div class="col-md-5">
                            <h5>Order Status</h5>
                            <ul class="tracking-timeline mt-3">
                                <?php
                                $statuses = ['pending', 'processing', 'shipped', 'delivered'];
                                $currentStatusIndex = array_search($order['status'], $statuses);
                                ?>
                                <?php foreach ($statuses as $index => $status): ?>
                                    <li class="tracking-timeline-item <?= $index <= $currentStatusIndex ? 'completed' : '' ?>">
                                        <div class="marker"></div>
                                        <div class="content">
                                            <strong><?= ucfirst($status) ?></strong>
                                            <?php if ($status === 'pending' && $index <= $currentStatusIndex): ?>
                                                <p class="text-muted small mb-0">Order placed on <?= date('d M Y', strtotime($order['created_at'])) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!-- Right side: Shipping & Payment Details -->
                        <div class="col-md-7">
                             <h5>Shipping Address</h5>
                             <address><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></address>
                             <hr>
                             <h5>Payment Details</h5>
                             <p class="mb-1"><strong>Total Amount:</strong> ৳<?= number_format($order['total_amount'], 2) ?></p>
                             <p class="mb-1"><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
                             <p class="mb-0"><strong>Payment Status:</strong> <span class="badge <?= $order['payment_status'] === 'paid' ? 'bg-success' : 'bg-warning' ?>"><?= ucfirst(htmlspecialchars($order['payment_status'])) ?></span></p>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <hr class="my-4">
                    <h5>Items in this order</h5>
                    <?php foreach ($order_items as $item): ?>
                    <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                        <img src="/assets/images/products/<?= htmlspecialchars($item['product_image'] ?? 'placeholder.png') ?>" width="60" class="rounded me-3">
                        <div class="flex-grow-1">
                            <a href="/product/<?= htmlspecialchars($item['product_slug']) ?>"><?= htmlspecialchars($item['product_name']) ?></a>
                            <p class="mb-0 text-muted">Qty: <?= $item['quantity'] ?> x ৳<?= number_format($item['price'], 2) ?></p>
                            
                            <!-- ★★★ রিভিউ বাটন লজিক শুরু ★★★ -->
                            <div class="mt-2">
                                <?php if ($order['status'] === 'delivered' && !$item['is_reviewed']): ?>
                                    <a href="/product/<?= htmlspecialchars($item['product_slug']) ?>#reviews" class="btn btn-sm btn-warning">
                                        <i class="bi bi-star-fill"></i> Write a Review
                                    </a>
                                <?php elseif ($item['is_reviewed']): ?>
                                    <span class="text-success small">
                                        <i class="bi bi-check-circle-fill"></i> Reviewed
                                    </span>
                                <?php endif; ?>
                            </div>
                            <!-- ★★★ রিভিউ বাটন লজিক শেষ ★★★ -->

                        </div>
                        <strong class="ms-auto align-self-start">৳<?= number_format($item['price'] * $item['quantity'], 2) ?></strong>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require(ROOT_PATH . '/views/partials/footer.php'); ?>