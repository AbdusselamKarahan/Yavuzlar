<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    header("Location: edit_users.php");
    exit();
}

if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $role]);
    header("Location: edit_users.php");
    exit();
}

$stmt = $db->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcıları Yönet</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #B2CAEE;
            justify-content: center;
            display: flex;
            align-items: center;
            min height: 100vh;
            margin: 0;
            padding: 0;
            background-image: url("images/yavuzlar_logo1.png");
            background-position: top right;
            background-repeat: no-repeat;
            background-size: 350px;
        }
        .sidebar {
            width: 250px;
            background-color: #F1F4FB;
            color: #000;
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
            color: #000;
            text-align: left;
        }
        .add_user_container {
            display: block;
            color: #000;
            margin-bottom: 5px;
            background: #F1F4FB;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 300px;
            width: 100%;
            }
        input[type="text"], input[type="password"] {
            width: 95%;
            padding: 10px;
            margin-bottom: 0px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .user-table{
            display: block;
            color: #000;
            margin-bottom: 5px;
            background: #F1F4FB;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 300px;
            width: 100%;    
        }
        .rol {
            width: 50%;
            max-width: 300px;
            margin: 10px auto;
            background: #fff;
            padding: 8px;
            border-radius: 8px;
        }
        a {
            color: #fff;
            text-decoration: none;
        }
        h2 {
            color: #000;
        }
        .button {
            background-color: #F1F4FB;
            color: #000;
            border: none;
            margin: 5px;
            padding: 8px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 16px;
        }
        .add-button {
            background-color: #B7C7D6;
            color: #000;
            border: none;
            margin: 5px;
            padding: 8px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 16px;       
        }
        .dlt-button {
            background-color: #B7C7D6;
            color: #000;
            border: none;
            margin: 15px;
            padding: 7px;
            border-radius: 6px;
        }
        .add-button:hover {
            background-color: #929FAB;
            color: #000;
        }
        .button:hover {
            background-color: #B7C7D6;
            color: #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            
        }
        table, th, td {
            border: 1px solid #3A5985;
            border-radius: 3px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            color: #000;

        }
        th, td {
            background-color: #F1F4FB;

        }
        .add_user_container, .user-table {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Paneli</h2>
        <a href="index.php" class="button">Sınavı Başlat</a><br><br>
        <a href="view_questions.php" class="button">Soruları Görüntüle</a><br><br>
        <a href="scoreboard.php" class="button">ScoreBoard</a><br><br>
        <a href="edit_users.php" class="button">Kullanıcıları Yönet</a><br><br>
        <a href="admin_dashboard.php" class="button">Ana Sayfa</a><br><br>
        <a href="index.php" class="button">Çıkış Yap</a>
    </div>
    <div class="content">
        <h1>Kullanıcı Yönetimi</h1>
        <div class="add_user_container">
            <h2>Kullanıcı Ekle</h2>
            <form action="edit_users.php" method="POST">
                <label for="username">Kullanıcı Adı:</label><br><br>
                <input type="text" id="username" name="username" required><br><br>
                <label for="password">Şifre:</label><br><br>
                <input type="password" id="password" name="password" required><br><br>
                <label for="role">Rol:</label>
                <select class="rol" name="role" id="role" required>
                    <option value="student">Student</option>
                    <option value="admin">Admin</option>
                </select><br><br>
                <button type="submit" name="add_user" class="add-button">Kullanıcı Ekle</button>
            </form>
        </div>
        <div class="user-table">
            <h2>Kullanıcıları Görüntüle</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kullanıcı Adı</th>
                        <th>Rol</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td>
                                <a href="edit_users.php?delete_user=<?php echo $user['id']; ?>" class="dlt-button">Sil</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
