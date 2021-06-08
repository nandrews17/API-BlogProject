<?php

include_once("../rest.php");
include_once("../lib.php");

$request = new RestRequest();

//GET, get json of a comment
if($request->isGet())
{
	$db = pg_connect("host=localhost dbname=blog_nandrews17 user=nandrews17 password=1921905");
	$reqVars = $request->getRequestVariables();

	if(array_key_exists("id", $reqVars))
	{
		$comment_id = $reqVars['id'];
		$comment = pg_fetch_row(pg_query("SELECT * FROM blog_comment WHERE id = $comment_id"));

		if ($comment)
		{
			echo json_encode($comment);
			http_response_code(200);
		}
		else
		{
			echo "Comment with given ID not found\n";
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

	if(array_key_exists("post_id", $reqVars) && array_key_exists("subject", $reqVars) && isset($_SESSION['user']))
	{
		//plaintext from the user
		$post_id = $reqVars["post_id"];
		$text = $reqVars["subject"];
		$user = $_SESSION['user'];

		if (strlen($text) < 3) {
			echo "Comment must be 3+ characters\n";
			http_response_code(400);
		}
		elseif (strlen($text) > 500) {
			echo "Comment must not be more than 500 characters\n";
			http_response_code(400);
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

			http_response_code(201);
		}
	}
	else
	{
		echo "Must be authenticated and send propper post id and subject fields\n";
		http_response_code(403);
	}
}
//DELETE, remove post
else if($request->isDelete())
{
	$db = pg_connect("host=localhost dbname=blog_nandrews17 user=nandrews17 password=1921905");
	$reqVars = $request->getRequestVariables();
	session_start();

	if(array_key_exists("id", $reqVars) && isset($_SESSION['user']))
	{
		$comment_id = $reqVars['id'];
		$comment_user_id = pg_fetch_row(pg_query("SELECT user_id FROM blog_comment WHERE id = $comment_id"))[0];
		$comment_user = pg_fetch_row(pg_query("SELECT username FROM blog_user WHERE id = $comment_user_id"))[0];

		if($_SESSION['user'] == $comment_user)
		{
			$data = array("id" => $comment_id);
			$result = pg_delete($db, "blog_comment", $data);

			if ($result)
			{
				http_response_code(200);
			}
			else
			{
				echo "Comment with given ID not found\n";
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
