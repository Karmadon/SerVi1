<?php
/**
 * Created by PhpStorm.
 * User: strem
 * Date: 28.07.2015
 * Time: 23:28
 */

session_start ();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Classes/Classes.php';

$params = explode ('/', $_SERVER['REQUEST_URI']);
$abspath = getcwd ();


$section = sanitizeString ($params[1]);
$action = sanitizeString ($params[2]);
$actionAdd = sanitizeString ($params[3]);
if ($params[5])
{
	$$params[4] = sanitizeString ($params[5]);
}
if ($params[7])
{
	$$params[6] = sanitizeString ($params[7]);
}


if (isset ($_SESSION ['login']))
{
	$username = $_SESSION ['firstname'];

	$loggedin = TRUE;
	$settingsArray = getSettings ();

	$companyName = $settingsArray['CompanyName'];
	$systemName = $settingsArray['SysName'];
	$systemVer = $settingsArray['Version'];

	$counterArray = getMenuCounters ();
	$deviceCount = $counterArray['deviceCount'];
	$clientsCount = $counterArray['clientsCount'];


	if ($counterArray['clientsMetaCount'] !== 0)
	{
		$clientsMetaCount = $counterArray['clientsMetaCount'];
		$clientsMetaCount = " <span class=\"label label-warning\">$clientsMetaCount</span> ";
	} else
	{
		$clientsMetaCount = '';
	}
	if ($counterArray['devicesMetaCount'] !== 0)
	{
		$devicesMetaCount = $counterArray['devicesMetaCount'];
		$devicesMetaCount = " <span class=\"label label-warning\">$devicesMetaCount</span> ";
	} else
	{
		$devicesMetaCount = '';
	}
	$openOrdersCount = $counterArray['openOrderCount'];
	$username = $_SESSION ['login'];
	$userID = $_SESSION['loginId'];



	if ($section !== 'print')
	{
		Echo <<<HTML
<head>
	<meta http-equiv = "content-type" content = "text/html; charset=UTF-8">
	<meta charset = "utf-8">
	<meta name = "generator" content = "Bootply"/>
	<meta name = "viewport" content = "width=device-width, initial-scale=1, maximum-scale=1">

	<title>$companyName</title>
	<link href = "https://system.cm.org.ua/Assets/css/bootstrap/bootstrap.min.css" rel = "stylesheet">
	<!--<link rel = "stylesheet" href = "https://system.cm.org.ua/Assets/css/bootstrap/bootstrap-theme.min.css">-->
	<link href = "https://system.cm.org.ua/Assets/css/jquery/jquery-ui.css" rel = "stylesheet" type = "text/css"/>
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic&subset=latin,cyrillic-ext,greek-ext,greek,vietnamese,latin-ext,cyrillic' rel='stylesheet' type='text/css'>

</head>
<body>
<!-- header -->
<div id = "top-nav" class = "navbar navbar-inverse navbar-static-top">
	<div class = "container-fluid">
		<div class = "navbar-header">
			<button type = "button" class = "navbar-toggle" data-toggle = "collapse" data-target = ".navbar-collapse">
				<span class = "icon-bar"></span>
				<span class = "icon-bar"></span>
				<span class = "icon-bar"></span>
			</button>
			<a class = "navbar-brand" href = "/dashboard/view/">$systemName v. $systemVer</a>
		</div>
		<div class = "navbar-collapse collapse">
			<ul class = "nav navbar-nav navbar-right">
			<form class = "navbar-form navbar-left" action="barcode.php" method="post"><input class="form-control" placeholder="Штрихкод" name="barcode" onmouseover="this.focus();" type="text"></form>
				<form class = "navbar-form navbar-left" role = "search" method="post" accept-charset="utf-8" action="/search/all/">
					<div class = "form-group">
						<input id="q" name="q" type = "text" class = "form-control" placeholder = "Поиск">
					</div>
					<button type = "submit" class = "btn btn-link">Искать</button>
				</form>
				<li class = "dropdown">
					<a class = "dropdown-toggle" role = "button" data-toggle = "dropdown" href = "#"><i
							class = "glyphicon glyphicon-user"></i> $username <span class = "caret"></span></a>
					<ul id = "g-account-menu" class = "dropdown-menu" role = "menu">
						<li><a href = "/user/view/$userID">Мой профиль</a></li>
					</ul>
				</li>
				<li><a href = "/user/logout/"><i class = "glyphicon glyphicon-lock"></i> Выход</a></li>
			</ul>

		</div>
	</div>
	<!-- /container -->
</div>
<!-- /Header -->

<div class = "container-fluid">
	<div class = "row">
		<div class = "col-sm-2">
			<!-- Left column -->
			<a href = "#"><strong><i class = "glyphicon glyphicon-wrench"></i> Главная работа</strong></a>

			<hr>

			<ul class = "nav nav-stacked">
				<li class = "nav-header"><a href = "#" data-toggle = "collapse" data-target = "#userMenu">Основное <i
						class = "glyphicon glyphicon-chevron-down"></i></a>
					<ul class = "nav nav-stacked collapse in" id = "userMenu">
						<li class = "active"><a href = "/dashboard/view"><i class = "glyphicon glyphicon-home"></i>
							Главная</a></li>
						<li><a data-toggle = "collapse" data-target = "#clients" href = "#">
							<i class = "glyphicon glyphicon-user"></i> Клиенты <span class = "badge badge-info">$clientsCount</span>$clientsMetaCount</a>
						</li>
						<ul class = "nav nav-stacked collapse" id = "clients">
							<li ><a href = "/client/new/"><span style="padding-right:2em"></span><span	class = "glyphicon glyphicon-plus-sign" ></span> Добавить Клиента</a>
							</li>
							<li><a href = "/client/list/"><span style="padding-right:2em"></span><span class = "glyphicon glyphicon-list"></span> Список Клиентов</a>
							</li>
							<li><a href = "/client/view/"><span style="padding-right:2em"></span><span class = "glyphicon glyphicon-eye-open"></span> Открыть Клиента</a>
							</li>
						</ul>


						<li><a data-toggle = "collapse" data-target = "#devices" href = "#"><i
								class = "glyphicon glyphicon-cog"></i> Устройства <span class = "badge badge-info">$deviceCount</span>$devicesMetaCount</a>
						</li>
						<ul class = "nav nav-stacked collapse" id = "devices">
							<li><a href = "/device/new/"><span style="padding-right:2em"></span><span
									class = "glyphicon glyphicon-plus-sign"></span> Добавить Устройство</a>
							</li>
							<li><a href = "/device/list/"><span style="padding-right:2em"></span><span
									class = "glyphicon glyphicon-list"></span> Список Устройств</a>
							</li>
							<li><a href = "/device/view/"><span style="padding-right:2em"></span><span
									class = "glyphicon glyphicon-eye-open"></span> Открыть Устройство</a>
							</li>
						</ul>
						<li><a data-target = "#orders" data-toggle = "collapse" href = "#"><i
								class = "glyphicon glyphicon-folder-open"></i> Заявки <span class = "badge badge-info">$openOrdersCount</span></a>
						</li>
						<ul class = "nav nav-stacked collapse" id = "orders">
							<li><a href = "/order/new/"><span style="padding-right:2em"></span><span
									class = "glyphicon glyphicon-plus-sign"></span> Добавить Заявку</a>
							</li>
							<li><a href = "/order/list/"><span style="padding-right:2em"></span><span
									class = "glyphicon glyphicon-list"></span> Список Заявок</a>
							</li>
							<li><a href = "/order/view/"><span style="padding-right:2em"></span><span
									class = "glyphicon glyphicon-eye-open"></span> Открыть Заявку</a>
							</li>
						</ul>
						<li><a data-target = "#accounting" data-toggle = "collapse" href = "#"><i
								class = "glyphicon glyphicon-flag"></i> Бухгалтерия </a>
						</li>
						<ul class = "nav nav-stacked collapse" id = "accounting">
							<li><a href = "/payment/new/noOrder/drcr/in"><span style="padding-right:2em"></span>Приход</a></li>
							<li><a href = "/payment/new/noOrder/drcr/out"><span style="padding-right:2em"></span>Расход</a></li>
							<li><a href = "/payment/list"><span style="padding-right:2em"></span>Транкзакции</a></li>
							<li><a href = "/payment/account"><span style="padding-right:2em"></span>Счета</a></li>
						</ul>


						<li><a href = "/system/settings"><i class = "glyphicon glyphicon-exclamation-sign"></i> Настройки</a></li>
						<li><a href = "/system/history"><i class = "glyphicon glyphicon-alert"></i> Активность</a></li>
						<li><a href = "/user/logout"><i class = "glyphicon glyphicon-off"></i> Выход</a>
						</li>
					</ul>
				</li>
				<hr>

				<a href = "#"><strong><i class = "glyphicon glyphicon-link"></i> Плагины</strong></a>
				<hr>
				<li class = "nav-header"><a href = "#" data-toggle = "collapse" data-target = "#menu2"> Отчеты <i
						class = "glyphicon glyphicon-chevron-right"></i></a>

					<ul class = "nav nav-stacked collapse" id = "menu2">
						<li><a href = "#">Основная статистика</a>
						</li>
						<li><a href = "#">Доход/Расход</a>
						</li>
						<li><a href = "#">Выполнение Заявок</a>
						</li>
						<li><a href = "#">Проблемы</a>
						</li>
						<li><a href = "#">Дополнительные</a>
						</li>
					</ul>
				</li>


			</ul>

</div>
HTML;
	}
}
else
{
	$loggedin = FALSE;

	ECHO <<<HTML

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <link href="https://system.cm.org.ua/Assets/css/bootstrap/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
</head>
<body>

HTML;
}