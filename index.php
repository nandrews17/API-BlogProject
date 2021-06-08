<?php
	include_once("rest.php");
	include_once("lib.php");
	$request = new RestRequest();

	$db = connect_to_db();
	$reqVars = $request->getRequestVariables();
	//pick the correct operation based on request type
	if($request->isGet())
	{
		if (empty(explode('?', $_SERVER['QUERY_STRING'])[0]))
		{
			header('Location: ./home');
		}
		else
		{
			header('Location: ./home');
		}
	}
	else
	{
		echo "Not supported\n";
		http_response_code(501);
	}
?>
