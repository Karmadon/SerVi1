<?php
/**
 * Created by PhpStorm.
 * User: strem
 * Date: 28.07.2015
 * Time: 21:13
 */
if(empty($_SERVER["HTTPS"]))
{
	$_SERVER["HTTPS"]='on';

}

require_once 'header.php';



//print_r($params);

$pageDir = 'Sections/';

if ($loggedin)
{
	switch ($section)
	{
		case 'user':
		{
			include_once $pageDir . 'user.php';
			break;
		}
		case 'dashboard':
		{
			include_once $pageDir . 'dashboard.php';
			break;
		}
		case 'device':
		{
			include_once $pageDir . 'device.php';
			break;
		}
		case 'order':
		{
			include_once $pageDir . 'order.php';
			break;
		}
		case 'labor':
		{
			include_once $pageDir . 'labor.php';
			break;
		}
		case 'client':
		{
			include_once $pageDir . 'client.php';
			break;
		}
		case 'system':
		{
			include_once $pageDir . 'system.php';
			break;
		}
		case 'payment':
		{
			include_once $pageDir . 'payment.php';
			break;
		}
		case 'search':
		{
			include_once $pageDir . 'search.php';
			break;
		}
		case 'print':
		{
			include_once $pageDir . 'print.php';
			break;
		}
		default:
		{

			Echo "<script> location.replace('/dashboard/view/'); </script>";
		}
			Echo 'Такой Страницы нифига нет';
	}

}
else
{
	switch ($section)
	{
		case 'user':
		{
			include_once $pageDir .'user.php';
			break;
		}
		default:
		{

			echo <<<HTML
        <div class="col-sm-9 col-sm-9">
            <div id="content">
                <h1>Добро пожаловать!</h1>
                <strong>CMP 2.5812</strong>
                <div class='main'><h3>Вход для администраторов <a href='user/login/'>тут</a></h3></div>
            </div>
HTML;

			break;
		}
	}
}

require_once 'footer.php';