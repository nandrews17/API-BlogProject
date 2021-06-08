<?php

session_start();

# If not logged in or there was no request to comment/submit a comment, redirect them to home page.
if (!isset($_SESSION['user']) or (!isset($_POST['submit']) and !isset($_POST['comment']))) {
        header('Location: ../home');
}

include_once("../rest.php");
include_once("../lib.php");
$db = connect_to_db();
$pgDb = pg_connect("host=localhost dbname=blog_nandrews17 user=nandrews17 password=1921905");

# Catches the comment submitions to validate them and either create an error message or create comment
if (isset($_POST['submit'])) {

	$text = $_POST['subject'];
	$user = $_SESSION['user'];
	$post_id = $_POST['post_id'];

	if (strlen($text) < 3) {
			$_POST['comment'] = "<div class='footer'><h2>ERROR: Subject field must contain 3+ characters</h2></div>";
			$_POST['post_id'] = $post_id;
	}
	elseif (strlen($text) > 500) {
			$_POST['comment'] = "<div class='footer'><h2>ERROR: This is a comment, not a book (500 max)</h2></div>";
			$_POST['post_id'] = $post_id;
	}
	else {
			$id = floor(rand()) + floor(microtime());
			date_default_timezone_set("America/Los_Angeles");
			$date = date("Y/m/d H:i:s");

			$sqlID = "SELECT id FROM blog_user WHERE username = ?";
			$statement = $db->prepare($sqlID);
			$statement->execute([$user]);
			$results = $statement->fetch(PDO::FETCH_ASSOC);
			$userID = $results['id'];

			$sql = "INSERT INTO blog_comment VALUES (?, ?, ?, ?, ?, ?)";
			$statement = $db->prepare($sql);
			$statement->execute([$id, $userID, $post_id, $text, $date, $date]);
			$results = $statement->fetch(PDO::FETCH_ASSOC);

			$_POST['comment'] = 'true';
			$_POST['post_id'] = $post_id;
	}
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
		<title>Comment</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../blogstyle.css">
		<link rel="stylesheet" href="../post/poststyle.css">
</head>
<body>

<div class="header">
        <h1><a style="Color: black; Text-Decoration: none;" href="../home">The Blog</a></h1>
</div>

<div class="row">
        <div class="leftcolumn">
<?php
# Displays the post and all the comments for the UI
if (isset($_POST['comment'])) {
	$post_id = $_POST['post_id'];

	$sqlPOST = "SELECT * FROM post WHERE id = ?";
	$statement = $db->prepare($sqlPOST);
	$statement->execute([$post_id]);
	$post = $statement->fetch(PDO::FETCH_ASSOC);

	$post_date = date_format(date_create($post['post_datetime']), "[h:i a] [Y-m-d]");
	$post_title = $post['post_title'];
	$post_text = $post['post_text'];
	$post_user_id = $post['user_id'];

	$getUser = "SELECT username FROM blog_user WHERE id = $post_user_id";
	$username = pg_fetch_row(pg_query($getUser))[0];
	echo("
			<div class='card'>
				<h2>$post_title</h2>
				<form method='POST' action='../search/' name='search'><h5>
					<input type='hidden' name='fromPost' value='true'>
					<input type='hidden' name='username' value='$username'>
					<button type='submit' name='search' value='$username' style='margin-right: 10em'>$username</button>
					</form> $post_date</h5>
				<p>$post_text</p><br><div class='fakeimg' style='height:5px;'></div>");

		$sqlCOMMENTS = "SELECT * FROM blog_comment WHERE post_id = ? ORDER BY comment_datetime ASC";
		$statement = $db->prepare($sqlCOMMENTS);
		$statement->execute([$post_id]);
		$postComments = $statement->fetchAll(PDO::FETCH_ASSOC);

		if ($postComments) {
			foreach($postComments as $comment) {
				$comment_user_id = $comment['user_id'];
				$getUser = "SELECT username FROM blog_user WHERE id = $comment_user_id";
				$comment_user = pg_fetch_row(pg_query($getUser))[0];
				$comment_date = date_format(date_create($comment['comment_datetime']), "h:i a] [Y-m-d");
				$comment_text = $comment['comment_text'];
				echo("
					<div class='row'>
						<div class='col-25'>
							<h4>$comment_user<a style='margin-left: 2em;'>[$comment_date]</a></h4>
						</div>
						<div class='col-75'>
							<p>$comment_text</p>
						</div>
					</div><br>");
			}
		}
		else {
			echo("<h5>No comments</h5>");
		}

		echo("<div class='fakeimg' style='height:5px;'></div>
				<div style='font-family: Arial;' class='container'>
				  <form method='POST' action='.' name='submit'>
					<input type='hidden' name='post_id' value='$post_id'>
					<div class='row'>
					  <div class='col-25'>
						<label for='subject' style='font-size: large;'>Comment Subject</label>
					  </div>
					  <div class='col-75'>
						<textarea id='title' name='subject' placeholder='write something...' style='height:100px; width:100%; font-family: Arial; font-size: large;'></textarea>
					  </div>
					</div>
					<div class='row'>
					  <input id='specialSubmit' type='submit' name='submit' value='Comment'>
					</div>
				  </form>
				</div>
			</div>");
}
?>
        </div>
		<div class="rightcolumn">
                <div class="card">
<?php
# If logged in, let them see their username, logout option, etc.
if (isset($_SESSION['user'])) {
        $user = $_SESSION['user'];

        echo nl2br("<h3>$user<a style='margin-left: 3em;' href='../logout.php'>logout</a></h3>");
		echo nl2br("<a style='color: #FFFFFF; Text-Decoration: none;' href='../post'><button class='button'><span>Make a Post</span></button></a>");
}
else {
        echo nl2br("<h3><a href='../login'>login here</a><br>  or  <br><a href='../register'>register here</a> if you are new.</h3>");
}
?>
                </div>
                <div class="card">
                        <h3>Errors? Email Me</h3>
                        <p>nandrews17@georgefox.edu</p>
                </div>
        </div>
</div>

</body>
</html>
<?php
# Displays the footer formatted message in 'comment' in the case of invalid input
if (isset($_POST['comment']) and $_POST['comment'] != 'true') {
	echo nl2br($_POST['comment']);
}

# If logged in, let them see their name and logout at the bottom for convenience
if (isset($_SESSION['user'])) {
        $user = $_SESSION['user'];

        echo nl2br("<div class='footer'><h2>$user<a style='margin-left: 5em;' href='../logout.php'>logout</a></h2></div>");
}
else {
        echo nl2br("<div class='footer'><h2><a style='margin-right: 10em;' href='../login'>login</a><a href='../register'>register</a></h2></div>");
}

?>