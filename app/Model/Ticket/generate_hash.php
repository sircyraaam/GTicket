<?php
$password = 'ITT3am2025@';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
// $hashedPassword = '$2y$10$t3Svj6WSSxgvyI0h.ox2aObJIspR7F4T.qVUVutGhdCVQNh60QFMK';
echo $hashedPassword;
?>