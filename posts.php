<?php
define("CSV_ARTICLE", "./csv/article.csv");
define("CSV_COMMENTS", "./csv/comment.csv");

// 投稿取得
function find_article($id) {
    $file = file_get_contents(CSV_ARTICLE);
    $rows = explode("\n", $file);
    foreach($rows as $row) {
        $row = explode(',', $row);
        if(empty($row[0])){
            continue;
        }
        if($row[0] === $id) {
            $data = $row;
            break;
        }
    }
    return $data;
}

// コメント取得
function find_comments($id) {
    $file = file_get_contents(CSV_COMMENTS);
    $rows = explode("\n", $file);
    $comments = [];

    if(empty($rows[0])) {
        return $comments;
    }

    foreach($rows as $row) {
        $row = explode(',', $row);
        if(empty($row[0])){
            continue;
        } 
        if($row[1] === $id) {
            $comments[] = $row;
        }
    }
    return $comments;
}

// コメント投稿
function post_comment($post, $article_id) {
    $rows = explode("\n", file_get_contents(CSV_COMMENTS));
    $last_row = $rows[array_key_last($rows) - 1];
    if(!$last_row) {
        $id = 1;
    }else{
        $last_row_id = explode(',', $last_row)[0];  
        $id = $last_row_id + 1;
    }
    $fp = fopen(CSV_COMMENTS, "a");
    fputcsv($fp, [$id, $article_id, $post["comment"]]);
}

// コメント削除
function delete_comment($comment_id) {
    $rows = file(CSV_COMMENTS);
    $find_index = 0;

    foreach($rows as $index => $row) {
        $row = explode(',', $row); 

        if($row[0] === $comment_id) {
            $find_index = $index;
        }
    }
    unset($rows[$find_index]);
    file_put_contents(CSV_COMMENTS, $rows);

}

$id = $_GET["id"];
$article = find_article($id);
$comments = find_comments($id);

// コメント削除
if($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["method"] === "delete"){
    delete_comment($_POST["id"]);
    header("Location: ./posts.php?id={$id}");
    exit;
}

// コメント投稿
if($_SERVER["REQUEST_METHOD"] === "POST"){
    post_comment($_POST, $id);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Laravel News</h1>
    <?php
echo <<< EOF
    <section>
        <h3>{$article[1]}</h3>
        <p>{$article[2]}</p>
    </section>
EOF;
?>
    <div>
        <form action=<?php echo "./posts.php?id={$id}"; ?> method="post">
            <div><textarea name="comment" id="" cols="30" rows="10"></textarea></div>
            <button type="submit">コメントを書く</button>
        </form>
    </div>

    <?php foreach($comments as $comment) : ?>
    <div>
        <p><?php echo $comment[2]; ?></p>
        <form method="post" action=<?php echo "./posts.php?id={$id}"; ?>>
            <input type="hidden" value="delete" name="method" />
            <input type="hidden" value="<?php echo $comment[0]; ?>" name="id" />
            <button type="submit">コメントを消す</button>
        </form>
    </div>

    <?php endforeach; ?>

</body>

</html>