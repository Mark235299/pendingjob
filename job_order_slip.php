<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM pending_works WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$work = $stmt->get_result()->fetch_assoc();
if (!$work) { die('Job order not found.'); }

function d($date) {
    if (!$date) return '';
    return date('M d, Y', strtotime($date));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Job Order Slip <?= e($work['job_no']) ?></title>
<link rel="stylesheet" href="assets/style.css">
<style>
    body{background:#eef2f7;color:#111;font-family:Arial,Helvetica,sans-serif;margin:0;padding:20px}
    .slip-page{width:210mm;min-height:297mm;margin:0 auto;background:#fff;padding:18mm;border-radius:10px;box-shadow:0 20px 60px rgba(0,0,0,.15)}
    .slip-actions{width:210mm;margin:0 auto 14px;display:flex;justify-content:flex-end;gap:10px}
    .slip-header{display:flex;align-items:center;justify-content:space-between;border-bottom:3px solid #0b2240;padding-bottom:12px;margin-bottom:16px}
    .slip-brand h1{margin:0;font-size:25px;letter-spacing:.5px;color:#0b2240}.slip-brand p{margin:4px 0 0;color:#555;font-size:13px}.slip-no{text-align:right;border:2px solid #0b2240;padding:10px 14px;border-radius:8px}.slip-no small{display:block;color:#555}.slip-no strong{font-size:20px;color:#0b2240}.slip-title{text-align:center;background:#0b2240;color:#fff;padding:10px;border-radius:8px;font-weight:900;letter-spacing:1px;margin:16px 0}.slip-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}.slip-field{border:1px solid #cfd7e3;border-radius:8px;padding:9px 11px;min-height:54px}.slip-field.full{grid-column:1/-1}.slip-field label{display:block;font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#5c6878;font-weight:800;margin-bottom:5px}.slip-field div{font-size:15px;line-height:1.35;white-space:pre-wrap}.status-box{display:flex;gap:8px;flex-wrap:wrap;margin-top:6px}.check-item{display:flex;align-items:center;gap:7px;border:1px solid #cfd7e3;border-radius:8px;padding:8px 10px}.box{width:14px;height:14px;border:1.8px solid #111;display:inline-grid;place-items:center;font-size:12px;font-weight:bold}.checked .box:after{content:'✓'}.section-label{margin:18px 0 8px;font-size:14px;color:#0b2240;text-transform:uppercase;font-weight:900;border-bottom:1px solid #d7dfe9;padding-bottom:6px}.signature-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-top:32px}.signature{text-align:center;padding-top:28px}.line{border-top:1.7px solid #111;margin-bottom:7px}.signature span{font-size:12px;font-weight:800}.footer-note{margin-top:20px;font-size:11px;color:#666;text-align:center}.btn-print{border:0;border-radius:10px;background:#0b2240;color:white;padding:11px 15px;font-weight:800;cursor:pointer}.btn-back{border:1px solid #b8c3d3;border-radius:10px;background:white;color:#0b2240;padding:11px 15px;font-weight:800;text-decoration:none}
    @page{size:A4;margin:10mm}
    @media print{body{background:white;padding:0}.slip-actions{display:none}.slip-page{width:auto;min-height:auto;margin:0;box-shadow:none;border-radius:0;padding:0}.btn,.sidebar{display:none!important}}
</style>
</head>
<body>
<div class="slip-actions no-print">
    <a class="btn-back" href="index.php">Back</a>
    <button class="btn-print" onclick="window.print()">Print / Save as PDF</button>
</div>

<div class="slip-page">
    <div class="slip-header">
        <div class="slip-brand">
            <h1>JOB ORDER SLIP</h1>
                    </div>
        <div class="slip-no">
            <small>Job Order No.</small>
            <strong><?= e($work['job_no']) ?></strong>
        </div>
    </div>

    <div class="slip-title">WORK REQUEST DETAILS</div>

    <div class="slip-grid">
        <div class="slip-field"><label>Date Requested</label><div><?= e(d($work['work_date'])) ?></div></div>
        <div class="slip-field"><label>Department</label><div><?= e($work['department']) ?></div></div>
        <div class="slip-field"><label>Requestor</label><div><?= e($work['requestor']) ?></div></div>
        <div class="slip-field"><label>Location / Room</label><div><?= e($work['location'] ?? '') ?></div></div>
        <div class="slip-field"><label>Contact No.</label><div><?= e($work['contact_no'] ?? '') ?></div></div>
        <div class="slip-field"><label>Category</label><div><?= e($work['category']) ?></div></div>
        <div class="slip-field"><label>Priority</label><div><?= e($work['priority']) ?></div></div>
        <div class="slip-field"><label>Assigned To</label><div><?= e($work['assigned_to']) ?></div></div>
        <div class="slip-field full"><label>Work Title</label><div><?= e($work['title']) ?></div></div>
        <div class="slip-field full"><label>Description / Problem Reported</label><div><?= e($work['description']) ?></div></div>
    </div>

    <div class="section-label">Status</div>
    <div class="status-box">
        <?php foreach(['Pending','In Progress','Completed','Cancelled'] as $s): ?>
            <div class="check-item <?= $work['status'] === $s ? 'checked' : '' ?>"><span class="box"></span> <?= e($s) ?></div>
        <?php endforeach; ?>
    </div>

    <div class="section-label">Completion Details</div>
    <div class="slip-grid">
        <div class="slip-field"><label>Completed Date</label><div><?= e(d($work['completed_date'])) ?></div></div>
        <div class="slip-field"><label>Printed Date</label><div><?= date('M d, Y h:i A') ?></div></div>
        <div class="slip-field full"><label>Remarks / Action Taken</label><div><?= e($work['remarks']) ?></div></div>
    </div>

    <div class="signature-grid">
        <div class="signature"><div class="line"></div><span>Requested By</span></div>
        <div class="signature"><div class="line"></div><span>Performed By</span></div>
        <div class="signature"><div class="line"></div><span>Verified / Approved By</span></div>
    </div>

  
</div>
</body>
</html>
