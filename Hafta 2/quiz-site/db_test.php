<?php
$db_file = 'db/quiz.db';

try {
    $db = new SQLite3($db_file);
    echo "Veritabanı bağlantısı başarılı!";
} catch (Exception $e) {
    echo "Veritabanı bağlantı hatası: " . $e->getMessage();
}
?>
