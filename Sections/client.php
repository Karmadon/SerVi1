<?php
	/**
	 * Created by PhpStorm.
	 * User: Karmadon
	 * Date: 15.09.2015
	 * Time: 0:48
	 */

global $action,$actionAdd;

if (isset($action))
{
	switch ($action)
	{
		case 'new':
		{

			$new = $actionAdd;

			if (isset ($_POST ['clientname']))
			{
				$client = new Client('POST', NULL);
				$client->createRecordInDB();
				$createdID = $connection->insert_id;
				Echo "<script> location.replace('/client/view/$createdID'); </script>";
			} else
			{
				$client = new Client('POST', NULL);
			}
			echo <<<HTML
<!--Client Form -->
<div class = 'col-sm-10'>
	<div class = 'panel panel-success'>
		<div class = 'panel-heading'>Данные Клиента</div>
		<div class = 'panel-body'>

			<form method = 'post' class = 'form-horizontal' id = 'ClientData' name = 'ClientData'
			      action = '/client/new/' accept-charset = 'utf-8' onClick = 'this.form.submit()'>
<div class = 'col-sm-12'>
				<label for = 'clientname'>Отображаемое имя</label>
				<input name = 'clientname' id = 'clientname' autocomplete = 'on' value = '$client->ScreenName'
				       class = 'form-control' placeholder = 'отображаемое имя Клиента' required>

				<p class = 'help-block'>Введите имя клиента в том виде, в котором оно будет отображаться везде.</p>
</div>
<div class = 'col-sm-6'>
				<label for = 'organizationName'>Организация</label>
				<input name = 'organizationName' id = 'organizationName' autocomplete = 'on' value = '$client->organizationName'
				       class = 'form-control' placeholder = 'Название Организации'>

				<p class = 'help-block'>Введите Название организации.</p>

				<label for = 'FirstName'>Имя Клиента</label>
				<input name = 'FirstName' id = 'FirstName' autocomplete = 'on' value = '$client->FirstName'
				       class = 'form-control' placeholder = 'Имя Клиента'>

				<p class = 'help-block'>Настоящее имя клиента.</p>

				<label for = 'LastName'>Фамилия Клиента</label>
				<input name = 'LastName' id = 'LastName' autocomplete = 'on' value = '$client->LastName'
				       class = 'form-control' placeholder = 'Фамилия Клиента'>

				<p class = 'help-block'>Настоящее фамилия клиента.</p>
</div>
<div class = 'col-sm-6'>
				<label for = 'phone1'>Телефоны Клиента</label>
				<input name = 'phone1' id = 'phone1' autocomplete = 'on' value = '$client->phone1' type="tel"
				       class = 'form-control' placeholder = 'Основной телефон'>


				<input name = 'phone2' id = 'phone2' autocomplete = 'on' value = '$client->phone2' type="tel"
				       class = 'form-control' placeholder = 'Дополнительный телефон'>

				<p class = 'help-block'>Контактные телефоны нужно указывать с кодом оператора.</p>

				<label for = 'email'>Электронная почта Клиента</label>
				<input name = 'email' id = 'email' autocomplete = 'on' value = '$client->email' type="email"
				       class = 'form-control' placeholder = 'email Клиента'>

				<p class = 'help-block'>Электронная почта клиента.</p>

				<label for = 'website'>Персональный сайт Клиента</label>
				<input name = 'website' id = 'website' autocomplete = 'on' value = '$client->website' type="url"
				       class = 'form-control' placeholder = 'Персональный сайт Клиента'>

				<p class = 'help-block'>Персональный сайт клиента нужен просто для информации.</p>

				<label for = 'city'>Город обслуживания Клиента</label>
				<input name = 'city' id = 'city' autocomplete = 'on' value = '$client->city' class = 'form-control'
				       placeholder = 'Город обслуживания Клиента'>

				<p class = 'help-block'>Город обслуживания клиента нужен для информации и построения маршрута.</p>

				<label for = 'adress'>Адрес обслуживания Клиента</label>
				<input name = 'adress' id = 'adress' autocomplete = 'on' value = '$client->adress'
				       class = 'form-control' placeholder = 'Адрес обслуживания Клиента'>

				<p class = 'help-block'>Адрес обслуживания клиента нужен для информации и построения маршрута.</p>
</div>
<div class = 'col-sm-12'>
				<button form = 'ClientData' type = 'submit' class = 'btn btn-success center-block'>Сохранить все изменения</button>
				</div>
			</form>


		</div>
	</div>
</div>
</div>
<hr>
<!--/Client Form -->
HTML;
			break;
		}
		case 'edit': {

			$editClientID = $actionAdd;

			if (!isset($editClientID))
			{
				die;
			}

			if (!isset ($_POST ['ScreenName']))
			{
				$client = new Client('DB', $editClientID);
			} else
			{
				$client = new Client('POST', NULL);
				$client->editRecordInDB($editClientID);
				Echo "<script> location.replace('/client/view/$editClientID'); </script>";
			}
			//************************************************

			$checked = ($client->meta) ? 'checked' : '';

			echo <<<HTML
<!--Client Form -->
<div class = 'col-lg-10'>
	<div class = 'panel panel-success'>
		<div class = 'panel-heading'>Данные Клиента</div>
		<div class = 'panel-body'>
			<form method = 'post' class = 'form-horizontal' id = 'ClientData' name = 'ClientData'
			      action = '/client/edit/$editClientID' accept-charset = 'utf-8' onClick = 'this.form.submit()'>
<div class = 'col-sm-12'>
				<label for = 'ScreenName'>Отображаемое имя</label>
				<input name = 'ScreenName' id = 'ScreenName' autocomplete = 'on' value = '$client->ScreenName'
				       class = 'form-control' placeholder = 'отображаемое имя Клиента' required>

				<p class = 'help-block'>Введите имя клиента в том виде, в котором оно будет отображаться везде.</p>
</div>
<div class = 'col-sm-6'>
				<label for = 'organizationName'>Организация</label>
				<input name = 'organizationName' id = 'organizationName' autocomplete = 'on' value = '$client->organizationName'
				       class = 'form-control' placeholder = 'Название Организации'>

				<p class = 'help-block'>Введите Название организации.</p>

				<label for = 'FirstName'>Имя Клиента</label>
				<input name = 'FirstName' id = 'FirstName' autocomplete = 'on' value = '$client->FirstName'
				       class = 'form-control' placeholder = 'Имя Клиента'>

				<p class = 'help-block'>Настоящее имя клиента.</p>

				<label for = 'LastName'>Фамилия Клиента</label>
				<input name = 'LastName' id = 'LastName' autocomplete = 'on' value = '$client->LastName'
				       class = 'form-control' placeholder = 'Фамилия Клиента'>
				<p class = 'help-block'>Настоящее фамилия клиента.</p>
				<br>
				<br>
				<br>
				<br>
				<br>
				<br>
								<label for = 'Discount'>Скидка Клиента</label>
				<input name = 'Discount' id = 'Discount' autocomplete = 'on' value = '$client->discount'
				       class = 'form-control' placeholder = 'Скидка'>
				<p class = 'help-block'>Скидка для клиента в процентах.</p>
</div>
<div class = 'col-sm-6'>
				<label for = 'phone1'>Телефоны Клиента</label>
				<input name = 'phone1' id = 'phone1' autocomplete = 'on' value = '$client->phone1' type="tel"
				       class = 'form-control' placeholder = 'Основной телефон'>


				<input name = 'phone2' id = 'phone2' autocomplete = 'on' value = '$client->phone2' type="tel"
				       class = 'form-control' placeholder = 'Дополнительный телефон'>

				<p class = 'help-block'>Контактные телефоны нужно указывать с кодом оператора.</p>

				<label for = 'email'>Электронная почта Клиента</label>
				<input name = 'email' id = 'email' autocomplete = 'on' value = '$client->email' type="email"
				       class = 'form-control' placeholder = 'email Клиента'>

				<p class = 'help-block'>Электронная почта клиента.</p>

				<label for = 'website'>Персональный сайт Клиента</label>
				<input name = 'website' id = 'website' autocomplete = 'on' value = '$client->website' type="url"
				       class = 'form-control' placeholder = 'Персональный сайт Клиента'>

				<p class = 'help-block'>Персональный сайт клиента нужен просто для информации.</p>

				<label for = 'city'>Город обслуживания Клиента</label>
				<input name = 'city' id = 'city' autocomplete = 'on' value = '$client->city' class = 'form-control'
				       placeholder = 'Город обслуживания Клиента'>

				<p class = 'help-block'>Город обслуживания клиента нужен для информации и построения маршрута.</p>

				<label for = 'adress'>Адрес обслуживания Клиента</label>
				<input name = 'adress' id = 'adress' autocomplete = 'on' value = '$client->adress'
				       class = 'form-control' placeholder = 'Адрес обслуживания Клиента'>

				<p class = 'help-block'>Адрес обслуживания клиента нужен для информации и построения маршрута.</p>
</div>
<div class = 'col-sm-12'>
<label for = 'clientmeta'>Этот Клиент временный</label>
			<input name = 'clientmeta' type = 'checkbox' id = 'clientmeta' $checked>
			<p class = 'help-block'>Отмечено когда, данные клиента сформированы не полностью</p>
				<button form = 'ClientData' type = 'submit' class = 'btn btn-success center-block'>Сохранить все изменения</button>
				</div>
			</form>


		</div>
	</div>
</div>
</div>
<hr>
<!--/Client Form -->

HTML;
			break;
		}
		case 'view':{
			
			
			$clientID = $actionAdd;

			
			if ($clientID)
			{
				echo $clientID;

				$client = new Client('DB', $clientID);
				
				echo <<<HTML

<div class="col-sm-10">
	<div class="table-responsive">
		<table class="table table-stiped"
		<!-- cellspacing='0' is important, must stay -->
		<thead>
		<tr>
			<th>Категория</th>
			<th>Даные</th>
		</tr>

		</thead>
		<tbody>
HTML;
				
				
				echo "<tr><td> Код № </td><td><b> " . $client->id . "</b></td></tr>";
				echo "<tr><td> Отображаемое имя</td><td>" . $client->ScreenName . "</td></tr>";
				echo "<tr><td> Организация</td><td>" . $client->organizationName . "</td></tr>";
				echo "<tr><td> Фамилия</td><td>" . $client->LastName . "</td></tr>";
				echo "<tr><td> Имя</td><td>" . $client->FirstName . "</td></tr>";
				echo "<tr><td> Страничка<b></td><td>" . $client->website . "</b></td></tr>";
				echo "<tr><td> Эл. почта</td><td>" . $client->email . "</td></tr>";
				echo "<tr><td> Город</td><td>" . $client->city . "</td></tr>";
				echo "<tr><td> Адрес<b></td><td>" . $client->adress . "</b></td></tr>";
				echo "<tr><td> Телефон <b></td><td>" . $client->phone1 . "</b></td></tr>";
				echo "<tr><td> Доп. Телефон <b></td><td>" . $client->phone2 . "</b></td></tr>";
				echo "<tr><td> Баланс <b></td><td>" . $client->balance . "</b></td></tr>";
				echo "<tr><td> Скидка клиента <b></td><td><strong>" . $client->discount . "%</strong></td></tr>";
				
				echo "<tr><td> <a href = '/client/edit/" . $client->id . "' ><span class=' glyphicon glyphicon-pencil'></a ></td></tr>";
				echo "<tr><td> <a href = '/device/new/" . $client->id . "' >Добавить устройство этого клиента</a ></td></tr>";
				echo "<tr><td> <a target='_blank' href = 'https://maps.google.com?q=" . $client->city . '+' . $client->adress . "' >Окрыть карту по адресу</a ></td></tr>";
				
				
				echo <<<HTML

	</tbody>
	<!-- Table Body -->
</table>

</div>



HTML;
				
				$client->echoClientHistory($clientID);
				
				echo <<<HTML

HTML;
			} else
			{
				echo <<<HTML



          <div id="getClientBySticker" class="overlay">
	<div class="popup">
		<h2>Введите ID клиента</h2>
		<a class="close" href="#">×</a>

			<form method="get" id="content" action="/client/view">
		  <p><input class="textbox" type="number" name="view"required></p>
          <p><input class="button" type="submit" value="Отправить"></p>

	</div>
</div>

</form>
HTML;
				
			}
			break;
		}
		case 'list':{
			
			$page = $actionAdd;
			
			if (!isset($order))
			{
				$order = 'ORDER BY ID';
			} else
			{
				$order = 'ORDER BY ' . $order;
			}

			$result = queryMysql("SELECT * FROM clients WHERE DELETED = '0' $order");
			if ($result->num_rows)
			{
				$num = $result->num_rows;
			} else
			{
				Echo 'Error';
			}
			
			echo <<<HTML
<div class="table-responsive">
                        <table class="table table-striped">
	<thead>
		<tr>
			<th><a href="/client/list/$page/order/ID">Код</a></th>
			<th><a href="/client/list/$page/order/SCREEN_NAME">Отображать как</a></th>
			<th><a href="/client/list/$page/order/ORG_NAME">Организация</a></th>
			<th><a href="/client/list/$page/order/LASTNAME">Фамилия, Имя</a></th>
			<th><a href="/client/list/$page/order/EMAIL">Эл. почта</a></th>
			<th width="140"><a href="/client/list/$page/order/PHONE1">Телефоны</a></th>
			<th><a href="/client/list/$page/order/WWW">Сайт</a></th>
			<th><a href="/client/list/$page/order/ADRESS">Адрес</a></th>
			<th><a href="/client/list/$page/order/BALANCE">Баланс</a></th>
			<th>Действия</th>
		</tr>
	</thead>
	<tbody>

HTML;
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


HTML;
			break;
		}
	}
}
