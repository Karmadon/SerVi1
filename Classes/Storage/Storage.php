<?php

	/**
	 * Created by PhpStorm.
	 * User: strem
	 * Date: 08.10.2015
	 * Time: 11:38
	 */

	require_once __DIR__ . '/../Common/Records.php';

	class Storage extends Records
	{
		public
			$itemId,
			$itemName,
			$itemQuantity,


	public function createRecordInDB()
	{

		if (!isset($this->connection))
		{
			$this->getConnection();
		}

		$querry = "INSERT INTO orders (
	ORDER_ID,
	ORDER_DATE,
	ORDER_NAME,
	ORDER_CLIENT_ID,
	ORDER_DEVICE_ID,
	ORDER_TYPE_ID,
	ORDER_TECHNICIAN_ID,
	ORDER_TILL_DATE,
	ORDER_SPEED_ID,
	ORDER_STATE_ID,
	ORDER_DESCRIPTION)
VALUES
	(
		NULL ,
		'$this->orderDate',
		'$this->orderName',
		'$this->clientID',
		'$this->deviceID',
		'$this->orderTypeID',
		'$this->technicianID',
		'$this->tillDate',
		'$this->orderSpeedID',
		'$this->orderStateID',
		'$this->description'
		)";


		$result = queryMysql($querry);


		if (!$result)
		{
			$this->makeRecordInHistory('6', '3', $this->connection->insert_id, 'Ошибка при вставке записи');
			die ($this->connection->error);
		} else
		{

			return 1;
		}


	}

	public function deleteRecordFromDB($recordID)
	{
		//TODO: Implement delete

	}

	public function editRecordInDB()
	{

	}

	public function initFromDB($recordID)
	{
		if (!isset($this->connection))
		{
			$this->getConnection();
		}

		$sql = "SELECT
  ORDER_ID            AS ID,
  ORDER_DATE          AS DATE,
  ORDER_NAME          AS NAME,
  ORDER_SPEED_ID      AS SPEED_ID,
  ORDER_TYPE_ID       AS TYPE_ID,
  ORDER_STATE_ID      AS STATE_ID,
  ORDER_CLIENT_ID     AS CLIENT_ID,
  ORDER_TECHNICIAN_ID AS TECHNICIAN_ID,
  ORDER_DEVICE_ID     AS DEVICE_ID,
  ORDER_TILL_DATE     AS TILL_DATE,
  ORDER_DESCRIPTION   AS DESCRIPTION,
  ORDER_CLOSE_DATE    AS CLOSE_DATE,
  ORDER_CLOSE_MESSAGE AS CLOSE_MESSAGE,
  spd.SPEED           AS SPEED,
  typ.ORDER_TYPE      AS TYPE,
  sta.STATE           AS STATE,
  cli.SCREEN_NAME     AS CLIENT,
  dev.NAME            AS DEVICE,
  usr.login           AS TECHNICIAN,
  sta.STATUS_COLOR    AS COLOR
FROM orders ord
  INNER JOIN order_speed spd ON ord.ORDER_SPEED_ID = spd.ID
  INNER JOIN order_types typ ON ord.ORDER_TYPE_ID = typ.ID
  INNER JOIN order_states sta ON ord.ORDER_STATE_ID = sta.ID
  INNER JOIN clients cli ON ord.ORDER_CLIENT_ID = cli.ID
  INNER JOIN devices dev ON ord.ORDER_DEVICE_ID = dev.ID
  INNER JOIN users usr ON ord.ORDER_TECHNICIAN_ID = usr.ID
WHERE ORDER_ID = '" . $recordID . "'";

		$result = queryMysql($sql);

		if ($result->num_rows)
		{
			$this->num = $result->num_rows;
			$this->row = $result->fetch_array(MYSQLI_ASSOC);

			$this->orderId = $this->row ['ID'];
			$this->orderDate = $this->row ['DATE'];
			$this->orderName = $this->row ['NAME'];
			$this->clientID = $this->row ['CLIENT_ID'];
			$this->clientSTR = $this->row ['CLIENT'];
			$this->deviceID = $this->row ['DEVICE_ID'];
			$this->deviceSTR = $this->row ['DEVICE'];
			$this->orderTypeID = $this->row ['TYPE_ID'];
			$this->orderTypeSTR = $this->row ['TYPE'];
			$this->technicianID = $this->row ['TECHNICIAN_ID'];
			$this->technicianSTR = $this->row ['TECHNICIAN'];
			$this->tillDate = $this->row ['TILL_DATE'];
			$this->orderSpeedSTR = $this->row ['SPEED'];
			$this->orderSpeedID = $this->row ['SPEED_ID'];
			$this->orderStateID = $this->row ['STATE_ID'];
			$this->orderStateSTR = $this->row ['STATE'];
			$this->description = $this->row ['DESCRIPTION'];
			$this->closeDate = $this->row ['CLOSE_DATE'];
			$this->closeMessage = $this->row ['CLOSE_MESSAGE'];
			$this->color = $this->row['COLOR'];
		}

		$result->close();
	}

	public function initFromPOST()
	{

		$this->orderName = $this->post('ordername');
		$this->technicianID = $_SESSION ['loginId'];
		$this->orderTypeID = $this->post('ordertype');
		$this->orderSpeedID = $this->post('orderspeed');
		$this->orderStateID = $this->post('orderstatus');
		$this->description = $this->post('orderdescription');

	}

	public function echoComboControl($table, $rowToEcho, $header, $selectName, $form, $selected)
	{
		if (!isset($this->connection))
		{
			$this->getConnection();
		}

		$sql = "SELECT * FROM $table ORDER BY ID";
		$result = queryMysql($sql);
		echo <<<HTML
<select form= "$form" name="$selectName" class = "form-control">
<!--<option disabled value='0'>$header</option>-->
HTML;
		for ($j = 0; $j < $result->num_rows; ++$j)
		{
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$select = ($row ['ID'] === $selected) ? 'selected' : '';
			echo "<option $select value=\"" . $row ['ID'] . "\">" . $row [$rowToEcho] . '</option>';
			$first = '';
		}
		echo '</select>';
		$result->close();
	}

}