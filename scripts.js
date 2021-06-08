function redirect(url, delay)
{
	setTimeout( function(){ window.location = url; }, (delay * 1000) );
}

function loginUser(user, pass)
{
	let auth = {};
	auth.username = user;
	auth.password = pass;

	var request = new XMLHttpRequest();

	request.open("POST", "/~nandrews17/blog/login/login.php", true);

	request.onreadystatechange = function()
	{
		if (request.readyState == 4)
		{
			let footerMessage = document.getElementById("footer");

			if (this.status != 200)
			{
				footerMessage.innerHTML = "<div class='footer'><h2>Not registered?\nMaybe <a href='../register'>Register Here</a></h2></div>";
			}
			else
			{
				footerMessage.innerHTML = "<div class='footer'><h2>Login Successful</h2></div>";
				redirect("/~nandrews17/blog/home/", 1);
			}
		}
	}

	request.send(JSON.stringify(auth));
}

function registerUser(user, pass)
{
	let footerMessage = document.getElementById("footer");

	if (user.length < 5)
	{
		footerMessage.innerHTML = "<div class='footer'><h2>ERROR: Username Must Be 5+ Characters\n<a href='../login'>Login</a> if you have an account</h2></div>";
	}
	else if (pass.length < 5)
	{
		footerMessage.innerHTML = "<div class='footer'><h2>ERROR: Password is terrible\n<a href='../login'>Login</a> if you have an account, please</h2></div>";
	}
	else
	{
		let auth = {};
		auth.username = user;
		auth.password = pass;

		var request = new XMLHttpRequest();
		request.open("POST", "/~nandrews17/blog/register/register.php", true);

		request.onreadystatechange = function()
		{
			if (request.readyState == 4)
			{
				if (this.status != 201)
				{
					footerMessage.innerHTML = "<div class='footer'><h2>ERROR: Username Already Exists\nMaybe <a href='../login'>Login</a></h2></div>";
				}
				else
				{
					footerMessage.innerHTML = "<div class='footer'><h2>Registration Successful</h2></div>";
					redirect("/~nandrews17/blog/login/", 1);
				}
			}
		}

		request.send(JSON.stringify(auth));
	}
}

function makePost(title, subject)
{
	let footerMessage = document.getElementById("footer");

	if (title.length < 3)
	{
			footerMessage.innerHTML = "<div class='footer'><h2>ERROR: Title is really bad...make it longer</h2></div>";
	}
	else if (title.length > 200)
	{
			footerMessage.innerHTML = "<div class='footer'><h2>ERROR: Titles should be short...ish</h2></div>";
	}
	else if (subject.length < 3)
	{
			footerMessage.innerHTML = "<div class='footer'><h2>ERROR: Subject field must contain 3+ characters</h2></div>";
	}
	else if (subject.length > 2000)
	{
			footerMessage.innerHTML = "<div class='footer'><h2>ERROR: This is a post, not a book (2000 max)</h2></div>";
	}
	else
	{
		let post = {};
		post.title = title;
		post.subject = subject;

		var request = new XMLHttpRequest();
		request.open("POST", "/~nandrews17/blog/post/post.php", true);

		request.onreadystatechange = function()
		{
			if (request.readyState == 4)
			{
				footerMessage.innerHTML = "<div class='footer'><h2>Posting...</h2></div>";
				redirect("/~nandrews17/blog/home/", 1);
			}
		}

		request.send(JSON.stringify(post));
	}
}

function editPost(title, subject, id)
{
	let footerMessage = document.getElementById("footer");

	if (title.length < 3)
	{
			footerMessage.innerHTML = "<div class='footer'><h2>ERROR: Title is really bad...make it longer</h2></div>";
	}
	else if (title.length > 200)
	{
			footerMessage.innerHTML = "<div class='footer'><h2>ERROR: Titles should be short...ish</h2></div>";
	}
	else if (subject.length < 3)
	{
			footerMessage.innerHTML = "<div class='footer'><h2>ERROR: Subject field must contain 3+ characters</h2></div>";
	}
	else if (subject.length > 2000)
	{
			footerMessage.innerHTML = "<div class='footer'><h2>ERROR: This is a post, not a book (2000 max)</h2></div>";
	}
	else
	{
		let post = {};
		post.title = title;
		post.subject = subject;
		post.post_id = id;

		var request = new XMLHttpRequest();
		request.open("PUT", "/~nandrews17/blog/post/post.php", true);

		request.onreadystatechange = function()
		{
			if (request.readyState == 4)
			{
				footerMessage.innerHTML = "<div class='footer'><h2>Editing...</h2></div>";
				redirect("/~nandrews17/blog/home/", 1);
			}
		}

		request.send(JSON.stringify(post));
	}
}

function deletePost(id)
{
	let post = {};
	post.post_id = id;

	var request = new XMLHttpRequest();
	request.open("DELETE", "/~nandrews17/blog/post/post.php", true);

	request.onreadystatechange = function()
	{
		if (request.readyState == 4)
		{
			redirect("/~nandrews17/blog/home/", 0.2);
		}
	}

	request.send(JSON.stringify(post));
}
