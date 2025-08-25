<?php 
include 'config.php';

// Search value
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Sorting setup
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sortOrder = (isset($_GET['order']) && strtolower($_GET['order']) === 'asc') ? 'ASC' : 'DESC';

$allowedColumns = ['id', 'name', 'email', 'phone', 'created_at'];
if (!in_array($sortColumn, $allowedColumns)) {
    $sortColumn = 'id';
}

// Pagination setup
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Total records
$count_sql = "SELECT COUNT(*) as total FROM entries WHERE name LIKE '%$search%' OR email LIKE '%$search%'";
$count_result = $conn->query($count_sql);
$total_entries = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_entries / $limit);

// Final query with dynamic sorting
$sql = "SELECT * FROM entries 
        WHERE name LIKE '%$search%' OR email LIKE '%$search%' 
        ORDER BY $sortColumn $sortOrder 
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Entries - Library System Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        tr:hover {
            background-color: #b0c2d4a0;
        }
        th a {
            color: white;
            text-decoration: none;
        }
        th a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>All Entries</h2>

    <!-- Search -->
    <form method="GET" class="mb-3">
        <input type="text" name="search" class="form-control" placeholder="Search by Name or Email" value="<?= htmlspecialchars($search); ?>">
    </form>

    <!-- Table -->
     <div class="table-responsive">
     <table class="table table-bordered table-striped table-hover" id="entriesTable">
        <thead class="table-dark">
            <tr>
                <?php
                function sortLink($column, $label, $currentSort, $currentOrder, $search) {
                    $newOrder = ($currentSort === $column && $currentOrder === 'ASC') ? 'desc' : 'asc';
                    $arrow = '';
                    if ($currentSort === $column) {
                        $arrow = $currentOrder === 'ASC' ? ' ↑' : ' ↓';
                    }
                    return "<a href=\"?search=" . urlencode($search) . "&sort=$column&order=$newOrder\">$label$arrow</a>";
                }
                ?>
                <th><?= sortLink('id', 'ID', $sortColumn, $sortOrder, $search); ?></th>
                <th><?= sortLink('name', 'Name', $sortColumn, $sortOrder, $search); ?></th>
                <th><?= sortLink('email', 'Email', $sortColumn, $sortOrder, $search); ?></th>
                <th><?= sortLink('phone', 'Phone', $sortColumn, $sortOrder, $search); ?></th>
                <th>Actions</th>
                <th><?= sortLink('created_at', 'Created At', $sortColumn, $sortOrder, $search); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id']; ?></td>
            <td><?= htmlspecialchars($row['name']); ?></td>
            <td><?= htmlspecialchars($row['email']); ?></td>
            <td><?= htmlspecialchars($row['phone']); ?></td>
            <td>
                <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>

                <!-- Button trigger modal -->
                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['id']; ?>">
                    Delete
                </button>

                <!-- Modal -->
                <div class="modal fade" id="deleteModal<?= $row['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $row['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel<?= $row['id']; ?>">Confirm Delete</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this entry: <strong><?= htmlspecialchars($row['name']); ?></strong>?
                            </div>
                            <div class="modal-footer">
                                <a href="delete.php?id=<?= $row['id']; ?>" class="btn btn-danger">Yes, Delete</a>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
            <td><?= date("Y-m-d H:i", strtotime($row['created_at'])); ?></td>
        </tr>

    <?php endwhile; ?>
<?php else: ?>
    <tr><td colspan="6" class="text-center">No entries found</td></tr>
<?php endif; ?>
</table>
<div>
   


    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav>
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?search=<?= urlencode($search); ?>&sort=<?= $sortColumn; ?>&order=<?= strtolower($sortOrder); ?>&page=<?= $page - 1; ?>">Previous</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?search=<?= urlencode($search); ?>&sort=<?= $sortColumn; ?>&order=<?= strtolower($sortOrder); ?>&page=<?= $i; ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?search=<?= urlencode($search); ?>&sort=<?= $sortColumn; ?>&order=<?= strtolower($sortOrder); ?>&page=<?= $page + 1; ?>">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>