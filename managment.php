<?php

//pass
//データベース
require "define.php";

date_default_timezone_set('Asia/Tokyo');

//初期化
$now_date = null;
$data = null;
$file_handle = null;
$split_data = null;
$message = array();
$message_array = array();
$error_message = array(); //エラーメッセージ
$clean = array();
session_start();

//ログイン
if(!empty($_POST['btn_submit'])){
    if(!empty($_POST['admin_password']) && $_POST['admin_password'] === PASSWORD){
        $_SESSION['admin_login'] = true;
    }else{
        $error_message[] = 'ログインに失敗しました';
    }
}

//データベースに接続
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $mysqli->set_charset('utf8');

//一覧
if($mysqli->connect_errno){
    $error_message[] = 'データの読み込みに失敗しました。エラー番号'.$mysqli->connect_errno.' : '.$mysqli->connect_error;
}else{
    //書き込み日時を取得
    $now_date = date("Y-m-d H:i:s");
    $sql = "SELECT id,name,message,post_date,edit_date FROM message ORDER BY post_date DESC";
    $res = $mysqli->query($sql);
    if($res){
        $message_array = $res->fetch_all(MYSQLI_ASSOC);
    }
    $mysqli->close();
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>掲示板 管理ページ</title>
        <link rel="stylesheet" href="ss.css">
    </head>
    <body>
        <h1>掲示板 管理ページ</h1>
<!--        TOPページに戻るボタンはここ（右上）-->
        <p style="text-align: right">
        <input type="button" onclick="location.href='top.php'" value="トップページへ">
        </p>

<!--        エラーメッセージ-->
        <?php if(!empty($error_message) ): ?>
        <ul class="error_message">
            <?php  foreach($error_message as $value): ?>
            <li> <?php echo $value; ?></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        
    
<!--        投稿内容をここに表示-->
        <section>
        
        <?php if(!empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true ): ?>
<!--            投稿-->
            
            <?php if(!empty($message_array) ): ?>
            <?php foreach($message_array as $value ): ?>
            <article>
	           <div class="info">
		          <h2><?php echo $value['name']; ?></h2>
                   <time>投稿時間:<?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
                   <?php if($value['edit_date'] != '0000-00-00 00:00:00' ): ?>
                   <time>編集時間:<?php echo date('Y年m月d日 H:i', strtotime($value['edit_date'])); ?></time>
                   <?php endif; ?>
		           <p><a href="edit.php?message_id=<?php echo $value['id']; ?>">編集</a>
                     &nbsp;&nbsp;
                      <a href="delete.php?message_id=<?php echo $value['id']; ?>">削除</a>
                   </p>
	           </div>
	           <p><?php echo nl2br($value['message']); ?></p>
            </article>
            <?php endforeach; ?>
            <?php endif; ?>
            <?php else: ?>
            <form method="post">
                <div>
                    <label for="admin_password">ログインパスワード</label>
                    <input id="admin_password" type="passwsord" name="admin_password" value="">
                </div>
                <input type="submit" name="btn_submit" value="ログイン">
            </form>
            <?php endif; ?>
        </section>
    </body>
</html>