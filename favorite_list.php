<?php
session_start(); // 追加
include('functions.php');
check_session_id(); // 追加
$pdo = connect_to_db();
$user_id = $_SESSION['user_id']; // セッションから取得

// ノンブル補正
$page_offsets = [
    'RealBk3' => 5, 'RealBk2' => 7, 'RealBk1' => 13, 
    'NewReal1' => 15, 'NewReal2' => 12, 'NewReal3' => 10,
    'Library' => 4, 'JazzFake' => -1, 'JazzLTD' => 7,
    'EvansBk' => 3, 'Colorado' => 3,  
];

// 修正：ログイン中のユーザー(user_id)のデータのみ取得
$sql = "SELECT s.*, f.created_at AS fav_date 
        FROM favorite_table AS f 
        JOIN score_list AS s ON f.score_id = s.id 
        WHERE f.user_id = :user_id 
        ORDER BY f.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT); // バインド
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$output = "";
foreach ($result as $record) {
    $book_name = $record["book"];
    $pdf_page = intval($record["page"]) + (isset($page_offsets[$book_name]) ? $page_offsets[$book_name] : 0);
    $pdf_url = "view_pdf.php?book=" . rawurlencode($book_name) . "#page={$pdf_page}";
        // 一度開くためのphpを経由する

    $output .= "
      <tr id='row-{$record["id"]}'>
        <td>" . htmlspecialchars($record["song_title"]) . "</td>
        <td>" . htmlspecialchars($record["book"]) . "</td>
        <td><a href='{$pdf_url}' target='_blank' class='open-btn'>Open PDF</a></td>
        <td><button class='del-btn' data-id='{$record["id"]}'>削除</button></td>
      </tr>";
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>私のお気に入り</title>
  <style>
    body { font-family: sans-serif; margin: 20px; }
    .header-area { display: flex; justify-content: space-between; align-items: center; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 12px; }
    th { background: #f39c12; color: white; }
    .del-btn { background: #e74c3c; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
    .logout-btn { background: #95a5a6; color: white; text-decoration: none; padding: 5px 10px; border-radius: 4px; }
  </style>
</head>
<body>
  <div class="header-area">
    <a href="score_read.php">← 検索画面へ戻る</a>
    <a href="score_logout.php" class="logout-btn">ログアウト</a>
  </div>
  <h1>お気に入り (<?= htmlspecialchars($_SESSION['username']) ?>)</h1>

  <table>
    <thead><tr><th>Title</th><th>Book</th><th>Link</th><th></th></tr></thead>
    <tbody><?= $output ?: "<tr><td colspan='4' style='text-align:center;'>お気に入りは空です。</td></tr>" ?></tbody>
  </table>

  <script>
    // 削除処理のJS（非同期）
    document.querySelectorAll('.del-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        if (confirm('お気に入りから削除しますか？')) {
          fetch(`fav_create.php?score_id=${id}`)
            .then(res => res.json())
            .then(data => {
              if (data.action === 'removed') {
                document.getElementById(`row-${id}`).remove();
              }
            });
        }
      });
    });
  </script>
</body>
</html>