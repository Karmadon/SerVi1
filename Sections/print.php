<?php
/**
 * Created by PhpStorm.
 * User: strem
 * Date: 17.10.2015
 * Time: 10:17
 */
//==============================================================
//=======================HEADER=================================
//==============================================================

require_once __DIR__ . '/../Plugins/mpdf/mpdf.php';


$settingsArray = getSettings();

$companyName = $settingsArray['CompanyName'];
$systemName = $settingsArray['SysName'];
$systemVer = $settingsArray['Version'];
$workDir = $settingsArray['WorkDir'];

$companyAddres = $settingsArray['CompanyAdress'];
$CompanyRealName = $settingsArray['CompanyRealName'];
$CompanyTelephone = $settingsArray['CompanyTelephone'];
$CompanyPersNumber = $settingsArray['CompanyPersNumber'];
$CompanyBankNumber = $settingsArray['CompanyBankNumber'];
$CompanyBankAccountNumber = $settingsArray['CompanyBankAccountNumber'];
$CompanyBankName = $settingsArray['CompanyBankName'];
	$username = $_SESSION ['login'];
	$date = date ('l jS \of F Y h:i:s A');
	$techFullName = $_SESSION ['lastname'] . ' ' . $_SESSION ['firstname'];
	$techFullName = preg_replace ('~^(\S++)\s++(\S)\S++\s++(\S)\S++$~u', '$1 $2.$3.', $techFullName);
//==============================================================
//========================END OF HEADER=========================
//==============================================================

global $action,$actionAdd;

if (isset($action))
{
	switch ($action)
	{
		case 'tech_copy':
		{


			$OrderID = $actionAdd;
			if ($OrderID)
			{
				$order = new Order('DB', $OrderID);
				if (!$order->orderId)
				{
					header ('Location:/index.php');
				}

				if ($order->deviceID)
				{
					$device = new Device('DB', $order->deviceID);
				}
				if ($order->clientID)
				{
					$client = new Client('DB', $order->clientID);
				}
			} else
			{
				header ('Location:/index.php');
			}


			$barcodeNum = createBarcodeNumber ($order->orderId, $device->id, $client->id);

			$html = <<<HTML
<html>
<style>
	body {
		font-family: sans-serif;
		font-size: 9pt;
		background: transparent url('http://localhost/Assets/images/bgbarcode.png') repeat-y scroll left top;
	}

	h5, p {
		margin: 0pt;
	}

	table.items {
		font-size: 9pt;
		border-collapse: collapse;
	}

	td {
		vertical-align: top;
	}

	table {
		margin-left: 20px;
	}

	table thead td {
		background-color: #EEEEEE;
		text-align: center;
	}

	table tfoot td {
		background-color: #AAFFEE;
		text-align: center;
	}

	.barcode {
		padding: 1.5mm;
		margin: 0;
		vertical-align: top;
		color: #000000;
	}

	.barcodecell {
		text-align: center;
		vertical-align: middle;
		padding: 0;
	}
</style>
</head>
<body>
<!--mpdf
<htmlpageheader name="myheader">
<table width="100%"><tr>

<td width="50%" style="text-align: right;">Стикер No.<br /><span style="font-weight: bold; font-size: 12pt;">$device->id</span></td>
</tr></table>
</htmlpageheader>

<htmlpagefooter name="myfooter">
<p>Создана пользователем: <strong>$techFullName, </strong>$date</p>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->


<table class = "items" width = "100%" cellpadding = "5" border = "0">
	<tbody>


	<tr>
		<td width = "20%" align = "center" valign = "top"><img
				src = "/Assets/images/logo.png" width = "100" height = "64" alt = ""/></td>
		<td width = "60%" align = "center" valign = "top">
			<h1>Техническая копия для заявки</h1>

			<h3>Заявка № $order->orderId от $order->orderDate</h3></td>

		<td width = "20%" align = "center" valign = "top">
			<br>
			<barcode code = "$barcodeNum" type = "C128A"/>
			$barcodeNum
			<br><br>
	</tr>
</table>

<table class = "items" width = "100%" cellpadding = "5" border = "1">
	<thead>
	<tr>
		<td width = "20%" height = "23">Компания:</td>
		<td colspan = "2" valign = "top">Описание заявки:</td>
		<td width = "20%">Клиент:</td>
	</tr>
	</thead>
	<tr>
		<td height = "129">
			<ul>
			<small>
				<li>ФОП <strong>$CompanyRealName</strong></li>
				<li>Адрес: <strong>$companyAddres</strong></li>
				<li>Р/Р: <strong>$CompanyBankAccountNumber</strong></li>
				<li>Банк: <strong>$CompanyBankName</strong></li>
				<li>МФО: <strong>$CompanyBankNumber</strong></li>
				<li>ЕДРПОУ: <strong>$CompanyPersNumber</strong></li>
				<li>Тел.: <strong>$CompanyTelephone</strong></li>
				<li>Отв.: <strong>$CompanyRealName</strong></li>
			</small>
			</ul>
		</td>
		<td colspan = "2" align = "left" valign = "top"><p>Название: <strong>$order->orderName</strong></p>

			<p>Описание:</p>

			<p><br></p>

			<p><br></p>

			<p>Коментарии:</p>

			<p><br></p>

			<p><br></p>

			</td>
		<td width = "20%" align = "left">
		<small>
			<p>ID Клиента: <strong>$client->id</strong></p>

			<p>Наименование: <strong>$client->ScreenName</strong></p>

			<p>Организация: <strong>$client->organizationName</strong></p>

			<p>Имя, Фамилия: <strong>$client->LastName, $client->FirstName </strong></p>

			<p>Телефон: <strong>$client->phone1</strong></p>

			<p>Доп. Тел.: <strong>$client->phone2</strong></p>

			<p>Адрес: <strong>$client->city, $client->adress</strong></p>
		</small>
		</td>
	</tr>
	<thead>
	<tr>
		<td width = "20%">Дата и время.</td>
		<td colspan = "2">Комплект и дополнительное оборудование:</td>
		<td width = "20%">Устройство</td>
	</tr>
	</thead>
	<tr>
		<td width = "20%" height = "143" rowspan = "5" align = "left">
			<p>Прибытие:<strong> ___.___._____, __:__</strong></p>

			<p>Убытие: <strong>___.___._____, __:__</strong></p>

			<p>Время: <strong>___:___</strong></p>
			<hr>
			<p><strong> Служебные отметки:
			</strong>
			</p>

			<p>-</p>

			<p>-</p>

			<p>-</p>

			<p>-</p>

			<p>-</p>

			<p>-</p>
		</td>
		<td height = "73" colspan = "2">

		</td>
		<td width = "20%" rowspan = "5">
		<small>
			<p>Стикер на устройстве: <strong>$device->id</strong></p>

			<p>Наименование: <strong>$device->name</strong></p>

			<p>Модель: <strong>$device->modelSTR</strong></p>

			<p>Производитель: <strong>$device->manufacturerSTR</strong></p>

			<p>Серийный номер: <strong>$device->serial</strong></p>

			<p>Категория: <strong>$device->categorySTR</strong></p>

			<p>Тип устройства: <strong>$device->typeSTR</strong></p>
			</small>
			</td>
	</tr>
	<tr>
		<td width = "3%" align = "center" valign = "middle">1.</td>
		<td width = "57%"><br></td>
	</tr>
	<tr>
		<td align = "center" valign = "middle">2.</td>
		<td><br></td>
	</tr>
	<tr>
		<td align = "center" valign = "middle">3.</td>
		<td><br></td>
	</tr>
	<tr>
		<td align = "center" valign = "middle">4.</td>
		<td><br></td>
	</tr>
	<thead>
	<tr>
		<td width = "20%">Техник:</td>
		<td colspan = "2">Техническая информация по заявке:</td>
		<td width = "20%">Доставка.</td>
	</tr>
	<tr>
	</thead>
	<tr>
		<td width = "20%" height = "125"><p>Имя: $techFullName</p>

			<p>Телефон:</p>
			<hr>
			<p><strong> Служебные отметки:
			</strong>
			</p>

			<p>-</p>

			<p>-</p>

			<p>-</p>

			<p>-</p>

			<p>-</p>

			<p>-</p>
		</td>
		<td colspan = "2"><br></td>
		<td width = "20%"><p>Время пути: <strong>____:____.</strong></p>

			<p>Растояние: <strong>_______ km.</strong></p>
			<hr>
			<p><strong> Служебные отметки:
			</strong>
			</p>

			<p>-</p>

			<p>-</p>

			<p>-</p>

			<p>-</p>

			<p>-</p>

			<p>-</p>
</td>
	</tr>
	</tbody>
</table>

HTML;


			//==============================================================
			//==============================================================
			//==============================================================


			$mpdf = new mPDF('utf-8', 'A4-L');

			//$mpdf->progbar_altHTML = '<html><body>
			//	<div style="margin-top: 5em; text-align: center; font-family: Verdana; font-size: 12px;"><img style="vertical-align: middle" src="loading.gif" /> Создаем PDF файл. Подождите минутку...</div>';
			//$mpdf->StartProgressBarOutput();


			$mpdf->SetProtection (array ('print'));
			$mpdf->SetTitle ("СЦ \"$companyName\". - Техническая копия");
			$mpdf->SetAuthor ("$companyName.");
			$mpdf->SetWatermarkText ("Внутренний документ");
			$mpdf->showWatermarkText = TRUE;
			$mpdf->watermark_font = 'DejaVuSansCondensed';
			$mpdf->watermarkTextAlpha = 0.1;
			$mpdf->SetDisplayMode ('fullpage');

			$mpdf->WriteHTML ($html);

			$mpdf->Output ("tech_copy-$order->orderId.pdf",'I');
			exit;

			//==============================================================
			//==============================================================
			//==============================================================
			break;
		}
		case 'service_book':
		{


			$OrderID = $actionAdd;
			if ($OrderID)
			{
				$order = new Order('DB', $OrderID);
				if (!$order->orderId)
				{
					header ('Location:/index.php');
				}

				if ($order->deviceID)
				{
					$device = new Device('DB', $order->deviceID);
				}
				if ($order->clientID)
				{
					$client = new Client('DB', $order->clientID);
				}
			} else
			{
				header ('Location:/index.php');
			}


			$barcodeNum = createBarcodeNumber ($order->orderId, $device->id, $client->id);

			$html = <<<HTML
<html>
<style>
	body {
		font-family: sans-serif;
		font-size: 9pt;
		background: transparent url('http://localhost/Assets/images/bgbarcode.png') repeat-y scroll left top;
	}

	h5, p {
		margin: 0pt;
	}

	table.items {
		font-size: 9pt;
		border-collapse: collapse;
	}

	td {
		vertical-align: top;
	}

	table {
		margin-left: 20px;
	}

	table thead td {
		background-color: #EEEEEE;
		text-align: center;
	}

	table tfoot td {
		background-color: #AAFFEE;
		text-align: center;
	}

	.barcode {
		padding: 1.5mm;
		margin: 0;
		vertical-align: top;
		color: #000000;
	}

	.barcodecell {
		text-align: center;
		vertical-align: middle;
		padding: 0;
	}
</style>
</head>
<body>
<!--mpdf
<htmlpageheader name="myheader">
<table width="100%"><tr>

<td width="50%" style="text-align: right;">Стикер No.<br /><span style="font-weight: bold; font-size: 12pt;">$device->id</span></td>
</tr></table>
</htmlpageheader>

<htmlpagefooter name="myfooter">
<p>Создана пользователем: <strong>$techFullName, </strong>$date</p>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->


<table class = "items" width = "100%" cellpadding = "5" border = "0">
	<tbody>


	<tr>
		<td width = "20%" align = "center" valign = "top"><img
				src = "http://localhost/Assets/images/logo.png" width = "100" height = "64" alt = ""/></td>
		<td width = "60%" align = "center" valign = "top">
			<h1>Сервисная книга</h1>

			<h3>Устройство № $order->orderId от $order->orderDate</h3></td>

		<td width = "20%" align = "center" valign = "top">
			<br>
			<barcode code = "$barcodeNum" type = "C128A"/>
			$barcodeNum
			<br><br>
	</tr>
	</tbody>
</table>

<table class = "items" width = "100%" cellpadding = "5" border = "1">
<tbody>
	<thead>
	<tr>
		<td width = "20%" height = "23">Компания:</td>
		<td colspan = "2" valign = "top">Описание заявки:</td>
		<td width = "20%">Клиент:</td>
	</tr>
	</thead>

	</tbody>
</table>

HTML;


			//==============================================================
			//==============================================================
			//==============================================================


			$mpdf = new mPDF('utf-8', 'A4-L');

			//$mpdf->progbar_altHTML = '<html><body>
			//	<div style="margin-top: 5em; text-align: center; font-family: Verdana; font-size: 12px;"><img style="vertical-align: middle" src="loading.gif" /> Создаем PDF файл. Подождите минутку...</div>';
			//$mpdf->StartProgressBarOutput();


			$mpdf->SetProtection (array ('print'));
			$mpdf->SetTitle ("СЦ \"$companyName\". - Техническая копия");
			$mpdf->SetAuthor ("$companyName.");
			$mpdf->SetWatermarkText ("Внутренний документ");
			$mpdf->showWatermarkText = TRUE;
			$mpdf->watermark_font = 'DejaVuSansCondensed';
			$mpdf->watermarkTextAlpha = 0.1;
			$mpdf->SetDisplayMode ('fullpage');

			$mpdf->WriteHTML ($html);

			$mpdf->Output ("service_book-$order->orderId.pdf",'I');
			exit;

			//==============================================================
			//==============================================================
			//==============================================================
			break;
		}
		case 'ticket':
		{


			$OrderID = $actionAdd;
			if ($OrderID)
			{
				$order = new Order('DB', $OrderID);
				if (!$order->orderId)
				{
					header ('Location:/index.php');
				}

				if ($order->deviceID)
				{
					$device = new Device('DB', $order->deviceID);
				}
				if ($order->clientID)
				{
					$client = new Client('DB', $order->clientID);
				}
			} else
			{
				header ('Location:/index.php');
			}


			$barcodeNum = createBarcodeNumber ($order->orderId, $device->id, $client->id);

			$html = <<<HTML
<html>
<style>
	body {
		font-family: sans-serif;
		font-size: 9pt;
		background: transparent url('http://localhost/Assets/images/bgbarcode.png') repeat-y scroll left top;
	}

	p.small {
    font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: 8pt; /* Размер шрифта в пунктах */
   }

	h5, p {
		margin: 0pt;
	}

	table.items {
		font-size: 9pt;
		border-collapse: collapse;
	}

	td {
		vertical-align: top;
	}

	table {
		margin-left: 20px;
	}

	table thead td {
		background-color: #EEEEEE;
		text-align: center;
	}

	table tfoot td {
		background-color: #AAFFEE;
		text-align: center;
	}

	.barcode {
		padding: 1.5mm;
		margin: 0;
		vertical-align: top;
		color: #000000;
	}

	.barcodecell {
		text-align: center;
		vertical-align: middle;
		padding: 0;
	}
</style>
</head>
<body>
<!--mpdf
<htmlpageheader name="myheader">
<table width="100%"><tr>

<td width="50%" style="text-align: right;">Заявка No.<br /><span style="font-weight: bold; font-size: 12pt;">$order->orderId</span></td>
</tr></table>
</htmlpageheader>

<htmlpagefooter name="myfooter">
<p>Создана пользователем: <strong>$techFullName, </strong>$date</p>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table class = "items" width = "100%" cellpadding = "5" border = "0">
	<tbody>


	<tr>
		<td width = "20%" align = "center" valign = "top"><img
				src = "http://localhost/Assets/images/logo.png" width = "100" height = "64" alt = ""/></td>
		<td width = "60%" align = "center" valign = "top">
			<h1>Квитанция для клиента</h1>

			<h3>Заявка № $order->orderId от $order->orderDate</h3></td>

		<td width = "20%" align = "center" valign = "top">
			<br>
			<barcode code = "$barcodeNum" type = "C128A"/>
			$barcodeNum
			<br><br>
	</tr>
</table>
<hr>
<table width="100%" border="2" class = "items" border = "1">
  <tbody>
  <thead>
    <tr>
      <td width="50%">Устройство</td>
      <td width="50%">Клиент</td>
    </tr>
    </thead>
    <tr>
      <td><p>Стикер на устройстве: <strong>$device->id</strong></p>
        <p>Наименование: <strong>$device->name</strong></p>
        <p>Модель: <strong>$device->modelSTR</strong></p>
        <p>Производитель: <strong>$device->manufacturerSTR</strong></p>
        <p>Серийный номер: <strong>$device->serial</strong></p>
        <p>Категория: <strong>$device->categorySTR</strong></p>
      <p>Тип устройства: <strong>$device->typeSTR</strong></p></td>
      <td><p>ID Клиента: <strong>$client->id</strong></p>
        <p>Наименование: <strong>$client->ScreenName</strong></p>
        <p>Организация: <strong>$client->organizationName</strong></p>
        <p>Имя, Фамилия: <strong>$client->LastName $client->FirstName </strong></p>
        <p>Телефон: <strong>$client->phone1</strong></p>
        <p>Доп. Тел.: <strong>$client->phone2</strong></p>
      <p>Адрес: <strong>$client->city, $client->adress</strong></p></td>
    </tr>
    <thead>
    <tr>
      <td colspan="2" width="100%" align="center" valign="middle">Заявка:</td>
    </tr>
    </thead>
    <tr>
      <td height="80" colspan="2"><p>Название: <strong>$order->orderName</strong></p>
        <p>Описание: <strong>$order->description</strong></p>
        <p>Коментарии:</p></td>
    </tr>
  </tbody>
</table>
<hr>
<ol>
  <li>
    <p class='small'>Данный документ удостоверяет прием оборудования с целью  проведения проверки на возможность осуществления ремонта и не свидетельствует о  переходе прав владения и пользования</p>
  </li>
  <li>
    <p class='small'>Технический центр не несет ответственности за  принадлежности и аксессуары, а также дефекты внешнего вида, не указанные  Клиентом в квитанции при сдаче аппарата и ремонт.</p>
  </li>
  <li>
    <p class='small'>Технический центр не несет ответственности за возможную  потерю пользовательских данных (файлы записной книги. СМС-сообщения, мелодии  звонков и т.п.) в индивидуальной памяти устройств в процессе устранения  недостатков, связанную с перепрограммированием. заменой блоков памяти, плат,  установкой программного обеспечения.</p>
  </li>
  <li>
    <p class='small'>Оборудование  принято без подтверждения заявленных Клиентом неисправностей, без разборки и  проверки внутренних повреждений. Клиент подтверждает, что все неисправности и  внутренние повреждения, которые могут быть обнаружены в оборудовании в процессе  осуществления сервисного обслуживания, возникли до приема оборудования в  соответствии с данной квитанцией. </p>
  </li>
  <li>
    <p class='small'>Клиент принимает на себя риск, связанный с возможным проявлением  в сданном оборудовании дефектов, не указанных в настоящей квитанции, а также  риск возможной полной или частичной утраты работоспособности оборудования или  отдельных его узлов и блоков в процессе устранения недостатков в случае наличия  в оборудовании следов электрохимической коррозии, попадания влаги, механических  воздействий,</p>
  </li>
  <li>
    <p class='small'>Выдача оборудования, переданного в ремонт юридическим  лицом (организацией), а также в случае, если оплата услуги произведена  безналичным платежом, осуществляется только при наличии у получателя  доверенности от юридического лица (организации) установленного образца (ст. 244  Гражданского кодекса Украины).</p>
  </li>
  <li>
    <p class='small'>В случае утраты настоящей квитанции Клиент обязан незамедлительно  известить об этом Технический центр в письменной форме. В этом случае  оборудование, указанное в квитанции, возвращается на основании письменного  заявления Клиента по предъявлении им паспорта или иного документа,  удостоверяющего личность. (Документ: «Про затвердження правил побутового обслуговування  населення»)</p>
  </li>
  <li>
    <p class='small'>Срок исполнения  заказов (включая диагностику) составляет до шести месяцев с момента заключения  договора. </p>
  </li>
  <li>
    <p class='small'>Клиент обязуется  получить переданное ранее оборудование в течение пяти рабочих дней после  уведомления Техническим центром о готовности оборудования к выдаче. По  истечении указанного срока. Технический центр имеет право потребовать от  Клиента возмещения расходов на храпение в размере 17грн. за календарный день  хранения (Основание: Ст. 947 ГК Украины «Возмещение расходов на хранение») </p>
  </li>
  <li>
    <p class='small'>Претензии и  предложения по основным направлениям деятельности Технического центра  принимаются только в письменной форме в виде заказного почтового отправления с  обратным адресом. Срок ответа администрации составляет до 30 рабочих дней с  момента получения почтового отправления. </p>
  </li>
</ol>
<hr>
<table width="100%" border="2">
  <tbody>
    <tr>
      <td width="50%"><p>Представитель исполнителя</p>
      <p>&nbsp;</p></td>
      <td width="50%"><p>Квитанция заполнена верно. С условиями ремонта изложенными выше, ознакомлен и согласен.</p>
      <p>&nbsp;</p></td>
    </tr>
    <tr>
      <td width="50%">$techFullName/__________________/</td>
      <td width="50%">______________________/__________________/</td>
    </tr>
  </tbody>
</table>
HTML;


			//==============================================================
			//==============================================================
			//==============================================================


			$mpdf = new mPDF('utf-8', 'A4');

			//$mpdf->progbar_altHTML = '<html><body>
			//	<div style="margin-top: 5em; text-align: center; font-family: Verdana; font-size: 12px;"><img style="vertical-align: middle" src="loading.gif" /> Создаем PDF файл. Подождите минутку...</div>';
			//$mpdf->StartProgressBarOutput();

			$mpdf->SetProtection (array ('print'));
			$mpdf->SetTitle ("СЦ \"$companyName\". - Техническая копия");
			$mpdf->SetAuthor ("$companyName.");
			$mpdf->SetWatermarkText ("Документ Клиента");
			$mpdf->showWatermarkText = TRUE;
			$mpdf->watermark_font = 'DejaVuSansCondensed';
			$mpdf->watermarkTextAlpha = 0.1;
			$mpdf->SetDisplayMode ('fullpage');

			$mpdf->WriteHTML ($html);

			$mpdf->Output ("ticket-$order->orderId.pdf",'I');
			exit;

			//==============================================================
			//==============================================================
			//==============================================================
			break;
		}
		case 'empty_ticket':
		{

			$barcodeNum = time ();

			$html = <<<HTML
<html>
<style>
	body {
		font-family: sans-serif;
		font-size: 9pt;
		background: transparent url('http://localhost/Assets/images/bgbarcode.png') repeat-y scroll left top;
	}

	p.small {
    font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: 8pt; /* Размер шрифта в пунктах */
   }

	h5, p {
		margin: 0pt;
	}

	table.items {
		font-size: 9pt;
		border-collapse: collapse;
	}

	td {
		vertical-align: top;
	}

	table {
		margin-left: 20px;
	}

	table thead td {
		background-color: #EEEEEE;
		text-align: center;
	}

	table tfoot td {
		background-color: #AAFFEE;
		text-align: center;
	}

	.barcode {
		padding: 1.5mm;
		margin: 0;
		vertical-align: top;
		color: #000000;
	}

	.barcodecell {
		text-align: center;
		vertical-align: middle;
		padding: 0;
	}
</style>
</head>
<body>
<!--mpdf
<htmlpageheader name="myheader">
<table width="100%"><tr>

<td width="50%" style="text-align: right;">Заявка No.<br /><span style="font-weight: bold; font-size: 12pt;">$order->orderId</span></td>
</tr></table>
</htmlpageheader>

<htmlpagefooter name="myfooter">
<p>Создана пользователем: <strong>$techFullName, </strong>$date</p>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table class = "items" width = "100%" cellpadding = "5" border = "0">
	<tbody>


	<tr>
		<td width = "20%" align = "center" valign = "top"><img
				src = "http://localhost/Assets/images/logo.png" width = "100" height = "64" alt = ""/></td>
		<td width = "60%" align = "center" valign = "top">
			<h1>Квитанция для клиента</h1>

			<h3>Заявка № ______________ от ______________</h3></td>

		<td width = "20%" align = "center" valign = "top">
			<br>
			<barcode code = "$barcodeNum" type = "C128A"/>
			$barcodeNum
			<br><br>
	</tr>
</table>
<hr>
<table width="100%" border="2" class = "items" border = "1">
  <tbody>
  <thead>
    <tr>
      <td width="50%">Устройство</td>
      <td width="50%">Клиент</td>
    </tr>
    </thead>
    <tr>
      <td><p>Стикер на устройстве: <strong>__________________________</strong></p>
        <p>Наименование: <strong>_________________________________</strong></p>
        <p>Модель: <strong>_________________________________________</strong></p>
        <p>Производитель: <strong>_________________________________</strong></p>
        <p>Серийный номер: <strong>_______________________________</strong></p>
        <p>Категория: <strong>______________________________________</strong></p>
      <p>Тип устройства: <strong>_________________________________</strong></p></td>
      <td><p>ID Клиента: <strong>______________________________</strong></p>
        <p>Наименование: <strong>__________________________</strong></p>
        <p>Организация: <strong>____________________________</strong></p>
        <p>Имя, Фамилия: <strong>___________________________</strong></p>
        <p>Телефон: <strong>________________________________</strong></p>
        <p>Доп. Тел.: <strong>_______________________________</strong></p>
      <p>Адрес: <strong>___________________________________</strong></p></td>
    </tr>
    <thead>
    <tr>
      <td colspan="2" width="100%" align="center" valign="middle">Заявка:</td>
    </tr>
    </thead>
    <tr>
      <td height="80" colspan="2"><p>Название: <strong>________________________________________________________________________________________________________</strong></p>
        <p>Описание: <strong>________________________________________________________________________________________________________</strong></p>
        <p>Коментарии:</p></td>
    </tr>
  </tbody>
</table>
<hr>
<ol>
  <li>
    <p class='small'>Данный документ удостоверяет прием оборудования с целью  проведения проверки на возможность осуществления ремонта и не свидетельствует о  переходе прав владения и пользования</p>
  </li>
  <li>
    <p class='small'>Технический центр не несет ответственности за  принадлежности и аксессуары, а также дефекты внешнего вида, не указанные  Клиентом в квитанции при сдаче аппарата и ремонт.</p>
  </li>
  <li>
    <p class='small'>Технический центр не несет ответственности за возможную  потерю пользовательских данных (файлы записной книги. СМС-сообщения, мелодии  звонков и т.п.) в индивидуальной памяти устройств в процессе устранения  недостатков, связанную с перепрограммированием. заменой блоков памяти, плат,  установкой программного обеспечения.</p>
  </li>
  <li>
    <p class='small'>Оборудование  принято без подтверждения заявленных Клиентом неисправностей, без разборки и  проверки внутренних повреждений. Клиент подтверждает, что все неисправности и  внутренние повреждения, которые могут быть обнаружены в оборудовании в процессе  осуществления сервисного обслуживания, возникли до приема оборудования в  соответствии с данной квитанцией. </p>
  </li>
  <li>
    <p class='small'>Клиент принимает на себя риск, связанный с возможным проявлением  в сданном оборудовании дефектов, не указанных в настоящей квитанции, а также  риск возможной полной или частичной утраты работоспособности оборудования или  отдельных его узлов и блоков в процессе устранения недостатков в случае наличия  в оборудовании следов электрохимической коррозии, попадания влаги, механических  воздействий,</p>
  </li>
  <li>
    <p class='small'>Выдача оборудования, переданного в ремонт юридическим  лицом (организацией), а также в случае, если оплата услуги произведена  безналичным платежом, осуществляется только при наличии у получателя  доверенности от юридического лица (организации) установленного образца (ст. 244  Гражданского кодекса Украины).</p>
  </li>
  <li>
    <p class='small'>В случае утраты настоящей квитанции Клиент обязан незамедлительно  известить об этом Технический центр в письменной форме. В этом случае  оборудование, указанное в квитанции, возвращается на основании письменного  заявления Клиента по предъявлении им паспорта или иного документа,  удостоверяющего личность. (Документ: «Про затвердження правил побутового обслуговування  населення»)</p>
  </li>
  <li>
    <p class='small'>Срок исполнения  заказов (включая диагностику) составляет до шести месяцев с момента заключения  договора. </p>
  </li>
  <li>
    <p class='small'>Клиент обязуется  получить переданное ранее оборудование в течение пяти рабочих дней после  уведомления Техническим центром о готовности оборудования к выдаче. По  истечении указанного срока. Технический центр имеет право потребовать от  Клиента возмещения расходов на храпение в размере 17грн. за календарный день  хранения (Основание: Ст. 947 ГК Украины «Возмещение расходов на хранение») </p>
  </li>
  <li>
    <p class='small'>Претензии и  предложения по основным направлениям деятельности Технического центра  принимаются только в письменной форме в виде заказного почтового отправления с  обратным адресом. Срок ответа администрации составляет до 30 рабочих дней с  момента получения почтового отправления. </p>
  </li>
</ol>
<hr>
<table width="100%" border="2">
  <tbody>
    <tr>
      <td width="50%"><p>Представитель исполнителя</p>
      <p>&nbsp;</p></td>
      <td width="50%"><p>Квитанция заполнена верно. С условиями ремонта изложенными выше, ознакомлен и согласен.</p>
      <p>&nbsp;</p></td>
    </tr>
    <tr>
      <td width="50%"><u>$techFullName</u>/__________________/</td>
      <td width="50%">______________________/__________________/</td>
    </tr>
  </tbody>
</table>
HTML;


			//==============================================================
			//==============================================================
			//==============================================================


			$mpdf = new mPDF('utf-8', 'A4');

			//$mpdf->progbar_altHTML = '<html><body>
			//	<div style="margin-top: 5em; text-align: center; font-family: Verdana; font-size: 12px;"><img style="vertical-align: middle" src="loading.gif" /> Создаем PDF файл. Подождите минутку...</div>';
			//$mpdf->StartProgressBarOutput();

			$mpdf->SetProtection (array ('print'));
			$mpdf->SetTitle ("СЦ \"$companyName\". - Техническая копия");
			$mpdf->SetAuthor ('$companyName.');
			$mpdf->SetWatermarkText ('Документ Клиента');
			$mpdf->showWatermarkText = TRUE;
			$mpdf->watermark_font = 'DejaVuSansCondensed';
			$mpdf->watermarkTextAlpha = 0.1;
			$mpdf->SetDisplayMode ('fullpage');

			$mpdf->WriteHTML ($html);

			$mpdf->Output ("empty_ticket-$order->orderId.pdf",'I');
			exit;

			//==============================================================
			//==============================================================
			//==============================================================
			break;
		}
		case 'receipt':
		{


			$OrderID = $actionAdd;
			if ($OrderID)
			{
				$order = new Order('DB', $OrderID);
				$labor = new Labor();

				if (!$order->orderId)
				{
					header ('Location:/index.php');
				}

				if ($order->deviceID)
				{
					$device = new Device('DB', $order->deviceID);
				}
				if ($order->clientID)
				{
					$client = new Client('DB', $order->clientID);
				}
			} else
			{
				header ('Location:/index.php');
			}


			$barcodeNum = createBarcodeNumber ($order->orderId, $device->id, $client->id);


			$html = <<<HTML
<html>
<style>
	body {
		font-family: sans-serif;
		font-size: 9pt;
		background: transparent url('http://localhost/Assets/images/bgbarcode.png') repeat-y scroll left top;
	}

	p.small {
    font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: 8pt; /* Размер шрифта в пунктах */
   }

	h5, p {
		margin: 0pt;
	}

	table.items {
		font-size: 9pt;
		border-collapse: collapse;
	}

	td {
		vertical-align: top;
	}

	table {
		margin-left: 20px;
	}

	table thead td {
		background-color: #EEEEEE;
		text-align: center;
	}

	table tfoot td {
		background-color: #AAFFEE;
		text-align: center;
	}

	.barcode {
		padding: 1.5mm;
		margin: 0;
		vertical-align: top;
		color: #000000;
	}

	.barcodecell {
		text-align: center;
		vertical-align: middle;
		padding: 0;
	}
</style>
</head>
<body>
<!--mpdf
<htmlpageheader name="myheader">
<table width="100%"><tr>

<td width="50%" style="text-align: right;">Счет No.<br /><span style="font-weight: bold; font-size: 12pt;">$order->orderId</span></td>
</tr></table>
</htmlpageheader>

<htmlpagefooter name="myfooter">
<p>Создана пользователем: <strong>$techFullName, </strong>$date</p>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table class = "items" width = "100%" cellpadding = "5" border = "0">
	<tbody>


	<tr>
		<td width = "20%" align = "center" valign = "top"><img
				src = "http://localhost/Assets/images/logo.png" width = "100" height = "64" alt = ""/></td>
		<td width = "60%" align = "center" valign = "top">
			<h1>Счет для клиента</h1>

			<h3>Заявка № $order->orderId от $order->orderDate</h3>
			<h3>Устройство № $device->id ($device->name)</h3></td>

		<td width = "20%" align = "center" valign = "top">
			<br>
			<barcode code = "$barcodeNum" type = "C128A"/>
			$barcodeNum
			<br><br>
	</tr>
</table>
<hr>
<table class = "items" width="100%" border="1">
<thead>
<tr>
<td width="50%">Счет от:</td>
<td width="50%"> Счет для:</td>
</tr>
</thead>
<tr>
<td >
			<ul>
				<li>ФОП <strong>$CompanyRealName</strong></li>
				<li>Адрес: <strong>$companyAddres</strong></li>
				<li>Р/Р: <strong>$CompanyBankAccountNumber</strong></li>
				<li>Банк: <strong>$CompanyBankName</strong></li>
				<li>МФО: <strong>$CompanyBankNumber</strong></li>
				<li>ЕДРПОУ: <strong>$CompanyPersNumber</strong></li>
				<li>Тел.: <strong>$CompanyTelephone</strong></li>
			</ul>
		</td>
      <td>
      <ul>
        <li>ID Клиента: <strong>$client->id</strong></li>
        <li>Наименование: <strong>$client->ScreenName</strong></li>
        <li>Организация: <strong>$client->organizationName</strong></li>
        <li>Имя, Фамилия: <strong>$client->LastName, $client->FirstName </strong></li>
        <li>Телефон: <strong>$client->phone1</strong></li>
        <li>Доп. Тел.: <strong>$client->phone2</strong></li>
      <li>Адрес: <strong>$client->city, $client->adress</strong></li></td>
      </ul>
</tr>
</table>
<hr>

<table class = "items" width="100%" border="1">
<thead>
<tr>
  <td width="3%" align="center" valign="middle">№</td>
  <td width="4%" align="center" valign="middle">Код</td>
  <td width="20%" align="center" valign="middle">Наименование</td>
  <td width="52%" align="center" valign="middle">Описание</td>
<td width="6%" align="center" valign="middle">Кол-во</td>
<td width="8%" align="center" valign="middle">Цена</td>
<td width="8%" align="center" valign="middle">Сумма</td>
</tr>
</thead>
HTML;

			$html = $html . $labor->echoListOfLaborsForPrint ($order->orderId);

			$discount = ($labor->fullCost / 100) * $client->discount;
			$grandtotalCost = $labor->fullCost;
			$totalCostDisc = $labor->fullCost - $discount;

			$totalCost = $labor->fullCost - $discount;
			$speedCost = ($totalCostDisc * Order::getOrderSpeedMultiplier($order->orderId)) - $totalCostDisc;;
			$totalCost = ($totalCostDisc * Order::getOrderSpeedMultiplier($order->orderId));
			$discount = number_format ($discount, 2, '.', '');
			$totalCost = number_format ($totalCost, 2, '.', '');
			$speedCost = number_format ($speedCost, 2, '.', '');
			$grandtotalCost = number_format ($grandtotalCost, 2, '.', '');
			$totalCostDisc = number_format ($totalCostDisc, 2, '.', '');
			$totalCostSTR = num2str ($totalCost);

			if (isset($order->closeMessage))
			{
				$message = "<br>
		Заметки по заявке: <u>$order->closeMessage</u>";
			} else
			{
				$message = '';
			}

			$html .= <<<HTML
	</table>
<hr>
<table class = "items" width="100%" border="1">
<thead>
<tr>
  <td width="60%" align="center" valign="middle">Заметки</td>
  <td colspan="2" align="center" valign="middle">Итого</td>
</tr>
</thead>
<tr>
  <td rowspan="6" align="left" valign="top">Сумма прописью: <u>$totalCostSTR</u>
  <br>
  Скидка клиента: $client->discount%
  <br>
  $message
  </td>
  <td width="11%" align="left" valign="middle">Итого по работам:</td>
  <td width="13%" align="center" valign="middle">$grandtotalCost грн.</td>
</tr>
<tr>
  <td align="left" valign="middle">Скидка:</td>
  <td align="center" valign="middle">$discount грн.</td>
</tr><tr>
  <td align="left" valign="middle">Со скидкой:</td>
  <td align="center" valign="middle">$totalCostDisc грн.</td>
</tr>
<tr>
  <td align="left" valign="middle">Срочность:</td>
  <td align="center" valign="middle">$speedCost грн.</td>
</tr>
<tr>
  <td align="left" valign="middle">НДС:</td>
  <td align="center" valign="middle">0.00 грн.</td>
</tr>
<tr>
<td align="left" valign="middle"><strong>К оплате:</strong></td>
<td align="center" valign="middle"><strong>$totalCost грн.</strong></td>
</tr>
</table>

<hr>
<table class = "items" width="100%" border="0">
  <tbody>
    <tr>
      <td width="50%"><p>Представитель исполнителя</p>
      <p>&nbsp;</p></td>
      <td width="50%"><p>Счет заполнен верно. Условия счета изложенные выше верны.</p>
      <p>&nbsp;</p></td>
    </tr>
    <tr>
      <td width="50%">$techFullName/__________________/</td>
      <td width="50%">______________________/__________________/</td>
    </tr>
  </tbody>
</table>
</html>
HTML;


			//==============================================================
			//==============================================================
			//==============================================================

			$mpdf = new mPDF('utf-8', 'A4');

			//$mpdf->progbar_altHTML = '<html><body>
			//	<div style="margin-top: 5em; text-align: center; font-family: Verdana; font-size: 12px;"><img style="vertical-align: middle" src="loading.gif" /> Создаем PDF файл. Подождите минутку...</div>';
			//$mpdf->StartProgressBarOutput();

			$mpdf->SetProtection (array ('print'));
			$mpdf->SetTitle ("СЦ \"$companyName\". - Техническая копия");
			$mpdf->SetAuthor ("$companyName.");
			$mpdf->SetWatermarkText ("Документ Клиента");
			$mpdf->showWatermarkText = TRUE;
			$mpdf->watermark_font = 'DejaVuSansCondensed';
			$mpdf->watermarkTextAlpha = 0.1;
			$mpdf->SetDisplayMode ('fullpage');

			$mpdf->WriteHTML ($html);

			$mpdf->Output ("receipt-$order->orderId.pdf",'I');
			exit;

			//==============================================================
			//==============================================================
			//==============================================================
			break;
		}
		case 'invoice':
		{


			$OrderID = $actionAdd;


			if ($OrderID)
			{
				$order = new Order('DB', $OrderID);
				$labor = new Labor();


				if (!$order->orderId)
				{
					header ('Location:/index.php');
				}

				if ($order->deviceID)
				{
					$device = new Device('DB', $order->deviceID);
				}
				if ($order->clientID)
				{
					$client = new Client('DB', $order->clientID);
				}
			} else
			{
				header ('Location:/index.php');
			}


			if (isset($_GET ['email']))
			{
				$emailsend = 1;
			} else
			{
				$emailsend = 0;
			}


			$barcodeNum = createBarcodeNumber ($order->orderId, $device->id, $client->id);


			$day = date_format (date_create ($order->orderDate), 'd');
			$month = date_format (date_create ($order->orderDate), 'm');
			$year = date_format (date_create ($order->orderDate), 'Y');

			$html = <<<HTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
	<META HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html; charset=utf-8">
	<TITLE> Разунок фактура №$order->orderId</TITLE>
	<STYLE TYPE="text/css">
	<!--
		@page { size: 5.83in 8.27in; margin-right: 0.39in; margin-top: 0.79in; margin-bottom: 0.79in }
		P { margin-bottom: 0.08in; direction: ltr; color: #000000; widows: 2; orphans: 2 }
		P.western { font-family: "Times New Roman", serif; font-size: 10pt}
		P.cjk { font-family: "Times New Roman", serif; font-size: 10pt }
		P.ctl { font-family: "Times New Roman", serif; font-size: 10pt}
	-->
	</STYLE>
</HEAD>
<BODY  TEXT="#000000" DIR="LTR">
<CENTER>
	<TABLE WIDTH="100%" CELLPADDING=4 CELLSPACING=0 STYLE="page-break-before: always">
		<COLGROUP>
			<COL WIDTH=241>
			<COL WIDTH=30>
			<COL WIDTH=4364>
			<COL WIDTH=36>
		</COLGROUP>
		<COLGROUP>
			<COL WIDTH=39>
			<COL WIDTH=39>
		</COLGROUP>
		<COLGROUP>
			<COL WIDTH=50>
		</COLGROUP>
		<TR VALIGN=TOP>
			<TD WIDTH=241 STYLE="border-top: 1px solid #000000; border-bottom: none; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.04in; padding-right: 0in">
				<P  CLASS="western" >
				<FONT SIZE=2 STYLE="font-size: 9pt"><B>Постачальник:
				$CompanyRealName</B></FONT></P>
			</TD>
			<TD COLSPAN=6 WIDTH=229 STYLE="border-top: none; border-bottom: none; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.04in; padding-right: 0in">
				<P  CLASS="western" ><BR>
				</P>
			</TD>
		</TR>
		<TR VALIGN=TOP>
			<TD WIDTH=241 STYLE="border-top: none; border-bottom: none; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.04in; padding-right: 0in">
				<P  CLASS="western" >
				<FONT SIZE=2 STYLE="font-size: 9pt"><B>Адреса:
				$companyAddres</B></FONT></P>
			</TD>
			<TD COLSPAN=6 WIDTH=229 STYLE="border-top: none; border-bottom: none; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.04in; padding-right: 0in">
				<P  CLASS="western" ><FONT SIZE=4>РАХУНОК-ФАКТУРА</FONT></P>
			</TD>
		</TR>
		<TR VALIGN=TOP>
			<TD WIDTH=241 STYLE="border-top: none; border-bottom: none; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.04in; padding-right: 0in">
				<P  CLASS="western" >
				<FONT SIZE=2 STYLE="font-size: 9pt"><B>Р/рахунок:
				$CompanyBankAccountNumber </B></FONT>
				</P>
			</TD>
			<TD COLSPAN=3 WIDTH=77 STYLE="border-top: none; border-bottom: none; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.04in; padding-right: 0in">
				<P  CLASS="western" >№</P>
			</TD>
			<TD COLSPAN=2 WIDTH=86 STYLE="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.04in; padding-right: 0in">
				<P  CLASS="western" >РФ - $order->orderId<BR>
				</P>
			</TD>
			<TD WIDTH=50 STYLE="border-top: none; border-bottom: none; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.04in; padding-right: 0in">
				<P  CLASS="western" ><BR>
				</P>
			</TD>
		</TR>
		<TR VALIGN=TOP>
			<TD WIDTH=241 STYLE="border-top: none; border-bottom: none; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.04in; padding-right: 0in">
				<P  CLASS="western" > <FONT SIZE=2 STYLE="font-size: 9pt"><B>в
				$CompanyBankName</B></FONT></P>
			</TD>
			<TD WIDTH=30 STYLE="border-top: none; border-bottom: none; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.04in; padding-right: 0in">
				<P  CLASS="western" ><BR>
				</P>
			</TD>
			<TD COLSPAN=5 WIDTH=191 STYLE="border: none; padding: 0in">
				<P  CLASS="western" ><BR>
				</P>
			</TD>
		</TR>
		<TR VALIGN=TOP>
			<TD WIDTH=241 STYLE="border-top: none; border-bottom: none; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.04in; padding-right: 0in">
				<P  CLASS="western" >
				<FONT SIZE=2 STYLE="font-size: 9pt"><B> $CompanyPersNumber МФО $CompanyBankNumber</B></FONT></P>
			</TD>
			<TD WIDTH=30 STYLE="border-top: none; border-bottom: none; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.04in; padding-right: 0in">
				<P  CLASS="western" ><FONT SIZE=2 STYLE="font-size: 9pt"><B>від</B></FONT></P>
			</TD>
			<TD COLSPAN=5 WIDTH=191 STYLE="border: 1px solid #000000; padding: 0in 0.04in">
				<P  CLASS="western" >
				<FONT SIZE=2 STYLE="font-size: 8pt"><B>&quot;<u> $day </u>&quot;
				<u> $month </u> . <u>$year</u>р.</B></FONT></P>
			</TD>
		</TR>
		<TR VALIGN=TOP>
			<TD WIDTH=241 STYLE="border-top: none; border-bottom: none; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.04in; padding-right: 0in">
				<P  CLASS="western" >
				<FONT SIZE=2 STYLE="font-size: 9pt"><B>ЄДРПОУ:
				 14360570 </B></FONT></P>
			</TD>
			<TD COLSPAN=2 WIDTH=33 STYLE="border-top: none; border-bottom: none; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.04in; padding-right: 0in">
				<P  CLASS="western" ><BR>
				</P>
			</TD>
			<TD COLSPAN=2 WIDTH=83 STYLE="border: none; padding: 0in">
				<P  CLASS="western" ><BR>
				</P>
			</TD>
			<TD COLSPAN=2 WIDTH=97 STYLE="border: none; padding: 0in">
				<P  CLASS="western" ><BR>
				</P>
			</TD>
		</TR>
		<TR VALIGN=TOP>
			<TD WIDTH=241 STYLE="border-top: none; border-bottom: none; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.04in; padding-right: 0in">
				<P  CLASS="western" >
				<FONT SIZE=2 STYLE="font-size: 9pt"><B>Тел./ф.
				 $CompanyTelephone</B></FONT></P>
			</TD>
			<TD COLSPAN=4 WIDTH=124 STYLE="border-top: none; border-bottom: none; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.04in; padding-right: 0in">
				<P  CLASS="western" ><FONT SIZE=2 STYLE="font-size: 9pt"><B>до
				платіжн. вимоги №</B></FONT></P>
			</TD>
			<TD COLSPAN=2 WIDTH=97 STYLE="border: 1px solid #000000; padding: 0in 0.04in">
				<P  CLASS="western" ><BR>
				</P>
			</TD>
		</TR>
		<TR VALIGN=TOP>
			<TD WIDTH=241 STYLE="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: none; border-right: none; padding: 0in">
				<P  CLASS="western" ><BR>
				</P>
			</TD>
			<TD COLSPAN=6 WIDTH=229 STYLE="border: none; padding: 0in">
				<P  CLASS="western" ><BR>
				</P>
			</TD>
		</TR>
		<TR VALIGN=TOP>
			<TD WIDTH=241 STYLE="border-top: none; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.04in; padding-right: 0in">
				<P  CLASS="western" ><FONT SIZE=2 STYLE="font-size: 9pt"><B>Платник: $order->clientSTR
				 </B></FONT>
				</P>
			</TD>
			<TD COLSPAN=6 WIDTH=229 STYLE="border: 1px solid #000000; padding: 0in 0.04in">
				<P  CLASS="western" ><FONT SIZE=2 STYLE="font-size: 9pt"><B>Доповнення</B></FONT></P>
			</TD>
		</TR>
	</TABLE>
</CENTER>
<P  CLASS="western" STYLE="margin-bottom: 0in; widows: 0; orphans: 0">
<BR>
</P>
<CENTER>
	<TABLE WIDTH=100% CELLPADDING=7 CELLSPACING=0>
		<COL WIDTH=206>
		<COL WIDTH=43>
		<COL WIDTH=43>
		<COL WIDTH=52>
		<COL WIDTH=71>
		<TR VALIGN=TOP>
			<TD WIDTH=70% STYLE="border-top: 1.50pt solid #000000; border-bottom: none; border-left: 1.50pt solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0in">
				<P  CLASS="western" ALIGN=CENTER >
				<FONT SIZE=2 STYLE="font-size: 9pt"><I>Найменування</I></FONT></P>
			</TD>
			<TD WIDTH=43 STYLE="border-top: 1.50pt solid #000000; border-bottom: none; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0in">
				<P  CLASS="western" ALIGN=CENTER >
				<FONT SIZE=2 STYLE="font-size: 9pt"><I>Од. вим.</I></FONT></P>
			</TD>
			<TD WIDTH=43 STYLE="border-top: 1.50pt solid #000000; border-bottom: none; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0in">
				<P  CLASS="western" ALIGN=CENTER >
				<FONT SIZE=2 STYLE="font-size: 9pt"><I>К-сть</I></FONT></P>
			</TD>
			<TD WIDTH=52 STYLE="border-top: 1.50pt solid #000000; border-bottom: none; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0in">
				<P  CLASS="western" ALIGN=CENTER >
				<FONT SIZE=2 STYLE="font-size: 9pt"><I>Ціна</I></FONT></P>
			</TD>
			<TD WIDTH=71 STYLE="border-top: 1.50pt solid #000000; border-bottom: none; border-left: 1px solid #000000; border-right: 1.50pt solid #000000; padding: 0in 0.08in">
				<P  CLASS="western" ALIGN=CENTER >
				<FONT SIZE=2 STYLE="font-size: 9pt"><I>Сума</I></FONT></P>
			</TD>
		</TR>
HTML;

			$html = $html . $labor->invoiceListOfLabors ($order->orderId);

			$totalCost = num2str ($labor->fullCost);

			$html .= <<<HTML
		<TR VALIGN=TOP>
			<TD COLSPAN=4 WIDTH=386 STYLE="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1.50pt solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0in">
				<P  CLASS="western" ><FONT SIZE=2 STYLE="font-size: 9pt"><B>Всього</B></FONT></P>
			</TD>
			<TD WIDTH=71 STYLE="border-top: none; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1.50pt solid #000000; padding: 0in 0.08in">
				<P  CLASS="western" >$labor->fullCost грн.
				</P>
			</TD>
		</TR>
		<TR VALIGN=TOP>
			<TD COLSPAN=4 WIDTH=386 STYLE="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1.50pt solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0in">
				<P CLASS="western" ><FONT SIZE=2 STYLE="font-size: 9pt"><SPAN ><B>Податок
				на додану вартість (ПДВ)</B></SPAN></FONT></P>
			</TD>
			<TD WIDTH=71 STYLE="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1.50pt solid #000000; padding: 0in 0.08in">
				<P  CLASS="western" >0 грн.
				</P>
			</TD>
		</TR>
		<TR VALIGN=TOP>
			<TD COLSPAN=4 WIDTH=386 STYLE="border-top: none; border-bottom: 1.50pt solid #000000; border-left: 1.50pt solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0in">
				<P  CLASS="western" ><FONT SIZE=2 STYLE="font-size: 9pt"><B>Загальна
				сума з ПДВ</B></FONT></P>
			</TD>
			<TD WIDTH=71 STYLE="border-top: none; border-bottom: 1.50pt solid #000000; border-left: 1px solid #000000; border-right: 1.50pt solid #000000; padding: 0in 0.08in">
				<P  CLASS="western" >$labor->fullCost грн.
				</P>
			</TD>
		</TR>
	</TABLE>
</CENTER>
<P  CLASS="western" STYLE="margin-left: -0.79in; margin-bottom: 0in; widows: 0; orphans: 0">
<BR>
</P>
<P  CLASS="western" STYLE="margin-bottom: 0in; widows: 0; orphans: 0">
<FONT SIZE=2 STYLE="font-size: 9pt"><B>Загальна сума, що
підлягає оплаті <u>$labor->fullCost грн. ($totalCost)  </u></B></FONT>
</P>

<P  CLASS="western" STYLE="margin-bottom: 0in; widows: 0; orphans: 0">
<FONT SIZE=2 STYLE="font-size: 9pt">
<B>Директор $techFullName/__________________/ </B></FONT>
</P>
<P  CLASS="western" STYLE="margin-bottom: 0in; widows: 0; orphans: 0">
М.П.

</BODY>
</HTML>
HTML;

			if (isset($_GET ['email']))
			{
				//		ini_set( 'SMTP', 'mail.computer-masters.org' ); // must be set to your own local ISP
				//		ini_set( 'smtp_port', '26' ); // assumes no authentication (passwords) required
				//		ini_set( 'sendmail_from', $settingsArray['CompanyEmail'] ); // can be any e-mail address, but must be set
				//		ini_set( 'auth_username', $settingsArray['CompanyEmail'] ); // can be any e-mail address, but must be set
				//		ini_set( 'auth_password', '' ); // can be any e-mail address, but must be set

				$mpdf = new mPDF('utf-8', 'A4');
				$mpdf->SetProtection (array ('print'));
				$mpdf->SetTitle ("СЦ \"$companyName\". - Счет - Фактура");
				$mpdf->SetAuthor ("$companyName.");
				$mpdf->SetWatermarkText ("Счет - Фактура");
				$mpdf->showWatermarkText = TRUE;
				$mpdf->watermark_font = 'DejaVuSansCondensed';
				$mpdf->watermarkTextAlpha = 0.1;
				$mpdf->SetDisplayMode ('fullpage');

				$content = $mpdf->Output('', 'S');

				$content = chunk_split(base64_encode($content));
				$mailto = 'stremovskyy@gmail.com';
				$from_name = $companyName;
				$from_mail = $settingsArray['CompanyEmail'];
				$replyto = $settingsArray['CompanyEmail'];
				$uid = md5(uniqid(time()));
				$subject = "$companyName - Счет-Фактура № $order->orderId";
				$message = 'Your e-mail message here';
				$filename = "invoice-$order->orderId.pdf";

				$header = "From: ".$from_name." <".$from_mail.">\r\n";
				$header .= "Reply-To: ".$replyto."\r\n";
				$header .= "MIME-Version: 1.0\r\n";
				$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
				$header .= "This is a multi-part message in MIME format.\r\n";
				$header .= "--".$uid."\r\n";
				$header .= "Content-type:text/plain; charset=utf-8\r\n";
				$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
				$header .= $message."\r\n\r\n";
				$header .= "--".$uid."\r\n";
				$header .= "Content-Type: application/pdf; name=\"".$filename."\"\r\n";
				$header .= "Content-Transfer-Encoding: base64\r\n";
				$header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
				$header .= $content."\r\n\r\n";
				$header .= "--".$uid."--";

				$is_sent = @mail($mailto, $subject, "", $header);

				$mpdf->Output ("invoice-$order->orderId.pdf", 'I');
				exit;

			}

			else
			{
				//==============================================================
				//==============================================================
				//==============================================================

				$mpdf = new mPDF('utf-8', 'A4');
				$mpdf->SetProtection (array ('print'));
				$mpdf->SetTitle ("СЦ \"$companyName\". - Счет - Фактура");
				$mpdf->SetAuthor ("$companyName.");
				$mpdf->SetWatermarkText ("Счет - Фактура");
				$mpdf->showWatermarkText = TRUE;
				$mpdf->watermark_font = 'DejaVuSansCondensed';
				$mpdf->watermarkTextAlpha = 0.1;
				$mpdf->SetDisplayMode ('fullpage');

				$mpdf->WriteHTML ($html);

				$mpdf->Output ("invoice-$order->orderId.pdf", 'I');
				exit;

				//==============================================================
				//==============================================================
				//==============================================================
			}
			break;
		}
		case 'act':
		{


			$OrderID = $actionAdd;


			if ($OrderID)
			{
				$order = new Order('DB', $OrderID);
				$labor = new Labor();


				if (!$order->orderId)
				{
					header ('Location:/index.php');
				}

				if ($order->deviceID)
				{
					$device = new Device('DB', $order->deviceID);
				}
				if ($order->clientID)
				{
					$client = new Client('DB', $order->clientID);
				}
			} else
			{
				header ('Location:/index.php');
			}


			if (isset($_GET ['email']))
			{
				$emailsend = 1;
			} else
			{
				$emailsend = 0;
			}


			$barcodeNum = createBarcodeNumber ($order->orderId, $device->id, $client->id);


			$day = date_format (date_create ($order->orderDate), 'd');
			$month = date_format (date_create ($order->orderDate), 'm');
			$year = date_format (date_create ($order->orderDate), 'Y');

			$html = <<<HTML
	<STYLE TYPE="text/css">
	<!--
		@page { margin-left: 1.18in; margin-right: 0.59in; margin-top: 0.79in; margin-bottom: 0.79in }
		P { margin-bottom: 0.08in; direction: ltr; widows: 2; orphans: 2 }
	-->
	</STYLE>
</HEAD>
<P ALIGN=JUSTIFY STYLE="text-indent: 0.39in; margin-bottom: 0in; line-height: 0.1in">
<FONT COLOR="#666666">&nbsp;</FONT></P>
<P ALIGN=JUSTIFY STYLE="margin-bottom: 0in; line-height: 0.1in"><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3>СТРЕМОВСЬКИЙ
А.С. ФОП&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</FONT></FONT></FONT><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA">ПАТ
«Готель «МИР»</SPAN></FONT></FONT></P>
<P ALIGN=JUSTIFY STYLE="margin-bottom: 0.04in; line-height: 0.1in"><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3>ЄДРПОУ&nbsp;3075007153&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ЄДРПОУ</FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA">
 </SPAN></FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA">04824844</SPAN></FONT></FONT></FONT></P>
<P ALIGN=JUSTIFY STYLE="margin-bottom: 0.04in; line-height: 0.1in"><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3>Адреса&nbsp;</FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA">м.Харків,
вул.Римарська 24г</SPAN></FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Адреса</FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA">
м.Харків, пр.Леніна 27А</SPAN></FONT></FONT></FONT></P>
<P ALIGN=JUSTIFY STYLE="margin-bottom: 0.04in; line-height: 0.1in"><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3>Р/рахунок
№&nbsp;26007052335303</FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA">
                                      </SPAN></FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3>Р/рахунок</FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA">
№ </SPAN></FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA">26004000066973</SPAN></FONT></FONT></FONT></P>
<P ALIGN=JUSTIFY STYLE="margin-bottom: 0.04in; line-height: 0.1in"><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3>в&nbsp;ПАТ
КБ ПриватБанк&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA">
          </SPAN></FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3>
в</FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA">
</SPAN></FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA">ПАТ
«Укрсоцбанк»</SPAN></FONT></FONT></FONT></P>
<P ALIGN=JUSTIFY STYLE="margin-bottom: 0.04in; line-height: 0.1in"><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3>МФО&nbsp;&nbsp;&nbsp;351533&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;МФО&nbsp;</FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA">300023</SPAN></FONT></FONT></FONT></P>
<TABLE WIDTH=632 CELLPADDING=7 CELLSPACING=0>
	<COL WIDTH=618>
	<TR>
		<TD WIDTH=618 HEIGHT=6 VALIGN=BOTTOM BGCOLOR="#ffffff" STYLE="border: none; padding: 0in">
			<P ALIGN=JUSTIFY STYLE="margin-bottom: 0.04in"><FONT COLOR="#000000">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</FONT>
			</P>
			<P ALIGN=CENTER STYLE="margin-bottom: 0.04in"><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><B>Акт
			№</B></FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA"><B>
			7</B></SPAN></FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="en-US"><B>
			</B></SPAN></FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA"><B>від
			27 жовтня 2015р.</B></SPAN></FONT></FONT></FONT></P>
			<P ALIGN=JUSTIFY><FONT COLOR="#666666">&nbsp;</FONT></P>
		</TD>
	</TR>
	<TR>
		<TD WIDTH=618 HEIGHT=6 VALIGN=BOTTOM BGCOLOR="#ffffff" STYLE="border: none; padding: 0in">
			<P ALIGN=JUSTIFY STYLE="margin-bottom: 0.04in"><FONT COLOR="#000000">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</FONT><FONT COLOR="#000000">
			</FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><B>прийому-передачі
			виконаних робіт</B></FONT></FONT></FONT></P>
			<P ALIGN=CENTER STYLE="margin-bottom: 0in"><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><B>(наданих
			послуг)</B></FONT></FONT></FONT></P>
			<P ALIGN=CENTER STYLE="margin-bottom: 0in"><FONT COLOR="#666666">&nbsp;</FONT></P>
			<P STYLE="margin-top: 0.19in"><FONT COLOR="#000000">&nbsp;</FONT></P>
		</TD>
	</TR>
</TABLE>
<P ALIGN=JUSTIFY STYLE="text-indent: 0.39in; margin-bottom: 0in; line-height: 0.1in">
<FONT COLOR="#666666">&nbsp;</FONT></P>
<P ALIGN=JUSTIFY STYLE="text-indent: 0.39in; margin-bottom: 0in; line-height: 0.1in">
<FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3>Ми,
що нижче підписалися, представник
Виконавця і представник Замовника,
уклали цей акт про те, що Виконавець
виконав роботи (надав послуги) згідно
договору </FONT></FONT></FONT>
</P>
<P ALIGN=JUSTIFY STYLE="margin-bottom: 0in; line-height: 0.1in"><FONT COLOR="#000000">№
</FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA">2043</SPAN></FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3>/1-</FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA">ПАТ
</SPAN></FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3>від
</FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA">16
жовтня 2015р</SPAN></FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3>.</FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA">
у повному обсязі.</SPAN></FONT></FONT></FONT></P>
<P ALIGN=JUSTIFY STYLE="text-indent: 0.39in; margin-bottom: 0in; line-height: 0.1in">
<FONT COLOR="#666666">&nbsp;</FONT></P>
<P ALIGN=JUSTIFY STYLE="text-indent: 0.39in; margin-bottom: 0in; line-height: 0.1in">
<FONT COLOR="#666666">&nbsp;</FONT></P>
<P STYLE="margin-bottom: 0in; line-height: 100%"><FONT COLOR="#666666">&nbsp;</FONT><FONT COLOR="#000000">&nbsp;</FONT></P>
<TABLE WIDTH=548 CELLPADDING=7 CELLSPACING=0>
	<COL WIDTH=18>
	<COL WIDTH=208>
	<COL WIDTH=41>
	<COL WIDTH=51>
	<COL WIDTH=80>
	<COL WIDTH=61>
	<COL WIDTH=4359>
	<TR>
		<TD ROWSPAN=2 WIDTH=18 HEIGHT=5 BGCOLOR="#ffffff" STYLE="border: 1.00pt solid #000001; padding: 0in 0.08in">
			<P STYLE="margin-top: 0.19in"><FONT COLOR="#000000">№ <FONT FACE="Times New Roman, serif"><FONT SIZE=2><B>п/п</B></FONT></FONT></FONT></P>
		</TD>
		<TD ROWSPAN=2 WIDTH=208 BGCOLOR="#ffffff" STYLE="border-top: 1.00pt solid #000001; border-bottom: 1.00pt solid #000001; border-left: none; border-right: 1.00pt solid #000001; padding-top: 0in; padding-bottom: 0in; padding-left: 0in; padding-right: 0.08in">
			<P STYLE="margin-top: 0.19in"><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><B>Назва
			роботи (послуги)</B></FONT></FONT></FONT></P>
		</TD>
		<TD ROWSPAN=2 WIDTH=41 BGCOLOR="#ffffff" STYLE="border-top: 1.00pt solid #000001; border-bottom: 1.00pt solid #000001; border-left: none; border-right: 1.00pt solid #000001; padding-top: 0in; padding-bottom: 0in; padding-left: 0in; padding-right: 0.08in">
			<P STYLE="margin-top: 0.19in"><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><B>Од.
			вим.</B></FONT></FONT></FONT></P>
		</TD>
		<TD ROWSPAN=2 WIDTH=51 BGCOLOR="#ffffff" STYLE="border-top: 1.00pt solid #000001; border-bottom: 1.00pt solid #000001; border-left: none; border-right: 1.00pt solid #000001; padding-top: 0in; padding-bottom: 0in; padding-left: 0in; padding-right: 0.08in">
			<P STYLE="margin-top: 0.19in"><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><B>К-сть</B></FONT></FONT></FONT></P>
		</TD>
		<TD ROWSPAN=2 WIDTH=80 BGCOLOR="#ffffff" STYLE="border-top: 1.00pt solid #000001; border-bottom: 1.00pt solid #000001; border-left: none; border-right: 1.00pt solid #000001; padding-top: 0in; padding-bottom: 0in; padding-left: 0in; padding-right: 0.08in">
			<P STYLE="margin-top: 0.19in"><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><B>Ціна
			без ПДВ, грн</B></FONT></FONT></FONT></P>
		</TD>
		<TD ROWSPAN=2 WIDTH=61 BGCOLOR="#ffffff" STYLE="border-top: 1.00pt solid #000001; border-bottom: 1.00pt solid #000001; border-left: none; border-right: 1.00pt solid #000001; padding-top: 0in; padding-bottom: 0in; padding-left: 0in; padding-right: 0.08in">
			<P STYLE="margin-top: 0.19in"><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><B>Сума
			без ПДВ, грн</B></FONT></FONT></FONT></P>
		</TD>
		<TD WIDTH=4359 BGCOLOR="#ffffff" STYLE="border: none; padding: 0in">
			<P STYLE="margin-top: 0.19in"><FONT COLOR="#666666">&nbsp;</FONT></P>
		</TD>
	</TR>
	<TR>
		<TD WIDTH=4359 BGCOLOR="#ffffff" STYLE="border: none; padding: 0in">
			<P STYLE="margin-top: 0.19in"><FONT COLOR="#666666">&nbsp;</FONT></P>
		</TD>
	</TR>
	<TR>
HTML;

			$html = $html . $labor->invoiceListOfLabors ($order->orderId);

			$totalCost = num2str ($labor->fullCost);

			$html .= <<<HTML
	</TABLE>
<P STYLE="margin-bottom: 0.04in; line-height: 0.1in"><FONT COLOR="#666666">

                                          &nbsp;</FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><B>Разом,
без ПДВ, грн.</B></FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA"><B>
300 грн. 00коп.</B></SPAN></FONT></FONT></FONT></P>
<P ALIGN=CENTER STYLE="margin-bottom: 0.04in; line-height: 0.1in"><FONT COLOR="#000000">

                   </FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA"><B>ПДВ,
грн. 00 грн.00коп. </B></SPAN></FONT></FONT></FONT>
</P>
<P ALIGN=CENTER STYLE="margin-bottom: 0.04in; line-height: 0.1in"><FONT COLOR="#000000">

<FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA"><B>Всього
з ПДВ, грн. 300 грн. 00коп.</B></SPAN></FONT></FONT></FONT><FONT COLOR="#666666"><FONT FACE="Tahoma, serif"><FONT SIZE=1 STYLE="font-size: 6pt">&nbsp;</FONT></FONT></FONT></P>
<P ALIGN=JUSTIFY STYLE="margin-bottom: 0.04in; line-height: 0.1in"><FONT COLOR="#666666">&nbsp;</FONT></P>
<P ALIGN=JUSTIFY STYLE="margin-bottom: 0.04in; line-height: 0.1in"><A NAME="_GoBack"></A>
<FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA">Всього
(прописом)</SPAN></FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3>&nbsp;</FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA"><U><B>триста
 грн. 00коп.</B></U></SPAN></FONT></FONT></FONT></P>
<P ALIGN=JUSTIFY STYLE="text-indent: 0.39in; margin-bottom: 0in; line-height: 0.1in">
<FONT COLOR="#666666">&nbsp;</FONT></P>
<P ALIGN=JUSTIFY STYLE="text-indent: 0.39in; margin-bottom: 0in; line-height: 0.1in">
<FONT COLOR="#666666">&nbsp;</FONT></P>
<P ALIGN=JUSTIFY STYLE="text-indent: 0.39in; margin-bottom: 0in; line-height: 0.1in">
<FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3>Роботи
(послуги) виконані повністю, сторони
претензій одна до одної не мають.</FONT></FONT></FONT></P>
<P STYLE="margin-bottom: 0in; line-height: 100%"><FONT COLOR="#000000">&nbsp;&nbsp;</FONT><FONT COLOR="#666666">&nbsp;</FONT><FONT COLOR="#000000">&nbsp;</FONT></P>
<P ALIGN=JUSTIFY STYLE="text-indent: 0.39in; margin-bottom: 0in; line-height: 0.1in">
<FONT COLOR="#666666">&nbsp;</FONT></P>
<P ALIGN=JUSTIFY STYLE="text-indent: 0.39in; margin-bottom: 0in; line-height: 0.1in">
<FONT COLOR="#666666">&nbsp;</FONT></P>
<P ALIGN=JUSTIFY STYLE="text-indent: 0.39in; margin-bottom: 0in; line-height: 0.1in">
<FONT COLOR="#666666">&nbsp;</FONT></P>
<P ALIGN=JUSTIFY STYLE="text-indent: 0.39in; margin-bottom: 0in; line-height: 0.1in">
<FONT COLOR="#666666">&nbsp;</FONT></P>
<P ALIGN=JUSTIFY STYLE="margin-bottom: 0in; line-height: 0.1in"><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3>Роботу
здав&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Роботу
прийняв</FONT></FONT></FONT></P>
<P ALIGN=JUSTIFY STYLE="text-indent: 0.39in; margin-bottom: 0in; line-height: 0.1in">
<FONT COLOR="#666666">&nbsp;</FONT></P>
<P ALIGN=JUSTIFY STYLE="margin-bottom: 0.04in; line-height: 0.1in"><FONT COLOR="#666666">&nbsp;</FONT></P>
<P ALIGN=JUSTIFY STYLE="margin-bottom: 0.04in; line-height: 0.1in"><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3>від
Виконавця&nbsp;</FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA">Стремовський
А.С.</SPAN></FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;від
Замовника&nbsp;</FONT></FONT></FONT></P>
<P ALIGN=JUSTIFY STYLE="text-indent: 0.39in; margin-bottom: 0in; line-height: 0.1in">
<FONT COLOR="#666666">&nbsp;</FONT></P>
<P ALIGN=JUSTIFY STYLE="margin-bottom: 0.04in; line-height: 0.1in"><FONT COLOR="#666666">&nbsp;</FONT></P>
<P ALIGN=JUSTIFY STYLE="margin-bottom: 0.04in; line-height: 0.1in"><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3>М.П./Підпис&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3><SPAN LANG="uk-UA">
 </SPAN></FONT></FONT></FONT><FONT COLOR="#000000"><FONT FACE="Times New Roman, serif"><FONT SIZE=3>М.П./Підпис</FONT></FONT></FONT></P>
<P STYLE="margin-bottom: 0.14in"><BR><BR>
</P>
HTML;

			if (isset($_GET ['email']))
			{
				//		ini_set( 'SMTP', 'mail.computer-masters.org' ); // must be set to your own local ISP
				//		ini_set( 'smtp_port', '26' ); // assumes no authentication (passwords) required
				//		ini_set( 'sendmail_from', $settingsArray['CompanyEmail'] ); // can be any e-mail address, but must be set
				//		ini_set( 'auth_username', $settingsArray['CompanyEmail'] ); // can be any e-mail address, but must be set
				//		ini_set( 'auth_password', '' ); // can be any e-mail address, but must be set

				$mpdf = new mPDF('utf-8', 'A4');
				$mpdf->SetProtection (array ('print'));
				$mpdf->SetTitle ("СЦ \"$companyName\". - Счет - Фактура");
				$mpdf->SetAuthor ("$companyName.");
				$mpdf->SetWatermarkText ("Счет - Фактура");
				$mpdf->showWatermarkText = TRUE;
				$mpdf->watermark_font = 'DejaVuSansCondensed';
				$mpdf->watermarkTextAlpha = 0.1;
				$mpdf->SetDisplayMode ('fullpage');

				$content = $mpdf->Output('', 'S');

				$content = chunk_split(base64_encode($content));
				$mailto = 'stremovskyy@gmail.com';
				$from_name = $companyName;
				$from_mail = $settingsArray['CompanyEmail'];
				$replyto = $settingsArray['CompanyEmail'];
				$uid = md5(uniqid(time()));
				$subject = "$companyName - Счет-Фактура № $order->orderId";
				$message = 'Your e-mail message here';
				$filename = "invoice-$order->orderId.pdf";

				$header = "From: ".$from_name." <".$from_mail.">\r\n";
				$header .= "Reply-To: ".$replyto."\r\n";
				$header .= "MIME-Version: 1.0\r\n";
				$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
				$header .= "This is a multi-part message in MIME format.\r\n";
				$header .= "--".$uid."\r\n";
				$header .= "Content-type:text/plain; charset=utf-8\r\n";
				$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
				$header .= $message."\r\n\r\n";
				$header .= "--".$uid."\r\n";
				$header .= "Content-Type: application/pdf; name=\"".$filename."\"\r\n";
				$header .= "Content-Transfer-Encoding: base64\r\n";
				$header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
				$header .= $content."\r\n\r\n";
				$header .= "--".$uid."--";

				$is_sent = @mail($mailto, $subject, "", $header);

				$mpdf->Output ("invoice-$order->orderId.pdf", 'I');
				exit;

			}

			else
			{
				//==============================================================
				//==============================================================
				//==============================================================

				$mpdf = new mPDF('utf-8', 'A4');
				$mpdf->SetProtection (array ('print'));
				$mpdf->SetTitle ("СЦ \"$companyName\". - Счет - Фактура");
				$mpdf->SetAuthor ("$companyName.");
				$mpdf->SetWatermarkText ("Счет - Фактура");
				$mpdf->showWatermarkText = TRUE;
				$mpdf->watermark_font = 'DejaVuSansCondensed';
				$mpdf->watermarkTextAlpha = 0.1;
				$mpdf->SetDisplayMode ('fullpage');

				$mpdf->WriteHTML ($html);

				$mpdf->Output ("invoice-$order->orderId.pdf", 'I');
				exit;

				//==============================================================
				//==============================================================
				//==============================================================
			}
			break;
		}

		default:
		{

		}
	}
}

