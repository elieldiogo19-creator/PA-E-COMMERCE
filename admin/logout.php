<?php
session_start();

unset($_SESSION['admin_id']);
unset($_SESSION['admin_nome']);
unset($_SESSION['admin_email']);

header('Location: /PA-E-COMMERCE/admin/login.php');
exit;