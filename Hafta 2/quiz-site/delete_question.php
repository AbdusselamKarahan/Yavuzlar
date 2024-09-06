<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: view_questions.php");
    exit();
}
$question_id = $_GET['id'];

$stmt = $db->prepare("DELETE FROM questions WHERE id = ?");
$stmt->execute([$question_id]);

header("Location: view_questions.php");
exit();
?>
