<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['questions']) || empty($_SESSION['questions'])) {
    echo "<p>Henüz sınav başlamadı veya sorular yüklenmedi.</p>";
    echo "<a href='index.php'>Sınavı Başlat</a>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['question_id']) && isset($_POST['answer'])) {
        $question_id = $_POST['question_id'];
        $answer = $_POST['answer'];
        $current_question_index = $_SESSION['current_question'];

        $stmt = $db->prepare("SELECT correct_option FROM questions WHERE id = ?");
        $stmt->execute([$question_id]);
        $correct_option = $stmt->fetchColumn();
        
        $is_correct = ($answer == $correct_option) ? 1 : 0;

        $stmt = $db->prepare("INSERT INTO answers (user_id, question_id, answer, is_correct) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $question_id, $answer, $is_correct]);

        $_SESSION['answered_questions'][$question_id] = $answer;
    }

    $_SESSION['current_question'] += 1;
    $current_question_index = $_SESSION['current_question'];

    if ($current_question_index >= count($_SESSION['questions'])) {
        header("Location: submit_quiz.php");
        exit();
    } else {
        header("Location: quiz.php");
        exit();
    }
}

$questions = $_SESSION['questions'];
$current_question_index = $_SESSION['current_question'];
$question = $questions[$current_question_index];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sınav</title>
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
            max-width: 800px;
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
            margin-bottom: 5px;
        }
        p {
            font-weight: bold;
        }

        input[type="radio"] {
            margin-right: 10px;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #3A5985;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
            margin-top: 20px;
        }
        button:hover {
            background-color: #B7C7D6;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="quiz.php" method="POST">
            <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">  
            <h1>Sınav</h1>
            <p><?php echo htmlspecialchars($question['question']); ?></p>
            <input type="radio" name="answer" value="1" required> <?php echo htmlspecialchars($question['option1']); ?><br>
            <input type="radio" name="answer" value="2"> <?php echo htmlspecialchars($question['option2']); ?><br>
            <input type="radio" name="answer" value="3"> <?php echo htmlspecialchars($question['option3']); ?><br>
            <input type="radio" name="answer" value="4"> <?php echo htmlspecialchars($question['option4']); ?><br>         
            <button type="submit">Sonraki Soru</button>
        </form>
    </div>
</body>
</html>
