<?php
/**
 * One-time helper script: run this once in the browser
 * (e.g. http://localhost/cosmetics-store/admin/seed_admin.php)
 * to (re)create the default admin account with a correctly
 * hashed password, since password_hash() output is random per
 * machine and can't be hard-coded reliably in the .sql seed file.
 *
 * Default login after running this script:
 *   Username: admin
 *   Password: admin123
 *
 * Delete this file after first use in a production deployment.
 */
require_once __DIR__ . '/../config/db.php';

$pdo = getDBConnection();
$hash = password_hash('admin123', PASSWORD_DEFAULT);

$stmt = $pdo->prepare("SELECT id FROM admins WHERE username = 'admin'");
$stmt->execute();
$existing = $stmt->fetch();

if ($existing) {
    $upd = $pdo->prepare("UPDATE admins SET password = :hash WHERE username = 'admin'");
    $upd->execute([':hash' => $hash]);
    echo "Admin account updated. Username: admin / Password: admin123";
} else {
    $ins = $pdo->prepare("INSERT INTO admins (username, password, full_name) VALUES ('admin', :hash, 'Store Administrator')");
    $ins->execute([':hash' => $hash]);
    echo "Admin account created. Username: admin / Password: admin123";
}
