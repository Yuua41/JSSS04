<?php

// 本当はサーバに接続する
function connect_to_db() {
    $dbn = 'mysql:dbname=gs_php_db;charset=utf8mb4;port=3306;host=localhost';
    $user = 'root';
    $pwd = '';
    try {
        return new PDO($dbn, $user, $pwd);
    } catch (PDOException $e) {
        exit(json_encode(["db error" => "{$e->getMessage()}"]));
    }
}

// ログイン状態のチェック関数
function check_session_id() {
  if (!isset($_SESSION["session_id"]) || $_SESSION["session_id"] !== session_id()) {
    header("Location: score_login.php");
    exit();
  } else {
    session_regenerate_id(true);
    $_SESSION["session_id"] = session_id();
  }
}

