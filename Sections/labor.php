<?php
	/**
	 * Created by PhpStorm.
	 * User: strem
	 * Date: 21.10.2015
	 * Time: 14:43
	 */

global $action,$actionAdd;

if (isset($action))
{
	switch ($action)
	{
		case 'view':
		{
			$OrderID = $actionAdd;
			
			if ($OrderID)
			{
				$labor = new Labor();
				$laborData = $labor->getRecordInfo($OrderID);

				echo <<<HTML
		<div class="table-responsive">
<table class="table table-striped"> <!-- cellspacing='0' is important, must stay -->
	<thead>
		<tr>
			<th>Категория</th>
			<th>Даные</th>
			<th>Детали</th>
		</tr>

	</thead>
	<tbody>
HTML;
				echo "<tr><td> Номер задачи </td><td><b>" . $laborData['LABOR_ID'] . "</b></td></tr>";
				echo "<tr><td> Номер Заявки</td><td>" . $laborData['LABOR_ORDER_ID'] . "</td></tr>";
				echo "<tr><td> Код</td><td>" . $laborData['LABOR_FASTCODE'] . "</td></tr>";
				echo "<tr><td> Название</td><td>" . $laborData['LABOR_NAME'] . "</td></tr>";
				echo "<tr><td> Описание</td><td>" . $laborData['LABOR_DESCRIPTION'] . "</td></tr>";
				echo "<tr><td> Количество<b></td><td>" . $laborData['LABOR_QUANTITY'] . "</b></td></tr>";
				echo "<tr><td> Цена за еденицу<b></td><td>" . $laborData['LABOR_PRICE'] . "</b></td></tr>";
				echo "<tr><td></td><td><a href='/labor/edit/" . $laborData['LABOR_ID'] . "'>Редактировать задачу</a></b></td></tr>";
				echo <<<HTML
	</tbody>
	<!-- Table Body -->
</table>
</div>
</div>
</div>
HTML;
			}

			break;
		}
		case 'new':
		{

			$orderID = $actionAdd;

			if (isset ($_POST ['laborName']))
			{
				$labor = new Labor('POST', NULL);
				$labor->orderID = $orderID;
				$labor->createRecordInDB();
				Echo "<script> location.replace('/order/view/$labor->orderID'); </script>";
			}


			$labor = new Labor('POST', NULL);
			$labor->orderID = $orderID;

			echo <<<HTML

<!--Client Form -->
<div class="col-sm-7">
<a href="#"><strong><i class="glyphicon glyphicon-dashboard"></i> Создание новой задачи</strong></a>
            <hr>


	<div class = 'panel panel-primary'>
		<div class = 'panel-heading'>Данные Задачи</div>
		<div class = 'panel-body'>

			<form method = 'post' class = 'form-horizontal' id = 'LaborData' name = 'LaborData'
			      action = '/labor/new/$orderID' accept-charset = 'utf-8' onClick = 'this.form.submit()'>

				<label for = 'speedCode'>Быстрый код</label>
				<input onchange='fillUpFormLabors();' name = 'speedCode' id = 'speedCode' autocomplete = 'on' value = '$labor->laborFastCode' class = 'form-control' placeholder = 'Код задачи'>
				<p class = 'help-block'>Если знаете быстрый код работы введите его здесь.</p>
				<hr>

				<label for = 'laborName'>Наименование задачи</label>
				<input name = 'laborName' id = 'laborName' autocomplete = 'on' value = '$labor->name' class = 'form-control' placeholder = 'Краткое описание' required>
				<p class = 'help-block'>Краткое описание задачи (заправка, ремонт картриджа итд).</p>

				<label for = 'laborDescription'>Описание задачи</label>
				<input name = 'laborDescription' id = 'laborDescription' value = '$labor->description' class = 'form-control' placeholder = 'Описание задачи'>
				<p class = 'help-block'>Полное описание задачи для выполнения.</p>

				<label for = 'laborQuantity'>Количество работ</label>
				<input name = 'laborQuantity' id = 'laborQuantity' autocomplete = 'on' value = 1.00
				       class = 'form-control' placeholder = 'Количество выполняемых работ'>
				<p class = 'help-block'>Полное количество работ.</p>

				<label for = 'laborCost'>Цена за еденицу</label>
				<input name = 'laborCost' id = 'laborCost' value = 0.00 class = 'form-control' placeholder = 'Цена'>
				<p class = 'help-block'>Цена за еденицу работы.</p>

				<button form = 'LaborData' type = 'submit' class = 'btn btn-success'>Сохранить все изменения</button>
			</form>

		</div>
	</div>
	<div class = 'panel panel-info'>
		<div class = 'panel-heading'>Уже добавленые задачи для этой заявки</div>
		<div class = 'panel-body'>
HTML;

			$labor->echoListOfLabors($orderID);

			echo <<<HTML
</div>
</div>
</div>

<!--/Client Form -->


<div class="col-sm-3">
	<a href="#"><strong><i class="glyphicon glyphicon-dashboard"></i> Шаблоны</strong></a>

	<hr>
HTML;
			$labor->echoLaborsTemplate(1,'Картриджи',$orderID);
			$labor->echoLaborsTemplate(2,'Все устройства',$orderID);
			$labor->echoLaborsTemplate(3,'Офисная техника',$orderID);
			$labor->echoLaborsTemplate(4,'Компьютеры',$orderID);
			echo <<<HTML
</div>
HTML;


			break;
		}
		case 'list':
		{
			$OrderID = $actionAdd;
			$labor = new Labor('POST');
			break;
		}
		case 'edit':
		{

			$laborID = $actionAdd;

			if (isset ($_POST ['laborName']))
			{
				$labor = new Labor('DB', $laborID);
				$labor->initFromPOST();
				$labor->editRecordInDB($laborID);
				Echo "<script> location.replace('/order/view/$labor->orderID'); </script>";
			}

			$labor = new Labor('DB', $laborID);

			echo <<<HTML
<!--Client Form -->
<div class="col-sm-7">
	<a href="#"><strong><i class="glyphicon glyphicon-dashboard"></i> Редактирование задачи "$labor->name"</strong></a>
	<hr>

	<div class='panel panel-primary'>
		<div class='panel-heading'>Данные Задачи</div>
		<div class='panel-body'>

			<form method='post' class='form-horizontal' id='LaborData' name='LaborData'
			      action='/labor/new/$orderID' accept-charset='utf-8' onClick='this.form.submit()'>

				<label for='speedCode'>Быстрый код</label>
				<input name='speedCode' id='speedCode' autocomplete='on' value='$labor->laborFastCode' class='form-control' placeholder='Код задачи'>

				<p class='help-block'>Если знаете быстрый код работы введите его здесь.</p>
				<hr>

				<label for='laborName'>Наименование задачи</label>
				<input name='laborName' id='laborName' autocomplete='on' value='$labor->name' class='form-control' placeholder='Краткое описание' required>

				<p class='help-block'>Краткое описание задачи (заправка, ремонт картриджа итд).</p>

				<label for='laborDescription'>Описание задачи</label>
				<input name='laborDescription' id='laborDescription' value='$labor->description' class='form-control' placeholder='Описание задачи'>

				<p class='help-block'>Полное описание задачи для выполнения.</p>

				<label for='laborQuantity'>Количество работ</label>
				<input name='laborQuantity' id='laborQuantity' autocomplete='on' value=$labor->quantity type="number"
				       class='form-control' placeholder='Количество выполняемых работ'>

				<p class='help-block'>Полное количество работ.</p>

				<label for='laborCost'>Цена за еденицу</label>
				<input name='laborCost' id='laborCost' value=$labor->price class='form-control' placeholder='Цена'>

				<p class='help-block'>Цена за еденицу работы.</p>

				<button form='LaborData' type='submit' class='btn btn-success'>Сохранить все изменения</button>
			</form>

		</div>
	</div>
</div>
<!--/Client Form -->
HTML;



			break;
		}
		case 'template':
		{

			if (isset($actionAdd,$order))
			{
				$templateID = $actionAdd;
				$orderID = $order;

				Labor::addLaborFromTemplate($templateID,$orderID);
				Echo "<script> location.replace('/order/view/$orderID'); </script>";

			}
			else
			{
				echo "error";
			}


			break;
		}
		case 'delete':
		{

			if (isset($actionAdd))
			{
				$laborID = $actionAdd;
				$labor = new Labor('DB',$laborID);
				$order = new Order('DB',$labor->orderID);
				if (!$order->isPayed)
				{
					$labor->deleteRecordFromDB ($laborID);
					Echo "<script> location.replace('/order/view/$labor->orderID'); </script>";
				}
				else
				{
					Echo "<script> location.replace('/order/view/$labor->orderID'); </script>";
				}
			}
			else
			{
				echo "error";
			}


			break;
		}
		default:{}
	}
}
else
{
	exit;
}

