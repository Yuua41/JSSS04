<?php
session_start();
include('functions.php');
check_session_id(); 

if (!isset($_GET['book'])) {
    exit('ファイルが指定されていません');
}

$book = $_GET['book'];
$book = basename($book);

// RealBk1の表記揺れを修正する
$file_map = [
    'RealBk1' => 'Realbk1', 
];

// マッピングにあれば変換、なければそのままの名称を使用
$actual_file_name = isset($file_map[$book]) ? $file_map[$book] : $book;

$file_path = "score_combo/" . $actual_file_name . ".pdf";
// ----------------------------------------------

if (file_exists($file_path)) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $actual_file_name . '.pdf"');
    readfile($file_path);
} else {
    // デバッグ用：見つからない場合にどのパスを探したか表示
    exit("ファイルが見つかりません: " . htmlspecialchars($file_path));
}
exit();