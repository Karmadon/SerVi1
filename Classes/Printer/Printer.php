<?php

	/**
	 * Created by PhpStorm.
	 * User: РђРЅС‚РѕРЅ
	 * Date: 20.07.2015
	 * Time: 12:05
	 */

	require_once __DIR__ . '/../../config.php';

	class Printer
	{
		public $id, $name, $serNum, $manufacturer, $model, $currentCount, $pageCost, $lastEdit, $owner, $balance;


		public $row, $num, $result;
		private $connection;


		public function getId()
		{
			return $this->id;
		}

		function initFromPOST()
		{
			date_default_timezone_set('Europe/Kiev');
			$this->id = sanitizeString($_POST ['printernum']);
			$this->name = sanitizeString($_POST ['printername']);
			$this->manufacturer = sanitizeString($_POST ['printermanufac']);
			$this->model = sanitizeString($_POST ['printermodel']);
			$this->serNum = sanitizeString($_POST ['printerserial']);
			$this->currentCount = sanitizeString($_POST ['printercount']);
			$this->pageCost = sanitizeString($_POST ['printercost']);
			$this->lastEdit = date('Y-m-d H:i:s');
			$this->owner = 2;
			$this->balance = 0.000;

			return 1;
		}


		function changeBalance(&$new_counter)
		{

			$result = queryMysql("SELECT PRINTER_PAGE_COST,PRINTER_PCOUNT FROM ig_printers WHERE PRINTER_NUMBER = '$this->id'"
			);

			print_r($this->id);

			if (!empty($result))
			{

				$row = $result->fetch_array(MYSQLI_ASSOC);
				$pagesNumber = $new_counter - $row ['PRINTER_PCOUNT'];
				$pagesSumm = $pagesNumber * $row['PRINTER_PAGE_COST'];
				$this->balance += $pagesSumm;


				echo $row ['PRINTER_PCOUNT'] . "</br>" . $pagesSumm . "</br>" . $pagesNumber . "</br>" . $row['PRINTER_PAGE_COST'];

			}

			$result = queryMysql("UPDATE ig_printers SET PRINTER_BALANCE='$this->balance' WHERE PRINTER_NUMBER = '$this->id'"
			);

			if (!$result)
			{
				die ($this->connection->error);
			} else
			{
				Echo "<B>Баланс принтера изменен успешно</B>";
			}

		}

		function addPrinter()
		{


			if (!isset($this->connection))
			{
				$this->getConnection();
			}

			$query = "INSERT INTO printers (PRINTER_NUMBER, PRINTER_SERIAL, PRINTER_MANUFATURER, PRINTER_MODEL,
PRINTER_OWNER, PRINTER_NAME, PRINTER_PCOUNT, PRINTER_PAGE_COST, DATE_PRINTER_LAST_EDIT, PRINTER_BALANCE)
VALUES
('$this->id', '$this->serNum', '$this->manufacturer', '$this->model', '$this->owner',
'$this->name', '$$this->currentCount', '$this->pageCost', '$this->lastEdit','$this->balance')";

			echo $query;

			$result = $this->connection->query($query);

			if (!$result)
			{
				die ($this->connection->error);
			} else
			{
				Echo "<B>Принтер добавлен</B>";
			}
		}


		private function getConnection()
		{
			global $dbHostname;
			global $dbUser;
			global $dbPassword;
			global $dbName;


			$this->connection = new mysqli ($dbHostname, $dbUser, $dbPassword, $dbName);
			$result = $this->connection->query("SET NAMES 'utf8';");

			if (!$result)
			{
				die ($this->connection->error);
			}
		}

		function editPrinter(&$printerID)
		{

			if (!isset($this->connection))
			{
				$this->getConnection();
			}

			$query = "UPDATE ig_printers SET PRINTER_NUMBER = '$this->id', PRINTER_SERIAL = '$this->serNum',
PRINTER_MANUFATURER = '$this->manufacturer', PRINTER_MODEL = '$this->model',
PRINTER_OWNER = '$this->owner', PRINTER_NAME = '$this->name', PRINTER_PCOUNT = '$this->currentCount',
PRINTER_PAGE_COST= '$this->pageCost' ,
DATE_PRINTER_LAST_EDIT = '$this->lastEdit' WHERE PRINTER_NUMBER = '$this->id'";

			$result = $this->connection->query($query);

			if (!$result)
			{
				die ($this->connection->error);
			} else
			{
				Echo "<B>Принтер изменен</B>";
			}
		}


		function initFromDB(&$printernum)
		{
			if (!isset($this->connection))
			{
				$this->getConnection();
			}
			$result = queryMysql("SELECT * FROM ig_printers WHERE PRINTER_NUMBER='$printernum' AND PRINTER_DELETED = '0'");

			if ($result->num_rows)
			{
				$this->num = $result->num_rows;
				$this->row = $result->fetch_array(MYSQLI_ASSOC);


				$this->id = $this->row ['PRINTER_NUMBER'];
				$this->name = $this->row ['PRINTER_NAME'];
				$this->manufacturer = $this->row ['PRINTER_MANUFATURER'];
				$this->model = $this->row ['PRINTER_MODEL'];
				$this->serNum = $this->row ['PRINTER_SERIAL'];
				$this->currentCount = $this->row ['PRINTER_PCOUNT'];
				$this->pageCost = $this->row ['PRINTER_PAGE_COST'];
				$this->lastEdit = $this->row ['DATE_PRINTER_LAST_EDIT'];
				$this->owner = $this->row ['PRINTER_OWNER'];

			} else
			{
				return 0;
			}

			return 1;
		}


		function getPrinterInfo(&$printernum)
		{
			if (file_exists("$printernum.jpg"))
			{
				echo "<img src='$printernum.jpg' style='float:left;'>";
			}

			$result = queryMysql("SELECT * FROM ig_printers WHERE PRINTER_NUMBER='$printernum' AND PRINTER_DELETED = '0'");

			if ($result->num_rows)
			{
				$this->num = $result->num_rows;
				$this->row = $result->fetch_array(MYSQLI_ASSOC);
			} else
			{
				return 0;
			}

			return 1;
		}


		function removePrinter(&$printernum)
		{

			date_default_timezone_set('Europe/Kiev');
			$printerlastdate = date('Y-m-d H:i:s');
			$result = queryMysql("UPDATE ig_printers SET PRINTER_DELETED='1',DATE_PRINTER_LAST_EDIT='$printerlastdate' WHERE PRINTER_NUMBER='$printernum' AND PRINTER_DELETED = '0'"
			);
			if (!$result)
			{
				die ($this->connection->error);
			}

		}

		function viewAllPrinters()
		{
			$num = '';

			$result = queryMysql("SELECT * FROM ig_printers WHERE PRINTER_DELETED = '0' ORDER BY PRINTER_NUMBER");
			if (!empty($result))
			{
				$num = $result->num_rows;
			}

			echo "<h3>Принтеры пользовтеля " . $_SESSION['username'] . "</h3><ul>";
			echo "<table width=\"95%\"><tbody>";
			for ($j = 0; $j < $num; ++$j)
			{
				$row = $result->fetch_array(MYSQLI_ASSOC);
				if ($_SESSION ['group'] == 1)
				{
					echo "<tr><th scope=\"row\"><a href='printers.php?view=" . $row ['PRINTER_NUMBER'] . "'>Принтер №
					 " . $row ['PRINTER_NUMBER'] . "</a> </th><td>" . $row ['PRINTER_NAME'] . "</td><td> Баланс: " . $row
						['PRINTER_BALANCE'] . " грн.</td><td>
					 [<a
					 href='printers.php?edit=" . $row
						['PRINTER_NUMBER'] . "'>Редактировать</a>]</td><td>[<a href='printers.php?remove=" . $row ['PRINTER_NUMBER'] . "'>Удалить</a>]</td><td>[<a href='counters.php?byprinter=" . $row ['PRINTER_NUMBER'] . "'>Счетчики</a>]</td></tr>";
				} else
				{
					echo "<tr><th scope=\"row\"><a href='printers.php?view=" . $row ['PRINTER_NUMBER'] . "'>Принтер № " . $row ['PRINTER_NUMBER'] . "</a> </th><td>" . $row ['PRINTER_NAME'] . "</td><td> Баланс: " . $row
						['PRINTER_BALANCE'] . " грн.</td><td><td>[<a href='counters.php?byprinter=" . $row ['PRINTER_NUMBER'] . "'>Счетчики</a>]</td></tr>";
				}

			}
			echo "</tbody></table>";

			/*  $result = queryMysql("SELECT * FROM ig_printers WHERE PRINTER_DELETED = '0' ORDER BY PRINTER_NUMBER");
			  if (!empty($result)) {
				  $this->num = $result->num_rows;
				  $this->row = $result->fetch_array(MYSQLI_ASSOC);
			  }*/
		}


	}