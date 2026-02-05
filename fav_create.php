<?php
session_start();
include('functions.php');
check_session_id(); // 認証チェック

$pdo = connect_to_db();
$score_id = $_GET['score_id'];
$user_id = $_SESSION['user_id']; // セッションから取得

// ログイン中のユーザーが既に登録済みか確認
$sql = "SELECT COUNT(*) FROM favorite_table WHERE score_id = :score_id AND user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':score_id', $score_id, PDO::PARAM_INT);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$count = $stmt->fetchColumn();

if ($count > 0) {
    $sql = "DELETE FROM favorite_table WHERE score_id = :score_id AND user_id = :user_id";
    $action = 'removed';
} else {
    $sql = "INSERT INTO favorite_table (score_id, user_id, created_at) VALUES (:score_id, :user_id, sysdate())";
    $action = 'added';
}

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':score_id', $score_id, PDO::PARAM_INT);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

try {
    $stmt->execute();
    echo json_encode(['status' => 'success', 'action' => $action]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
exit();