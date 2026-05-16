<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

$error = '';
$jobNo = nextJobNo($conn);

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

    $stmt = $conn->prepare("INSERT INTO pending_works (job_no, work_date, department, requestor, location, contact_no, category, title, description, priority, assigned_to, status, remarks, completed_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssssssssssss', $jobNo, $workDate, $department, $requestor, $location, $contactNo, $category, $title, $description, $priority, $assignedTo, $status, $remarks, $completedDate);
    if ($stmt->execute()) { header('Location: index.php'); exit; }
    $error = 'Failed to save. Job No. may already exist.';
}
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Add Work</title><link rel="stylesheet" href="assets/style.css"></head>
<body><div class="app"><?php include 'sidebar.php'; ?><main class="main"><header class="topbar"><div><h1>Add Job Order</h1><p>Create new pending work or job order.</p></div><a href="index.php" class="btn ghost">Back</a></header>
<section class="form-card"><?php if($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<form method="POST" class="work-form">
    <div><label>Job No.</label><input name="job_no" value="<?= e($jobNo) ?>" required></div>
    <div><label>Work Date</label><input type="date" name="work_date" value="<?= date('Y-m-d') ?>" required></div>
    <div><label>Department</label><input name="department" placeholder="MIS / FO / FNB / ENGR" required></div>
    <div><label>Requestor</label><input name="requestor" placeholder="Requester name" required></div>
    <div><label>Location / Room</label><input name="location" placeholder="Room 101 / Office / Area"></div>
    <div><label>Contact No.</label><input name="contact_no" placeholder="Local / mobile number"></div>
    <div><label>Category</label><input name="category" list="categoryList" placeholder="TV / Internet / CCTV / Project" required>
        <datalist id="categoryList"><option>TV</option><option>Internet Connection</option><option>Lockset</option><option>CCTV</option><option>DigiBox</option><option>Opera</option><option>Projects</option><option>Printer</option><option>Computer</option><option>Telephone</option></datalist></div>
    <div><label>Priority</label><select name="priority"><option>Low</option><option selected>Medium</option><option>High</option><option>Urgent</option></select></div>
    <div class="full"><label>Title</label><input name="title" required placeholder="Short work title"></div>
    <div class="full"><label>Description</label><textarea name="description" rows="4" placeholder="Describe the issue or work needed"></textarea></div>
    <div><label>Assigned To</label><input name="assigned_to" placeholder="Technician / Staff"></div>
    <div><label>Status</label><select name="status"><option>Pending</option><option>In Progress</option><option>Completed</option><option>Cancelled</option></select></div>
    <div><label>Completed Date</label><input type="date" name="completed_date"></div>
    <div class="full"><label>Remarks</label><textarea name="remarks" rows="3"></textarea></div>
    <div class="full form-actions"><button class="btn primary">Save Job Order</button><a href="index.php" class="btn ghost">Cancel</a></div>
</form></section></main></div></body></html>
