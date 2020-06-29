<?php

//データベース
require "define.php";

date_default_timezone_set('Asia/Tokyo');

//初期化
$message_id = null;
$mysqli = null;
$sql = null;
$res = null;
$error_message = array();
$message_data = array();
session_start();

if( !empty($_GET['message_id']) && empty($_POST['message_id']) ) {

	$message_id = (int)htmlspecialchars($_GET['message_id'], ENT_QUOTES);

	//データベースに接続
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $mysqli->set_charset('utf8');
	
	// 接続エラーの確認
	if( $mysqli->connect_errno ) {
		$error_message[] = 'データベースの接続に失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
	} else {
	
		// データの読み込み
		$sql = "SELECT * FROM message WHERE id = $message_id";
		$res = $mysqli->query($sql);
		
		if( $res ) {
			$message_data = $res->fetch_assoc();
		} else {
		
			// データが読み込めなかったら一覧に戻る
			header("Location: managment.php");
		}
		
		$mysqli->close();
	}

} elseif( !empty($_POST['message_id']) ) {

	$message_id = (int)htmlspecialchars( $_POST['message_id'], ENT_QUOTES);

	//データベースに接続
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $mysqli->set_charset('utf8');
	
	// 接続エラーの確認
	if( $mysqli->connect_errno ) {
		$error_message[] = 'データベースの接続に失敗しました。 エラー番号 ' . $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
	} else {
        //削除
		$sql = "DELETE FROM message WHERE id = $message_id";
		$res = $mysqli->query($sql);
	}
	
	$mysqli->close();
	
	// 更新に成功したら一覧に戻る
	if( $res ) {
		header("Location: ./managment.php");
	}
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>掲示板 管理ページ-削除</title>
<link rel="stylesheet" href="ss.css">
</head>
<body>
<h1>掲示板 管理ページ-削除</h1>
<?php if( !empty($error_message) ): ?>
    <ul class="error_message">
		<?php foreach( $error_message as $value ): ?>
            <li>・<?php echo $value; ?></li>
		<?php endforeach; ?>
    </ul>
<?php endif; ?>
<p class="text-confirm">以下の投稿を削除します。よろしければ「削除」ボタンを押してください。</p>
<form method="post">
    <div>
        <label for="name">投稿者名</label>
        <input id="name" type="text" name="name" value="<?php if( !empty($message_data['name']) ){ echo $message_data['name']; } ?>" disabled>
    </div>
    <div>
        <label for="message">内容</label>
        <textarea id="message" name="message" disabled><?php if( !empty($message_data['message']) ){ echo $message_data['message']; } ?></textarea>
    </div>
    <input type="button" onclick="location.href='managment.php'" value="キャンセル"> 
    <input type="submit" name="submit" value="削除">
    <input type="hidden" name="message_id" value="<?php echo $message_data['id']; ?>">
</form>
</body>
</html>