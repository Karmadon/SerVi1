<?php

	/**
	 * Created by PhpStorm.
	 * User: strem
	 * Date: 22.09.2015
	 * Time: 15:44
	 */

	require_once __DIR__ . '/../../functions.php';
	require_once __DIR__ . '/../../Classes/Classes.php';


	class Labor extends
		Records
	{
		public
			$orderID,
			$laborID,
			$laborFastCode,
			$stateID,
			$stateSTR,
			$name,
			$description,
			$quantity,
			$price,
			$startDate,
			$endDate,
			$stateColor,
			$meta,
			$fullCost;

		static public function addLaborFromTemplate ($templateID,$orderID)
		{

			if (!isset($connection))
			{
				getConnection ();
			}

			$sql = "INSERT INTO labors (LABOR_ID, LABOR_ORDER_ID, LABOR_FASTCODE, LABOR_NAME, LABOR_STATE_ID, LABOR_QUANTITY, LABOR_PRICE, LABOR_DESCRIPTION, LABOR_START_DATE, LABOR_END_DATE)
	SELECT
		NULL,
		$orderID,
		LABOR_FASTCODE,
		LABOR_NAME,
		LABOR_STATE_ID,
		LABOR_QUANTITY,
		LABOR_PRICE,
		LABOR_DESCRIPTION,
		NOW(),
		NOW()+1
	FROM labors_template
	WHERE LABOR_ID = '$templateID'";

			$result = queryMysql ($sql);

		}

		static public function getFullCostOfOrder ($orderID)
		{

			if (!isset($connection))
			{
				getConnection ();
			}

			$fullCost  = floatval(0.00);

			$sql = "SELECT
	LABOR_ORDER_ID,
	LABOR_QUANTITY,
	LABOR_PRICE
FROM labors
WHERE LABOR_ORDER_ID = '$orderID'";


			$result = queryMysql ($sql);
			for ($j = 0; $j < $result->num_rows; ++$j)
			{
				$row = $result->fetch_array (MYSQLI_ASSOC);

				$quantity = $row['LABOR_QUANTITY'];
				$price = $row['LABOR_PRICE'];
				$sum = $price * $quantity;
				$fullCost += $sum;
			}

			$order = new Order('DB',$orderID);
			$client = new Client('DB',$order->clientID);

			$discount = ($fullCost / 100) * $client->discount;
			$fullCost = $fullCost - $discount;
			$fullCost = $fullCost * Order::getOrderSpeedMultiplier($orderID);

			$fullCost = number_format($fullCost, 2, '.', '');

return $fullCost;
		}

		public function createRecordInDB ()
		{
			if (!isset($this->connection))
			{
				$this->getConnection ();
			}

			global $insertID;

			$query = "INSERT INTO labors (LABOR_ID, LABOR_ORDER_ID,LABOR_FASTCODE, LABOR_NAME, LABOR_STATE_ID, LABOR_QUANTITY, LABOR_PRICE, LABOR_DESCRIPTION, LABOR_START_DATE, LABOR_END_DATE)
VALUES
	(
		NULL,
		'$this->orderID',
		'$this->laborFastCode',
		'$this->name',
		'$this->stateID',
		'$this->quantity',
		'$this->price',
		'$this->description',
		'$this->startDate',
		'$this->endDate')";

			$result = queryMysql ($query);


			if ($result)
			{
				$this->laborID = $insertID;
			} else
			{
				$this->laborID = 0;
			}

			if (!$result)
			{
				die ($this->connection->error);
			} else
			{
				return 1;
			}
		}

		public function deleteRecordFromDB ($recordID)
		{
			if (!isset($this->connection))
			{
				$this->getConnection ();
			}

			$this->fullCost = 0;

			$sql = "DELETE FROM labors WHERE LABOR_ID = $recordID";

			queryMysql ($sql);
			return 1;
		}

		public function editRecordInDB ($recordID)
		{


			$query = "UPDATE devices
SET
	NAME            = '$this->name',
	DESCRIPTION     = '$this->description',
	MANUFACTURER_ID = '$this->manufacturerID',
	CATEGORY_ID     = '$this->categoryID',
	TYPE_ID         = '$this->typeID',
	SERIAL          = '$this->serial',
	MODEL_ID        = '$this->modelID',
	OWNER_ID        = '$this->ownerID',
	LAST_EDIT       = '$this->lastEdit',
	DELETED         = '$this->deleted',
	META            = '$this->meta'

WHERE ID = '$recordID'";
			$result = queryMysql ($query);
			if (!$result)
			{
				die ($this->connection->error);
			} else
			{
				Echo '<B>Изменения приняты</B>';
			}
		}

		public function getRecordInfo (&$laborID)
		{

			$result = queryMysql ("SELECT
	LABOR_ID,
	LABOR_ORDER_ID,
	LABOR_FASTCODE,
	LABOR_NAME,
	LABOR_STATE_ID AS STATE_ID,
	LABOR_QUANTITY,
	LABOR_PRICE,
	LABOR_DESCRIPTION,
	LABOR_START_DATE,
	LABOR_END_DATE,
	stat.NAME      AS STATE,
	stat.COLOR     AS STATE_COLOR

FROM labors lab
	INNER JOIN labors_state stat ON lab.LABOR_STATE_ID = stat.ID
WHERE lab.LABOR_ID = '$laborID'"
			);

			if ($result->num_rows)
			{
				return $result->fetch_array (MYSQLI_ASSOC);
			} else
			{
				return 0;
			}
		}

		public function initFromDB (&$laborID)
		{
			if (!isset($this->connection))
			{
				$this->getConnection ();
			}

			$sql = "SELECT
	LABOR_ID,
	LABOR_ORDER_ID,
	LABOR_FASTCODE,
	LABOR_NAME,
	LABOR_STATE_ID AS STATE_ID,
	LABOR_QUANTITY,
	LABOR_PRICE,
	LABOR_DESCRIPTION,
	LABOR_START_DATE,
	LABOR_END_DATE,
	stat.NAME      AS STATE,
	stat.COLOR     AS STATE_COLOR

FROM labors lab
	INNER JOIN labors_state stat ON lab.LABOR_STATE_ID = stat.ID
WHERE lab.LABOR_ID = '$laborID'";

			$result = queryMysql ($sql);

			if ($result->num_rows)
			{
				$this->num = $result->num_rows;
				$this->row = $result->fetch_array (MYSQLI_ASSOC);

				$this->laborID = $this->row ['LABOR_ID'];
				$this->orderID = $this->row ['LABOR_ORDER_ID'];
				$this->laborFastCode = $this->row ['LABOR_FASTCODE'];
				$this->name = $this->row ['LABOR_NAME'];
				$this->stateID = $this->row ['STATE_ID'];
				$this->quantity = $this->row ['LABOR_QUANTITY'];
				$this->price = $this->row ['LABOR_PRICE'];
				$this->description = $this->row ['LABOR_DESCRIPTION'];
				$this->startDate = $this->row ['LABOR_START_DATE'];
				$this->endDate = $this->row ['LABOR_END_DATE'];
				$this->stateSTR = $this->row ['STATE'];
				$this->stateColor = $this->row ['STATE_COLOR'];


			} else
			{
				$result->close ();

				return 0;
			}
			$result->close ();

			return 1;
		}

		public function initFromPOST ()
		{
			date_default_timezone_set ('Europe/Kiev');

			$this->laborFastCode = post ('speedCode');
			$this->stateID = 1; /////-------------------------------TEMP
			$this->stateSTR = '';
			$this->name = post ('laborName');
			$this->description = post ('laborDescription');
			$this->quantity = post ('laborQuantity');
			$this->price = post ('laborCost');
			$this->startDate = date ('Y-m-d H:i:s');

			$this->meta = ($this->post ('devicemeta') === 'on') ? 1 : 0;
		}

		public function initMetaObject ()
		{
			$this->orderID = 0;
			$this->laborID = 0;
			$this->laborFastCode = '000';
			$this->stateID = 3;
			$this->name = 'Задача для заявки';
			$this->description = 'Автоматически созданная задача';
			$this->quantity = 1;
			$this->price = 0;
			$this->startDate = date ('d-m-Y H:i:s');
			$this->meta = '1';
		}

		public function echoListOfLabors ($orderID)
		{

			if (!isset($this->connection))
			{
				$this->getConnection ();
			}

			$order = new Order('DB',$orderID);


			$sql = "SELECT
	LABOR_ID,
	LABOR_ORDER_ID,
	LABOR_FASTCODE,
	LABOR_NAME,
	LABOR_STATE_ID AS STATE_ID,
	LABOR_QUANTITY,
	LABOR_PRICE,
	LABOR_DESCRIPTION,
	LABOR_START_DATE,
	LABOR_END_DATE,
	stat.NAME      AS STATE,
	stat.COLOR     AS STATE_COLOR

FROM labors lab
	INNER JOIN labors_state stat ON lab.LABOR_STATE_ID = stat.ID
WHERE lab.LABOR_ORDER_ID = '$orderID'";


			$result = queryMysql ($sql);
			for ($j = 0; $j < $result->num_rows; ++$j)
			{
				$row = $result->fetch_array (MYSQLI_ASSOC);

				$no = $j + 1;
				$name = $row['LABOR_NAME'];
				$state = $row['STATE'];
				$start = $row['LABOR_START_DATE'];
				$color = $row['STATE_COLOR'];
				$id = $row['LABOR_ID'];
				$price = $row['LABOR_PRICE'];
				$quantity = $row['LABOR_QUANTITY'];

				if($order->orderStateID !== '2')
				{
					$deletestring = "<span class = \"pull-right\"><a href = \"/labor/delete/$id\"><i class = \"glyphicon glyphicon-trash text-danger\"> </i> </a></span>";
				}
				else
				{
					$deletestring = '';
				}

				echo <<<HTML
<li class = "list-group-item">
	<span class = "glyphicon glyphicon-pushpin"></span>
	$no. <strong>"$name"</strong>
	сейчас<font color = "$color"> $state</font>
	Начало: $start.
	Количество: $quantity, Стоимость: <strong>$price</strong> грн.
	$deletestring
	<span class = "pull-right"><a href = "/labor/view/$id"> <i class = "glyphicon glyphicon-list-alt"></i> </a></span>
	</li>
HTML;

			}
		}

		public function echoListOfLaborsForPrint ($orderID)
		{

			if (!isset($this->connection))
			{
				$this->getConnection ();
			}

			$this->fullCost = 0;

			$sql = "SELECT
	LABOR_ID,
	LABOR_ORDER_ID,
	LABOR_FASTCODE,
	LABOR_NAME,
	LABOR_STATE_ID AS STATE_ID,
	LABOR_QUANTITY,
	LABOR_PRICE,
	LABOR_DESCRIPTION,
	LABOR_START_DATE,
	LABOR_END_DATE,
	stat.NAME      AS STATE,
	stat.COLOR     AS STATE_COLOR

FROM labors lab
	INNER JOIN labors_state stat ON lab.LABOR_STATE_ID = stat.ID
WHERE lab.LABOR_ORDER_ID = '$orderID'";

			$html = '';

			$result = queryMysql ($sql);
			for ($j = 0; $j < $result->num_rows; ++$j)
			{
				$row = $result->fetch_array (MYSQLI_ASSOC);

				$no = $j + 1;
				$name = $row['LABOR_NAME'];
				$code = $row['LABOR_FASTCODE'];
				$quantity = $row['LABOR_QUANTITY'];
				$price = $row['LABOR_PRICE'];
				$description = $row['LABOR_DESCRIPTION'];

				$sum = $price * $quantity;
				$this->fullCost += $sum;

				$html .= <<<HTML
<tr>
	<td align = "center" valign = "middle"><h5>$no</h5></td>
	<td align = "center" valign = "middle"><h5>$code</h5></td>
	<td align = "center" valign = "middle"><h5>$name</h5></td>
	<td align = "left" valign = "middle"><h5>$description</h5></td>
	<td align = "center" valign = "middle"><h5>$quantity</h5></td>
	<td align = "center" valign = "middle"><h5>$price грн.</h5></td>
	<td align = "center" valign = "middle"><h5>$sum грн.</h5></td>
</tr>
HTML;


			}

			return $html;
		}

		public function invoiceListOfLabors ($orderID)
		{

			if (!isset($this->connection))
			{
				$this->getConnection ();
			}

			$this->fullCost = 0;

			$sql = "SELECT
	LABOR_ID,
	LABOR_ORDER_ID,
	LABOR_FASTCODE,
	LABOR_NAME,
	LABOR_STATE_ID AS STATE_ID,
	LABOR_QUANTITY,
	LABOR_PRICE,
	LABOR_DESCRIPTION,
	LABOR_START_DATE,
	LABOR_END_DATE,
	stat.NAME      AS STATE,
	stat.COLOR     AS STATE_COLOR

FROM labors lab
	INNER JOIN labors_state stat ON lab.LABOR_STATE_ID = stat.ID
WHERE lab.LABOR_ORDER_ID = '$orderID'";

			$html = '';

			$result = queryMysql ($sql);
			for ($j = 0; $j < $result->num_rows; ++$j)
			{
				$row = $result->fetch_array (MYSQLI_ASSOC);

				$no = $j + 1;
				$name = $row['LABOR_NAME'];
				$quantity = $row['LABOR_QUANTITY'];
				$price = $row['LABOR_PRICE'];
				$sum = $price * $quantity;
				$this->fullCost += $sum;

				$html .= <<<HTML
<TR VALIGN=TOP>
			<TD WIDTH=206 STYLE="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1.50pt solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0in">
				<P  CLASS="western" >$no. $name
				</P>
			</TD>
			<TD WIDTH=43 STYLE="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0in">
				<P  CLASS="western" >шт.
				</P>
			</TD>
			<TD WIDTH=43 STYLE="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0in">
				<P  CLASS="western" >$quantity
				</P>
			</TD>
			<TD WIDTH=80 STYLE="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0in">
				<P  CLASS="western" >$price грн.
				</P>
			</TD>
			<TD WIDTH=80 STYLE="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1.50pt solid #000000; padding: 0in 0.08in">
				<P  CLASS="western" >$sum грн.
				</P>
			</TD>
		</TR>
HTML;

			}

			return $html;
		}

		public function echoLaborsTemplate ($panelType = 1, $panelName = 'NAME',$orderID)
		{
			/*
			 * Type 0. primary
			 *      1. success
			 *      2. info
			 *      3. default
			 *      4. danger
			 *      5. warning
			 */




			switch ($panelType)
			{
				case 0:
					$panel = 'primary';
					break;
				case 1:
					$panel = 'success';
					break;
				case 2:
					$panel = 'info';
					break;
				case 3:
					$panel = 'default';
					break;
				case 4:
					$panel = 'danger';
					break;
				case 5:
					$panel = 'warning';
					break;
				default:
					$panel = 'default';
					break;

			}

			if (!isset($this->connection))
			{
				$this->getConnection ();
			}

			$sql = "SELECT * FROM labors_template WHERE LABOR_TEMPLATE_ID = $panelType";

			echo <<<HTML
<div class='panel panel-$panel'>
		<div class='panel-heading'>$panelName</div>
		<div class='panel-body'>
			<div class="list-group">
HTML;

			$result = queryMysql ($sql);
			for ($j = 0; $j < $result->num_rows; ++$j)
			{
				$row = $result->fetch_array (MYSQLI_ASSOC);

				$id = $row['LABOR_ID'];
				$name = $row['LABOR_NAME'];
				$code = $row['LABOR_FASTCODE'];
				$quantity = $row['LABOR_QUANTITY'];
				$price = $row['LABOR_PRICE'];
				$description = $row['LABOR_DESCRIPTION'];
				$bage = $row['LABOR_BAGE'];


				echo "<a    href='/labor/template/$id/order/$orderID'
				            class='list-group-item list-group-item-$bage' alt='$description''>
				            $code. $name (
				            $quantity шт. -
				            <strong>$price.00 грн.</strong>)</a>";
			}
			echo <<<HTML
			</div>
		</div>
	</div>
HTML;


		}
	}