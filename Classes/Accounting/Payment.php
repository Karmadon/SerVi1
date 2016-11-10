<?php

/**
 * Created by PhpStorm.
 * User: strem
 * Date: 28.07.2015
 * Time: 23:59
 */

require_once __DIR__ . '/../../Classes/Common/Records.php';


class Payment extends
	Records
{
	public
		$id,
		$date,
		$fromId,
		$fromAccountId,
		$fromAccountSTR,
		$fromSTR,
		$toId,
		$toAccountId,
		$toAccountSTR,
		$toSTR,
		$typeId,
		$typeSTR,
		$typeBAGE,
		$stateId,
		$stateSTR,
		$categoryId,
		$categorySTR,
		$stateBAGE,
		$drCrBool,
		$amount,
		$orderID,
		$name,
		$description;

	static public function getFullAmount ($clientId,$typeID)
	{

		if (!isset($connection))
		{
			getConnection ();
		}

		$sql = "SELECT NAME,TYPE,BAGE,BALANCE FROM payment_accounts acc
INNER JOIN payment_type typ ON acc.TYPE_ID = typ.ID
WHERE CLIENT_ID='$clientId' AND TYPE_ID = '$typeID'";
		$result = queryMysql ($sql);
		if (!$result)
		{
			echo "нет данных по счетам этого клиента ($clientId)";
			exit;
		} else
		{
			$fullsum = 0;
			for ($j = 0; $j < $result->num_rows; ++$j)
			{
				$row = $result->fetch_array (MYSQLI_ASSOC);
				$fullsum += $row['BALANCE'];
			}

		}
		return $fullsum;

	}

	static public function getFullSum ()
	{
		if (!isset($connection))
		{
			getConnection ();
		}

		$sql = "SELECT SUM(BALANCE) AS SUM FROM clients WHERE 1";
		$result = queryMysql ($sql);
		$row = $result->fetch_array (MYSQLI_ASSOC);
		return $row['SUM'];

	}
	static public function getFullMinusSum ()
	{
		if (!isset($connection))
		{
			getConnection ();
		}

		$sql = "SELECT SUM(BALANCE) AS SUM FROM clients WHERE BALANCE<0";
		$result = queryMysql ($sql);
		$row = $result->fetch_array (MYSQLI_ASSOC);
		return $row['SUM'];

	}

	public function createRecordInDB ()
	{
		global $insertID;


		if (!isset($this->connection))
		{
			$this->getConnection ();
		}

		global $insertID;


		if (!$this->fromId)
		{
			$result = queryMysql ("SELECT ID FROM clients WHERE SCREEN_NAME = '$this->fromSTR'");
			if ($result->num_rows)
			{
				$this->row = $result->fetch_array (MYSQLI_ASSOC);
				$this->fromId = $this->row ['ID'];
			} else
			{
				Echo 'Ошибка в отправителе денег (нет такого)';
				exit;
			}
		}

		if (!$this->toId)
		{
			$result = queryMysql ("SELECT ID FROM clients WHERE SCREEN_NAME = '$this->toSTR'");
			if ($result->num_rows)
			{
				$this->row = $result->fetch_array (MYSQLI_ASSOC);
				$this->toId = $this->row ['ID'];
			} else
			{
				Echo 'Ошибка в получателе денег (нет такого)';
				exit;
			}
		}


		$result = queryMysql ("SELECT ID FROM payment_accounts WHERE CLIENT_ID = '$this->fromId' AND TYPE_ID = '$this->typeId'"
		);
		if ($result->num_rows)
		{
			$this->row = $result->fetch_array (MYSQLI_ASSOC);
			$this->fromAccountId = $this->row ['ID'];
		} else
		{
			$tempString = '';

			if ($this->typeId === '1')
			{
				$tempString = 'Наличный';
			} elseif ($this->typeId === '2')
			{
				$tempString = 'Безаличный';
			}

			queryMysql ("	INSERT INTO payment_accounts (ID, CLIENT_ID, TYPE_ID, BALANCE, NAME)
							VALUES (NULL, '$this->fromId', '$this->typeId', '0.00', '$tempString счет для $this->fromSTR')
							ON DUPLICATE KEY UPDATE NAME = '$this->fromSTR'"
			);
			$this->fromAccountId = $insertID;
		}

		$result = queryMysql ("SELECT ID FROM payment_accounts WHERE CLIENT_ID = '$this->toId' AND TYPE_ID = '$this->typeId'"
		);
		if ($result->num_rows)
		{
			$this->row = $result->fetch_array (MYSQLI_ASSOC);
			$this->toAccountId = $this->row ['ID'];
		} else
		{
			$tempString = '';

			if ($this->typeId === '1')
			{
				$tempString = 'Наличный';
			} elseif ($this->typeId === '2')
			{
				$tempString = 'Безаличный';
			}

			queryMysql ("	INSERT INTO payment_accounts (ID, CLIENT_ID, TYPE_ID, BALANCE, NAME)
							VALUES (NULL, '$this->toId', '$this->typeId', '0.00', '$tempString счет для $this->toSTR')
							ON DUPLICATE KEY UPDATE NAME = '$this->toId'"
			);
			$this->toAccountId = $insertID;
		}

		if ($this->orderID)
		{
			$result = queryMysql ("SELECT ID FROM payments WHERE ACC_FROM_ID = '$this->fromAccountId' AND ACC_TO_ID = '$this->toAccountId' AND ORDER_ID = '$this->orderID'");

			if ($result->num_rows)
			{
				Echo <<<HTML

	<div class = 'col-lg-7'>
<div id = "myAlert" class = "alert alert-danger">
   <a href = "#" class = "close" data-dismiss = "alert">&times;</a>
   <strong>Эта заявка уже оплачена!</strong>.
</div>
</div>


<script type = "text/javascript">
   $(function(){
      $("#myAlert").bind('closed.bs.alert', function () {
         location.replace('/dashboard/view/');
      });
   });
</script>
HTML;
				exit;
			}
		}




		$query = "INSERT INTO payments (ID, DATE, ACC_FROM_ID, ACC_TO_ID, TYPE_ID, STATE_ID, DR_CR, AMOUNT, ORDER_ID, NAME, DESCRIPTION, CATEGORY_ID)
VALUES
	(
		NULL,
		'$this->date',
		'$this->fromAccountId',
		'$this->toAccountId',
		'$this->typeId',
		'$this->stateId',
		'$this->drCrBool',
		'$this->amount',
		'$this->orderID',
		'$this->name',
		'$this->description',
		'$this->categoryId')";

		$result = queryMysql ($query);
		$this->id = $insertID;

		if (!$result)
		{
			makeRecordInHistory ('6', '5', $this->id, "Ошибка при вставке записи ($this->name)");
			die ($this->connection->error);
		} else
		{
			$drcr = ($this->drCrBool) ? 'Приходный' : 'Расходный';
			makeRecordInHistory ('1',
			                     '5',
			                     $this->id,
			                     "Добавлен $drcr Платеж <b>$this->name</b> на сумму <strong>$this->amount</strong> грн."
			);

			return 1;
		}
	}

	public function editRecordInDB ($recordID)
	{
		global $createdID;

		if (!isset($this->connection))
		{
			$this->getConnection ();
		}


		$query = "";

		$result = queryMysql ($query);

		if (!$result)
		{
			makeRecordInHistory ('6', '5', $recordID, "Ошибка при изменении записи Клиент <b>$this->ScreenName</b>");
			die ($this->connection->error);
		} else
		{
			makeRecordInHistory ('2', '5', $recordID, "Изменен Платеж <b>$this->ScreenName</b>");

			return 1;
		}
	}

	public function echoListFromDB ($orderBy = 'DATE DESC', $limit = '50', $where = '1')
	{
		if (!isset($this->connection))
		{
			$this->getConnection ();
		}

		$orderBy = 'ORDER BY ' . $orderBy;
		$limit = 'LIMIT ' . $limit;
		$where = 'WHERE ' . $where;

		$result = queryMysql ("SELECT
	pay.ID           AS ID,
	pay.DATE         AS DATE,
	cli.ID           AS FROM_ID,
	acc.ID           AS ACC_FROM_ID,
	acc.NAME         AS ACC_FROM,
	cli.SCREEN_NAME  AS FROMM,
	cli1.ID          AS TO_ID,
	acc1.ID          AS ACC_TO_ID,
	acc1.NAME        AS ACC_TO,
	cli1.SCREEN_NAME AS TOO,
	typ.ID           AS TYPE_ID,
	typ.TYPE         AS TYPE,
	typ.BAGE         AS TYPE_BAGE,
	sta.STATE        AS STATE,
	sta.ID           AS STATE_ID,
	sta.BAGE         AS STATE_BAGE,
	cat.ID           AS CATEGORY_ID,
	cat.NAME         AS CATEGORY,
	pay.DR_CR        AS DR_CR,
	pay.AMOUNT       AS AMOUNT,
	pay.ORDER_ID     AS ORDER_ID,
	pay.NAME         AS NAME,
	pay.DESCRIPTION  AS DESCRIPTION

FROM payments pay
	INNER JOIN payment_accounts acc
		ON pay.ACC_FROM_ID = acc.ID
	INNER JOIN clients cli
		ON acc.CLIENT_ID = cli.ID
	INNER JOIN payment_accounts acc1
		ON pay.ACC_TO_ID = acc1.ID
	INNER JOIN clients cli1
		ON acc1.CLIENT_ID = cli1.ID
	INNER JOIN payment_category cat
		ON pay.CATEGORY_ID = cat.ID
	INNER JOIN payment_type typ
		ON pay.TYPE_ID = typ.ID
	INNER JOIN payment_states sta
		ON pay.STATE_ID = sta.ID
$where
$orderBy
$limit"
		);

		echo <<< HTML
				<div class = "table-responsive">
					<table class = "table table-hover">
						<thead>
						<tr>
							<th>Дата</th>
							<th>Название</th>
							<th>Плательщик</th>
							<th>Получатель</th>
							<th>Сумма</th>
							<th>Тип</th>
							<th>Категория</th>
							<th>Состояние</th>
						</tr>
						</thead>
						<tbody>
HTML;

		for ($j = 0; $j < $result->num_rows; ++$j)
		{
			$row = $result->fetch_array (MYSQLI_ASSOC);


//			$this->id = $this->row ['ID'];
//			$this->date = $this->row ['DATE'];
//			$this->fromId = $this->row ['FROM_ID'];
//			$this->fromAccountId = $this->row ['ACC_FROM_ID'];
//			$this->fromAccountSTR = $this->row ['ACC_FROM'];
//			$this->fromSTR = $this->row ['FROMM'];
//			$this->toId = $this->row ['TO_ID'];
//			$this->toAccountId = $this->row ['ACC_TO_ID'];
//			$this->toAccountSTR = $this->row ['TOO'];
//			$this->toSTR = $this->row ['ID'];
//			$this->typeId = $this->row ['TYPE_ID'];
//			$this->typeSTR = $this->row ['TYPE'];
//			$this->typeBAGE = $this->row ['TYPE_BAGE'];
//			$this->stateId = $this->row ['STATE_ID'];
//			$this->stateSTR = $this->row ['STATE'];
//			$this->stateBAGE = $this->row ['STATE_BAGE'];
//			$this->categoryId = $this->row ['CATEGORY_ID'];
//			$this->categorySTR = $this->row ['CATEGORY'];
//			$this->drCrBool = $this->row ['DR_CR'];
//			$this->amount = $this->row ['AMOUNT'];
//			$this->orderID = $this->row ['ORDER_ID'];
//			$this->name = $this->row ['NAME'];
//			$this->description = $this->row ['DESCRIPTION'];


			echo '<tr class="' . $row['STATE_BAGE'] . '">
                                    <td>' . date_format (date_create ($row['DATE']), 'd.m.Y') . '</td>
                                    <td><a href="/payment/view/' . $row['ID'] . '">' . $row['NAME'] . '</a></td>
                                    <td><a href="/client/view/' . $row['FROM_ID'] . '">' . $row['FROMM'] . '</a></td>
                                    <td><a href="/client/view/' . $row['TO_ID'] . '">' . $row['TOO'] . '</a></td>
                                    <td><strong>' . number_format ($row['AMOUNT'], 2, '.', '') . '</strong> грн.</td>
                                    <td>' . $row['TYPE'] . '</td>
                                    <td>' . $row['CATEGORY'] . '</td>
                                    <td><span class = "label label-' . $row['STATE_BAGE'] . '">' . $row['STATE'] . '</span></td>
                            </tr>';

		}

		echo <<<HTML
						</tbody>
					</table>
				</div>
HTML;


	}

	public function initFromOrderObject ($orderId)
	{
		$order = new Order('DB', $orderId);

		$this->orderID = $order->orderId;
		$this->fromId = $order->clientID;
		$this->fromSTR = $order->clientSTR;
		$this->toId = 7;                                                                 // Very magic
		$this->toSTR = 'Computer Masters';                                      // Hard Coded ((
		$this->amount = Labor::getFullCostOfOrder ($orderId);
		$this->name = 'Оплата по заявке № ' . $order->orderId;
		$this->description = 'Приходная операция по заявке № ' . $order->orderId;
		$this->categoryId = 31;
		$this->categorySTR = 'Оплата заявок';
		$this->date = date ('Y-m-d H:i:s');
	}

	public function echoPaymentFilters ()
	{
		if (!isset($this->connection))
		{
			$this->getConnection ();
		}

		$sql = 'SELECT * FROM payment_states';
		$result = queryMysql ($sql);

		for ($j = 0; $j < $result->num_rows; ++$j)
		{
			$row = $result->fetch_array (MYSQLI_ASSOC);

			$stateID = $row['ID'];
			$stateSTR = $row['STATE'];
			$bage = $row['BAGE'];

			$sql = "SELECT COUNT(*) FROM payments WHERE STATE_ID = '$stateID'";
			$countResult = queryMysql ($sql);
			$rowCount = $countResult->fetch_array (MYSQLI_ASSOC);
			$count = $rowCount['COUNT(*)'];

			if ($count)
			{
				$count = "<span class='badge'>$count</span>";
			} else
			{
				$count = '';
			}

			echo <<<HTML
<a href="/payment/list/1/state/$stateID" class="list-group-item list-group-item-$bage">$stateSTR $count</a>
HTML;

		}

	}

	public function echoPayment ($recordID)
	{
		$this->initFromDB ($recordID);

		$niceAmount = number_format ($this->amount, 2, '.', '');

		$sign = $_SESSION ['sign'];
		echo <<<HTML
	<!--center-->
	<div class = "col-md-6">
<div class="list-group">
  <a href="/order/view/$this->orderID" class="list-group-item list-group-item-success"><h4>$this->name</h4></a>
  <a href="#" class="list-group-item panel-default">Дата: <strong>$this->date</strong></a>
  <a href="#" class="list-group-item panel-default">Категория: <strong>$this->categorySTR</strong></a>
  <a href="#" class="list-group-item panel-default">Вид платежа: <strong>$this->typeSTR</strong></a>
  <a href="#" class="list-group-item panel-default">Тип платежа: <strong>$this->drCrBool</strong></a>
  <a href="/client/view/$this->fromId" class="list-group-item list-group-item-info">Плательщик: <strong>$this->fromSTR</strong></a>
  <a href="/client/view/$this->toId" class="list-group-item list-group-item-warning">Получатель: <strong>$this->toSTR</strong></a>
  <a href="#" class="list-group-item list-group-item-danger"><h1>$niceAmount </h1>грн</a>
</div>

 <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Проводка платежа</h4>
        </div>
        <div class="modal-body">
          <div class="list-group">
  <a href="#" class="list-group-item active">
    <h4 class="list-group-item-heading">$this->name</h4>
    <p class="list-group-item-text">От $this->date</p>
  </a><a href="#" class="list-group-item">
    <h4 class="list-group-item-heading">Плательщик</h4>
    <p class="list-group-item-text">$this->fromSTR</p>
  </a>  </a><a href="#" class="list-group-item">
    <h4 class="list-group-item-heading">Получатель</h4>
    <p class="list-group-item-text">$this->toSTR</p>
  </a>  </a><a href="#" class="list-group-item">
    <h4 class="list-group-item-heading">Статус платежа</h4>
    <p class="list-group-item-text">$this->stateSTR</p>
  </a></a>  </a><a href="#" class="list-group-item">
    <h4 class="list-group-item-heading">Общая сумма</h4>
    <h1 class="list-group-item-text">$niceAmount  грн.</h1>
  </a>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="post('/payment/make/$this->id', {order: '$this->orderID',payment: '$this->id', sign: '$sign'});">Провести этот платеж</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
        </div>
      </div>

    </div>
  </div>
  </div>

HTML;


	}

	public function initFromDB ($recordID)
	{
		if (!isset($this->connection))
		{
			$this->getConnection ();
		}

		$result = queryMysql ("SELECT
	pay.ID           AS ID,
	pay.DATE         AS DATE,
	cli.ID           AS FROM_ID,
	acc.ID           AS ACC_FROM_ID,
	acc.NAME         AS ACC_FROM,
	cli.SCREEN_NAME  AS FROMM,
	cli1.ID          AS TO_ID,
	acc1.ID          AS ACC_TO_ID,
	acc1.NAME        AS ACC_TO,
	cli1.SCREEN_NAME AS TOO,
	typ.ID           AS TYPE_ID,
	typ.TYPE         AS TYPE,
	typ.BAGE         AS TYPE_BAGE,
	sta.STATE        AS STATE,
	sta.ID           AS STATE_ID,
	sta.BAGE         AS STATE_BAGE,
	cat.ID           AS CATEGORY_ID,
	cat.NAME         AS CATEGORY,
	pay.DR_CR        AS DR_CR,
	pay.AMOUNT       AS AMOUNT,
	pay.ORDER_ID     AS ORDER_ID,
	pay.NAME         AS NAME,
	pay.DESCRIPTION  AS DESCRIPTION

FROM payments pay
	INNER JOIN payment_accounts acc
		ON pay.ACC_FROM_ID = acc.ID
	INNER JOIN clients cli
		ON acc.CLIENT_ID = cli.ID
	INNER JOIN payment_accounts acc1
		ON pay.ACC_TO_ID = acc1.ID
	INNER JOIN clients cli1
		ON acc1.CLIENT_ID = cli1.ID
	INNER JOIN payment_category cat
		ON pay.CATEGORY_ID = cat.ID
	INNER JOIN payment_type typ
		ON pay.TYPE_ID = typ.ID
	INNER JOIN payment_states sta
		ON pay.STATE_ID = sta.ID
WHERE pay.ID = '$recordID'"
		);


		if ($result->num_rows)
		{
			$this->num = $result->num_rows;
			$this->row = $result->fetch_array (MYSQLI_ASSOC);

			$this->id = $this->row ['ID'];
			$this->date = $this->row ['DATE'];
			$this->fromId = $this->row ['FROM_ID'];
			$this->fromAccountId = $this->row ['ACC_FROM_ID'];
			$this->fromAccountSTR = $this->row ['ACC_FROM'];
			$this->fromSTR = $this->row ['FROMM'];
			$this->toId = $this->row ['TO_ID'];
			$this->toAccountId = $this->row ['ACC_TO_ID'];
			$this->toAccountSTR = $this->row ['ACC_TO'];
			$this->toSTR = $this->row ['TOO'];
			$this->typeId = $this->row ['TYPE_ID'];
			$this->typeSTR = $this->row ['TYPE'];
			$this->typeBAGE = $this->row ['TYPE_BAGE'];
			$this->stateId = $this->row ['STATE_ID'];
			$this->stateSTR = $this->row ['STATE'];
			$this->stateBAGE = $this->row ['STATE_BAGE'];
			$this->categoryId = $this->row ['CATEGORY_ID'];
			$this->categorySTR = $this->row ['CATEGORY'];
			$this->drCrBool = $this->row ['DR_CR'];
			$this->amount = $this->row ['AMOUNT'];
			$this->orderID = $this->row ['ORDER_ID'];
			$this->name = $this->row ['NAME'];
			$this->description = $this->row ['DESCRIPTION'];

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

		$this->date = date ('Y-m-d H:i:s');
		$this->name = post ('paymentName');
		$this->fromSTR = post ('paymentFrom');
		$this->toSTR = post ('paymentTo');
		$this->amount = post ('amount');
		$this->typeId = post ('paymentType');
		$this->categoryId = post ('paymentCategory');
		$this->description = post ('paymentDescription');

		if ($this->orderID)
		{
			$this->stateId = 2;                                                     // Very magic
		} else
		{
			$this->stateId = 4;
		}
	}

	public function makePayment ()
	{

		$orderId = post ('order');
		$paymentId = post ('payment');

		if (!isset($this->connection))
		{
			$this->getConnection ();
		}

		$this->initFromDB ($paymentId);

		$client = new Client('DB',$this->fromId);

		$totalAmount = $client->balance + $this->amount;


		$rest = $totalAmount - Labor::getFullCostOfOrder($this->orderID);
		$client->setBalanceOfClient($rest);




		$fromsql = "UPDATE payment_accounts SET BALANCE=BALANCE - $this->amount WHERE ID = '$this->fromAccountId'";
		$result = queryMysql ($fromsql);
		if (!$result)
		{
			makeRecordInHistory ('6', '5', $this->id, "Ошибка при изменении записи Платеж (from)<b>$this->name</b>");
			die ($this->connection->error);
		}

		$tosql = "UPDATE payment_accounts SET BALANCE=BALANCE + $this->amount WHERE ID = '$this->toAccountId'";
		$result = queryMysql ($tosql);
		if (!$result)
		{
			makeRecordInHistory ('6', '5', $this->id, "Ошибка при изменении записи Платеж (to) <b>$this->name</b>");
			die ($this->connection->error);
		}

		$sql = "UPDATE payments SET STATE_ID='1' WHERE ID = $this->id";
		$result = queryMysql ($sql);
		if (!$result)
		{
			makeRecordInHistory ('6', '5', $this->id, "Ошибка при изменении записи Платеж (to) <b>$this->name</b>");
			die ($this->connection->error);
		}

		User::UserReSign();
		Order::markOrderPayed ($orderId);


	}

	public function echoAccounts ($clientId)
	{
		if (!isset($this->connection))
		{
			$this->getConnection ();
		}

		//$client = new Client('DB',$clientId);

		$sql = "SELECT NAME,TYPE,BAGE,BALANCE FROM payment_accounts acc
INNER JOIN payment_type typ ON acc.TYPE_ID = typ.ID
WHERE CLIENT_ID='$clientId'";
		$result = queryMysql ($sql);
		if (!$result)
		{
			echo "нет данных по счетам этого клиента ($clientId)";
			exit;
		} else
		{
			echo <<<HTML
<div class="list-group">
HTML;
			for ($j = 0; $j < $result->num_rows; ++$j)
			{
				$row = $result->fetch_array (MYSQLI_ASSOC);

				$name = $row['NAME'];
				$type = $row['TYPE'];
				$bage = $row['BAGE'];
				$balance = number_format ($row['BALANCE'], 2, '.', '');

				echo <<<HTML

  <a href="#" class="list-group-item list-group-item-$bage">
    <h4 class="list-group-item-heading">$name</h4>
    <p class="list-group-item-text">Тип счета: <strong>$type</strong></p>
    <p class="list-group-item-text">Баланс: <strong>$balance</strong> грн</p>

  </a>

HTML;

			}
			echo <<<HTML
			</div>
HTML;

		}


	}


}