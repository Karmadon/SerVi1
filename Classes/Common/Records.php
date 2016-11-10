<?php

	/**
	 * Created by PhpStorm.
	 * User: strem
	 * Date: 22.09.2015
	 * Time: 17:03
	 */

	require_once __DIR__ . '/../../config.php';

	class Records
	{
		public
			$connection,
			$row,
			$num,
			$insertID;


		public function __construct($method = 'POST', $recordID = NULL)
		{
			if ($method === 'POST')
			{
				$this->initFromPOST();
			} elseif ($method === 'DB')
			{
				$this->initFromDB($recordID);
			} elseif ($method === 'META')
			{
				$this->initMetaObject();
			}
		}

		public function initFromPOST()
		{

		}

		public function initFromDB($recordID)
		{

		}

		public function initMetaObject()
		{

		}

		public function __destruct()
		{
		}

		public function createRecordInDB()
		{
		}

		public function deleteRecordFromDB($recordID)
		{
		}

		public function editRecordInDB($recordID)
		{

		}

		public function getRecordInfo($recordID)
		{

		}

		static public function post($index = NULL)
		{
			return isset($_POST[$index]) ? $_POST[$index] : NULL;
		}

		public function echoComboControl($table, $rowToEcho, $header, $selectName, $form = 'formName', $selected = 1, $autoSubmit = 0, $disabled = 0)
		{
			if (!isset($this->connection))
			{
				$this->getConnection();
			}

			$disabled = ($disabled) ? 'readonly' : '';
			$onchange = ($autoSubmit) ? 'onchange="this.form.submit()"' : '';


			$sql = "SELECT * FROM $table ORDER BY ID";
			$result = queryMysql($sql);
			echo <<<HTML
<select $onchange form= "$form" name="$selectName" class = "form-control" $disabled>
<!--<option disabled value='0'>$header</option>-->
HTML;
			for ($j = 0; $j < $result->num_rows; ++$j)
			{
				$row = $result->fetch_array(MYSQLI_ASSOC);
				$select = ($row ['ID'] === "$selected") ? 'selected' : '';

				echo "<option $select value=\"" . $row ['ID'] . "\">" . $row [$rowToEcho] . '</option>';
				$first = '';
			}
			echo '</select>';
		}

		public function getConnection()
		{
			global $dbHostname;
			global $dbUser;
			global $dbPassword;
			global $dbName;


			$this->connection = new mysqli ($dbHostname, $dbUser, $dbPassword, $dbName);
			$this->connection->set_charset('utf8');
			$result = $this->connection->query("SET NAMES 'utf8';");

			if (!$result)
			{
				die ($this->connection->error);
			}
		}

	}