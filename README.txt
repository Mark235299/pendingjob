PENDING WORKS / JOB ORDER SYSTEM

Database name: pending_works_db
Login: admin
Password: admin123

How to run:
1. Install XAMPP.
2. Copy the pending_works_system folder to C:\xampp\htdocs\
3. Start Apache and MySQL in XAMPP.
4. Open http://localhost/phpmyadmin
5. Create database named pending_works_db
6. Import database.sql
7. Open http://localhost/pending_works_system/login.php

Job Order Slip PDF:
- Open the dashboard.
- Click "Slip PDF" beside any job order.
- Click "Print / Save as PDF".
- In the print dialog, choose Save as PDF.

If your database already exists and you only need to add the new fields, run this in phpMyAdmin SQL:
ALTER TABLE pending_works ADD COLUMN location VARCHAR(160) NULL AFTER requestor;
ALTER TABLE pending_works ADD COLUMN contact_no VARCHAR(60) NULL AFTER location;
