<?php
session_start();
include('functions.php');
check_session_id();

$pdo = connect_to_db();
$user_id = $_SESSION['user_id'];

// ノンブル補正
$page_offsets = [
    'RealBk3'  => 5, 'RealBk2'  => 7, 'RealBk1'  => 13, 
    'NewReal1' => 15, 'NewReal2' => 12, 'NewReal3' => 10,
    'Library'  => 4, 'JazzFake' => -1, 'JazzLTD'  => 7,
    'EvansBk'  => 3, 'Colorado' => 3,  
];

$search_word = isset($_GET['search']) ? $_GET['search'] : '';
$result = [];
$output = "";

if ($search_word !== '') {
    $search_word_kanized = mb_convert_kana($search_word, 's');
    $search_words = preg_split('/\s+/', $search_word_kanized, -1, PREG_SPLIT_NO_EMPTY);
    
    $where_clauses = [];
    foreach ($search_words as $index => $word) {
        $where_clauses[] = "UPPER(s.song_title) LIKE UPPER(:word{$index})";
    }

    // favorite_tableを結合して★の状態を判定，ログインIDで結合する
    $sql = "SELECT s.*, f.id AS fav_id 
        FROM score_list AS s 
        LEFT JOIN favorite_table AS f ON s.id = f.score_id AND f.user_id = :user_id 
        WHERE " . implode(' AND ', $where_clauses) . " 
        ORDER BY s.song_title ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT); // 追加
        foreach ($search_words as $index => $word) {
        $stmt->bindValue(":word{$index}", "%{$word}%", PDO::PARAM_STR);
      }

    try {
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        exit("SQL Error: {$e->getMessage()}");
    }

    foreach ($result as $record) {
        $book_name = $record["book"];
        $pdf_page = intval($record["page"]) + (isset($page_offsets[$book_name]) ? $page_offsets[$book_name] : 0);
        $pdf_url = "view_pdf.php?book=" . rawurlencode($book_name) . "#page={$pdf_page}";
        // 一度開くためのphpを経由する
        $fav_class = $record['fav_id'] ? 'fav-active' : 'fav-inactive';

        $output .= "
          <tr class='clickable-row' data-url='{$pdf_url}'>
            <td>" . htmlspecialchars($record["song_title"]) . "</td>
            <td>" . htmlspecialchars($record["book"]) . "</td>
            <td>" . htmlspecialchars($record["page"]) . "</td>
            <td class='fav-cell' data-id='{$record["id"]}'>
                <span class='fav-star {$fav_class}'>★</span>
            </td>
          </tr>";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>Jazz Score Searcher</title>
  <style>
    body { font-family: sans-serif; margin: 20px; }
    .search-area { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
    h1 {text-align: center;}
    input[type="text"] { padding: 8px; width: 250px; border: 1px solid #ccc; border-radius: 4px; }
    button { padding: 8px 15px; background: #2c3e50; color: white; border: none; border-radius: 4px; cursor: pointer; }
    .nav-btn { background: #f39c12; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; margin-left: 5px; font-size: 14px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 12px; }
    th { background: #2c3e50; color: white; }
    .clickable-row { cursor: pointer; }
    .clickable-row:hover { background: #f1f3f5; }
    .fav-star { font-size: 1.5rem; cursor: pointer; transition: 0.2s; }
    .fav-inactive { color: #ccc; }
    .fav-active { color: #f39c12; }
    .fav-cell { text-align: center; }
  </style>
</head>
<body>
  <div style="display: flex; justify-content: space-between; align-items: center;">
  <p>ログイン中: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
  <a href="score_logout.php" style="background: #e74c3c; color: white; text-decoration: none; padding: 5px 15px; border-radius: 5px;">ログアウト</a>
</div>

<h1>Jazz Standard Score Searcher</h1>
  <div class="search-area">
    <form action="score_read.php" method="GET">
      <input type="text" name="search" value="<?= htmlspecialchars($search_word) ?>" placeholder="例：no greater love">
      <button type="submit">検索</button>
      <a href="score_read.php" style="margin-left:10px;">リセット</a>
      <a href="favorite_list.php" class="nav-btn">★私のお気に入り</a>

    </form>
  </div>

  <table>
    <thead><tr><th>Title</th><th>Book</th><th>Page</th><th>Fav</th></tr></thead>
    <tbody><?= $output ?></tbody>
  </table>

  <script>
    // PDFを開く処理
    document.querySelectorAll('.clickable-row').forEach(row => {
      row.addEventListener('click', (e) => {
        if (!e.target.classList.contains('fav-star')) {
          const url = row.getAttribute('data-url');
          if (url) window.open(url, '_blank', 'noopener noreferrer');
        }
      });
    });

    // お気に入り登録の非同期処理
    document.querySelectorAll('.fav-star').forEach(star => {
      star.addEventListener('click', function() {
        const scoreId = this.parentElement.getAttribute('data-id');
        fetch(`fav_create.php?score_id=${scoreId}`)
          .then(res => res.json())
          .then(data => {
            if (data.status === 'success') {
                this.classList.toggle('fav-active');
                this.classList.toggle('fav-inactive');
            }
          });
      });
    });
  </script>
</body>
</html>