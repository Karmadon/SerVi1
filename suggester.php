<?php

/* prevent direct access to this page */
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower ($_SERVER['HTTP_X_REQUESTED_WITH']
) === 'xmlhttprequest';

if (!$isAjax)
{
	$user_error = 'Access denied - direct call is not allowed...';
	trigger_error ($user_error,
	               E_USER_ERROR
	);
	die;
}
ini_set ('display_errors',
         1
);

/* if the 'term' variable is not sent with the request, exit */
if (!isset($_REQUEST['term']))
{
	exit;
}

if (isset ($_GET ['devicename']))
{

	echo Suggest (trim (strip_tags ($_GET ['term'])),
	              'devices',
	              'NAME'
	);
	die;
} else if (isset ($_GET ['devicemanufac']))
{
	echo Suggest (trim (strip_tags ($_GET ['term'])),
	              'dev_manufacturer',
	              'NAME'
	);
	die;
} else if (isset ($_GET ['devicemodel']))
{
	echo Suggest (trim (strip_tags ($_GET ['term'])),
	              'dev_models',
	              'NAME'
	);
	die;
} else if (isset ($_GET ['deviceserial']))
{
	echo Suggest (trim (strip_tags ($_GET ['term'])),
	              'devices',
	              'SERIAL'
	);
	die;
} else if (isset ($_GET ['devicetype']))
{
	echo Suggest (trim (strip_tags ($_GET ['term'])),
	              'dev_type',
	              'NAME'
	);
	die;
} else if (isset ($_GET ['devicecategory']))
{
	echo Suggest (trim (strip_tags ($_GET ['term'])),
	              'dev_category',
	              'NAME'
	);
	die;
} else if (isset ($_GET ['devicedescription']))
{
	echo Suggest (trim (strip_tags ($_GET ['term'])),
	              'devices',
	              'DESCRIPTION'
	);
	die;
} else if (isset ($_GET ['deviceowner']))
{
	echo Suggest (trim (strip_tags ($_GET ['term'])),
	              'clients',
	              'SCREEN_NAME'
	);
	die;
} else if (isset ($_GET ['ordername']))
{
	echo Suggest (trim (strip_tags ($_GET ['term'])),
	              'orders',
	              'ORDER_NAME'
	);
	die;
} else if (isset ($_GET ['clientname']))
{
	echo Suggest (trim (strip_tags ($_GET ['term'])),
	              'clients',
	              'SCREEN_NAME'
	);
	die;
} else if (isset ($_GET ['organizationName']))
{
	echo Suggest (trim (strip_tags ($_GET ['term'])),
	              'clients',
	              'ORG_NAME'
	);
	die;
} else if (isset ($_GET ['FirstName']))
{
	echo Suggest (trim (strip_tags ($_GET ['term'])),
	              'clients',
	              'FIRSTNAME'
	);
	die;
} else if (isset ($_GET ['LastName']))
{
	echo Suggest (trim (strip_tags ($_GET ['term'])),
	              'clients',
	              'LASTNAME'
	);
	die;
} else if (isset ($_GET ['laborName']))
{
	echo Suggest (trim (strip_tags ($_GET ['term'])),
	              'labors',
	              'LABOR_NAME'
	);
	die;
} else if (isset ($_GET ['laborDescription']))
{
	echo Suggest (trim (strip_tags ($_GET ['term'])),
	              'labors',
	              'LABOR_DESCRIPTION'
	);
	die;
} else if (isset ($_GET ['speedCode']))
{
	echo Suggest (trim (strip_tags ($_GET ['term'])),
	              'labors',
	              'LABOR_FASTCODE'
	);
	die;
} else if (isset ($_GET ['paymentName']))
{
	echo Suggest (trim (strip_tags ($_GET ['term'])),
	              'payments',
	              'NAME'
	);
	die;
} else if (isset ($_GET ['paymentFrom']))
{
	echo Suggest (trim (strip_tags ($_GET ['term'])),
	              'clients',
	              'SCREEN_NAME'
	);
	die;
} else if (isset ($_GET ['paymentTo']))
{
	echo Suggest (trim (strip_tags ($_GET ['term'])),
	              'clients',
	              'SCREEN_NAME'
	);
	die;
} else if (isset ($_GET ['paymentDescription']))
{
	echo Suggest (trim (strip_tags ($_GET ['term'])),
	              'payments',
	              'DESCRIPTION'
	);
	die;
}else if (isset ($_GET ['serchQuery']))
{
	echo searchSuggest (trim (strip_tags ($_GET ['term'])));
	die;
}
else if (isset ($_GET ['laborTemplate']))
{
	echo laborSuggest (trim (strip_tags ($_GET ['term'])));
	die;
}

function Suggest ($term, $table, $fetchRow)
{
		$dbHostname = 'comput20.mysql.ukraine.com.ua'; // Unlikely to require changing
	$dbName = 'comput20_system'; // Modify these...
	$dbUser = 'comput20_system'; // ...variables according
	$dbPassword = 'k5jwyq3e'; // ...to your installation

	$mysqli = new mysqli ($dbHostname,
	                      $dbUser,
	                      $dbPassword,
	                      $dbName
	);
	if ($mysqli->connect_error)
	{
		echo 'Database connection failed...' . 'Error: ' . $mysqli->connect_errno . ' ' . $mysqli->connect_error;
		exit;
	} else
	{
		$mysqli->set_charset ('utf8');
	}

	/* replace multiple spaces with one */
	$term = preg_replace ('/\s+/',
	                      ' ',
	                      $term
	);


	$a_json = array ();
	//$a_json_row = array ();

	$querry = "SELECT DISTINCT $fetchRow FROM $table WHERE $fetchRow LIKE '%$term%'";


	if ($data = $mysqli->query ($querry))
	{
		while ($row = mysqli_fetch_array ($data))
		{
			$a_row = htmlentities (stripslashes ($row[$fetchRow]), ENT_QUOTES, 'UTF-8');
			array_push ($a_json, $a_row);
		}
	}
	// jQuery wants JSON data

	flush ();
	$mysqli->close ();

	return json_encode ($a_json);
}
function laborSuggest ($term)
{
		$dbHostname = 'comput20.mysql.ukraine.com.ua'; // Unlikely to require changing
	$dbName = 'comput20_system'; // Modify these...
	$dbUser = 'comput20_system'; // ...variables according
	$dbPassword = 'k5jwyq3e'; // ...to your installation

	$mysqli = new mysqli ($dbHostname,
	                      $dbUser,
	                      $dbPassword,
	                      $dbName
	);
	if ($mysqli->connect_error)
	{
		echo 'Database connection failed...' . 'Error: ' . $mysqli->connect_errno . ' ' . $mysqli->connect_error;
		exit;
	} else
	{
		$mysqli->set_charset ('utf8');
	}

	/* replace multiple spaces with one */
	$term = preg_replace ('/\s+/',
	                      ' ',
	                      $term
	);


	$a_json = array ();

	if ($data = $mysqli->query ("SELECT DISTINCT * FROM labors_template WHERE LABOR_FASTCODE LIKE '%$term%'"))
	{

			$row = mysqli_fetch_array ($data);
			array_push ($a_json, htmlentities (stripslashes ($row['LABOR_FASTCODE']), ENT_QUOTES, 'UTF-8'));
			array_push ($a_json, htmlentities (stripslashes ($row['LABOR_NAME']), ENT_QUOTES, 'UTF-8'));
			array_push ($a_json, htmlentities (stripslashes ($row['LABOR_QUANTITY']), ENT_QUOTES, 'UTF-8'));
			array_push ($a_json, htmlentities (stripslashes ($row['LABOR_DESCRIPTION']), ENT_QUOTES, 'UTF-8'));
			array_push ($a_json, htmlentities (stripslashes ($row['LABOR_PRICE']), ENT_QUOTES, 'UTF-8'));

	}
	// jQuery wants JSON data

	flush ();
	$mysqli->close ();

	return json_encode ($a_json);
}

function searchSuggest ($term)
{
		$dbHostname = 'comput20.mysql.ukraine.com.ua'; // Unlikely to require changing
	$dbName = 'comput20_system'; // Modify these...
	$dbUser = 'comput20_system'; // ...variables according
	$dbPassword = 'k5jwyq3e'; // ...to your installation

	$mysqli = new mysqli ($dbHostname,
	                      $dbUser,
	                      $dbPassword,
	                      $dbName
	);
	if ($mysqli->connect_error)
	{
		echo 'Database connection failed...' . 'Error: ' . $mysqli->connect_errno . ' ' . $mysqli->connect_error;
		exit;
	} else
	{
		$mysqli->set_charset ('utf8');
	}

	/* replace multiple spaces with one */
	$term = preg_replace ('/\s+/',
	                      ' ',
	                      $term
	);


	$a_json = array ();
	//$a_json_row = array ();

	if ($data = $mysqli->query ( "SELECT DISTINCT SCREEN_NAME FROM clients WHERE SCREEN_NAME LIKE '%$term%'"))
	{
		while ($row = mysqli_fetch_array ($data))
		{
			$a_row = htmlentities (stripslashes ($row['SCREEN_NAME']), ENT_QUOTES, 'UTF-8');
			array_push ($a_json, $a_row);
		}
	}	if ($data = $mysqli->query ( "SELECT DISTINCT NAME FROM devices WHERE NAME LIKE '%$term%'"))
	{
		while ($row = mysqli_fetch_array ($data))
		{
			$a_row = htmlentities (stripslashes ($row['NAME']), ENT_QUOTES, 'UTF-8');
			array_push ($a_json, $a_row);
		}
	}if ($data = $mysqli->query ( "SELECT DISTINCT SERIAL FROM devices WHERE SERIAL LIKE '%$term%'"))
	{
		while ($row = mysqli_fetch_array ($data))
		{
			$a_row = htmlentities (stripslashes ($row['SERIAL']), ENT_QUOTES, 'UTF-8');
			array_push ($a_json, $a_row);
		}
	}if ($data = $mysqli->query ( "SELECT DISTINCT ORDER_NAME FROM orders WHERE ORDER_NAME LIKE '%$term%'"))
	{
		while ($row = mysqli_fetch_array ($data))
		{
			$a_row = htmlentities (stripslashes ($row['ORDER_NAME']), ENT_QUOTES, 'UTF-8');
			array_push ($a_json, $a_row);
		}
	}

	flush ();
	$mysqli->close ();

	return json_encode ($a_json);
}