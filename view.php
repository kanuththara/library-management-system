<?php
include 'config.php';

// Get search term
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_escaped = "%$search%";

// Pagination setup
$limit = 5; // Entries per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total matching records
$count_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM entries WHERE name LIKE ? OR email LIKE ?");
$count_stmt->bind_param("ss", $search_escaped, $search_escaped);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_row = $count_result->fetch_assoc();
$total_entries = $total_row['total'];
$total_pages = ceil($total_entries / $limit);

// Fetch data for current page
$stmt = $conn->prepare("SELECT * FROM entries WHERE name LIKE ? OR email LIKE ? ORDER BY id DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ssii", $search_escaped, $search_escaped, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Entries</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2 class="mb-4">All Entries</h2>

  <!-- Search Form -->
  <form method="GET" class="mb-3">
    <input type="text" name="search" class="form-control" placeholder="Search by Name or Email"
           value="<?php echo htmlspecialchars($search); ?>">
  </form>

  <!-- Entries Table -->
  <table class="table table-bordered table-striped table-hover">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Created At</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['id']); ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['phone']); ?></td>
            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="5" class="text-center">No entries found</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Pagination -->
  <?php if ($total_pages > 1): ?>
    <nav>
      <ul class="pagination justify-content-center">
        <!-- Previous Button -->
        <?php if ($page > 1): ?>
          <li class="page-item">
            <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>">Previous</a>
          </li>
        <?php endif; ?>

        <!-- Page Numbers -->
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
            <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>">
              <?php echo $i; ?>
            </a>
          </li>
        <?php endfor; ?>

        <!-- Next Button -->
        <?php if ($page < $total_pages): ?>
          <li class="page-item">
            <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>">Next</a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
  <?php endif; ?>
</div>
</body>
</html>