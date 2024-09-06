<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$questions = isset($_SESSION['questions']) ? $_SESSION['questions'] : [];
$answered_questions = isset($_SESSION['answered_questions']) ? $_SESSION['answered_questions'] : [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question_id = $_POST['question_id'];
    $answer = $_POST['answer'];

    $stmt = $db->prepare("SELECT correct_option FROM questions WHERE id = ?");
    $stmt->execute([$question_id]);
    $correct_option = $stmt->fetchColumn();
    
    $is_correct = ($answer == $correct_option) ? 1 : 0;

    $stmt = $db->prepare("INSERT INTO answers (user_id, question_id, answer, is_correct) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $question_id, $answer, $is_correct]);

    $_SESSION['answered_questions'][$question_id] = $answer;
}

$stmt = $db->prepare("SELECT COUNT(*) FROM answers WHERE user_id = ? AND is_correct = 1");
$stmt->execute([$user_id]);
$score = $stmt->fetchColumn();

unset($_SESSION['questions']);
unset($_SESSION['current_question']);
unset($_SESSION['answered_questions']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sınav Sonucu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #B2CAEE;
            justify-content: center;
            align-items: center;
            display: flex;
            height: 100vh;
            margin: 0;
            padding: 0;
            background-image: url('images/yavuzlar_logo1.png');
            background-position: top right;
            background-repeat: no-repeat;
            background-size: 350px;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background: #F1F4FB;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #000;
        }
        p {
            font-size: 18px;
            color: #333;
        }
        a {
            background-color: #3A5985 ;
            display: inline-block;
            margin-top: 20px;
            font-size: 16px;
            color: #fff;
            text-decoration: none;
            border: 1px solid #F1F4FB;
            padding: 10px 20px;
            border-radius: 4px;
        }
        a:hover {
            background-color: #B7C7D6;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sınav Tamamlandı!</h1>
        <p>Puanınız: <?php echo $score; ?></p>
        <a href="student_dashboard.php">Geri Dön</a>
    </div>
</body>
</html>
