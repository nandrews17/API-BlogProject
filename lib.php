<?php

function connect_to_db()
{
	$db = new PDO("pgsql:dbname=blog_nandrews17 host=localhost user=nandrews17 password=1921905");

	return $db;
}


function found_all_keys($inputs, $keys)
{
	$found_all = true;

	foreach($keys as $key)
	{
		if(!array_key_exists($key, $inputs))
		{
			$found_all = false;
		}
	}

	return $found_all;
}


?>
