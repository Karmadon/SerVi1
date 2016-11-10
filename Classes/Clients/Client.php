<?php
	/**
	 * Created by PhpStorm.
	 * User: Karmadon
	 * Date: 15.09.2015
	 * Time: 0:49
	 */

	require_once __DIR__ . '/../../Classes/Common/Records.php';
	require_once __DIR__ . '/../../config.php';


	class Client extends
		Records
	{
		public
			$id,
			$organizationName,
			$ScreenName,
			$FirstName,
			$LastName,
			$email,
			$phone1,
			$phone2,
			$website,
			$adress,
			$city,
			$balance,
			$username,
			$password,
			$discount,
			$deleted,
			$meta,
			$lastEdit;


		public function createRecordInDB()
		{
			global $insertID;

			if (!isset($this->connection))
			{
				$this->getConnection();
			}

			$query = "INSERT INTO clients (ID,
                     ORG_NAME,
                     SCREEN_NAME,
                     FIRSTNAME,
                     LASTNAME,
                     EMAIL,
                     PHONE1,
                     PHONE2,
                     WWW,
                     ADRESS,
                     CITY,
                     BALANCE,
                     CLIENT_LOGIN,
                     CLIENT_PASSWORD,
                     DISCOUNT,
                     DELETED,
                     META)
VALUES (
	NULL,
	'$this->organizationName',
	'$this->ScreenName',
	'$this->FirstName',
	'$this->LastName',
	'$this->email',
	'$this->phone1',
	'$this->phone2',
	'$this->website',
	'$this->adress',
	'$this->city',
	'$this->balance',
	'$this->username',
	'$this->password',
	'$this->discount',
	'$this->deleted',
	'$this->meta')";

			$result = queryMysql($query);
			$this->id = $insertID;

			if (!$result)
			{
				makeRecordInHistory('6', '2', $this->id, "Ошибка при вставке записи ($this->ScreenName)");
				die ($this->connection->error);
			} else
			{
				makeRecordInHistory('1', '2', $this->id, "Добавлен Клиент <b>$this->ScreenName</b>");

				return 1;
			}
		}

		public function editRecordInDB($recordID)
		{
			global $createdID;

			if (!isset($this->connection))
			{
				$this->getConnection();
			}

			if (!isset($this->ScreenName))
			{
				if (isset($this->FirstName) === 0 && isset($this->LastName) === 0)
				{
					if (isset($this->organizationName))
					{
						$this->ScreenName = $this->organizationName;
					} else
					{
						$this->ScreenName = time();
					}
				} else
				{
					$this->ScreenName = $this->LastName . ' ' . $this->FirstName;
				}
			}


			$query = "UPDATE clients
SET
	ORG_NAME        = '$this->organizationName',
	SCREEN_NAME     = '$this->ScreenName',
	FIRSTNAME       = '$this->FirstName',
	LASTNAME        = '$this->LastName',
	EMAIL           = '$this->email',
	PHONE1          = '$this->phone1',
	PHONE2          = '$this->phone2',
	WWW             = '$this->website',
	ADRESS          = '$this->adress',
	CITY            = '$this->city',
	BALANCE         = '$this->balance',
	CLIENT_LOGIN    = '$this->username',
	CLIENT_PASSWORD = '$this->password',
	DISCOUNT        = '$this->discount',
	DELETED         = '$this->deleted',
	META            = '$this->meta'
WHERE ID = '$recordID'";

			$result = queryMysql($query);

			if (!$result)
			{
				makeRecordInHistory('6', '2', $recordID, "Ошибка при изменении записи Клиент <b>$this->ScreenName</b>");
				die ($this->connection->error);
			} else
			{
				makeRecordInHistory('2', '2', $recordID, "Изменен Клиент <b>$this->ScreenName</b>");
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
				return 1;
			}
		}

		public function setBalanceOfClient($amount)
		{
			$this->balance = $amount;
			$clientID = $this->id;



			if (!isset($this->connection))
			{
				$this->getConnection();
			}

			$query = "UPDATE clients SET BALANCE = '$this->balance' WHERE ID = '$clientID'";

			$result = queryMysql($query);

			if (!$result)
			{
				makeRecordInHistory('6', '2', $clientID, "Ошибка при изменении баланса  Клиента <b>$this->ScreenName</b>");
				die ($this->connection->error);
			} else
			{
				makeRecordInHistory('2', '2', $clientID, "Изменен баланс Клиента <b>$this->ScreenName</b> на $this->balance грн.");
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
				return 1;
			}
		}

		public function initFromDB($recordID)
		{
			if (!isset($this->connection))
			{
				$this->getConnection();
			}

			$result = queryMysql("SELECT * FROM clients WHERE ID='$recordID' AND DELETED = '0'");


			if ($result->num_rows)
			{
				$this->num = $result->num_rows;
				$this->row = $result->fetch_array(MYSQLI_ASSOC);

				$this->id = $this->row ['ID'];
				$this->organizationName = $this->row ['ORG_NAME'];
				$this->ScreenName = $this->row ['SCREEN_NAME'];
				$this->FirstName = $this->row ['FIRSTNAME'];
				$this->LastName = $this->row ['LASTNAME'];
				$this->email = $this->row ['EMAIL'];
				$this->phone1 = $this->row ['PHONE1'];
				$this->phone2 = $this->row ['PHONE2'];
				$this->website = $this->row ['WWW'];
				$this->adress = $this->row ['ADRESS'];
				$this->city = $this->row ['CITY'];
				$this->balance = $this->row ['BALANCE'];
				$this->deleted = $this->row ['DELETED'];
				$this->discount = $this->row ['DISCOUNT'];
				$this->meta = $this->row ['META'];
				$this->lastEdit = $this->row ['LastEdit'];


			} else
			{

				$result->close();

				return 0;
			}

			$result->close();

			return 1;
		}

		public function initFromPOST()
		{

			$this->id = 0;
			$this->organizationName = post('organizationName');
			$this->ScreenName = post('ScreenName');
			$this->FirstName = post('FirstName');
			$this->LastName = post('LastName');
			$this->email = post('email');
			if (post('phone1')) $this->phone1 = format_phone_number(post('phone1'), 7);
			if (post('phone1')) $this->phone2 = format_phone_number(post('phone2'), 7);
			$this->website = post('website');
			$this->adress = post('adress');
			$this->city = post('city');
			$this->balance = post('balance');
			$this->discount = post('Discount');
			$this->deleted = post('deleted');
			$this->username = post('username');
			$this->password = post('password');
			$this->meta = (post('clientmeta') === 'on') ? 1 : 0;
			$this->lastEdit = date('Y-m-d H:i:s');
		}

		public function initMetaObject()
		{
			$this->id = '0';
			$this->organizationName = 'Организация';
			$this->ScreenName = time();
			$this->FirstName = 'Имя';
			$this->LastName = 'Фамилия';
			$this->email = 'email@host.com';
			$this->phone1 = '(000) 000-00-00';
			$this->website = 'www.example.com';
			$this->adress = 'ул. ';
			$this->city = 'Харьков';
			$this->balance = '0';
			$this->deleted = '0';
			$this->meta = '1';
			$this->lastEdit = date('Y-m-d H:i:s');
		}

		public function echoClientHistory($clientID)
		{
			if (!isset($this->connection))
			{
				$this->getConnection();
			}

			$sql = "SELECT ORDER_ID, ORDER_DEVICE_ID, ORDER_DATE, ORDER_NAME, state.STATE, BAGE, PAYED AS PAYED, ORDER_STATE_ID AS STATE_ID
FROM orders ordr INNER JOIN order_states state
		ON ordr.ORDER_STATE_ID = state.ID
WHERE ORDER_CLIENT_ID = '$clientID'
ORDER BY ORDER_DATE DESC";

			$result = queryMysql($sql);

			echo <<<HTML
<div class = "panel panel-default">
	<div class = "panel-heading">Заявки Клиента</div>
	<div class = "panel-body">
		<section id = "tables">
			<table class = "table table-bordered table-striped table-hover">
				<thead>
				<tr>
					<th>Дата</th>
					<th>№ Заявки</th>
					<th>№ Стикера</th>
					<th>Имя заявки</th>
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
        <td>' . showDate(strtotime($row['ORDER_DATE'])) . ' (' . date_format(date_create($row['ORDER_DATE']),
				                                                             'd.m.Y'
					) . ')</td>
        <td>' . $row['ORDER_ID'] . '</td>
        <td><a href="/device/view/' . $row['ORDER_DEVICE_ID'] . '">' . $row['ORDER_DEVICE_ID'] . '</a></td>
        <td><a href="/order/view/' . $row['ORDER_ID'] . '">' . $row['ORDER_NAME'] . '</a></td>
       <td><span class = "label label-' . $row['BAGE'] . '">' . $row['STATE'] . '</span> '.$echoPaidStr.'</p></td>
      </tr>';
			}
			echo <<<HTML
						</tbody>
			</table>

		</div>
	</div>


HTML;
			//-------------------------------------------------------------------------------------------DEV
			$sql = "
SELECT
  dev.ID              AS ID,
  dev.STICKER         AS STICKER,
  dev.NAME            AS NAME,
  dev.DESCRIPTION     AS DESCRIPTION,
  dev.SERIAL          AS SERIAL,
  dev.LAST_EDIT       AS LAST_EDIT,
  dev.DELETED         AS DELETED,
  dev.META            AS META,
  man.NAME            AS MANUFACTURER,
  cat.NAME            AS CATEGORY,
  modl.NAME           AS MODEL,
  typ.NAME            AS TYPE,
  cli.SCREEN_NAME     AS OWNER,
  dev.OWNER_ID        AS OWNER_ID
FROM devices dev
  INNER JOIN dev_manufacturer man ON dev.MANUFACTURER_ID = man.ID
  INNER JOIN dev_category cat ON dev.CATEGORY_ID = cat.ID
  INNER JOIN dev_models modl ON dev.MODEL_ID = modl.ID
  INNER JOIN dev_type typ ON dev.TYPE_ID = typ.ID
  INNER JOIN clients cli ON dev.OWNER_ID = cli.ID
WHERE dev.OWNER_ID = '$clientID' AND dev.DELETED = '0'";

			$result = queryMysql($sql);

			echo <<<HTML
<div class = "panel panel-success">
	<div class = "panel-heading">Устройства Клиента</div>
	<div class = "panel-body">
		<section id = "tables">
			<table class = "table table-bordered table-striped table-hover">
				<thead>
				<tr>
					<th>Стикер</th>
					<th>Название</th>
					<th>Производитель</th>
					<th>Модель</th>
					<th>Серийный номер</th>
				</tr>
				</thead>
				<tbody>
HTML;


			for ($j = 0; $j < $result->num_rows; ++$j)
			{
				$row = $result->fetch_array(MYSQLI_ASSOC);

				echo '<tr class="' . $row['BAGE'] . '">
        <td><a href="/device/view/' . $row['ID'] . '">' . $row['ID'] . '</a></td>
        <td><a href="/device/view/' . $row['ID'] . '">' . $row['NAME'] . ' (' . $row['TYPE'] . ')</a></td>
        <td>' . $row['MANUFACTURER'] . '</td>
        <td>' . $row['MODEL'] . '</td>
        <td>' . $row['SERIAL'] . '</td>
      </tr>';
			}
			echo <<<HTML
						</tbody>
			</table>

		</div>
	</div>

</div>
</div>
HTML;


		}

	}