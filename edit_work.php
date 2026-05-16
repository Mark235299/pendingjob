<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM pending_works WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$work = $stmt->get_result()->fetch_assoc();
if (!$work) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobNo = trim($_POST['job_no']);
    $workDate = $_POST['work_date'];
    $department = trim($_POST['department']);
    $requestor = trim($_POST['requestor']);
    $location = trim($_POST['location'] ?? '');
    $contactNo = trim($_POST['contact_no'] ?? '');
    $category = trim($_POST['category']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $priority = $_POST['priority'];
    $assignedTo = trim($_POST['assigned_to']);
    $status = $_POST['status'];
    $remarks = trim($_POST['remarks']);
    $completedDate = $status === 'Completed' ? ($_POST['completed_date'] ?: date('Y-m-d')) : null;

    $stmt = $conn->prepare("UPDATE pending_works SET job_no=?, work_date=?, department=?, requestor=?, location=?, contact_no=?, category=?, title=?, description=?, priority=?, assigned_to=?, status=?, remarks=?, completed_date=? WHERE id=?");
    $stmt->bind_param('ssssssssssssssi', $jobNo, $workDate, $department, $requestor, $location, $contactNo, $category, $title, $description, $priority, $assignedTo, $status, $remarks, $completedDate, $id);
    if ($stmt->execute()) { header('Location: index.php'); exit; }
    $error = 'Failed to update record.';
}
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Edit Work</title><link rel="stylesheet" href="assets/style.css"></head>
<body><div class="app"><?php include 'sidebar.php'; ?><main class="main"><header class="topbar"><div><h1>Edit Job Order</h1><p>Update pending work details and status.</p></div><a href="index.php" class="btn ghost">Back</a></header>
<section class="form-card"><?php if($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<form method="POST" class="work-form">
    <div><label>Job No.</label><input name="job_no" value="<?= e($work['job_no']) ?>" required></div>
    <div><label>Work Date</label><input type="date" name="work_date" value="<?= e($work['work_date']) ?>" required></div>
    <div><label>Department</label><input name="department" value="<?= e($work['department']) ?>" required></div>
    <div><label>Requestor</label><input name="requestor" value="<?= e($work['requestor']) ?>" required></div>
    <div><label>Location / Room</label><input name="location" value="<?= e($work['location'] ?? '') ?>"></div>
    <div><label>Contact No.</label><input name="contact_no" value="<?= e($work['contact_no'] ?? '') ?>"></div>
    <div><label>Category</label><input name="category" list="categoryList" value="<?= e($work['category']) ?>" required>
        <datalist id="categoryList"><option>TV</option><option>Internet Connection</option><option>Lockset</option><option>CCTV</option><option>DigiBox</option><option>Opera</option><option>Projects</option><option>Printer</option><option>Computer</option><option>Telephone</option></datalist></div>
    <div><label>Priority</label><select name="priority"><?php foreach(['Low','Medium','High','Urgent'] as $p): ?><option <?= $work['priority']===$p?'selected':'' ?>><?= $p ?></option><?php endforeach; ?></select></div>
    <div class="full"><label>Title</label><input name="title" value="<?= e($work['title']) ?>" required></div>
    <div class="full"><label>Description</label><textarea name="description" rows="4"><?= e($work['description']) ?></textarea></div>
    <div><label>Assigned To</label><input name="assigned_to" value="<?= e($work['assigned_to']) ?>"></div>
    <div><label>Status</label><select name="status"><?php foreach(['Pending','In Progress','Completed','Cancelled'] as $s): ?><option <?= $work['status']===$s?'selected':'' ?>><?= $s ?></option><?php endforeach; ?></select></div>
    <div><label>Completed Date</label><input type="date" name="completed_date" value="<?= e($work['completed_date']) ?>"></div>
    <div class="full"><label>Remarks</label><textarea name="remarks" rows="3"><?= e($work['remarks']) ?></textarea></div>
    <div class="full form-actions"><button class="btn primary">Update Job Order</button><a href="index.php" class="btn ghost">Cancel</a></div>
</form></section></main></div></body></html>
