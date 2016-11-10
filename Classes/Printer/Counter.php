<?php

	/**
	 * Created by PhpStorm.
	 * User: Антон
	 * Date: 20.07.2015
	 * Time: 14:36
	 */

	require_once __DIR__ . '/../../Classes/Printer/Printer.php';
	require_once __DIR__ . '/../../config.php';

	/**
	 * Class Counter
	 */
	class Counter
	{
		public $id, $printerID, $date, $oldCounter, $newCounter, $isPayed, $payDate, $pageCost;

		/**
		 *
		 */
		function __construct()
		{
			$this->id = 0;
			$this->printerID = 0;
			date_default_timezone_set('Europe/Kiev');
			$this->date = date('Y-m-d H:i:s');
			$this->oldCounter = 0;
			$this->newCounter = 0;
			$this->isPayed = FALSE;
			$this->payDate = "";
			$this->pageCost = 0.000;
		}

		/**
		 * @return int
		 */
		public function getId()
		{
			return $this->id;
		}

		/**
		 *
		 */
		function __destruct()
		{
			$this->id = "";
			$this->printerID = "";
			$this->date = "";
			$this->oldCounter = "";
			$this->newCounter = "";
			$this->isPayed = "";
			$this->payDate = "";
			$this->pageCost = "";
		}

		/**
		 * @return int
		 */
		function initFromPOST()
		{
			date_default_timezone_set('Europe/Kiev');
			$this->printerID = sanitizeString($_POST ['Sticker']);
			$this->newCounter = sanitizeString($_POST ['counter']);
			$this->date = date('Y-m-d H:i:s');

			return 1;
		}


		/**
		 * @param \Printer $printer
		 */
		function createCounter(Printer &$printer)
		{
			$this->printerID = $printer->id;
			$this->oldCounter = $printer->currentCount;
			$this->pageCost = $printer->pageCost;

			if ($this->newCounter < $this->oldCounter)
			{
				echo("Этот счетчик меньше уже введенного для этого принтера. Проверяйте внимательно<br><br>");
				require_once "footer.php";
				die;
			}

			if (!isset($this->connection))
			{
				$this->getConnection();
			}

			$result = queryMysql("SELECT * FROM ig_printers WHERE PRINTER_PCOUNT='$this->newCounter'");
			if ($result->num_rows)
			{
				echo("Этот счетчик уже введен для этого принтера. Проверяйте внимательно<br><br>");
				require_once "footer.php";
				die;
			} else
			{
				$query = "INSERT INTO ig_counters (PRINTER_ID, DATE_UPDATE, OLD_COUNTER, NEW_COUNTER, PAGE_COST) VALUES ('$this->printerID','$this->date','$this->oldCounter','$this->newCounter','$this->pageCost')";
				$result = $this->connection->query($query);
				if (!$result)
				{
					die ($this->connection->error);
				} else
				{
					$query = "UPDATE ig_printers SET PRINTER_PCOUNT='$this->newCounter',DATE_PRINTER_LAST_EDIT='$this->date' WHERE PRINTER_NUMBER= '$this->printerID'";
					$result = $this->connection->query($query);
					if (!$result)
					{
						die ($this->connection->error);
					}
				}
			}
		}

		/**
		 *
		 */
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

	}