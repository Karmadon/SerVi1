<?php
/**
 * Created by PhpStorm.
 * User: strem
 * Date: 30.10.2015
 * Time: 22:17
 */

require_once __DIR__ . '/../Common/Records.php';

class Search extends Records
{
	public $query;

	public function searchInDevices($query)
	{
		if (!isset($this->connection))
		{
			$this->getConnection();
		}

		$sql = "SELECT
  dev.ID              AS ID,
  dev.STICKER         AS STICKER,
  dev.NAME            AS NAME,
  dev.DESCRIPTION     AS DESCRIPTION,
  dev.MANUFACTURER_ID AS MANUFACTURER_ID,
  dev.CATEGORY_ID     AS CATEGORY_ID,
  dev.TYPE_ID         AS TYPE_ID,
  dev.SERIAL          AS SERIAL,
  dev.MODEL_ID        AS MODEL_ID,
  dev.OWNER_ID        AS OWNER_ID,
  dev.LAST_EDIT       AS LAST_EDIT,
  dev.DELETED         AS DELETED,
  dev.META            AS META,
  man.NAME            AS MANUFACTURER,
  cat.NAME            AS CATEGORY,
  modl.NAME           AS MODEL,
  typ.NAME            AS TYPE,
  cli.SCREEN_NAME     AS OWNER

FROM devices dev
  INNER JOIN dev_manufacturer man ON dev.MANUFACTURER_ID = man.ID
  INNER JOIN dev_category cat ON dev.CATEGORY_ID = cat.ID
  INNER JOIN dev_models modl ON dev.MODEL_ID = modl.ID
  INNER JOIN dev_type typ ON dev.TYPE_ID = typ.ID
  INNER JOIN clients cli ON dev.OWNER_ID = cli.ID
WHERE   dev.ID LIKE '%$query%' OR
		dev.NAME LIKE '%$query%' OR
		dev.DESCRIPTION LIKE '%$query%' OR
		dev.SERIAL LIKE '%$query%' OR
		man.NAME LIKE '%$query%' OR
		cat.NAME LIKE '%$query%' OR
		modl.NAME LIKE '%$query%' OR
		typ.NAME LIKE '%$query%' OR
		cli.SCREEN_NAME LIKE '%$query%'
";


		$result = queryMysql($sql);
		if ($result->num_rows)
		{
			$num = $result->num_rows;
			echo <<<HTML
			<div class="panel panel-info">
  <div class="panel-heading">
    <h3 class="panel-title">Устройства</h3>
  </div>
  <div class="panel-body">
<div class="table-responsive">
                        <table class="table table-striped"><!-- cellspacing='0' is important, must stay -->
	<thead>
	<tr>
		<th><a href = "/device/list//order/ID">Стикер</a></th>
		<th><a href = "/device/list//order/NAME">Устройство</a></th>
		<th><a href = "/device/list//order/MANUFACTURER">Производитель</a></th>
		<th><a href = "/device/list//order/MODEL">Модель</a></th>
		<th><a href = "/device/list//order/SERIAL">Серийный №</a></th>
		<th><a href = "/device/list//order/TYPE">Тип</a></th>
		<th><a href = "/device/list//order/OWNER">Владелец</a></th>
		<th>Действия</th>
	</tr>
	</thead>
	<tbody>

HTML;
		} else
		{
			return 1;
		}

		for ($j = 0; $j < $num; ++$j)
		{
			$row = $result->fetch_array (MYSQLI_ASSOC);

			if ($j % 2 == 0)
			{
				$even = " class=\"even\"";
			} else
			{
				$even = '';
			}
			$id = $row ['ID'];
			$name = $row ['NAME'];
			$manufacturer = $row ['MANUFACTURER'];
			$model = $row ['MODEL'];
			$serial = $row ['SERIAL'];
			$type = $row ['TYPE'];
			$ownerID = $row ['OWNER_ID'];
			$owner = $row ['OWNER'];
			if ($row['META'])

			{
				$meta = 'class="danger"';
			} else
			{
				$meta = '';
			}


			echo <<<HTML
<tr $meta>
	<td $even><a
			href = '/device/view/$id'>$id</a></td>
		<td><a href = '/device/view/$id'>$name</a></td>
		<td>$manufacturer</td>
		<td>$model</td>
		<td>$serial</td>
		<td>$type</td>
		<td><a href = '/client/view/$ownerID'>$owner</td>
		<td><a href = "/device/view/$id"><span class="glyphicon glyphicon-list-alt"></span> </a>
		<a href = "/device/edit/$id"> <span class="glyphicon glyphicon-pencil"></span></a>
		<a href = "/order/edit/$id"> <span class="glyphicon glyphicon-wrench"></span></a></td>
</tr><!-- Table Row -->

HTML;
		}
		echo <<<HTML
	</tbody>
	<!-- Table Body -->
</table>
</div>
  </div>
</div>


HTML;
}

	public function searchInClients($query)
	{
		if (!isset($this->connection))
		{
			$this->getConnection();
		}

		$result = queryMysql("SELECT * FROM clients WHERE
															ORG_NAME LIKE '%$query%' OR
															SCREEN_NAME LIKE '%$query%' OR
															FIRSTNAME LIKE '%$query%' OR
															LASTNAME LIKE '%$query%' OR
															EMAIL LIKE '%$query%' OR
															PHONE1 LIKE '%$query%' OR
															PHONE2 LIKE '%$query%' OR
															WWW LIKE '%$query%' OR
															ADRESS LIKE '%$query%' OR
															CLIENT_LOGIN LIKE '%$query%'
															");
		if ($result->num_rows)
		{
			$num = $result->num_rows;
			echo <<<HTML
			            <div class="panel panel-warning">
  <div class="panel-heading">
    <h3 class="panel-title">Клиенты</h3>
  </div>
  <div class="panel-body">

<div class="table-responsive">
                        <table class="table table-striped">
	<thead>
		<tr>
			<th><a href="/client/list//order/ID">Код</a></th>
			<th><a href="/client/list//order/SCREEN_NAME">Отображать как</a></th>
			<th><a href="/client/list//order/ORG_NAME">Организация</a></th>
			<th><a href="/client/list//order/LASTNAME">Фамилия, Имя</a></th>
			<th><a href="/client/list//order/EMAIL">Эл. почта</a></th>
			<th width="140"><a href="/client/list//order/PHONE1">Телефоны</a></th>
			<th><a href="/client/list//order/WWW">Сайт</a></th>
			<th><a href="/client/list//order/ADRESS">Адрес</a></th>
			<th><a href="/client/list//order/BALANCE">Баланс</a></th>
			<th>Действия</th>
		</tr>
	</thead>
	<tbody>

HTML;
		} else
		{
			return 1;
		}



		for ($j = 0; $j < $num; ++$j)
		{
			$row = $result->fetch_array(MYSQLI_ASSOC);

			if ($j % 2 == 0)
			{
				$even = " class=\"even\"";
			} else
			{
				$even = "";
			}

			$id = $row ['ID'];
			$organizationName = $row ['ORG_NAME'];
			$ScreenName = $row ['SCREEN_NAME'];
			$FirstName = $row ['FIRSTNAME'];
			$LastName = $row ['LASTNAME'];
			$email = $row ['EMAIL'];
			$phone1 = $row ['PHONE1'];
			$phone2 = $row ['PHONE2'];
			$website = $row ['WWW'];
			$adress = $row ['ADRESS'];
			$city = $row ['CITY'];
			$balance = $row ['BALANCE'];
			if ($row['META'])
			{
				$meta = 'class="danger"';
			} else
			{
				$meta = '';
			}


			echo <<<HTML
		<tr $meta>
			<td $even><a href='/client/view/$id'>$id</a></td>
			<td><a href='/client/view/$id'>$ScreenName</a></td>
			<td>$organizationName</td>
			<td>$LastName $FirstName</td>
			<td>$email</td>
			<td>$phone1<br>$phone2</td>
			<td><a href='$website'>$website</td>
			<td>$city, $adress</td>
			<td>$balance</td>
			<td>
			<a href="/client/view/$id"><span class="glyphicon glyphicon-list-alt"></span></a>
			 <a href="/client/edit/$id"><span class="glyphicon glyphicon-pencil"></a>
			 </td>
		</tr><!-- Table Row -->

HTML;

		}
		echo <<<HTML
	</tbody>
	<!-- Table Body -->
</table>
  </div>
</div>
</div>

HTML;
}

	public function searchInOrders($query)
	{
		if (!isset($this->connection))
		{
			$this->getConnection();
		}

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
WHERE   ORDER_NAME LIKE '%$query%' OR
		ORDER_DESCRIPTION LIKE '%$query%' OR
		ORDER_CLOSE_MESSAGE LIKE '%$query%' OR
		typ.ORDER_TYPE LIKE '%$query%' OR
		cli.SCREEN_NAME LIKE '%$query%' OR
		dev.NAME LIKE '%$query%'
SQL;





		$result = queryMysql($sql);

		if ($result->num_rows)
		{

			echo <<< HTML

<div class="panel panel-danger">
  <div class="panel-heading">
    <h3 class="panel-title">Заявки</h3>
  </div>
  <div class="panel-body">
				<div class = "table-responsive">
					<table class = "table table-hover">
						<thead>
						<tr>
							<th>Заявка</th>
							<th>Стикер</th>
							<th>Название заявки</th>
							<th>Дата начала</th>
							<th>Сделать до..</th>
							<th>Срочность</th>
							<th>Статус заявки</th>
						</tr>
						</thead>
						<tbody>
HTML;
		}
		else
		{
			return 1;
		}

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
                                    <td>' . date_format(date_create($row['TILL_DATE']), 'd.m.Y') . '</td>
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

}