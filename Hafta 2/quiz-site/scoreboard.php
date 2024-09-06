<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$current_user = $stmt->fetchColumn();

$stmt = $db->query("
    SELECT u.username, SUM(a.is_correct) as score 
    FROM answers a 
    JOIN users u ON a.user_id = u.id 
    GROUP BY a.user_id 
    ORDER BY score DESC
");
$scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skor Tablosu</title>
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
        .highlight {
            background-color: #B7C7D6;
            color: #000;
            font-weight: bold;
        }
        a {
            background-color: #3A5985; 
            display: flex;
            justify-content: center;
            text-align: center;
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
        <h1>Skor Tablosu</h1>
        <table>
            <thead>
                <tr>
                    <th>Öğrenci Adı</th>
                    <th>Skor</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($scores as $score): ?>
                    <tr class="<?php echo $score['username'] === $current_user ? 'highlight' : ''; ?>">
                        <td><?php echo htmlspecialchars($score['username']); ?></td>
                        <td><?php echo htmlspecialchars($score['score']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="student_dashboard.php">Ana Sayfaya Dön</a>
    </div>
</body>
</html>
