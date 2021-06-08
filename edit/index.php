<?php
# If not logged in, redirect them to home page. They need to log in!
session_start();

if (!isset($_SESSION['user'])) {
        header('Location: ../login');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
		<title>Edit</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../blogstyle.css">
		<link rel="stylesheet" href="../post/poststyle.css">
		<script src="../scripts.js"></script>
</head>
<body>

<div class="header">
        <h1><a style="Color: black; Text-Decoration: none;" href="../home">The Blog</a></h1>
</div>

<div class="row">
        <div class="leftcolumn">
                <div class="card">
						<h2>Edit Post</h2><br>

                        <div style="font-family: Arial;" class="container">
						  <form action="" name="post" onsubmit="return false;">
							<div class="row">
							  <div class="col-25">
								<label for="fname" style="font-size: 150%;">Title</label>
							  </div>
							  <div class="col-75">
<?php
$db = pg_connect("host=localhost dbname=blog_nandrews17 user=nandrews17 password=1921905");

# Parses the URL for the id of the post to edit
$post_id = explode('=', explode('?', $_SERVER['QUERY_STRING'])[0])[1];

# Get post information to fill in for user to edit
$post = pg_fetch_row(pg_query("SELECT * FROM post WHERE id = $post_id"));
$post_title = $post[5];
$post_text = $post[3];

echo nl2br("<input type='text' id='title' name='title' value='$post_title' style='width:100%; font-size: 150%;'>");
?>
							  </div>
							</div>
							<div class="row">
							  <div class="col-25">
								<label for="subject" style="font-size: 150%;">Subject</label>
							  </div>
							  <div class="col-75">
<?php
echo nl2br("<textarea id='subject' name='subject' style='height:200px; width:100%; font-family: Arial; font-size: large;'>$post_text</textarea>");
?>
							  </div>
							</div>
							<div class="row">
<?php
echo ("<input type='submit' name='edit' value='Submit' onclick='editPost(document.forms.post.title.value, document.forms.post.subject.value, $post_id)'>");
?>
							</div>
						  </form>
						</div>
                </div>
        </div>
		<div class="rightcolumn">
                <div class="card">
<?php
# Let them see their username and the option to logout
$user = $_SESSION['user'];
echo nl2br("<h3>$user<a style='margin-left: 3em;' href='../logout.php'>logout</a></h3>\n\n");
?>
                </div>
                <div class="card">
                        <h3>Errors? Email Me</h3>
                        <p>nandrews17@georgefox.edu</p>
                </div>
        </div>
</div>
<div id="footer"></div>
</body>
</html>
