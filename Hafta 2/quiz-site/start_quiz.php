<?php
session_start();
include('db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['difficulty'])) {
    $difficulty = $_POST['difficulty'];

    $stmt = $db->prepare("SELECT id, question, option1, option2, option3, option4 FROM questions WHERE difficulty = ?");
    $stmt->execute([$difficulty]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $_SESSION['questions'] = $questions;
    $_SESSION['current_question'] = 0;
    $_SESSION['answered_questions'] = [];

    header("Location: quiz.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sınav Başlat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #B2CAEE;
            display: flex;
            justify-content: center;
            align-items: center;
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
        }
        h1 {
            text-align: center;
            color: #000000;
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        select, button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #3A5985;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #B7C7D6;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sınav Başlat</h1>
        <form action="start_quiz.php" method="POST">
            <label for="difficulty">Zorluk Seviyesi:</label>
            <select id="difficulty" name="difficulty" required>
                <option value="Kolay">Kolay</option>
                <option value="Orta">Orta</option>
                <option value="Zor">Zor</option>
            </select>
            <button type="submit">Sınavı Başlat</button>
        </form>
    </div>
</body>
</html>
