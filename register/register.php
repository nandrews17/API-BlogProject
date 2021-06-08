<?php

include_once("../rest.php");
include_once("../lib.php");

$request = new RestRequest();

//POST, register
if($request->isPost())
{
	$db = connect_to_db();
	$pgDb = pg_connect("host=localhost dbname=blog_nandrews17 user=nandrews17 password=1921905");
	$reqVars = $request->getRequestVariables();

	if(array_key_exists("username", $reqVars) && array_key_exists("password", $reqVars))
	{
		//plaintext from the user
		$password = $reqVars["password"];
		$username = $reqVars["username"];

		$sqlUsername = "SELECT * FROM blog_user WHERE username = ?";
		$statement = $db->prepare($sqlUsername);
		$statement->execute([$username]);
		$results = $statement->fetch(PDO::FETCH_ASSOC);

		if(json_encode($results) == "false")
		{
			$getID = "SELECT setval('user_id_seq', (SELECT last_value FROM user_id_seq) + 1)";
			$id = pg_fetch_row(pg_query($getID))[0];
			$passwordHash = password_hash($password, PASSWORD_DEFAULT);

			$sql = "INSERT INTO blog_user VALUES ($id, ?, ?)";
			$statement = $db->prepare($sql);
			$statement->execute([$username, $passwordHash]);
			$results = $statement->fetch(PDO::FETCH_ASSOC);

			http_response_code(201);
		}
		else
		{
			echo "Username already exists";
			http_response_code(409);
		}
	}
	else
	{
		echo "Invalid or missing field(s)";
		http_response_code(400);
	}
}

else
{
	echo "Not supported";
	http_response_code(501);
}

?>