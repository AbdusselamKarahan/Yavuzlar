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

$stmt = $db->prepare("SELECT * FROM questions WHERE id = ?");
$stmt->execute([$question_id]);
$question = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question_text = $_POST['question'];
    $option1 = $_POST['option1'];
    $option2 = $_POST['option2'];
    $option3 = $_POST['option3'];
    $option4 = $_POST['option4'];
    $correct_option = $_POST['correct_option'];
    $difficulty = $_POST['difficulty'];

    $stmt = $db->prepare("UPDATE questions SET question = ?, option1 = ?, option2 = ?, option3 = ?, option4 = ?, correct_option = ?, difficulty = ? WHERE id = ?");
    $stmt->execute([$question_text, $option1, $option2, $option3, $option4, $correct_option, $difficulty, $question_id]);

    header("Location: view_questions.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soru Düzenle</title>
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
            margin-bottom: 10px;
            font-weight: bold;
        }
        input[type="text"], input[type="number"], select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            background-color: #3A5985;
            color: #fff;
            cursor: pointer;
        }
        button:hover {
            background-color: #929FAB;
            color: #000;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Soru Düzenle</h1>
        <form action="edit_question.php?id=<?php echo $question_id; ?>" method="POST">
            <label for="question">Soru:</label>
            <input type="text" id="question" name="question" value="<?php echo htmlspecialchars($question['question']); ?>" required>

            <label for="option1">Seçenek 1:</label>
            <input type="text" id="option1" name="option1" value="<?php echo htmlspecialchars($question['option1']); ?>" required>

            <label for="option2">Seçenek 2:</label>
            <input type="text" id="option2" name="option2" value="<?php echo htmlspecialchars($question['option2']); ?>" required>

            <label for="option3">Seçenek 3:</label>
            <input type="text" id="option3" name="option3" value="<?php echo htmlspecialchars($question['option3']); ?>" required>

            <label for="option4">Seçenek 4:</label>
            <input type="text" id="option4" name="option4" value="<?php echo htmlspecialchars($question['option4']); ?>" required>

            <label for="correct_option">Doğru Seçenek:</label>
            <input type="number" id="correct_option" name="correct_option" min="1" max="4" value="<?php echo htmlspecialchars($question['correct_option']); ?>" required>

            <label for="difficulty">Zorluk:</label>
            <select id="difficulty" name="difficulty" required>
                <option value="easy" <?php echo $question['difficulty'] == 'easy' ? 'selected' : ''; ?>>Kolay</option>
                <option value="medium" <?php echo $question['difficulty'] == 'medium' ? 'selected' : ''; ?>>Orta</option>
                <option value="hard" <?php echo $question['difficulty'] == 'hard' ? 'selected' : ''; ?>>Zor</option>
            </select>

            <button type="submit">Güncelle</button>
        </form>
        <a href="view_questions.php">Geri Dön</a>
    </div>
</body>
</html>
