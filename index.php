<?php
require '../config/database.php';
require '../utils/session.php';

// Pagination settings
$postsPerPage = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $postsPerPage;

// Search handling
$search = $_GET['search'] ?? '';
$params = [];
$where = '';
if ($search) {
    $where = "WHERE title LIKE ? OR content LIKE ?";
    $params = ["%$search%", "%$search%"];
}
// Count total posts for pagination
$countSql = "SELECT COUNT(*) FROM posts $where";
$countStmt = $conn->prepare($countSql);
$countStmt->execute($params);
$totalPosts = $countStmt->fetchColumn();
$totalPages = ceil($totalPosts / $postsPerPage);

// Fetch posts for current page
$sql = "SELECT * FROM posts $where ORDER BY created_at DESC LIMIT $postsPerPage OFFSET $offset";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();
?>

<!-- Bootstrap CSS for better UI -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    min-height: 100vh;
    background: linear-gradient(-45deg, #ff9a9e, #fad0c4, #a18cd1, #fbc2eb, #fcb69f, #ffdde1, #cfd9df, #e2ebf0);
    background-size: 400% 400%;
    animation: gradientBG 18s ease infinite;
}
@keyframes gradientBG {
    0% {background-position: 0% 50%;}
    50% {background-position: 100% 50%;}
    100% {background-position: 0% 50%;}
}
.list-group-item {
    background: rgba(255,255,255,0.92);
    border-radius: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 4px 16px 0 rgba(31, 38, 135, 0.08);
    transition: transform 0.2s, box-shadow 0.2s;
}
.list-group-item:hover {
    transform: scale(1.01);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
    background: rgba(255,255,255,0.98);
}
.btn-success {
    background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
    border: none;
    font-weight: 600;
    letter-spacing: 1px;
}
.btn-success:hover {
    background: linear-gradient(90deg, #38f9d7 0%, #43e97b 100%);
}
.btn-warning, .btn-danger {
    font-weight: 600;
    letter-spacing: 1px;
}
.pagination .page-item.active .page-link {
    background: linear-gradient(90deg, #e73c7e 0%, #23a6d5 100%);
    border: none;
    color: #fff;
}
.pagination .page-link {
    color: #23a6d5;
    font-weight: 600;
}
</style>

<div class="container mt-4">
    <form method="GET" action="index.php" class="d-flex mb-3">
        <input type="text" name="search" class="form-control me-2" placeholder="Search posts..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <a href="create.php" class="btn btn-success mb-3">Create New Post</a>
    <ul class="list-group mb-3">
        <?php foreach ($posts as $post): ?>
            <li class="list-group-item">
                <h5><?= htmlspecialchars($post['title']) ?></h5>
                <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                <small class="text-muted"><?= htmlspecialchars($post['created_at']) ?></small><br>
                <a href="edit.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="delete.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this post?')">Delete</a>
            </li>
        <?php endforeach; ?>
        <?php if (empty($posts)): ?>
            <li class="list-group-item">No posts found.</li>
        <?php endif; ?>
    </ul>

    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="index.php?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

