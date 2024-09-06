<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = $_POST['question'];
    $option1 = $_POST['option1'];
    $option2 = $_POST['option2'];
    $option3 = $_POST['option3'];
    $option4 = $_POST['option4'];
    $correct_option = $_POST['correct_option'];
    $difficulty = $_POST['difficulty'];

    try {
        $stmt = $db->prepare("INSERT INTO questions (question, option1, option2, option3, option4, correct_option, difficulty) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$question, $option1, $option2, $option3, $option4, $correct_option, $difficulty]);
        $success = "Soru başarıyla eklendi!";
    } catch (PDOException $e) {
        $error = "Soru eklenirken bir hata oluştu: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soru Ekle</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #B2CAEE;     
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            background-image: url('images/yavuzlar_logo1.png');
            background-position: top right;
            background-repeat: no-repeat;
            background-size: 350px;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            background: #F1F4FB;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #000;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], select {
            width: 97%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #3A5985;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
        }
        button:hover {
            background-color: #B7C7D6;
            color: #000;
        }
        .error, .success {
            color: red;
            font-weight: bold;
        }
        .success {
            color: green;
        }
        a {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #000;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Soru Ekle</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <form action="add_question.php" method="POST">
            <label for="question">Soru:</label>
            <input type="text" id="question" name="question" required>

            <label for="option1">Seçenek 1:</label>
            <input type="text" id="option1" name="option1" required>

            <label for="option2">Seçenek 2:</label>
            <input type="text" id="option2" name="option2" required>

            <label for="option3">Seçenek 3:</label>
            <input type="text" id="option3" name="option3" required>

            <label for="option4">Seçenek 4:</label>
            <input type="text" id="option4" name="option4" required>

            <label for="correct_option">Doğru Seçenek (1-4):</label>
            <select id="correct_option" name="correct_option" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>

            <label for="difficulty">Zorluk Seviyesi:</label>
            <select id="difficulty" name="difficulty" required>
                <option value="Kolay">Kolay</option>
                <option value="Orta">Orta</option>
                <option value="Zor">Zor</option>
            </select>
            <button type="submit">Soru Ekle</button>
            <a href="view_questions.php" class="button">Geri Dön</a>
        </form>
    </div>
</body>
</html>
