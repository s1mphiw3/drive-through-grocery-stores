<?php
@include 'config.php';
session_start();

$vid = $_SESSION['vendor_id'];

$sql = $conn->prepare("SELECT * FROM orders WHERE vendor_id = ? AND status = ?");
$sql -> execute ([$vid,'unread']);

echo $sql->rowCount();

?>