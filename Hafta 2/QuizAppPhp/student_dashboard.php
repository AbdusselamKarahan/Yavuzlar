<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT username FROM users WHERE id = ?");

$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Öğrenci';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğrenci Paneli</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #B2CAEE;
            margin: 0;
            padding: 0;
            background-image: url('images/yavuzlar_logo1.png');
            background-position: top right;
            background-repeat: no-repeat;
            background-size: 350px;
        }
        .sidebar {
            width: 250px;
            background-color: #F1F4FB;
            color: #fff;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px;
            border-radius: 6px;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        a {
            color: #000;
            text-decoration: none;
        }
        h1 {
            color: #F1F4FB;
        }
        h2 {
            color: #000;
        }
        .button {
            background-color: #F1F4FB;
            color: #6c6e6b;
            border: none;
            margin: 5px;
            padding: 8px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 16px;
        }
        .button:hover {
            background-color: #B7C7D6;
            color: #000;
        }
        .content{
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Öğrenci Paneli</h2>
        <a href="start_quiz.php" class="button">Sınav Başlat</a><br><br>
        <a href="scoreboard.php" class="button">ScoreBoard</a><br><br>
        <a href="index.php" class="button">Çıkış Yap</a><br><br>
    </div>
    <div class="content">
        <h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hoş geldiniz, <?php echo $username; ?>!</h1>
    </div>
</body>
</html>
