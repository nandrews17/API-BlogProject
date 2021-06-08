<?php
# If logged in, redirect them to home page. They don't need to log in!
session_start();

if (isset($_SESSION['user'])) {
        header('Location: ../home');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
		<title>Login</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../blogstyle.css">
		<script src="../scripts.js"></script>
</head>
<body>

<div class="header">
        <h1><a style="Color: black; Text-Decoration: none;" href="../home">The Blog</a></h1>
</div>

<div class="row">
        <div class="leftcolumn">
                <div class="card">
                        <h2>Login</h2><br>
						<form action="" name="login" onsubmit="return false;">
							<label for="username"><h3>Username</h3></label> <input type="text" name="username" id="username"><br>
							<label for="password"><h3>Password</h3></label> <input type="password" name="password" id="password"><br>
							<button onclick="loginUser(document.forms.login.username.value, document.forms.login.password.value)">Submit</button>
						</form>
                </div>
        </div>
</div>
<div id="footer"></div>
</body>
</html>