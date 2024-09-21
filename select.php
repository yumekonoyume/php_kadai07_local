<?php
// 1. DB接続
try {
    $pdo = new PDO('mysql:dbname=flower_lsn_db;charset=utf8;host=localhost', 'root', '');
} catch (PDOException $e) {
    exit('DB_CONNECT_ERROR: ' . $e->getMessage());
}

// 2. レッスン名がGETリクエストで渡された場合、絞り込みを実行
$course = isset($_GET['course']) ? $_GET['course'] : '';

// 3. データ取得SQL作成
if ($course) {
    // 特定のレッスンに絞り込む
    $sql = "SELECT * FROM lessons_feedback WHERE course = :course ORDER BY indate DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':course', $course, PDO::PARAM_STR);
    $status = $stmt->execute();  // true or false

    // 各スコアの件数を集計するSQL
    $sql_count = "SELECT score, COUNT(score) as count FROM lessons_feedback WHERE course = :course GROUP BY score";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->bindValue(':course', $course, PDO::PARAM_STR);
    $stmt_count->execute();
    $score_counts = $stmt_count->fetchAll(PDO::FETCH_ASSOC);

    // 平均スコアを計算するSQL
    $sql_avg = "SELECT AVG(score) as avg_score FROM lessons_feedback WHERE course = :course";
    $stmt_avg = $pdo->prepare($sql_avg);
    $stmt_avg->bindValue(':course', $course, PDO::PARAM_STR);
    $stmt_avg->execute();
    $avg_score = $stmt_avg->fetch(PDO::FETCH_ASSOC)['avg_score'];

} else {
    // すべてのレッスンを取得
    $sql = "SELECT * FROM lessons_feedback ORDER BY indate DESC";
    $stmt = $pdo->prepare($sql);
    $status = $stmt->execute();  // true or false

    $score_counts = [];
    $avg_score = null;
}

// 4. データ表示処理
if ($status == false) {
    // SQL実行時にエラーがあればエラーメッセージを表示
    $error = $stmt->errorInfo();
    exit("SQL_ERROR: " . $error[2]);
} else {
    // データがある場合は取得、ない場合は空の配列に
    $values = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($values)) {
        // データがない場合のメッセージ表示（オプション）
        echo "口コミがまだ登録されていません。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>口コミ一覧</title>
    <link rel="stylesheet" href="css/range.css">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <header>
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="index.php">◀ 口コミする</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- レッスンごとに絞り込むためのフォーム -->
    <div class="container">
        <form method="GET" action="select.php">
            <label for="course">レッスンを選択：</label>
            <select name="course" id="course" onchange="this.form.submit()">
                <option value="">すべてのレッスン</option>
                <option value="プリザーブドフラワーアレンジメント" <?= $course === 'プリザーブドフラワーアレンジメント' ? 'selected' : '' ?>>プリザーブドフラワーアレンジメント</option>
                <option value="季節の生花アレンジメント" <?= $course === '季節の生花アレンジメント' ? 'selected' : '' ?>>季節の生花アレンジメント</option>
                <option value="花束レッスン" <?= $course === '花束レッスン' ? 'selected' : '' ?>>花束レッスン</option>
                <option value="コサージュレッスン" <?= $course === 'コサージュレッスン' ? 'selected' : '' ?>>コサージュレッスン</option>
                <option value="ウェディングブーケレッスン" <?= $course === 'ウェディングブーケレッスン' ? 'selected' : '' ?>>ウェディングブーケレッスン</option>
                <option value="ハーバリウムレッスン" <?= $course === 'ハーバリウムレッスン' ? 'selected' : '' ?>>ハーバリウムレッスン</option>
            </select>
        </form>
    </div>

    <!-- 各評価集計と平均スコアの表示 -->
    <div class="container">
        <?php if (!empty($score_counts)) { ?>
            <h4>レッスン「<?= htmlspecialchars($course, ENT_QUOTES, 'UTF-8') ?>」の評価</h4>
            <ul>
                <?php foreach ($score_counts as $score_count) { ?>
                    <li>
                        <?= str_repeat("★", (int)$score_count['score']) ?>: <?= $score_count['count'] ?> 件
                    </li>
                <?php } ?>
            </ul>
            <p>平均スコア: <?= round($avg_score, 2) ?></p>
        <?php } ?>
    </div>

    <div class="container">
        <!-- 口コミがある場合のみ表示 -->
        <?php if (!empty($values)) { ?>
            <?php foreach ($values as $value) { ?>
                <div class="review-card">
                    <!-- 画像がある場合のみ表示 -->
                    <?php if ($value["image_path"]): ?>
                        <img src="<?= $value["image_path"] ?>" alt="画像">
                    <?php endif; ?>

                    <div class="review-details">
                        <div class="review-header">
                            <!-- レッスン名をユーザーネームの位置に表示 -->
                            <h3><?= htmlspecialchars($value["course"], ENT_QUOTES, 'UTF-8') ?></h3>
                            <!-- ユーザーネームをレッスン名の位置に表示 -->
                            <p><?= htmlspecialchars($value["username"], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>

                        <!-- スコアを★で表示 -->
                        <div class="review-score">
                            <?= str_repeat("★", (int)$value["score"]) ?>
                        </div>

                        <div class="review-comment">
                            <?= htmlspecialchars($value["comment"], ENT_QUOTES, 'UTF-8') ?>
                        </div>

                        <div class="review-footer">
                            <p>投稿日: <?= htmlspecialchars($value["indate"], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>現在、口コミはありません。</p>
        <?php } ?>
    </div>

    <script>
        // JSONデータを受け取り、consoleで確認
        const jsonData = '<?= $json ?>';
        const obj = JSON.parse(jsonData);
        console.log(obj);
    </script>
</body>

</html>
