<?php
/**
 * Created by PhpStorm.
 * User: strem
 * Date: 24.09.2015
 * Time: 14:44
 */

global $action, $actionAdd, $payed, $state;

if (isset($action))
{
	switch ($action)
	{
		case 'view':
		{
			$OrderID = $actionAdd;
			
			if (isset ($_POST ['orderstatus']))
			{
				$order = new Order('DB', $OrderID);
				$order->initFromPOSTSideBar ();
				$order->editRecordInDB ($OrderID);
			}
			elseif (isset ($_POST ['ordertech']))
			{
				$order = new Order('DB', $OrderID);
				$order->initFromPOSTSideBar ();
				$order->editRecordInDB ($OrderID);
			}
			elseif (isset ($_POST ['closemessage']))
			{
				$order = new Order('DB', $OrderID);
				$order->initFromPOSTCloseMessage ();
				$order->editRecordInDB ($OrderID);
			}
			
			if ($OrderID)
			{
				$order = new Order('DB', $OrderID);
				$labor = new Labor('DB', $order->orderId);
				
				if (!$order->orderId)
				{
					Echo 'No Sticker';
				}
				
				if ($order->deviceID)
				{
					$device = new Device('DB', $order->deviceID);
				}
				if ($order->clientID)
				{
					$client = new Client('DB', $order->clientID);
				}
				
				if ($order->orderStateID === '2')
				{
					$disabledFlag = 1;
					$disabled = 'disabled';
					$addString = '';
				}
				else
				{
					$disabledFlag = 0;
					$disabled = '';
					$addString = "<a href='/labor/new/$order->orderId' class='list-group-item alert-success'><span class='glyphicon glyphicon-plus-sign'></span> Добавить новую задачу для заявки</a>";
				}

				echo <<<HTML


<div class = "container-fluid">

	<!--center-->
	<div class = "col-sm-7">
		<div class = "row">

			<a href="#"><strong><i class="glyphicon glyphicon-list-alt"></i> Заявка: №<font color = "black">$order->orderId </font>от $order->orderDate, Статус: <font color = "$order->color">$order->orderStateSTR</font></strong></a>
<hr>



    <div class="col-sm-12">
      <div class="panel panel-primary">
  		<div class="panel-heading">Основное задание.</div>
  		<div class="panel-body"><strong>$order->description</strong></div>
	  </div>
    </div>



			<div class = "col-xs-12">
<div class = "panel panel-success">
			<div class = "panel-heading">Задачи - Выполняемые работы</div>
			<div class = "panel-body">
<div class="list-group">
       $addString
HTML;
				$labor->echoListOfLabors ($order->orderId);
				
				echo <<<HTML
      </div>
      </div>

      </div>
      </div>
		<hr>

					<div class = "col-xs-12">
<div class = "panel panel-default">

</div>
<div class = "panel panel-info">
			<div class = "panel-heading">Интеренет запросы</div>
			<div class = "panel-body">
<div class="list-group">
<a href="https://google.com.ua?q=$device->name" class="list-group-item"  target="_blank"> Найти <strong>$device->name</strong> в Google </a>
<a href="https://google.com.ua?q=Расходные+материалы+для+$device->name" class="list-group-item"  target="_blank"> Найти <strong>Расходные материалы для $device->name</strong> в Google </a>
</div>
</div>
</div>
</div>

<div class="col-sm-12">
	<div class="panel panel-danger">
		<div class="panel-heading">Заключение или дополнение к заявке.</div>
		<div class="panel-body">
			<form method="post" class="form-horizontal" id="review" name="review" action = "/order/view/$order->orderId" accept-charset = "utf-8" onClick = "this.form.submit()">
			<label for = 'closemessage'>Заметки о заявке</label>

				<textarea $disabled id = 'closemessage' name = 'closemessage' class="form-control">$order->closeMessage</textarea>

								<p class = 'help-block'>Эта информация будет доступна Клиенту</p>

				<button  form="review" type="submit" class = "btn btn-info">Записать дополнение.</button>
			</form>
		</div>
	</div>
</div>

</div>
</div>
<!--/center-->
HTML;
				$order->echoRightSideBar ($client, $device, 'SideBar', 1, $order->orderId, $disabledFlag);
				
				echo <<<HTML
	<hr>




</div><!--/container-fluid-->
HTML;
				
			}
			else
			{
				echo <<<HTML
<div class="col-sm-3">
<a href="#"><strong><i class="glyphicon glyphicon-dashboard"></i> Открываем заявку</strong></a>
            <hr>

		<h2>Введите Номер заказа</h2>

		<form  method = "get" action = "/order/view">
			<input class = "form-control input-lg" type = "number" name = "view" required>
			<input class = "form-control btn-green" type = "submit" value = "Отправить">
		</form>
</div>

<!--right-->
	<div class = "col-sm-7">
<a href="#"><strong><i class="glyphicon glyphicon-dashboard"></i> Заявки за последние 5 дней</strong></a>
            <hr>


		<div class = "panel panel-default">
			<div class = "panel-heading">Список</div>
			<div class = "table-responsive">
					<table class = "table table-hover">
						<thead>
						<tr>
							<th>Номер Заявки</th>
							<th>Название заявки</th>
							<th>Дата начала</th>
							<th>Сделать до..</th>
							<th>Срочность</th>
							<th>Дата закрытия</th>
							<th>Статус заявки</th>
						</tr>
						</thead>
						<tbody>
HTML;
				
				$order = new Order();
				Order::echoListFromDB ('DATE DESC', '20', 'ORDER_DATE > DATE_SUB(CURDATE(),INTERVAL 5 DAY)');
				
				echo <<<HTML
</tbody>
					</table>
				</div>
		</div>
		<hr>

	</div>
	<!--/right-->
</div>
</div>

HTML;
			}
			break;
		}
		case 'new':
		{
			if(isset($_POST ['sticker']))
			{
				$stickerID = sanitizeString ($_POST ['sticker']);
			}
			else
			{
				$stickerID = $actionAdd;
			}

			if (!$stickerID)
			{
				echo <<<HTML
<div class = "col-sm-5">
	<a href = "#"><strong><i class = "glyphicon glyphicon-dashboard"></i> Рабочий стол</strong></a>
	<hr>
	<form method = "post" class = "for" id = "newOrderSticker" action = '/order/new/'>
		<input class = "form-control input-lg " type = "number" max = "99999" name = "sticker" placeholder = "Номер стикера" accesskey = "s" autofocus>
	</form>
	<br>

	<button form = "newOrderSticker" type = "submit" class = "btn btn-success">Новая заявка для стикера</button>
	<BR><br>
	<a href = "/order/new/nosticker" class = "btn btn-danger">СТИКЕРА НЕТ (НОВОЕ УСТРОЙСТВО)</a>
</div>


<!--/center-->

HTML;
				//$order->echoRightSideBar($client, $device);
				Echo <<<HTML
	<hr>

</div>
<BR>
<BR>
<BR>
HTML;
			}
			else
			{
				if (is_numeric ($stickerID))
				{
					$device = new Device('DB', $stickerID);
					
					if ($device->id)
					{
						$client = new Client('DB', $device->ownerID);
						
					}
					else
					{
						echo <<<HTML
<div class="col-sm-5">
<a href="#"><strong><i class="glyphicon glyphicon-dashboard"></i> Рабочий стол</strong></a>
            <hr>

	<form method="get" class="for" id="newOrderSticker">
	<input class="form-control input-lg " type="number" max="99999" name="new" placeholder="Номер стикера" accesskey="s" autofocus >
	</form>

<br>

<button  form="newOrderSticker" type="submit" class = "btn btn-success">Новая заявка для стикера</button>
<BR><br>
<a href="/order/new/nosticker" class="btn btn-warning">СТИКЕРА НЕТ (НОВОЕ УСТРОЙСТВО)</a>


		</div>


	<!--/center-->
HTML;
						//$order->echoRightSideBar($client, $device);
						
						echo <<<HTML

	<hr>

</div>
<BR>
<BR>
<BR>
HTML;
					}
				}
				else
				{
					$client = new Client('META');
					$device = new Device('META');
					
				}
				
				$order = new Order();
				$order->initFromObjects ($client, $device);
				
				if (isset ($_POST ['ordername']))
				{
					$order->initFromPOST ();
					
					if ($order->isReadyToCreate)
					{
						$order->createRecordInDB ();
						
						Echo "<script> location.replace('/order/view/$order->orderId'); </script>";
					}
					else
					{
						echo '<BR><h2>Meta OBJECTS!!!</h2><BR>';
						
						$client->createRecordInDB ();
						
						$device->ownerID = $client->id;
						$device->createRecordInDB ();
						
						$order->deviceID = $device->id;
						$order->clientID = $client->id;
						$order->createRecordInDB ();
						
						Echo "<script> location.replace('/order/review/$order->orderId'); </script>";
					}
				}
				
				
				echo <<<HTML
<div class = "container-fluid">


	<!--center-->
	<div class = "col-sm-7">
	<a href="#"><strong><i class="glyphicon glyphicon-dashboard"></i>  Сведения о заявке</strong></a>

            <hr>
		<div class = "row">

				<form method = "post" class = "form-horizontal well" id = "AddOrder" name = "AddOrder"
			      action = "/order/new/$stickerID" accept-charset = "utf-8" onClick = "this.form.submit()">

				<label for="ordername">Название заявки</label>
				<input class = "form-control input-group" name = "ordername" id = "ordername" autocomplete = "on" value = "$order->orderName" placeholder = "Название заявки (Необходимое описание самой заявки)" required>
				<p class="help-block">Название заявки должно быть кратким и понятным. Название так же идет на все документы по заявке.</p>
				<label for="orderdate">Дата создания заявки</label>
				<input class = "form-control input-group" name = "orderdate" id = "orderdate" disabled value = "$order->orderDate" placeholder = "Дата заявки" required>
				<p class="help-block">Дата и время заявки становится автоматически. </p>
				<label for="ordercategory">Тип заявки</label>
HTML;
				$order->echoComboControl ('order_types', 'ORDER_TYPE', '-Тип Заявки-', 'ordertype', 'AddOrder', 1, 0);
				echo <<<HTML
<p class = "help-block">Тип заявки нужен для правильной отработки заявки.</p>
<label for = "orderdescription">Заметки к заявке</label>
<textarea class = "form-control input-group" name = "orderdescription" type = "text" id = "orderdescription"
          autocomplete = "on" placeholder = "Заметки к заявке..."></textarea>
<p class = "help-block">При необходимости можно оставить заметки к самой заявке, они будут видны только техникам.</p>
<input class = "btn btn-success" type = "submit" name = "AddorderBTN" value = "Добавить Заявку">
<input class = "btn btn-danger" type = "reset" name = "Cancel" value = "Отменить Заявку">
</form>

<div class = "panel panel-default">
	<div class = "panel-heading">История Устройства</div>
	<div class = "panel-body">
		<section id = "tables">
			<table class = "table table-bordered table-striped table-hover">
				<thead>
				<tr>
					<th>Дата</th>
					<th>№ Заявки</th>
					<th>Имя заявки</th>
					<th>Статус заявки</th>
				</tr>
				</thead>
				<tbody>
HTML;
				$order->echoDeviceHistory ($device->id);
				Echo <<<HTML
				</tbody>
			</table>

		</div>
	</div>

</div>
</div>

<!--center-->
HTML;
				$order->echoRightSideBar ($client, $device, 'AddOrder', 0, $order->orderId);
				echo <<<HTML
</div>
HTML;
				
			}
			break;
		}
		case 'review':
		{
			
			$OrderID = $actionAdd;
			
			if ($OrderID)
			{
				if (isset ($_POST ['ordername']) === 0 || isset ($_POST ['ScreenName']) === 0 || isset ($_POST ['devicename']) === 0)
				{
					$order = new Order('DB', $OrderID);
					
					if ($order->deviceID)
					{
						$device = new Device('DB', $order->deviceID);
					}
					if ($order->clientID)
					{
						$client = new Client('DB', $order->clientID);
					}
				}
				else
				{
					$order = new Order('DB', $OrderID);
					
					
					if (isset ($_POST ['ordername']))
					{
						$order->initFromPOST ();
						$order->editRecordInDB ($OrderID);
					}
					
					
					if (isset ($_POST ['devicename']))
					{
						$device = new Device('POST', 0);
						$device->ownerID = $order->clientID;
						$device->editRecordInDB ($order->deviceID);
						$device->initFromDB ($order->deviceID);
					}
					else
					{
						$device = new Device('DB', $order->deviceID);
					}
					
					
					if (isset ($_POST ['ScreenName']))
					{
						$client = new Client('POST', 0);
						$client->editRecordInDB ($order->clientID);
						$client->initFromDB ($order->clientID);
					}
					else
					{
						$client = new Client('DB', $order->clientID);
					}
					
				}
				
				
			}
			
			
			echo <<<HTML
<a href='#'><strong><i class='glyphicon glyphicon-dashboard'></i> Проверьте Сведения о заявке</strong></a>
<hr>
<div class = 'row'>
	<div class = 'col-lg-7'>

	<!--tabs-->
                    <div class="panel">
                        <ul class="nav nav-tabs" id="myTab">
                            <li class="active"><a href="#order" data-toggle="tab">Данные Заявки</a></li>
                            <li><a href="#" data-toggle="tab">Данные Устройства</a></li>
                            <li><a href="#" data-toggle="tab">Данные Клиента</a></li>
                        </ul>
                        <div class="tab-content">

                    <div class="tab-content">
HTML;
			// ------------------------------------------------------------- EDIT ORDER ------------------------------------------------------------------------
			echo <<<HTML

                            <div class="tab-pane active well" id="order">

				<form method = 'post' name = 'OrderData' id = 'OrderData' class = 'form-horizontal' action = '/order/review/$OrderID'>

					<label for='ordername'>Название заявки</label>
					<input class = 'form-control' placeholder='Название Заявки' name= 'ordername' type = 'text' value='$order->orderName'>
					<p class = 'help-block'>Понятное название заявки для отображение в списках.</p>

					<label for='orderdescription'>Заметки к заявке</label>
					<textarea class = 'form-control' placeholder='Заметки весьма полезны' name = 'orderdescription' type='text' value='$order->description'></textarea>
					<p class = 'help-block'>При необходимости можно оставить заметки к самой заявке, они будут видны только техникам..</p>

					<label for='ordertype'>Тип заявки</label>

HTML;
			$order->echoComboControl ('order_types',
			                          'ORDER_TYPE',
			                          '-Тип Заявки-',
			                          'ordertype',
			                          'OrderData',
			                          $order->orderTypeID,
			                          0);
			echo <<<HTML
					<p class = 'help-block'>При необходимости можно поменять тип завки..</p>
<label for='orderstate'>Статус заявки</label>
HTML;
			$order->echoComboControl ('order_states',
			                          'STATE',
			                          '-Статус Заявки-',
			                          'orderstatus',
			                          'OrderData',
			                          $order->orderStateID,
			                          0);
			echo <<<HTML
		<p class='help-block'>Статус завки можно менять несколько раз.</p>
				<label for='orderstate'>Срочность заявки</label>
HTML;
			$order->echoComboControl ('order_speed',
			                          'SPEED',
			                          '-Срочность Заявки-',
			                          'orderspeed',
			                          'OrderData',
			                          $order->orderSpeedID,
			                          0);
			echo <<<HTML
<p class='help-block'>Срочность заявки влияет на конечную стоимость заявки.</p>
					<button form = 'OrderData' type = 'submit' class = 'btn btn-success'>Сохранить все изменения</button>
				</form>

			</div>

HTML;
			// ------------------------------------------------------------- END OF EDIT ORDER ------------------------------------------------------------------------
			
			
			$metaDeviceSTR = ($device->meta) ? '<font color="red">Да</font>' : '<font color="green">Нет</font>';
			$metaClientSTR = ($client->meta) ? '<font color="red">Да</font>' : '<font color="green">Нет</font>';
			
			
			// ------------------------------------------------------------- EDIT DEVICE ------------------------------------------------------------------------
			echo <<<HTML
<div class="tab-pane well" id="device">

			<form method = 'post' class = 'form-horizontal' id = 'DeviceData' name = 'DeviceData'
			      action = '/order/review/$OrderID' accept-charset = 'utf-8'
			'>
			<script src = '/Assets/js/autocomplete.js'></script>
			<label for = 'deviceid'>ID Устройства</label>
			<input name = 'deviceid' id = 'deviceid' disabled class = 'form-control' value = '$device->id'>

			<p class = 'help-block'>Номер устройства, он же стикер на устройстве.</p>

			<label for = 'devicename'>Название Устройства</label>
			<input name = 'devicename' id = 'devicename' autocomplete = 'on' value = '$device->name'
			       class = 'form-control' placeholder = 'Название Устройства (Для определения самого устройства в БД)'
			       required>

			<p class = 'help-block'>Название Устройства (Для определения самого устройства в БД).</p>

			<label for = 'deviceid'>Изготовитель Устройства</label>
			<input name = 'devicemanufac' id = 'devicemanufac' autocomplete = 'on' value = '$device->manufacturerSTR'
			       class = 'form-control' placeholder = 'Производитель Устройства' required>

			<p class = 'help-block'>Производителя можно выбрать из списка или добавить новый.</p>

			<label for = 'deviceid'>Модель устройства</label>
			<input name = 'devicemodel' type = 'text' id = 'devicemodel' autocomplete = 'on' value = '$device->modelSTR'
			       class = 'form-control' placeholder = 'Модель Устройства' required '>
			<p class = 'help-block'>Модель можно выбрать из списка или добавить новый.</p>

			<label for = 'deviceid'>Серийный номер устройства</label>
			<input name = 'deviceserial' type = 'text' id = 'deviceserial' autocomplete = 'on'
			       value = '$device->serial' class = 'form-control'
			       placeholder = 'Серийный номер Устройства (полностью)' pattern = '^[0-9a-zA-Z]+$'>

			<p class = 'help-block'>Серийный номер проверяется при каждом сохранении.</p>

			<label for = 'deviceid'>Тип Устройства</label>
			<input name = 'devicetype' type = 'text' id = 'devicetype' value = '$device->typeSTR'
			       class = 'form-control' placeholder = 'Тип устройства' required>

			<p class = 'help-block'>Тип можно выбрать из списка или добавить новый.</p>

			<label for = 'deviceid'>Категория устройства</label>
			<input name = 'devicecategory' type = 'text' id = 'devicecategory' autocomplete = 'on'
			       value = '$device->categorySTR' class = 'form-control' placeholder = 'Категория устройства'>

			<p class = 'help-block'>Категорию можно выбрать из списка или добавить новый.</p>

			<label for = 'deviceid'>Заметки устройства</label>
			<input name = 'devicedescription' type = 'text' id = 'devicedescription' value = '$device->description'
			       autocomplete = 'on' class = 'form-control' placeholder = 'Описание Устройства (полностью)'>

			<p class = 'help-block'>Заметки полезны.</p>

			<button form = 'DeviceData' type = 'submit' class = 'btn btn-success'>Сохранить все изменения</button>
			</form>

		</div>

HTML;
			// -------------------------------------------------------------END OF EDIT DEVICE ------------------------------------------------------------------------
			
			
			// ------------------------------------------------------------- EDIT CLIENT ------------------------------------------------------------------------
			echo <<<HTML
<!--Client Form -->
<div class="tab-pane well" id="client">


			<form method = 'post' class = 'form-horizontal' id = 'ClientData' name = 'ClientData'
			      action = '/order/review/$OrderID' accept-charset = 'utf-8' onClick = 'this.form.submit()'>
				<script src = '/Assets/js/autocomplete.js'></script>

				<label for = 'ScreenName'>Отображаемое имя</label>
				<input name = 'ScreenName' id = 'ScreenName' autocomplete = 'on' value = '$client->ScreenName'
				       class = 'form-control' placeholder = 'отображаемое имя Клиента' required>

				<p class = 'help-block'>Введите имя клиента в том виде, в котором оно будет отображаться везде.</p>

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

				<label for = 'phone1'>Телефоны Клиента</label>
				<input name = 'phone1' id = 'phone1' autocomplete = 'on' value = '$client->phone1'
				       class = 'form-control' placeholder = 'Основной телефон'>

				<input name = 'phone2' id = 'phone2' autocomplete = 'on' value = '$client->phone2'
				       class = 'form-control' placeholder = 'Дополнительный телефон'>

				<p class = 'help-block'>Контактные телефоны нужно указывать с кодом оператора.</p>

				<label for = 'email'>Электронная почта Клиента</label>
				<input name = 'email' id = 'email' autocomplete = 'on' value = '$client->email'
				       class = 'form-control' placeholder = 'email Клиента'>

				<p class = 'help-block'>Электронная почта клиента.</p>

				<label for = 'website'>Персональный сайт Клиента</label>
				<input name = 'website' id = 'website' autocomplete = 'on' value = '$client->website'
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

				<button form = 'ClientData' type = 'submit' class = 'btn btn-success'>Сохранить все изменения</button>
			</form>


		</div>

<!--/Client Form -->

</div>

                    </div>
                    <!--/tabs-->
HTML;
			
			// ------------------------------------------------------------- END OF EDIT CLIENT ------------------------------------------------------------------------
			
			break;
		}
		case 'list':
		{

			$page = $actionAdd;



		$isPayed = sanitizeString ($_GET ['payed']);
			
			$order = new Order();


			echo <<<HTML
<div class = "container-fluid">
	<strong><i class = "glyphicon glyphicon-dashboard"></i> Список заявок</strong>
	<hr>
	<!--center-->
	<div class = "col-md-8">

			<div class = "panel panel-default">
				<div class = "panel-heading">Активные заявки</div>


HTML;
			
			if ($state)
			{
				if ($payed === 'yes')
				{
					Order::echoListFromDB ('DATE DESC', 250, "ORDER_STATE_ID = '$state' AND PAYED = '1'");
				}
				elseif ($payed === 'no')
				{
					Order::echoListFromDB ('DATE DESC', 250, "ORDER_STATE_ID = '$state' AND PAYED = '0'");
				}
				else
				{
					Order::echoListFromDB ('DATE DESC', 250, "ORDER_STATE_ID = '$state'");
				}
			}
			else
			{
				Order::echoListFromDB ();
			}
			
			echo <<<HTML


		</div>
		</div>





	<!--right-->
	<div class = "col-sm-2">
		<a href="#"><strong><i class="glyphicon glyphicon-dashboard"></i> Фильтры</strong></a>
	<hr>


		<div class = "panel panel-primary">
			<div class = "panel-heading">Статус заявки</div>
			<div class = "panel-body">
			<div class="list-group">
HTML;
			
			
			$order->echoOrderFilters ();
			
			echo <<<HTML


</div>
			</div>
		</div>

				<div class = "panel panel-success">
			<div class = "panel-heading">Оплата заявок</div>
			<div class = "panel-body">
			<div class="list-group">

<a href="/order/list/$page/state/2/payed/yes" class="list-group-item list-group-item-success">Оплаченые <span class='badge'>$count</span></a>
<a href="/order/list/$page/state/2/payed/no" class="list-group-item list-group-item-danger">Не оплаченные <span class='badge'>$count</span></a>

</div>
			</div>
		</div>




<hr>
</div>
</div>
</div>



HTML;
			break;
		}
		default:
		{
			exit;
		}
	}
}
else
{
	exit;
}