<?php require(ROOT_PATH . '/views/partials/header.php'); ?>
<main>
<div class="container my-5">
    <h2 class="mb-4 text-center">Complete Your Order</h2>
    
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['flash_message']['text'] ?></div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <form action="/place-order" method="POST" enctype="multipart/form-data">
        <div class="row g-4">
            <!-- Left Side: Payment & Shipping -->
            <div class="col-lg-7">
                <!-- Step 1: Payment Instructions -->
                <div class="card mb-4 border-danger">
                    <div class="card-header bg-danger text-white">
                        <strong>Step 1: Complete <?= PREPAYMENT_PERCENTAGE * 100 ?>% Prepayment</strong>
                    </div>
                    <div class="card-body">
                        <p>To confirm your order, please send <strong>৳<?= number_format($prepayment_amount, 2) ?></strong> via bKash.</p>
                        <div class="row align-items-center">
                            <div class="col-md-5 text-center mb-3 mb-md-0">
                                <img src="/assets/images/bkash-qr.png" alt="bKash QR Code" class="img-fluid rounded" style="max-width: 200px;">
                                <p class="mt-2 small text-muted">Scan to Pay</p>
                            </div>
                            <div class="col-md-7">
                                <p class="mb-1"><strong>Or Send Money to:</strong></p>
                                <h4 class="mb-2">017XX-XXXXXX</h4>
                                <p class="small">(Personal bKash Number)</p>
                                <p class="text-muted small">Please use your name or phone number in the reference field.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Shipping Information -->
                <div class="card">
                    <div class="card-header">
                        <strong>Step 2: Provide Your Details</strong>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Full Shipping Address <span class="text-danger">*</span></label>
                            <textarea id="address" name="address" class="form-control" rows="4" placeholder="e.g., House no, Road no, Area, Thana, District" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                            <div class="form-text">Your address will be saved for future orders.</div>
                        </div>
                        <hr>
                        <p><strong>Provide Payment Proof:</strong> (Submit at least one)</p>
                        <div class="mb-3">
                            <label for="transaction_id" class="form-label">bKash Transaction ID (TrxID)</label>
                            <input type="text" id="transaction_id" name="transaction_id" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="payment_screenshot" class="form-label">Or Upload Screenshot</label>
                            <input type="file" id="payment_screenshot" name="payment_screenshot" class="form-control" accept="image/png,image/jpeg">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Order Summary -->
            <div class="col-lg-5">
                <div class="card bg-light sticky-top" style="top: 100px;">
                    <div class="card-header">
                       <strong>Step 3: Review & Confirm Order</strong>
                    </div>
                    <div class="card-body">
                        <!-- [MODIFIED] This loop now shows variation details -->
                        <?php foreach($cart_items as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <div>
                                <span class="d-block small fw-bold"><?= htmlspecialchars($item['name']) ?></span>
                                <?php if (!empty($item['attributes'])): ?>
                                <span class="d-block text-muted" style="font-size: 0.8em;"><?= htmlspecialchars($item['attributes']) ?></span>
                                <?php endif; ?>
                                <span class="d-block text-muted" style="font-size: 0.8em;">Qty: <?= $item['quantity'] ?></span>
                            </div>
                            <span class="small fw-bold">৳<?= number_format($item['price'] * $item['quantity']) ?></span>
                        </div>
                        <?php endforeach; ?>
                        
                        <hr>
                        
                        <ul class="list-group list-group-flush">
                           <li class="list-group-item d-flex justify-content-between px-0 bg-transparent">
                               <span>Subtotal</span>
                               <span>৳<?= number_format($subtotal, 2) ?></span>
                           </li>
                           <li class="list-group-item d-flex justify-content-between px-0 bg-transparent">
                               <span>Shipping Fee</span>
                               <span>৳<?= number_format(SHIPPING_FEE, 2) ?></span>
                           </li>
                           <li class="list-group-item d-flex justify-content-between px-0 bg-transparent h5 border-top pt-3">
                               <strong>Grand Total</strong>
                               <strong>৳<?= number_format($total_amount, 2) ?></strong>
                           </li>
                           <li class="list-group-item d-flex justify-content-between px-0 bg-transparent text-danger">
                               <strong>Prepayment (<?= PREPAYMENT_PERCENTAGE * 100 ?>%)</strong>
                               <strong>৳<?= number_format($prepayment_amount, 2) ?></strong>
                           </li>
                           <li class="list-group-item d-flex justify-content-between px-0 bg-transparent">
                               <strong>Due Amount</strong>
                               <strong>৳<?= number_format($total_amount - $prepayment_amount, 2) ?></strong>
                           </li>
                        </ul>
                        
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" value="" id="termsCheck" required>
                            <label class="form-check-label small" for="termsCheck">
                                I agree to the <a href="/terms-and-conditions" target="_blank">Terms and Conditions</a>.
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mt-2 btn-lg">Place Order</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
</main>
<?php require(ROOT_PATH . '/views/partials/footer.php'); ?>