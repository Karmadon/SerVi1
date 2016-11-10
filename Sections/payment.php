<?php
/**
 * Created by PhpStorm.
 * User: strem
 * Date: 30.10.2015
 * Time: 17:52
 */

global $action,$actionAdd,$cash,$drcr,$type;

if (isset($action))
{
	switch ($action)
	{
		case 'new':
		{

			if($drcr === 'in')
			{
				$drCr = 1;
			}
			else
			{
				$drCr = 0;
			}
			if(is_numeric($actionAdd))
			{
				$orderId = $actionAdd;
			}
			else
			{
				$orderId = 0;
			}
			if($cash === 'no')
			{
				$type = 2;
			}
			else
			{
				$type = 1;
			}




			if (isset ($_POST ['paymentName']))
			{

				if ($orderId > 0)
				{
					$payment = new Payment();

					if ($drCr)
					{
						$payment->drCrBool = 1;
					}
					else
					{
						$payment->drCrBool = 0;
					}
					$payment->initFromOrderObject ($orderId);
					$payment->initFromPOST ();

				} else
				{
					$payment = new Payment('POST', NULL);
				}

				$payment->createRecordInDB ();

				Echo "<script> location.replace('/payment/list'); </script>";
			} else
			{
				$payment = new Payment('POST', NULL);
			}

			if ($drCr)
			{
				$payment->drCrBool = 1;
				$payment->toSTR = 'Computer Masters';                       ///// MAGIC NUMBERS!!!!!
				$payment->toId = '7';                                     ///// MAGIC NUMBERS!!!!!
				$cashChecked1 = 'checked';
				$drCrString = '<h2><i class="glyphicon glyphicon-plus-sign text-success"></i> Приходная операция</h2>';
				$drCrChecked0 = '0';
				$drCrChecked1 = 'readonly';

			} else
			{
				$payment->drCrBool = 0;
				$payment->fromSTR = 'Computer Masters';                                     ///// MAGIC NUMBERS!!!!!
				$payment->fromId = '7';                                                      ///// MAGIC NUMBERS!!!!!
				$drCrChecked0 = 'readonly';
				$cashChecked1 = 'checked';
				$drCrChecked1 = '';
				$drCrString = '<h2><i class="glyphicon glyphicon-minus-sign text-danger"></i> Расходная операция</h2>';
			}

			if ($orderId > 0)
			{
				$payment->initFromOrderObject ($orderId);
				$disabledStr = 'readonly';
				$disabledBool = 1;
				$disableChecked = 'checked';

			} else
			{
				$disabledStr = '';
				$disabledBool = 0;
				$disableChecked = '';
			}


			if ($type === 1)
			{
				$cashChecked1 = 'checked';
				$cashChecked0 = '';
			} elseif ($type === 2)
			{
				$cashChecked0 = 'checked';
				$cashChecked1 = '';
			}


			echo <<<HTML
<!--Client Form -->
<div class = 'col-sm-10'>
	<div class = 'panel panel-primary'>
		<div class = 'panel-heading'>Данные Транкзакции</div>
		<div class = 'panel-body'>


			<form method = 'post' class = 'form-horizontal' id = 'PaymentData' name = 'PaymentData' action = '/payment/new/$orderId/drcr/$drCr/type/$type' accept-charset = 'utf-8' onClick = 'this.form.submit()'>
				<center>
$drCrString
				</center>
				<br>
				<label for = 'orderID'><i class = "glyphicon glyphicon-pushpin"></i> Номер Связанной заявки</label>
				<input type = "checkbox" $disabledStr $disableChecked
				       onclick = "if(this.checked){document.getElementById('orderID').style.display='inline-block'}else {document.getElementById('orderID').style.display='none';  document.getElementById('orderID').value='';}">
				<input value = "$orderId" class = 'form-control' type = "text" placeholder = "Номер заявки"
				       style = "display: none;" id = "orderID">

				<p class = 'help-block'>Если опреация связана с заявкой. То нужно ее тут вписать.</p>
<div class = 'col-sm-6'>
				<label for = 'paymentName'><i class = "glyphicon glyphicon-pushpin"></i> Название Транкзакции</label>
				<input name = 'paymentName' id = 'paymentName' autocomplete = 'on' value = '$payment->name'
				       class = 'form-control' placeholder = 'Имя транкзакции' required>
				<p class = 'help-block'>Краткое название операции.</p>


				<label for = 'paymentDescription'><i class = "glyphicon glyphicon-adjust"></i> Описание Транкзакции</label>
				<input name = 'paymentDescription' id = 'paymentDescription' autocomplete = 'on' value = '$payment->description'
				       class = 'form-control' placeholder = 'Описание'>
				<p class = 'help-block'>Описание транкзакции или заметки касающиеся транкзакции.</p>
</div><div class = 'col-sm-6'>
				<label for = 'paymentFrom'><i class = "glyphicon glyphicon-user"></i> Плательщик</label>
				<input name = 'paymentFrom' $disabledStr id = 'paymentFrom' autocomplete = 'on'
				       value = '$payment->fromSTR' class = 'form-control' placeholder = 'Имя клиента - плательщика'
				       required $drCrChecked0>

				<p class = 'help-block'>Введите плательщика.</p>

				<label for = 'paymentTo'><i class = "glyphicon glyphicon-usd"></i> Получатель</label>
				<input name = 'paymentTo' $disabledStr id = 'paymentTo' autocomplete = 'on'
				       value = '$payment->toSTR' class = 'form-control' placeholder = 'Имя Клиента - получателя'
				       required $drCrChecked1>

				<p class = 'help-block'>Введите получателя.</p>



<label for = 'paymentTo'><i class = "glyphicon glyphicon-lamp"></i> Категория платежа</label>


HTML;
			$payment->echoComboControl('payment_category',
			                           'NAME',
			                           '-Категория платежа-',
			                           'paymentCategory',
			                           'PaymentData',
			                           31,
			                           0,
			                           $disabledBool
			);
			echo <<<HTML


<label for = 'optradio'>Тип Оплаты</label>
<center>

				<label class="radio-inline"><input $disabledStr type="radio" name="paymentType" $cashChecked1 value="1"><i class="glyphicon glyphicon-usd"></i> Наличный</label>
				<label class="radio-inline"><input $disabledStr type="radio" name="paymentType" $cashChecked0 value="2"><i class="glyphicon glyphicon-credit-card"></i> Безналичный</label>
</center>
</div><div class = 'col-sm-6'>

<label for = 'amount'>Сумма платежа</label>
				<input type="number" step="0.01" name = 'amount' id = 'amount' autocomplete = 'on' value = '$payment->amount' class = 'form-control' placeholder = 'Сумма' required>
				<p class = 'help-block'>Полная сумма платежа.</p>

				<button form = 'PaymentData' type = 'submit' class = 'btn btn-success center-block'><i class="glyphicon glyphicon-floppy-disk"></i> Сохранить все изменения</button>
				</div>
			</form>


		</div>
	</div>
</div>
</div>
<!--/Client Form -->
HTML;


			break;
		}
		case 'view':
		{
			$paymentID = $actionAdd;

			if ($paymentID)
			{
				$payment = new Payment('DB', $paymentID);

				echo <<<HTML
<div class = "container-fluid">
	<strong><i class = "glyphicon glyphicon-dashboard"></i> Список заявок</strong>
	<hr>
HTML;
				$payment->echoPayment($paymentID);
				if($payment->stateId !== '1')
				{
					echo <<<HTML
 <!-- Trigger the modal with a button -->
  <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal"> Провести платеж</button>
HTML;
				}
			}
			break;
		}
		case 'list':
		{

			$view = sanitizeString($_GET ['view']);
			$state = sanitizeString($_GET ['state']);
			$type = sanitizeString($_GET ['type']);

			$payment = new Payment();

			echo <<<HTML

<div class = "container-fluid">
	<strong><i class = "glyphicon glyphicon-dashboard"></i> Последние транкзакции</strong>
	<hr>
	<!--center-->

<!--center-->
	<div class = "col-md-8">

			<div class = "panel panel-default">
				<div class = "panel-heading">Основные транкзакции</div>


HTML;
			if ($state)
			{
				$payment->echoListFromDB ('DATE DESC', 50, "STATE_ID = '$state'");
			}
			else
			{
				$payment->echoListFromDB ('DATE DESC', 50);
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

			$payment->echoPaymentFilters();

			echo <<<HTML


</div>
			</div>
		</div>

				<div class = "panel panel-success">
			<div class = "panel-heading">Тип платежа</div>
			<div class = "panel-body">
			<div class="list-group">

<a href="/payment/list/1/type/1" class="list-group-item list-group-item-success">Наличные </a>
<a href="/payment/list/1/type/2" class="list-group-item list-group-item-danger">Безналичные </a>

</div>
			</div>
		</div>

<div class = "panel panel-info">
			<div class = "panel-heading">Вид Платежа</div>
			<div class = "panel-body">
			<div class="list-group">

<a href="/payment/list/1/type/1" class="list-group-item list-group-item-success">Приход </a>
<a href="/payment/list/1/type/2" class="list-group-item list-group-item-danger">Расход </a>

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
		case 'make':
		{
			$paymentID = $actionAdd;
			$sign = post('sign');
			if ($sign === $_SESSION['sign'])
			{
				if ($paymentID)
				{
					$payment = new Payment('DB', $paymentID);
					$payment->makePayment ();
					print_r($_SESSION);
					Echo "<script> location.replace('/payment/list/'); </script>";
				}
			}
			else
			{
				User::UserReSign();
				Echo "<script> location.replace('/payment/list/'); </script>";
			}
			break;
		}
		case 'account':
		{

			$accClientID = sanitizeString ($_GET ['account']);



			if(!$accClientID)
			{
				$accClient = 7;

			} else
			{
				$accClient = $accClientID;
			}

			$payment = new Payment();

			echo <<<HTML

<div class = "container-fluid">
	<strong><i class = "glyphicon glyphicon-dashboard"></i> Наши счета</strong>
	<hr>
	<!--center-->
	<div class = "col-md-6">

			<div class = "panel panel-default">
				<div class = "panel-heading">Данные счетов</div>
HTML;

			$payment->echoAccounts($accClient);

			echo <<<HTML

</div>
</div>
</div>






HTML;
			break;
		}
		default:
		{

		}
	}
}
