<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$stmt = $db->query("SELECT id, question, option1, option2, option3, option4, correct_option, difficulty FROM questions");
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soruları Görüntüle ve Düzenle</title>
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
            margin: 0 auto;
            background: #F1F4FB;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #F1F4FB;
        }
        .button {
            display: inline-block;
            padding: 8px 16px;
            margin: 4px;
            border-radius: 4px;
            text-decoration: none;
            color: #fff;
            background-color: #3A5985;
        }
        .button1 {
            display: inline-block;
            padding: 8px 16px;
            margin: 4px;
            border-radius: 4px;
            text-decoration: none;
            color: #fff;
            background-color: #3A5985;
        }
        .button:hover {
            background-color: #B7C7D6;
            color: #000;
        }
        .button1:hover {
            background-color: #B7C7D6;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Soruları Görüntüle ve Düzenle</h1>
        <a href="add_question.php" class="button">Yeni Soru Ekle</a>
        <a href="admin_dashboard.php" class="button">Geri Dön</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Soru</th>
                    <th>Seçenekler</th>
                    <th>Doğru Seçenek</th>
                    <th>Zorluk</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($questions as $question): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($question['id']); ?></td>
                        <td><?php echo htmlspecialchars($question['question']); ?></td>
                        <td>
                            1: <?php echo htmlspecialchars($question['option1']); ?><br>
                            2: <?php echo htmlspecialchars($question['option2']); ?><br>
                            3: <?php echo htmlspecialchars($question['option3']); ?><br>
                            4: <?php echo htmlspecialchars($question['option4']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($question['correct_option']); ?></td>
                        <td><?php echo htmlspecialchars($question['difficulty']); ?></td>
                        <td>
                            <a href="edit_question.php?id=<?php echo $question['id']; ?>" class="button1">Düzenle</a>
                            <a href="delete_question.php?id=<?php echo $question['id']; ?>" class="button1">Sil</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
