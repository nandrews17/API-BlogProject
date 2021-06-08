<!DOCTYPE html>
<html lang="en">

<head>
		<title>Search</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../blogstyle.css">
</head>
<body>

<div class="header">
        <h1><a style="Color: black; Text-Decoration: none;" href="../home">The Blog</a></h1>
</div>

<div class="row">
        <div class="leftcolumn">
<?php
session_start();

include_once("../rest.php");
include_once("../lib.php");
$db = connect_to_db();

# Internal PHP commands to display all of the posts of a given user
if (isset($_POST['search'])) {
	$username = $_POST['username'];

# If someone came to this page by clicking a username vs the search bar, display differently
	if (isset($_POST['fromPost'])) {
		echo("<div class='card'>
				<h1>$username</h1>
				<h2>Blog Posts:</h2>
			  </div>");
	}
	else {
		echo("<div class='card'>
				<h2>Results for user: $username</h2><br>
			  </div>");
	}

	$sqlID = "SELECT id FROM blog_user WHERE username = ?";
	$statement = $db->prepare($sqlID);
	$statement->execute([$username]);
	$results = $statement->fetch(PDO::FETCH_ASSOC);

# If a user exists and has posts, loop to display each one in formatted "cards"
	if ($results)
	{
		$post_user_id = $results['id'];

		$sqlPOSTS = "SELECT * FROM post WHERE user_id = ? ORDER BY post_datetime DESC";
		$statement = $db->prepare($sqlPOSTS);
		$statement->execute([$post_user_id]);
		$recentPosts = $statement->fetchAll(PDO::FETCH_ASSOC);

		if ($recentPosts) {
			foreach($recentPosts as $post) {
				$post_date = date_format(date_create($post['post_datetime']), "[h:i a] [Y-m-d]");
				$post_title = $post['post_title'];
				$post_text = $post['post_text'];
				$post_id = $post['id'];

				echo("
						<div class='card'>
							<h2>$post_title");
				# Let users delete posts
				if (isset($_SESSION['user']) && $_SESSION['user'] == $username) {
					echo("	<div style='float: right; font-size:75%;'>
							<form action='' name='delete' onsubmit='return false;'>
								<button style='color: #999999' onclick='if(confirm(" . '"DELETE: Are you sure?"' . ")) deletePost($post_id);'>delete</button>
							</form></div>");
				}
				echo("		</h2>
							<form method='POST' action='.' name='search'><h5>
								<input type='hidden' name='fromPost' value='true'>
								<input type='hidden' name='username' value='$username'>
								<input type='submit' name='search' value='$username' style='margin-right: 10em'>
							</form> $post_date</h5>
							<p>$post_text</p>");
				if (isset($_SESSION['user'])) {
					echo("<form method='POST' action='../comment/' name='comment'>
							<input type='hidden' name='post_id' value='$post_id'>");
					echo nl2br("<button class='button' name='comment' value='true'><span><a style='color: #FFFFFF; Text-Decoration: none;'>Comment</a></span></button>");
					echo("</form>");

					if ($_SESSION['user'] == $username) {
						echo("<form action='../edit/' method='GET' name='edit'>
						<input type='hidden' name='id' value='$post_id' />
						<button class='button'><a style='color: #FFFFFF; Text-Decoration: none;'>Edit</a></button>
						</form>");
					}
				}
				echo("</div>");
			}
		}
		else {
			echo nl2br("<div class='card'>
					<h3>$username has no posts yet!</h3>
					</div>");
		}
	}
	else {
		echo nl2br("<div class='card'>
				<h3>$username</h3>
				<h4>Not found</h4>
				</div>");
	}
}
?>
        </div>
		<div class="rightcolumn">
                <div class="card">
                        <h3>Search Users</h3><br>
						<form method="POST" action="." name="search">
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