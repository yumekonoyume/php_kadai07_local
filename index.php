<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>レッスン口コミ入力</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        div {
            padding: 10px;
            font-size: 16px;
        }
    </style>
</head>

<body>

    <!-- Head[Start] -->
    <header>
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="select.php">◀ 口コミ一覧へ</a>
                </div>
            </div>
        </nav>
    </header>
    <!-- Head[End] -->

    <!-- Main[Start] -->
    <form method="POST" action="insert.php" enctype="multipart/form-data">
        <div class="jumbotron">
            <fieldset>
                <legend>◆レッスンの感想をぜひお聞かせください (^^)/</legend>

                <!-- 名前入力 -->
                <label>名前：<input type="text" name="username" required></label><br>

                <!-- コース選択 -->
                <label>コース：
                    <select name="course" required>
                        <option value="プリザーブドフラワーアレンジメント">プリザーブドフラワーアレンジメント</option>
                        <option value="季節の生花アレンジメント">季節の生花アレンジメント</option>
                        <option value="花束レッスン">花束レッスン</option>
                        <option value="コサージュレッスン">コサージュレッスン</option>
                        <option value="ウェディングブーケレッスン">ウェディングブーケレッスン</option>
                        <option value="ハーバリウムレッスン">ハーバリウムレッスン</option>
                    </select>
                </label><br>

                <!-- スコア選択（プルダウンリスト） -->
                <label>評価：
                    <select name="score" required>
                        <option value="5">5 ★★★★★</option>
                        <option value="4">4 ★★★★</option>
                        <option value="3">3 ★★★</option>
                        <option value="2">2 ★★</option>
                        <option value="1">1 ★</option>
                    </select>
                </label><br>

                <!-- コメント入力 -->
                <label>コメント：
                    <textarea name="comment" rows="4" cols="40"></textarea>
                </label><br>

                <!-- 画像アップロード -->
                <label>画像（作品や風景）：<input type="file" name="image" accept="image/*"></label><br>

                <!-- 送信ボタン -->
                <input type="submit" value="送信">
            </fieldset>
        </div>
    </form>
    <!-- Main[End] -->

</body>

</html>
