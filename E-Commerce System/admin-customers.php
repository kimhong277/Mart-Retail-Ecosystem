<?php
// Ensure this script is only accessed through your database-connected application
if (!isset($conn)) {
    require_once 'config/db.php';
}

// Fetch all registered users, sorted by the newest accounts first
$query = "SELECT user_id, username, email, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($query);
?>
<div class="container-fluid px-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Registered Customers</h2>
        <span class="badge bg-secondary p-2">Total Users: <?php echo $result ? $result->num_rows : 0; ?></span>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3">User ID</th>
                            <th>Username</th>
                            <th>Email Address</th>
                            <th>Joined Date</th>
                            <th class="text-center pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($customer = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="fw-bold ps-3">#<?php echo $customer['user_id']; ?></td>
                                    <td class="fw-semibold text-dark"><?php echo htmlspecialchars($customer['username']); ?></td>
                                    <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                    <td>
                                        <!-- Formats the database timestamp neatly (e.g., Jul 13, 2026) -->
                                        <?php echo date('M d, Y', strtotime($customer['created_at'])); ?>
                                    </td>
                                    <td class="text-center pe-3">
                                        <!-- Action to view this user's specific history if needed -->
                                        <a href="index.php?page=customer-history&id=<?php echo $customer['user_id']; ?>" class="btn btn-outline-primary btn-sm">
                                            View History
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    👥 No customers have registered an account yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>