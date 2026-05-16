<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

$statusFilter = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');

$where = [];
$params = [];
$types = '';

if ($statusFilter !== '') {
    $where[] = 'status = ?';
    $params[] = $statusFilter;
    $types .= 's';
}
if ($search !== '') {
    $where[] = '(job_no LIKE ? OR department LIKE ? OR requestor LIKE ? OR category LIKE ? OR title LIKE ? OR assigned_to LIKE ?)';
    $like = "%$search%";
    for ($i = 0; $i < 6; $i++) { $params[] = $like; $types .= 's'; }
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT * FROM pending_works $whereSql ORDER BY FIELD(status,'Pending','In Progress','Completed','Cancelled'), work_date DESC, id DESC";
$stmt = $conn->prepare($sql);
if ($params) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$works = $stmt->get_result();

$stats = [];
$res = $conn->query("SELECT status, COUNT(*) total FROM pending_works GROUP BY status");
while ($row = $res->fetch_assoc()) { $stats[$row['status']] = (int)$row['total']; }
$total = array_sum($stats);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Pending Works</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="app">
    <?php include 'sidebar.php'; ?>
    <main class="main">
        <header class="topbar">
            <div>
                <h1>Pending Works Dashboard</h1>
                <p>Monitor job orders, pending tasks, and completed works.</p>
            </div>
            <div class="top-actions">
                <button onclick="window.print()" class="btn ghost">Print / Save PDF</button>
                <a href="add_work.php" class="btn primary">+ Add Job Order</a>
            </div>
        </header>

        <section class="stats-grid">
            <div class="stat-card"><span>Total Works</span><strong><?= $total ?></strong></div>
            <div class="stat-card"><span>Pending</span><strong><?= $stats['Pending'] ?? 0 ?></strong></div>
            <div class="stat-card"><span>In Progress</span><strong><?= $stats['In Progress'] ?? 0 ?></strong></div>
            <div class="stat-card"><span>Completed</span><strong><?= $stats['Completed'] ?? 0 ?></strong></div>
        </section>

        <section class="panel">
            <div class="panel-head">
                <h2>Work Log</h2>
                <form class="filters" method="GET">
                    <input name="search" value="<?= e($search) ?>" placeholder="Search job, department, category...">
                    <select name="status">
                        <option value="">All Status</option>
                        <?php foreach(['Pending','In Progress','Completed','Cancelled'] as $s): ?>
                            <option value="<?= $s ?>" <?= $statusFilter === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn small">Filter</button>
                </form>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Job No.</th><th>Work Date</th><th>Department</th><th>Category</th><th>Title</th><th>Priority</th><th>Assigned</th><th>Status</th><th class="no-print">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($works->num_rows === 0): ?>
                        <tr><td colspan="9" class="empty">No records found.</td></tr>
                    <?php endif; ?>
                    <?php while($work = $works->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?= e($work['job_no']) ?></strong></td>
                            <td><?= e(date('M d, Y', strtotime($work['work_date']))) ?></td>
                            <td><?= e($work['department']) ?></td>
                            <td><?= e($work['category']) ?></td>
                            <td>
                                <b><?= e($work['title']) ?></b>
                                <small><?= e($work['description']) ?></small>
                            </td>
                            <td><span class="<?= priorityClass($work['priority']) ?>"><?= e($work['priority']) ?></span></td>
                            <td><?= e($work['assigned_to']) ?></td>
                            <td><span class="<?= badgeClass($work['status']) ?>"><?= e($work['status']) ?></span></td>
                            <td class="actions no-print">
                                <a href="job_order_slip.php?id=<?= $work['id'] ?>" target="_blank">Slip PDF</a>
                                <a href="edit_work.php?id=<?= $work['id'] ?>">Edit</a>
                                <a href="delete_work.php?id=<?= $work['id'] ?>" onclick="return confirm('Delete this work?')" class="danger">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
</body>
</html>
