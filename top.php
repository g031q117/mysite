<?php

//データベース定義

require "define.php";

date_default_timezone_set('Asia/Tokyo');

//初期化
$now_date = null;
$data = null;
$file_handle = null;
$split_data = null;
$message_array = array();
$error_message = array(); //エラーメッセージ
$clean = array();
session_start();

if( !empty($_POST['submit']) ) {
    
    //投稿者の入力チェック
    if(empty($_POST['name'])){
        $error_message[] = '投稿者名を入力してください。';
    }else{
        $clean['name'] = htmlspecialchars($_POST['name'], ENT_QUOTES);
        $_SESSION['name'] = $clean['name'];
    }
    
    
    //内容の入力チェック
    if(empty($_POST['message'])){
        $error_message[] = '内容を入力してください。';
    }else{
        $clean['message'] = htmlspecialchars($_POST['message'], ENT_QUOTES);
    }
    
    if(empty($error_message)){
   
    //データベースに接続
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $mysqli->set_charset('utf8');
    //接続エラーの確認
        if($mysqli->connect_errno){
        $error_message[] = '投稿に失敗しました。エラー番号'.$mysqli->connect_errno.' : '.$mysqli->connect_error;
        }else{
            //文字コード設定
            $mysqli->set_charset('utf8');
            //書き込み日時を取得
            $now_date = date("Y-m-d H:i:s");
            //データを登録するSQL作成
            $sql = "INSERT INTO message (name, message, post_date) VALUES('$clean[name]', '$clean[message]', '$now_date')";
            //データを登録
            $res = $mysqli->query($sql);
        
            if($res){
                $_SESSION['success_message'] = '投稿しました。';
            }else{
                $error_message[] = '投稿に失敗しました。';
            }
        //データベースの接続を閉じる
        $mysqli->close();
        }
        header('Location: top.php');
    }
}


//データベースに接続
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$mysqli->set_charset('utf8');
//接続エラーの確認
if($mysqli->connect_errno){
    $error_message[] = 'データの読み込みに失敗しました。エラー番号'.$mysqli->connect_errno.' : '.$mysqli->connect_error;
}else{
    $sql = "SELECT name,message,post_date,edit_date FROM message ORDER BY post_date DESC";
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
        <title>掲示板</title>
        <link rel="stylesheet" href="ss.css">
    </head>
    <body>
        <h1>掲示板へようこそ</h1>
        
<!--        管理者ページに行くボタンはここ（右上）-->
        <p style="text-align: right">
        <input type="button" onclick="location.href='managment.php'" value="管理ページへ">
        </p>
        
        <?php if( empty($_POST['btn_submit']) && !empty($_SESSION['success_message']) ): ?>
        <p class="success_message"><?php echo $_SESSION['success_message']; ?></p>
        <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
<!--        エラーメッセージ-->
        <?php if(!empty($error_message) ): ?>
        <ul class="error_message">
            <?php  foreach($error_message as $value): ?>
            <li> <?php echo $value; ?></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        
<!--        投稿フォーム-->
        <form method="post">
            <div>
                <lavel for="name">投稿者</lavel><br>
                <input id="name" type="text" name="name" value="">
            </div>
            
            <div>
                <lavel for="message">内容</lavel><br>
                <textarea id="message" name="message"></textarea>
            </div>
            <input type="submit" name="submit" value="投稿">
        </form>
        <hr>
<!--        投稿内容をここに表示-->
        <section>
            <?php if(!empty($message_array) ): ?>
            <?php foreach($message_array as $value ): ?>
            <article class="info">
                <div>
                    <h2><?php echo $value['name']; ?></h2>
                    <time>投稿時間:<?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
                    
                    <?php if($value['edit_date'] != '0000-00-00 00:00:00' ): ?>
                    <time>編集時間:<?php echo date('Y年m月d日 H:i', strtotime($value['edit_date'])); ?></time>
                    <?php endif; ?>
                </div>
                <p><?php echo nl2br($value['message']); ?></p>
            </article>
            <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </body>
</html>