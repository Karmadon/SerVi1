<?php

require_once __DIR__ . '/config.php';

$connection = new mysqli ($dbHostname, $dbUser, $dbPassword, $dbName);

if ($connection->connect_error)
{
	echo 'Database connection failed...' . 'Error: ' . $connection->connect_errno . ' ' . $connection->connect_error;
	exit;
}
else
{
	$connection->set_charset ('utf8');
}

function createTable ($name, $query)
{
	queryMysql ("CREATE TABLE IF NOT EXISTS $name($query)");
	echo "Table '$name' created or already exists.<br>";
}

function queryMysql ($query)
{
	global $connection;
	global $insertID;

	$result = $connection->query ($query);
	$insertID = $connection->insert_id;


	if (!$result)
	{
		Echo 'SQL Error<br>';
		//die ($connection->error);
	}

	return $result;
}

function destroySession ()
{
	$_SESSION = array ();

	if (session_id () !== '' || isset ($_COOKIE [session_name ()]))
	{
		setcookie (session_name (), '', time () - 2592000, '/');
	}

	session_destroy ();
}

function sanitizeString ($var)
{
	global $connection;

	$var = strip_tags ($var);
	$var = htmlentities ($var, ENT_QUOTES, 'UTF-8');
	$var = stripslashes ($var);

	return $connection->real_escape_string ($var);
}

function showProfile ($login)
{
	$result = queryMysql ("SELECT * FROM users WHERE login='$login'");

	if ($result->num_rows)
	{
		$row = $result->fetch_array (MYSQLI_ASSOC);
		echo stripslashes ($row ['text']) . "<br style='clear:left;'><br>";
	}
}

function getMenuCounters ()
{
	$outArray = array ();
	$result = queryMysql ('SELECT DELETED FROM devices WHERE DELETED != 1');
	$outArray['deviceCount'] = $result->num_rows;
	$result = queryMysql ('SELECT DELETED FROM clients WHERE DELETED != 1');
	$outArray['clientsCount'] = $result->num_rows;
	$result = queryMysql ('SELECT ORDER_STATE_ID FROM orders WHERE ORDER_STATE_ID != 2 OR ORDER_STATE_ID != 6');
	$outArray['openOrderCount'] = $result->num_rows;
	$result = queryMysql ('SELECT DELETED FROM clients WHERE META = 1');
	$outArray['clientsMetaCount'] = $result->num_rows;
	$result = queryMysql ('SELECT DELETED FROM devices WHERE META = 1');
	$outArray['devicesMetaCount'] = $result->num_rows;

	return $outArray;

}

function getIndexCounters ()
{
	$outArray = array ();
	$realterSum = 4000;
	$ourCompanyId = 7;

	$result = queryMysql ('SELECT ORDER_ID FROM orders WHERE ORDER_STATE_ID = 2 OR ORDER_STATE_ID = 6');
	$openOrderCount = $result->num_rows;
	$result = queryMysql ('SELECT ORDER_ID FROM orders WHERE 1');
	$outArray['orderCompletePrec'] = ($openOrderCount / $result->num_rows) * 100;

	$result = queryMysql ("SELECT ORDER_ID FROM orders WHERE PAYED='1'");
	$outArray['orderPayedPrec'] = ($result->num_rows / $openOrderCount) * 100;


	$result = queryMysql ("SELECT ID FROM clients WHERE META = '1'");
	$allCount = $result->num_rows;
	$result = queryMysql ('SELECT ID FROM clients WHERE 1');
	if ($allCount === 0)
	{
		$outArray['clientsCompletePrec'] = 100;
	}
	else
	{
		$outArray['clientsCompletePrec'] = ($allCount / $result->num_rows) * 100;
	}


	$result = queryMysql ("SELECT ID FROM devices WHERE META = '1'");
	$allCount = $result->num_rows;
	$result = queryMysql ('SELECT ID FROM devices WHERE 1');
	if ($allCount === 0)
	{
		$outArray['devicesCompletePrec'] = 100;
	}
	else
	{
		$outArray['devicesCompletePrec'] = ($allCount / $result->num_rows) * 100;
	}

	$outArray['realSumRatio'] = (Payment::getFullAmount ($ourCompanyId,1) / $realterSum) * 100;
	$outArray['fullSumAccounts'] = Payment::getFullSum();
	$outArray['getFullMinusSum'] = Payment::getFullMinusSum();
	$outArray['fullSumAccount'] = Payment::getFullAmount (7,1)+Payment::getFullAmount (7,2);
	$outArray['fullcashSumAccount'] = Payment::getFullAmount (7,1);
	$outArray['fullnocashSumAccount'] = Payment::getFullAmount (7,2);

	return $outArray;

}

function post ($index = NULL)
{
	return array_key_exists ($index, $_POST) ? $_POST[$index] : NULL;
	//return isset($_POST[$index]) ? $_POST[$index] : null;
}

function generate_password ($number)
{
	$arr = array ('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'v', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'X', 'Y', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '.', ',', '(', ')', '[', ']', '!', '?', '&', '^', '%', '@', '*', '$', '<', '>', '/', '|', '+', '-', '{', '}', '`', '~');
	// Генерируем пароль
	$pass = '';
	for ($i = 0; $i < $number; $i++)
	{
		// Вычисляем случайный индекс массива
		$index = rand (0, count ($arr) - 1);
		$pass .= $arr[$index];
	}

	return $pass;


}

function echoLastActivity ()
{
	if (!isset($connection))
	{
		getConnection ();
	}

	$sql = 'SELECT EVENT_RECORD_ID, EVENT_DATE, TYPE, CLASS, EVENT_STRING, COLOR, URL
FROM history his
	INNER JOIN history_class class
		ON his.EVENT_CLASS = class.ID
	INNER JOIN history_type type
		ON his.EVENT_TYPE = type.ID
ORDER BY his.EVENT_DATE DESC
LIMIT 30';
	$result = queryMysql ($sql);

	for ($j = 0; $j < $result->num_rows; ++$j)
	{
		$row = $result->fetch_array (MYSQLI_ASSOC);

		echo "
	<a class='list-group-item list-group-item-" . $row ['COLOR'] . "' href='" . $row['URL'] . $row ['EVENT_RECORD_ID'] . "'><i class='glyphicon glyphicon-flash'></i>
		<small> " . showDate (strtotime ($row['EVENT_DATE'])) . " :</small>
		<font color='" . $row ['COLOR'] . "'><b>" . $row ['TYPE'] . " </b></font> - " . $row ['EVENT_STRING'] . " для Класса: <b>" . $row ['CLASS'] . "</b>,
		Номер записи: <b>" . $row ['EVENT_RECORD_ID'] . "</b></a></li>";

	}
}

function makeRecordInHistory ($eventType, $eventClass, $eventRecordId, $eventString)
{
	/*
	 * Type
				1 - Add
				2 - Edit
				3 - Delete
				4 - Action
				5 - Change Ststus
				6 - ERROR

	Class
				1- Device
				2- Client
				3 - Order
				4 - Part
				5 - Payment
				6 - Labor
				7 - user
				8 - System

	*/
	$eventTime = date ('Y-m-d H:i:s');


	if (!isset($connection))
	{
		getConnection ();
	}

	$querry = "INSERT INTO `history` (`ID`, `EVENT_DATE`, `EVENT_TYPE`, `EVENT_CLASS`, `EVENT_RECORD_ID`, `EVENT_STRING`)
VALUES ('NULL', '$eventTime', '$eventType', '$eventClass', '$eventRecordId', '$eventString')";

	$result = queryMysql ($querry);

	if (!$result)
	{
		die ($connection->error);
	}
	else
	{
		return 1;
	}


}

function getConnection ()
{
	global $dbHostname;
	global $dbUser;
	global $dbPassword;
	global $dbName;


	$connection = new mysqli ($dbHostname, $dbUser, $dbPassword, $dbName);
	$connection->set_charset ('utf8');
	$result = $connection->query ("SET NAMES 'utf8';");

	if (!$result)
	{
		die ($connection->error);
	}
}

function showDate ($date) // $date --> время в формате Unix time
{
	$stf = 0;
	$cur_time = time ();
	$diff = $cur_time - $date;

	$seconds = array ('секунда', 'секунды', 'секунд');
	$minutes = array ('минута', 'минуты', 'минут');
	$hours = array ('час', 'часа', 'часов');
	$days = array ('день', 'дня', 'дней');
	$weeks = array ('неделя', 'недели', 'недель');
	$months = array ('месяц', 'месяца', 'месяцев');
	$years = array ('год', 'года', 'лет');
	$decades = array ('десятилетие', 'десятилетия', 'десятилетий');

	$phrase = array ($seconds, $minutes, $hours, $days, $weeks, $months, $years, $decades);
	$length = array (1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);

	for ($i = sizeof ($length) - 1; ($i >= 0) && (($no = $diff / $length[$i]) <= 1); $i--)
	{
		;
	}
	if ($i < 0)
	{
		$i = 0;
	}
	$_time = $cur_time - ($diff % $length[$i]);
	$no = floor ($no);
	$value = sprintf ("%d %s ", $no, getPhrase ($no, $phrase[$i]));

	if (($stf == 1) && ($i >= 1) && (($cur_time - $_time) > 0))
	{
		$value .= time_ago ($_time);
	}

	return $value . ' назад';
}

function getPhrase ($number, $titles)
{
	$cases = array (2, 0, 1, 1, 1, 2);

	return $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min ($number % 10, 5)]];
}

function format_phone_number ($mynum, $mask)
{
	/*********************************************************************/
	/*   Purpose: Return either masked phone number or '(000) 000-00-00'             */
	/*     Masks: Val=1 or xxx xxx xxxx                                             */
	/*            Val=2 or xxx xxx.xxxx                                             */
	/*            Val=3 or xxx.xxx.xxxx                                             */
	/*            Val=4 or (xxx) xxx xxxx                                           */
	/*            Val=5 or (xxx) xxx.xxxx                                           */
	/*            Val=6 or (xxx).xxx.xxxx                                           */
	/*            Val=7 or (xxx) xxx-xx-xx                                          */
	/*            Val=8 or (xxx)-xxx-xxxx                                           */
	/*********************************************************************/
	$val_num = validate_phone_number ($mynum);
	if (!$val_num && !is_string ($mynum))
	{
		echo "Number $mynum is not a valid phone number! \n";

		return '';
	}   // end if !$val_num
	if (($mask === 1) || ($mask === 'xxx xxx xxxx'))
	{
		$phone = preg_replace ('~.*(\d{3})[^\d]*(\d{3})[^\d]*(\d{4}).*~',
		                       '$1 $2 $3' . " \n",
		                       $mynum);

		return $phone;
	}   // end if $mask === 1
	if (($mask === 2) || ($mask === 'xxx xxx.xxxx'))
	{
		$phone = preg_replace ('~.*(\d{3})[^\d]*(\d{3})[^\d]*(\d{4}).*~',
		                       '$1 $2.$3' . " \n",
		                       $mynum);

		return $phone;
	}   // end if $mask === 2
	if (($mask === 3) || ($mask === 'xxx.xxx.xxxx'))
	{
		$phone = preg_replace ('~.*(\d{3})[^\d]*(\d{3})[^\d]*(\d{4}).*~',
		                       '$1.$2.$3' . " \n",
		                       $mynum);

		return $phone;
	}   // end if $mask === 3
	if (($mask === 4) || ($mask === '(xxx) xxx xxxx'))
	{
		$phone = preg_replace ('~.*(\d{3})[^\d]*(\d{3})[^\d]*(\d{4}).*~',
		                       '($1) $2 $3' . " \n",
		                       $mynum);

		return $phone;
	}   // end if $mask === 4
	if (($mask === 5) || ($mask === '(xxx) xxx.xxxx'))
	{
		$phone = preg_replace ('~.*(\d{3})[^\d]*(\d{3})[^\d]*(\d{4}).*~',
		                       '($1) $2.$3' . " \n",
		                       $mynum);

		return $phone;
	}   // end if $mask === 5
	if (($mask === 6) || ($mask === '(xxx).xxx.xxxx'))
	{
		$phone = preg_replace ('~.*(\d{3})[^\d]*(\d{3})[^\d]*(\d{4}).*~',
		                       '($1).$2.$3' . " \n",
		                       $mynum);

		return $phone;
	}   // end if $mask === 6
	if (($mask === 7) || ($mask === '(xxx) xxx-xx-xx'))
	{
		$phone = preg_replace ('~.*(\d{3})[^\d]*(\d{3})[^\d]*(\d{2})[^\d]*(\d{2}).*~', '($1) $2-$3-$4' . " \n", $mynum);

		return $phone;
	}   // end if $mask === 7
	if (($mask === 8) || ($mask === '(xxx)-xxx-xxxx'))
	{
		$phone = preg_replace ('~.*(\d{3})[^\d]*(\d{3})[^\d]*(\d{4}).*~',
		                       '($1)-$2-$3' . " \n",
		                       $mynum);

		return $phone;
	}   // end if $mask === 8
	return FALSE;       // Returns false if no conditions meet or input
}  // end function format_phone_number
function validate_phone_number ($phone)
{
	/*********************************************************************/
	/*   Purpose:   To determine if the passed string is a valid phone  */
	/*              number following one of the establish formatting        */
	/*                  styles for phone numbers.  This function also breaks    */
	/*                  a valid number into it's respective components of:      */
	/*                          3-digit area code,                                      */
	/*                          3-digit exchange code,                                  */
	/*                          4-digit subscriber number                               */
	/*                  and validates the number against 10 digit US NANPA  */
	/*                  guidelines.                                                         */
	/*********************************************************************/
	$format_pattern = '/^(?:(?:\((?=\d{3}\)))?(\d{3})(?:(?<=\(\d{3})\))' . '?[\s.\/-]?)?(\d{3})[\s\.\/-]?(\d{4})\s?(?:(?:(?:' . '(?:e|x|ex|ext)\.?\:?|extension\:?)\s?)(?=\d+)' . '(\d+))?$/';
	$nanpa_pattern = '/^(?:1)?(?(?!(37|96))[2-9][0-8][0-9](?<!(11)))?' . '[2-9][0-9]{2}(?<!(11))[0-9]{4}(?<!(555(01([0-9]' . '[0-9])|1212)))$/';

	// Init array of variables to false
	$valid = array ('format' => FALSE, 'nanpa' => FALSE, 'ext' => FALSE, 'all' => FALSE);

	//Check data against the format analyzer
	if (preg_match ($format_pattern, $phone, $matchset))
	{
		$valid['format'] = TRUE;
	}

	//If formatted properly, continue
	//if($valid['format']) {
	if (!$valid['format'])
	{
		return FALSE;
	}
	else
	{
		//Set array of new components
		$components = array ('ac' => $matchset[1], //area code
			'xc' => $matchset[2], //exchange code
			'sn' => $matchset[3] //subscriber number
		);
		//              $components =   array ( 'ac' => $matchset[1], //area code
		//                                              'xc' => $matchset[2], //exchange code
		//                                              'sn' => $matchset[3], //subscriber number
		//                                              'xn' => $matchset[4] //extension number
		//                                              );

		//Set array of number variants
		$numbers = array ('original' => $matchset[0], 'stripped' => substr (preg_replace ('[\D]', '', $matchset[0]),
		                                                                    0,
		                                                                    10));

		//Now let's check the first ten digits against NANPA standards
		if (preg_match ($nanpa_pattern, $numbers['stripped']))
		{
			$valid['nanpa'] = TRUE;
		}

		//If the NANPA guidelines have been met, continue
		if ($valid['nanpa'])
		{
			if (!empty ($components['xn']))
			{
				if (preg_match ('/^[\d]{1,6}$/', $components['xn']))
				{
					$valid['ext'] = TRUE;
				}   // end if if preg_match
			}
			else
			{
				$valid['ext'] = TRUE;
			}   // end if if  !empty
		}   // end if $valid nanpa

		//If the extension number is valid or non-existent, continue
		if ($valid['ext'])
		{
			$valid['all'] = TRUE;
		}   // end if $valid ext
	}   // end if $valid
	return $valid['all'];
}   // end functon validate_phone_number

function createBarcodeNumber ($orderId, $deviceId, $clientId)
{
	return str_pad ($orderId, 5, '0', STR_PAD_LEFT) . str_pad ($deviceId, 5, '0', STR_PAD_LEFT) . str_pad ($clientId,
	                                                                                                       5,
	                                                                                                       '0',
	                                                                                                       STR_PAD_LEFT);
}

function num2str ($num)
{
	$nul = 'ноль';
	$ten = array (array ('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'), array ('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),);
	$a20 = array ('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
	$tens = array (2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
	$hundred = array ('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
	$unit = array ( // Units
		array ('копейка', 'копейки', 'копеек', 1), array ('гривня', 'гривни', 'гривен', 0), array ('тысяча', 'тысячи', 'тысяч', 1), array ('миллион', 'миллиона', 'миллионов', 0), array ('миллиард', 'милиарда', 'миллиардов', 0),);
	//
	list($rub, $kop) = explode ('.', sprintf ("%015.2f", floatval ($num)));
	$out = array ();
	if (intval ($rub) > 0)
	{
		foreach (str_split ($rub, 3) as $uk => $v)
		{ // by 3 symbols
			if (!intval ($v))
			{
				continue;
			}
			$uk = sizeof ($unit) - $uk - 1; // unit key
			$gender = $unit[$uk][3];
			list($i1, $i2, $i3) = array_map ('intval', str_split ($v, 1));
			// mega-logic
			$out[] = $hundred[$i1]; # 1xx-9xx
			if ($i2 > 1)
			{
				$out[] = $tens[$i2] . ' ' . $ten[$gender][$i3];
			} # 20-99
			else
			{
				$out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3];
			} # 10-19 | 1-9
			// units without rub & kop
			if ($uk > 1)
			{
				$out[] = morph ($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
			}
		} //foreach
	}
	else
	{
		$out[] = $nul;
	}
	$out[] = morph (intval ($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
	$out[] = $kop . ' ' . morph ($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
	return trim (preg_replace ('/ {2,}/', ' ', join (' ', $out)));
}

function morph ($n, $f1, $f2, $f5)
{
	$n = abs (intval ($n)) % 100;
	if ($n > 10 && $n < 20)
	{
		return $f5;
	}
	$n = $n % 10;
	if ($n > 1 && $n < 5)
	{
		return $f2;
	}
	if ($n == 1)
	{
		return $f1;
	}

	return $f5;
}

function getSettings ()
{
	$settings = array ();
	if (!isset($connection))
	{
		getConnection ();
	}

	$sql = 'SELECT * FROM settings WHERE 1';
	$result = queryMysql ($sql);

	for ($j = 0; $j < $result->num_rows; ++$j)
	{
		$row = $result->fetch_array (MYSQLI_ASSOC);

		$settings[$row['PARAMETER']] = $row['VALUE'];

	}


	return $settings;
}