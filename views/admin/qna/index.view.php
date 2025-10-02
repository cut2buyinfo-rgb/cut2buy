<?php require(ROOT_PATH . '/views/partials/header.php'); ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2">
            <?php require(ROOT_PATH . '/views/partials/admin_sidebar.php'); ?>
        </div>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Questions & Answers</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Question</th>
                                    <th>Asked By</th>
                                    <th>Answer</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($qna_items)): ?>
                                    <tr><td colspan="5" class="text-center">No questions found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($qna_items as $item): ?>
                                        <tr class="<?= is_null($item['answer']) ? 'table-warning' : '' ?>">
                                            <td><a href="/product/<?= $item['product_slug'] ?>" target="_blank"><?= htmlspecialchars(substr($item['product_name'], 0, 40)) ?>...</a></td>
                                            <td>
                                                <?= htmlspecialchars($item['question']) ?>
                                                <small class="d-block text-muted"><?= date('d M Y', strtotime($item['created_at'])) ?></small>
                                            </td>
                                            <td><?= htmlspecialchars($item['asker_name']) ?></td>
                                            <td>
                                                <form action="/admin/qna/answer" method="POST">
                                                    <input type="hidden" name="qna_id" value="<?= $item['id'] ?>">
                                                    <div class="input-group">
                                                        <textarea name="answer" class="form-control form-control-sm" rows="2" placeholder="Write an answer..."><?= htmlspecialchars($item['answer'] ?? '') ?></textarea>
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td>
                                                <form action="/admin/qna/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                                    <input type="hidden" name="qna_id" value="<?= $item['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
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

<?php require(ROOT_PATH . '/views/partials/footer.php'); ?>