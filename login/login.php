<?php

include_once("../rest.php");
include_once("../lib.php");

$request = new RestRequest();

//GET, show who is logged in
if($request->isGet())
{
	//join the the session if it exists
	session_start();

	//check if anyone is logged in
	if(array_key_exists('user', $_SESSION))
	{
		$user = $_SESSION['user'];
	}
	else
	{
		$user = "Not Logged In";
	}

	echo $user;
}
//POST, log in
else if($request->isPost())
{
	$db = connect_to_db();
	$reqVars = $request->getRequestVariables();

	if(array_key_exists("username", $reqVars) && array_key_exists("password", $reqVars))
	{
		//plaintext from the user
		$password = $reqVars["password"];
		$username = $reqVars["username"];
		
		$sql = "SELECT * FROM blog_user WHERE username = ?";
		$statement = $db->prepare($sql);
		$statement->execute([$username]);
		$results = $statement->fetch(PDO::FETCH_ASSOC);

		if (json_encode($results)!="false" and password_verify($password, $results['password']))
		{
			session_start();
			$_SESSION['user'] = $username;
			http_response_code(200);
		}
		else
		{
			echo "Wrong username or password";
			http_response_code(401);
		}
	}
	else
	{
		echo "Invalid or missing field(s)";
		http_response_code(400);
	}
}
//DELETE, logout
else if($request->isDelete())
{
	header("Location: ../logout.php");
}

else
{
	echo "Not supported";
	http_response_code(501);
}

?>
