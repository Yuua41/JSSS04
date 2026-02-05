<?php
session_start();
$_SESSION = array(); // セッション変数を空にする
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/'); // クッキーの破棄
}
session_destroy(); // セッションの破棄
header('Location: score_login.php'); // ログイン画面へ移動
exit();