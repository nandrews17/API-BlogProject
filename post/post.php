<?php

include_once("../rest.php");
include_once("../lib.php");

$request = new RestRequest();

//GET, get json of a post
if($request->isGet())
{
	$db = pg_connect("host=localhost dbname=blog_nandrews17 user=nandrews17 password=1921905");
	$reqVars = $request->getRequestVariables();

	if(array_key_exists("post_id", $reqVars))
	{
		$post_id = $reqVars['post_id'];
		$post = pg_fetch_row(pg_query("SELECT * FROM post WHERE id = $post_id"));

		if ($post)
		{
			echo json_encode($post);
			http_response_code(200);
		}
		else
		{
			echo "Post with given ID not found\n";
			http_response_code(404);
		}
	}
	else
	{
		echo "Invalid argument(s)\n";
		http_response_code(400);
	}
}
//POST, make a new post
else if($request->isPost())
{
	$db = connect_to_db();
	$reqVars = $request->getRequestVariables();
	session_start();

	if(array_key_exists("title", $reqVars) && array_key_exists("subject", $reqVars) && isset($_SESSION['user']))
	{
		//plaintext from the user
		$title = $reqVars["title"];
		$text = $reqVars["subject"];
		$user = $_SESSION['user'];

		if (strlen($title) < 3)
		{
			echo "Titles are 3+ characters";
			http_response_code(400);
		}
		elseif (strlen($title) > 200)
		{
				echo "Titles should be less than 200 characters\n";
				http_response_code(400);
		}
		elseif (strlen($text) < 3)
		{
				echo "Subject field must contain 3+ characters\n";
				http_response_code(400);
		}
		elseif (strlen($text) > 2000)
		{
				echo "This is a post, not a book (2000 max in post subject)\n";
				http_response_code(400);
		}
		else
		{
			$id = floor(rand()) + floor(microtime());
			date_default_timezone_set("America/Los_Angeles");
			$date = date("Y/m/d H:i:s");

			$sqlID = "SELECT id FROM blog_user WHERE username = ?";
			$statement = $db->prepare($sqlID);
			$statement->execute([$user]);
			$results = $statement->fetch(PDO::FETCH_ASSOC);
			$userID = $results['id'];

			$sql = "INSERT INTO post VALUES (?, ?, ?, ?, ?, ?, ?)";
			$statement = $db->prepare($sql);
			$statement->execute([$id, $userID, $date, $text, '{}', $title, $date]);
			$results = $statement->fetch(PDO::FETCH_ASSOC);

			http_response_code(201);
		}
	}
	else
	{
		echo "Must be authenticated and send propper title and subject fields\n";
		http_response_code(403);
	}
}
//PUT, update a post
else if($request->isPut())
{
	$db = connect_to_db();
	$reqVars = $request->getRequestVariables();
	session_start();

	if(found_all_keys($reqVars, ["post_id", "title", "subject"]) && isset($_SESSION['user']))
	{
		//plaintext from the user
		$post_id = $reqVars["post_id"];
		$title = $reqVars["title"];
		$text = $reqVars["subject"];
		$user = $_SESSION['user'];

		if (strlen($title) < 3)
		{
			echo "Titles are 3+ characters";
			http_response_code(400);
		}
		elseif (strlen($title) > 200)
		{
				echo "Titles should be less than 200 characters\n";
				http_response_code(400);
		}
		elseif (strlen($text) < 3)
		{
				echo "Subject field must contain 3+ characters\n";
				http_response_code(400);
		}
		elseif (strlen($text) > 2000)
		{
				echo "This is a post, not a book (2000 max in post subject)\n";
				http_response_code(400);
		}
		else
		{
			$sqlID = "UPDATE post SET post_title = ?, post_text = ? where id = ?";
			$statement = $db->prepare($sqlID);
			$statement->execute([$title, $text, $post_id]);
			$results = $statement->fetch(PDO::FETCH_ASSOC);

			if (json_encode($results)!="false")
			{
				http_response_code(201);
			}
			else
			{
				echo "Post with given ID not found\n";
				http_response_code(404);
			}
		}
	}
	else
	{
		echo "Must be authenticated and send valid post id with a title and subject field to update to\n";
		http_response_code(403);
	}
}
//DELETE, remove post
else if($request->isDelete())
{
	$db = pg_connect("host=localhost dbname=blog_nandrews17 user=nandrews17 password=1921905");
	$reqVars = $request->getRequestVariables();
	session_start();

	if(array_key_exists("post_id", $reqVars) && isset($_SESSION['user']))
	{
		$post_id = $reqVars['post_id'];
		$post_user_id = pg_fetch_row(pg_query("SELECT user_id FROM post WHERE id = $post_id"))[0];
		$post_user = pg_fetch_row(pg_query("SELECT username FROM blog_user WHERE id = $post_user_id"))[0];

		if($_SESSION['user'] == $post_user)
		{
			$data = array("id" => $post_id);
			$result = pg_delete($db, "post", $data);

			if ($result)
			{
				http_response_code(200);
			}
			else
			{
				echo "Post with given ID not found\n";
				http_response_code(404);
			}
		}
		else
		{
			echo "Unauthorized\n";
			http_response_code(403);
		}
	}
	else
	{
		echo "Must be logged in and provide valid argument(s)\n";
		http_response_code(403);
	}
}
// Other requests not supported
else
{
	echo "Not supported\n";
	http_response_code(501);
}

?>
