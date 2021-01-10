<?php
define("CSV_FILE", "./csv/article.csv");

function validation($title, $article) {
    $errors = [];

    if(empty($title)) {
        $errors[] = "タイトルは必須です";
    }
    if(strlen($title)>30){
        $errors[] = "タイトルは30文字以内です";
    }
    if(empty($article)) {
        $errors[] = "記事は必須です";
    }
    return $errors;
}

function post_data($title, $article) {
    $fp = fopen(CSV_FILE, "a");
    $id = count(file(CSV_FILE)) + 1;
    fputcsv($fp, [$id,$title,$article]);
}

function get_file_data() {
    $file = file_get_contents(CSV_FILE);
    return explode("\n", $file);
}


$rows = get_file_data();

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $title = $_POST["title"];
    $article = $_POST["article"];
    
   $errors = validation($title, $article);

   if(count($errors) === 0) {
        post_data($title, $article);
        header('Location: ./');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel News</title>
</head>

<body>
    <h1>Laravel News</h1>
    <h2>さぁ、最新のニュースをシェアしましょう</h2>
    <?php foreach($errors as $error){
        echo "<p>$error</p>";
    }
    ?>
    <form action="index.php" method="post">
        <div>
            <label for="title">タイトル</label>
            <input type="text" name="title" id="title">
        </div>
        <div>
            <label for="article">記事</label>
            <textarea name="article" id="article" cols="30" rows="10"></textarea>
        </div>
        <div><button type="submit">投稿</button></div>
    </form>

    <?php foreach($rows as $row) {
        $data = explode(',', $row);
        if(empty($data[0])){
            continue;
        }
echo <<< EOF
        <section>
            <h3>{$data[1]}</h3>
            <p>{$data[2]}</p>
            <a href="./posts.php?id={$data[0]}">記事全文・コメントを見る</a>
        </section>
EOF;
    }
    ?>

</body>

</html>