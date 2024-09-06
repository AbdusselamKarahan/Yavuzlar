<?php
session_start();
include('db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = 'student'; 

    try {
        $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, $role]);
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $error = "Kullanıcı adı zaten var!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #B2CAEE;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-image: url('images/yavuzlar_logo1.png');
            background-position: top;
            background-repeat: no-repeat;
            background-size: 350px;
        }
        .container {
            background: #F1F4FB;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        h2 {
            margin-top: 0;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="password"] {
            width: 95%;
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
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Kayıt Ol</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <label for="username">Kullanıcı Adı</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Şifre</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Kayıt Ol</button>
        </form>
    </div>
</body>
</html>
