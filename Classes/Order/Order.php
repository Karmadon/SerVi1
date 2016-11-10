-<?php
	/**
	 * Created by PhpStorm.
	 * User: strem
	 * Date: 30.08.2015
	 * Time: 14:24
	 */

	require_once __DIR__ . '/../../functions.php';
	require_once __DIR__ . '/../../Classes/Classes.php';

	class Order extends
		Records
	{
		public
			$orderId,
			$orderDate,
			$orderName,
			$clientID,
			$clientSTR,
			$deviceID,
			$deviceSTR,
			$orderTypeID,
			$orderTypeSTR,
			$technicianID,
			$technicianSTR,
			$tillDate,
			$orderSpeedID,
			$orderSpeedSTR,
			$orderStateID,
			$orderStateSTR,
			$description,
			$closeDate,
			$closeMessage,
			$color,
			$isReadyToCreate,
		$isPayed;

		static public function echoListFromDB($orderBy = 'DATE DESC', $limit = '50', $where = '1')
		{
			if (!isset($connection))
			{
				getConnection();
			}

			$orderBy = 'ORDER BY ' . $orderBy;
			$limit = 'LIMIT ' . $limit;
			$where = 'WHERE ' . $where;

			$sql = <<<SQL
SELECT
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
	sta.STATUS_COLOR    AS COLOR,
	sta.BAGE            AS BAGE,
	PAYED               AS PAYED
FROM orders ord
	INNER JOIN order_speed spd
		ON ord.ORDER_SPEED_ID = spd.ID
	INNER JOIN order_types typ
		ON ord.ORDER_TYPE_ID = typ.ID
	INNER JOIN order_states sta
		ON ord.ORDER_STATE_ID = sta.ID
	INNER JOIN clients cli
		ON ord.ORDER_CLIENT_ID = cli.ID
	INNER JOIN devices dev
		ON ord.ORDER_DEVICE_ID = dev.ID
	INNER JOIN users usr
		ON ord.ORDER_TECHNICIAN_ID = usr.ID
$where
$orderBy
$limit
SQL;



			echo <<< HTML
				<div class = "table-responsive">
					<table class = "table table-hover">
						<thead>
						<tr>
							<th>Заявка</th>
							<th>Стикер</th>
							<th>Название заявки</th>
							<th>Дата начала</th>
							<th>Срочность</th>
							<th>Статус заявки</th>
						</tr>
						</thead>
						<tbody>
HTML;


			$result = queryMysql($sql);

			for ($j = 0; $j < $result->num_rows; ++$j)
			{
				$row = $result->fetch_array(MYSQLI_ASSOC);

				if($row['PAYED'])
				{
					$echoPaidStr = '<span class = "label label-success">Оплачена</span>';
				}
				else
				{
					if ($row['STATE_ID'] === '2')
					{
						$echoPaidStr = '<span class = "label label-danger">Не оплачена</span>';
					}
					else
					{
						$echoPaidStr = '';
					}
				}


				echo '<tr class="' . $row['BAGE'] . '">
                                    <td>' . $row['ID'] . '</td>
                                    <td><a href="/device/view/' . $row['DEVICE_ID'] . '">' . $row['DEVICE_ID'] . '</a></td>
                                    <td><a href="/order/view/' . $row['ID'] . '">' . $row['NAME'] . '</a></td>
                                    <td>' . date_format(date_create($row['DATE']), 'd.m.Y') . '</td>
                                    <td>' . $row['SPEED'] . '</td>
                                    <td nowrap><span class = "label label-' . $row['BAGE'] . '">' . $row['STATE'] . '</span> '.$echoPaidStr.'</p></td>
                            </tr>';

			}

			echo <<<HTML
						</tbody>
					</table>
				</div>
HTML;

		}
		static public function echoListFromDBSmall($orderBy = 'DATE DESC', $limit = '50', $where = '1')
		{
			if (!isset($connection))
			{
				getConnection();
			}

			$orderBy = 'ORDER BY ' . $orderBy;
			$limit = 'LIMIT ' . $limit;
			$where = 'WHERE ' . $where;

			$sql = <<<SQL
SELECT
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
	sta.STATUS_COLOR    AS COLOR,
	sta.BAGE            AS BAGE,
	PAYED               AS PAYED
FROM orders ord
	INNER JOIN order_speed spd
		ON ord.ORDER_SPEED_ID = spd.ID
	INNER JOIN order_types typ
		ON ord.ORDER_TYPE_ID = typ.ID
	INNER JOIN order_states sta
		ON ord.ORDER_STATE_ID = sta.ID
	INNER JOIN clients cli
		ON ord.ORDER_CLIENT_ID = cli.ID
	INNER JOIN devices dev
		ON ord.ORDER_DEVICE_ID = dev.ID
	INNER JOIN users usr
		ON ord.ORDER_TECHNICIAN_ID = usr.ID
$where
$orderBy
$limit
SQL;


			$result = queryMysql($sql);
			if(!$result->num_rows) return 0;

			echo <<< HTML
			                  <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4>Открытые заявки</h4></div>
                        <div class="panel-body">

				<div class = "table-responsive">
					<table class = "table table-hover">
						<thead>
						<tr>
							<th>Название заявки</th>
							<th>Срочность</th>
							<th>Статус заявки</th>
						</tr>
						</thead>
						<tbody>
HTML;



			for ($j = 0; $j < $result->num_rows; ++$j)
			{
				$row = $result->fetch_array(MYSQLI_ASSOC);

				if($row['PAYED'])
				{
					$echoPaidStr = '<span class = "label label-success">Оплачена</span>';
				}
				else
				{
					if ($row['STATE_ID'] === '2')
					{
						$echoPaidStr = '<span class = "label label-danger">Не оплачена</span>';
					}
					else
					{
						$echoPaidStr = '';
					}
				}


				echo '<tr class="' . $row['BAGE'] . '">

                                    <td><a href="/order/view/' . $row['ID'] . '">' . $row['NAME'] . '</a></td>

                                    <td>' . $row['SPEED'] . '</td>
                                    <td><span class = "label label-' . $row['BAGE'] . '">' . $row['STATE'] . '</span> '.$echoPaidStr.'</p></td>
                            </tr>';

			}

			echo <<<HTML
						</tbody>
					</table>
				</div>
				</div>
</div>

HTML;

		}
		static public function echoMYListFromDBSmall($orderBy = 'DATE DESC', $limit = '50')
		{
			if (!isset($connection))
			{
				getConnection();
			}

			$orderBy = 'ORDER BY ' . $orderBy;
			$limit = 'LIMIT ' . $limit;
			$where = 'WHERE ORDER_TECHNICIAN_ID=\''.$_SESSION['loginId'].'\' AND ORDER_STATE_ID=1';

			$sql = <<<SQL
SELECT
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
	sta.STATUS_COLOR    AS COLOR,
	sta.BAGE            AS BAGE,
	PAYED               AS PAYED
FROM orders ord
	INNER JOIN order_speed spd
		ON ord.ORDER_SPEED_ID = spd.ID
	INNER JOIN order_types typ
		ON ord.ORDER_TYPE_ID = typ.ID
	INNER JOIN order_states sta
		ON ord.ORDER_STATE_ID = sta.ID
	INNER JOIN clients cli
		ON ord.ORDER_CLIENT_ID = cli.ID
	INNER JOIN devices dev
		ON ord.ORDER_DEVICE_ID = dev.ID
	INNER JOIN users usr
		ON ord.ORDER_TECHNICIAN_ID = usr.ID
$where
$orderBy
$limit
SQL;
			$result = queryMysql($sql);
			if(!$result->num_rows) return 0;


			echo <<< HTML
			                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4>Мои заявки</h4></div>
                        <div class="panel-body">
				<div class = "table-responsive">
					<table class = "table table-hover">
						<thead>
						<tr>
							<th>Название заявки</th>
							<th>Срочность</th>
							<th>Статус заявки</th>
						</tr>
						</thead>
						<tbody>
HTML;




			for ($j = 0; $j < $result->num_rows; ++$j)
			{
				$row = $result->fetch_array(MYSQLI_ASSOC);

				if($row['PAYED'])
				{
					$echoPaidStr = '<span class = "label label-success">Оплачена</span>';
				}
				else
				{
					if ($row['STATE_ID'] === '2')
					{
						$echoPaidStr = '<span class = "label label-danger">Не оплачена</span>';
					}
					else
					{
						$echoPaidStr = '';
					}
				}


				echo '<tr class="' . $row['BAGE'] . '">

                                    <td><a href="/order/view/' . $row['ID'] . '">' . $row['NAME'] . '</a></td>

                                    <td>' . $row['SPEED'] . '</td>
                                    <td><span class = "label label-' . $row['BAGE'] . '">' . $row['STATE'] . '</span> '.$echoPaidStr.'</p></td>
                            </tr>';

			}

			echo <<<HTML
						</tbody>
					</table>
				</div>
				</div></div>



HTML;

		}
		static public function getOrderSpeedMultiplier($order)
		{
			if (!isset($connection))
			{
				getConnection();
			}

			$sql = <<<SQL
SELECT spd.MULTIPLIER AS MULTIPLIER FROM orders ord INNER JOIN order_speed spd ON ord.ORDER_SPEED_ID = spd.ID WHERE ORDER_ID = '$order';
SQL;
			echo $sql;
			$result = queryMysql($sql);
			if(!$result->num_rows) return 1;
				$row = $result->fetch_array (MYSQLI_ASSOC);
			return $row['MULTIPLIER'];
		}

		static public function markOrderPayed($orderID)
		{
			if (!isset($connection))
			{
				getConnection();
			}

			$sql = "UPDATE orders SET PAYED='1' WHERE ORDER_ID = '$orderID'";

			$result = queryMysql($sql);

			if (!$result)
			{
				makeRecordInHistory('6', '3', $orderID, "Ошибка при оплате заявки № <b>$orderID</b>");
				die ($connection->error);
			} else
			{
				makeRecordInHistory('2', '3', $orderID, "Оплачена заявка №<b>$orderID</b>");
			}
		}

		public function createRecordInDB()
		{

			global $insertID;

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
	ORDER_DESCRIPTION,
	PAYED)
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
		'$this->description',
		0
		)";

			$result = queryMysql($querry);
			$this->orderId = $insertID;

			if (!$result)
			{
				makeRecordInHistory('6', '3', $this->connection->insert_id, 'Ошибка при вставке записи');
				die ($this->connection->error);
			} else
			{
				makeRecordInHistory('1', '3', $this->orderId, "Добавлена заявка <b>$this->orderName</b>");

				return 1;
			}


		}

		public function deleteRecordFromDB($recordID)
		{
			//TODO: Implement delete

		}

		public function editRecordInDB($recordID)
		{
			if (!isset($this->connection))
			{
				$this->getConnection();
			}
			$query = <<<SQL
UPDATE orders
SET
	ORDER_DATE          = '$this->orderDate',
	ORDER_NAME          = '$this->orderName',
	ORDER_CLIENT_ID     = '$this->clientID',
	ORDER_DEVICE_ID     = '$this->deviceID',
	ORDER_TYPE_ID       = '$this->orderTypeID',
	ORDER_TECHNICIAN_ID = '$this->technicianID',
	ORDER_TILL_DATE     = '$this->tillDate',
	ORDER_SPEED_ID      = '$this->orderSpeedID',
	ORDER_STATE_ID      = '$this->orderStateID',
	ORDER_DESCRIPTION   = '$this->description',
	ORDER_CLOSE_DATE    = '$this->closeDate',
	ORDER_CLOSE_MESSAGE = '$this->closeMessage'
WHERE ORDER_ID = '$recordID'
SQL;
			$result = queryMysql($query);

			if (!$result)
			{
				makeRecordInHistory('6', '3', $recordID, 'Ошибка при изменении записи');
				die ($this->connection->error);
			} else
			{
				makeRecordInHistory('2', '3', $recordID, "Изменена заявка <b>$this->orderName</b>");
				Echo <<<HTML

	<div class = 'col-lg-7'>
<div id = "myAlert" class = "alert alert-success">
   <a href = "#" class = "close" data-dismiss = "alert">&times;</a>
   <strong>Изменения приняты!</strong>.
</div>
</div>


<script type = "text/javascript">
   $(function(){
      $("#myAlert").bind('closed.bs.alert', function () {
         alert("Alert message box is closed.");
      });
   });
</script>
HTML;

			}


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
  sta.STATUS_COLOR    AS COLOR,
  PAYED               AS PAYED
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
				$this->isPayed = $this->row['PAYED'];
			}

			$result->close();
		}

		public function initFromPOST()
		{
			$this->orderName = post('ordername');
			$this->technicianID = $_SESSION ['loginId'];
			$this->orderTypeID = post('ordertype');
			$this->orderSpeedID = post('orderspeed');
			$this->orderStateID = post('orderstatus');
			$this->description = post('orderdescription');

		}

		public function initFromPOSTSideBar()
		{
			$this->orderSpeedID = post('orderspeed');
			$this->orderStateID = post('orderstatus');
			$this->technicianID = post('ordertech');
		}

		public function initFromPOSTCloseMessage()
		{
			$this->closeMessage = post('closemessage');
		}

		public function initFromObjects(Client &$client, Device &$device)
		{

			$this->orderDate = date('Y-m-d H:i:s');
			$this->orderName = 'Заявка для (' . $device->name . ')';
			$this->clientID = $client->id;
			$this->clientSTR = $client->ScreenName;
			$this->deviceID = $device->id;
			$this->deviceSTR = $device->name;
			$this->technicianID = $_SESSION ['loginId'];;
			$this->technicianSTR = $_SESSION ['login'];
			$this->tillDate = date('Y-m-d G:i:s', mktime(date('G'), date('i'), date('s'), date('m'), date('d') + 1, date('Y')));

			$this->isReadyToCreate = ($device->meta || $client->meta) ? 0 : 1;

		}

		public function echoDeviceHistory($deviceID)
		{
			if (!isset($this->connection))
			{
				$this->getConnection();
			}

			$sql = "SELECT `ORDER_ID`,`ORDER_DATE`,`ORDER_NAME`, state.STATE, BAGE FROM orders ordr INNER JOIN order_states state ON ordr.ORDER_STATE_ID = state.ID WHERE `ORDER_DEVICE_ID` = '$deviceID' ORDER BY ORDER_DATE DESC LIMIT 10";
			$result = queryMysql($sql);

			for ($j = 0; $j < $result->num_rows; ++$j)
			{
				$row = $result->fetch_array(MYSQLI_ASSOC);

				echo '<tr class="' . $row['BAGE'] . '">
        <td>' . showDate(strtotime($row['ORDER_DATE'])) . ' (' . date_format(date_create($row['ORDER_DATE']),
				                                                             'd.m.Y'
					) . ')</td>
        <td>' . $row['ORDER_ID'] . '</td>
        <td><a href="/order/view/' . $row['ORDER_ID'] . '">' . $row['ORDER_NAME'] . '</a></td>
        <td><span class="label label-' . $row['BAGE'] . '">' . $row['STATE'] . '</span></td>
      </tr>';
			}

		}

		public function echoRightSideBar(Client $client, Device $device, $form = 'SideBar', $autoSubmit = 1, $orderID = 0, $disabled=0)
		{
			if ($form === 'SideBar')
			{
				$formHTML = "<form id='$form' class='form-horizontal' method='post' action='/order/view/$orderID'>";
			} else
			{
				$formHTML = '';
			}

			if($client->email)
			{
				$emailBTN =<<<HTML

				<div class="btn-group">
				<button type="button"
				        class="btn btn-info dropdown-toggle"
				        data-toggle="dropdown"
				        aria-haspopup="true"
				        aria-expanded="false"><i class="glyphicon glyphicon-envelope"></i> email <span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<li><a href="/print/ticket/$this->orderId/email/yes" target="_blank">отпроавить Квитанцию</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="/print/receipt/$this->orderId/email/yes" target="_blank">отправить Счет</a></li>
					<li><a href="/print/invoice/$this->orderId/email/yes" target="_blank">отправить Счет-Фактуру</a></li>

				</ul>
			</div>
HTML;

			}
			else
			{
				$emailBTN = '';

			}

			if($this->isPayed)
			{
				$panelColor = 'success';
				$echoIsPayed = '<span class="bg-success">Заявка оплачена</span>';
				$echoPayed = "<li><a  href='/payment/view/$this->orderId'>Найти платеж</a></li>";

			} else
			{
				$panelColor = 'danger';
				$echoIsPayed = '<span class="bg-danger"><strong>Неоплаченая</strong> Заявка</span>';
				$echoPayed = "	<li><a href='/payment/new/$this->orderId/drcr/in/cash/yes'>Наличный Платеж</a></li>
								<li><a href='/payment/new/$this->orderId/drcr/in/cash/no'>Безналичный Платеж</a></li>";
			}

			if (!$disabled)
			{
				$panelColor = 'info';
				$echoIsPayed = '';

				$DocBTN = <<<HTML
			<div class="btn-group">
				<button type="button"
				        class="btn btn-info dropdown-toggle"
				        data-toggle="dropdown"
				        aria-haspopup="true"
				        aria-expanded="false">
				        <i class="glyphicon glyphicon-list-alt"> </i>
				        Тех. Документы <span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<li><a href="/print/ticket/$this->orderId" target="_blank">Квитанция</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="/print/tech_copy/$this->orderId" target="_blank">Техническая копия</a></li>
				</ul>
			</div>
			$emailBTN
HTML;
			} else
			{
				$DocBTN = <<<HTML
<div class="btn-group">
				<button type="button"
				        class="btn btn-warning dropdown-toggle"
				        data-toggle="dropdown"
				        aria-haspopup="true"
				        aria-expanded="false">
				        <i class="glyphicon glyphicon-usd"></i>
					 Оплата заявки <span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					$echoPayed
				</ul>
			</div>
			$emailBTN
HTML;
			}

			if (!isset($this->orderSpeedID)) $this->orderSpeedID=2;

			echo <<<HTML
<!--right SideBar-->
	<div class = "col-sm-3">
		<a href="#"><strong><i class="glyphicon glyphicon-dashboard"></i> Управление заявкой</strong></a>
	<hr>

		<div class = "panel panel-$panelColor">
			<div class = "panel-heading">Заявка</div>
			<div class = "panel-body">

<h2>$echoIsPayed</h2>

			$formHTML
			<label for="orderstate">Статус заявки</label>
HTML;
			$this->echoComboControl('order_states',
			                        'STATE',
			                        '-Статус Заявки-',
			                        'orderstatus',
			                        "$form",
			                        $this->orderStateID,
			                        $autoSubmit,
			                        $disabled
			);
			echo <<<HTML
		<p class="help-block">Статус завки можно менять несколько раз.</p>
				<label for="orderstate">Срочность заявки</label>
HTML;
			$this->echoComboControl('order_speed',
			                        'SPEED',
			                        '-Срочность Заявки-',
			                        'orderspeed',
			                        "$form",
			                        $this->orderSpeedID,
			                        $autoSubmit,
			                        $disabled
			);

			$metaDeviceSTR = ($device->meta) ? '<font color="red">Да</font>' : '<font color="green">Нет</font>';
			$metaClientSTR = ($client->meta) ? '<font color="red">Да</font>' : '<font color="green">Нет</font>';
			echo <<<HTML

<p class="help-block">Срочность заявки влияет на конечную стоимость заявки.</p>
<label for="orderstate">Назначеный техник</label>
HTML;
			$this->echoComboControl('users',
			                        'login',
			                        '-Техник-',
			                        'ordertech',
			                        "$form",
			                        $this->technicianID,
			                        $autoSubmit,
			                        $disabled
			);
			echo<<<HTML
			<p class="help-block">Техник выполняемый данную заявку.</p>
</form>
</div>
</div>
<hr>
<div class="panel panel-success">
	<div class="panel-heading">Документы</div>
	<div class="panel-body">
		<center>
			<!-- Single button -->
			<div class="btn-group">
				<button type="button"
				        class="btn btn-success dropdown-toggle"
				        data-toggle="dropdown"
				        aria-haspopup="true"
				        aria-expanded="false">
				        <i class="glyphicon glyphicon-shopping-cart"> </i>
					 Счета <span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<li><a href="/print/invoice/$this->orderId" target="_blank">Счет-Фактура</a></li>
					<li><a href="/print/receipt/$this->orderId" target="_blank">Счет</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="#">Квитанция об оплате</a></li>
				</ul>
			</div>

			<!-- Single button -->
			$DocBTN
		</center>
	</div>
</div>
<div class="panel panel-default">
	<div class="panel-heading">Устройство <a href="/device/view/$device->id"><i>
		открыть</i></a> / <a href="/device/edit/$device->id"><i> редактировать</i></a>
	</div>
	<div class="panel-body">
		<table class="table table-bordered table-striped table-hover">
			<tbody>
			<tr>
				<td>Стикер</td>
				<td><a href="/device/view/$device->id">$this->deviceID</a></td>
			</tr>
			<tr>
				<td>Устройство</td>
				<td>$device->name</td>
			</tr>
			<tr>
				<td>Производитель</td>
				<td>$device->manufacturerSTR</td>
			</tr>
			<tr>
				<td>Модель</td>
				<td>$device->modelSTR</td>
			</tr>
			<tr>
				<td><b>Серийный номер</td>
				<td><b>$device->serial</td>
			</tr>
			<tr>
				<td>Незаконченный</td>
				<td>$metaDeviceSTR</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>
<hr>

<div class="panel panel-default">
	<div class="panel-heading">Владелец устройства <a href="/client/view/$client->id"><i>
		открыть</i></a> / <a href="/client/edit/$client->id"><i> редактировать</i></a>
	</div>
	<div class="panel-body">
		<table class="table table-bordered table-striped table-hover">
			<tbody>
			<tr>
				<td>Отображаемое Имя</td>
				<td> $client->ScreenName</td>
			</tr>
			<tr>
				<td>Фамилия, Имя</td>
				<td>$client->LastName, $client->FirstName</td>
			</tr>
			<tr>
				<td>Организация</td>
				<td>$client->organizationName</td>
			</tr>
			<tr>
				<td>Основной телефон</td>
				<td>$client->phone1</td>
			</tr>
			<tr>
				<td>Дополнительный телефон</td>
				<td>$client->phone2</td>
			</tr>
			<tr>
				<td>email</td>
				<td>$client->email</td>
			</tr>
			<tr>
				<td>Сайт</td>
				<td>$client->website</td>
			</tr>
			<tr>
				<td>Адрес</td>
				<td>$client->city, $client->adress</td>
			</tr>
			<tr>
				<td>Незаконченный</td>
				<td>$metaClientSTR</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>
<hr>
<div class="panel panel-default">
	<div class="panel-heading">Описание заявки</div>
	<div class="panel-body">
		$this->description
	</div>
</div>
<hr>
<div class="panel panel-default">
	<div class="panel-heading">Дополнительно</div>
	<div class="panel-body">Пока незнаю что написать..</div>
</div>
<hr>
</div>
<!--/right SideBar-->
HTML;

		}

		public function echoOrderFilters()
		{
			if (!isset($this->connection))
			{
				$this->getConnection();
			}

			$sql = 'SELECT * FROM adminosi_ignite.order_states';
			$result = queryMysql($sql);

			for ($j = 0; $j < $result->num_rows; ++$j)
			{
				$row = $result->fetch_array(MYSQLI_ASSOC);

				$stateID = $row['ID'];
				$stateSTR = $row['STATE'];
				$bage = $row['BAGE'];

				$sql = "SELECT COUNT(*) FROM orders WHERE ORDER_STATE_ID = '$stateID'";
				$countResult = queryMysql($sql);
				$rowCount = $countResult->fetch_array(MYSQLI_ASSOC);
				$count = $rowCount['COUNT(*)'];
				if ($count)
				{
					$count = "<span class='badge'>$count</span>";
				} else
				{
					$count = '';
				}

				echo <<<HTML
<a href="/order/list/1/state/$stateID" class="list-group-item list-group-item-$bage">$stateSTR $count</a>
HTML;

			}

		}

	}
