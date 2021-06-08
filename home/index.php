<!DOCTYPE html>
<html lang="en">

<head>
        <title>Home</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../blogstyle.css">
		<script src="../scripts.js"></script>
</head>
<body>

<div class="header">
        <h1><a style="Color: black; Text-Decoration: none;" href=".">The Blog</a></h1>
</div>

<div class="row">
        <div class="leftcolumn">
                <div class="card">
                        <h2>Welcome To The Blog!</h2>
                        <h5>Contribute to new posts below</h5>
                        <div class="fakeimg" style="height:150px;"></div>
                        <p>"Where posting is easy and . . . still kinda lame."</p>
                </div>
<?php
session_start();

# Connect to database to display the most recent posts
$db = pg_connect("host=localhost dbname=blog_nandrews17 user=nandrews17 password=1921905");
$recentPosts = pg_fetch_all(pg_query("SELECT * FROM post ORDER BY post_datetime DESC LIMIT 4"));

# If there are any, loop to display them all with a max of 4 on the home page
if ($recentPosts) {
	foreach($recentPosts as $post) {
		$post_user_id = $post['user_id'];
		$results = pg_query("SELECT username FROM blog_user WHERE id = $post_user_id");
		$post_user = pg_fetch_row($results)[0];
		$post_date = date_format(date_create($post['post_datetime']), "[h:i a] [Y-m-d]");
		$post_title = $post['post_title'];
		$post_text = $post['post_text'];
		$post_id = $post['id'];

		# Echo the formatted HTML for each post in a "card" on the left hand side
		echo("
				<div class='card'>
					<h2>$post_title");
		# Let users delete posts
		if (isset($_SESSION['user']) && $_SESSION['user'] == $post_user) {
			echo("	<div style='float: right; font-size:75%;'>
					<form action='' name='delete' onsubmit='return false;'>
						<button style='color: #999999' onclick='if(confirm(" . '"DELETE: Are you sure?"' . ")) deletePost($post_id);'>delete</button>
					</form></div>");
		}
		echo("		</h2>
					<form method='POST' action='../search/' name='search'><h5>
						<input type='hidden' name='fromPost' value='true'>
						<input type='hidden' name='username' value='$post_user'>
						<input type='submit' name='search' value='$post_user' style='margin-right: 10em'>
					</form> $post_date</h5>
					<p>$post_text</p>");

		# If logged in, let them comment, etc.
		if (isset($_SESSION['user'])) {
			echo("<form method='POST' action='../comment/' name='comment'>
					<input type='hidden' name='post_id' value='$post_id'>");
			echo nl2br("<button class='button' name='comment' value='true'><span><a style='color: #FFFFFF; Text-Decoration: none;'>Comment</a></span></button>");
			echo("</form>");

			if ($_SESSION['user'] == $post_user) {
				echo("<form action='../edit/' method='GET' name='edit'>
				<input type='hidden' name='id' value='$post_id' />
				<button class='button'><a style='color: #FFFFFF; Text-Decoration: none;'>Edit</a></button>
				</form>");
			}
		}
		echo("</div>");
	}
}
?>
        </div>
        <div class="rightcolumn">
                <div class="card">
                        <h3>Search Users</h3><br>
						<form method="POST" action="../search/" name="search">
							<input type="text" name="username" id="username"><br>
							<input type="submit" name="search" value="Search">
						</form>
                </div>
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
# If logged in, let them see their name and logout at the bottom for convenience
if (isset($_SESSION['user'])) {
        $user = $_SESSION['user'];

        echo nl2br("<div class='footer'><h2>$user<a style='margin-left: 5em;' href='../logout.php'>logout</a></h2></div>");
}
else {
        echo nl2br("<div class='footer'><h2><a style='margin-right: 10em;' href='../login'>login</a><a href='../register'>register</a></h2></div>");
}

?>