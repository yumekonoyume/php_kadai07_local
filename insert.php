<?php
// 1. POSTデータ取得
$username   = $_POST["username"];
$course     = $_POST["course"];
$score      = $_POST["score"];
$comment    = $_POST["comment"];
$image_path = null;

// 画像ファイルがアップロードされたか確認
if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
    $upload_dir = "uploads/";
    $filename = basename($_FILES["image"]["name"]);
    $target_file = $upload_dir . $filename;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($file_type, $allowed_types)) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        } else {
            exit("ファイルのアップロードに失敗しました。");
        }
    } else {
        exit("対応していないファイル形式です。");
    }
}

// 2. DB接続
try {
    // データベース名を指定
    $pdo = new PDO('mysql:dbname=flower_lsn_db;charset=utf8;host=localhost', 'root', '');
} catch (PDOException $e) {
    exit('DB_CONNECT:'.$e->getMessage());
}

// 3. データ登録SQL作成 (テーブル名を指定)
$sql = "INSERT INTO lessons_feedback(username, course, score, comment, image_path, indate)
        VALUES(:username, :course, :score, :comment, :image_path, sysdate());";
$stmt = $pdo->prepare($sql);

// データのバインド
$stmt->bindValue(':username',   $username,   PDO::PARAM_STR);
$stmt->bindValue(':course',     $course,     PDO::PARAM_STR);
$stmt->bindValue(':score',      $score,      PDO::PARAM_INT);
$stmt->bindValue(':comment',    $comment,    PDO::PARAM_STR);
$stmt->bindValue(':image_path', $image_path, PDO::PARAM_STR);

$status = $stmt->execute();

// 4. データ登録処理後
if ($status == false) {
    $error = $stmt->errorInfo();
    exit("SQL_ERROR:".$error[2]);
} else {
    header("Location: index.php");
    exit();
}
?>
