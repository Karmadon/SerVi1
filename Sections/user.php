<?php
/**
 * Created by PhpStorm.
 * User: strem
 * Date: 28.07.2015
 * Time: 21:13
 */

global $action,$actionAdd;

switch ($action)
{
	case 'login':
	{

		if ($loggedin)
		{
			Echo "<script> location.replace('/dashboard/view/'); </script>";
		}
		else
		{
			$error = $username = $password = '';

			if (isset ($_POST['username']))
			{
				$user = new User();
				$user->initFromPOST ();
				$user->userLogin ();
			}

			echo <<<HTML
<div class="col-lg-offset-5 col-lg-2">
<br>
	<div class = 'panel panel-primary'>
		<div class = 'panel-heading'>Вход в систему</div>
		<div class = 'panel-body'>
          <form id="hello" method="post" action='/user/login/' class="form-horizontal">
              <input type="text" id="username" name="username" class="form-control" placeholder="Имя пользователя">
              <br>
              <input type="password" id="password" name="password" class="form-control" placeholder="Пароль">
              <br>
              <button class="btn btn-primary btn-lg btn-block">Войти</button>
          </form>
      </div>
      </div>
HTML;
		}
		break;
	}
	case 'logout':
	{
		if (isset ($_SESSION ['login']))
		{
			destroySession ();
			Echo "<script> location.replace('/'); </script>";
		} else
		{
			echo "<div class='main'><br>" . 'Вы не можете выйти';
		}
		break;
	}
	case 'new':
	{
		if ($loggedin)
		{
			if ($_SESSION ['group'] == 1)
			{

				$error = $username = $password = '';
				/*if (isset ($_SESSION ['username'])) {
				destroySession();
			}*/

				if (isset ($_POST ['username']))
				{

					$user = new User();
					$user->initFromPOST ();


					if ($user->login == '' || $user->password == '')
					{
						$error = 'Не заполнены логин и пароль<br><br>';
					} else
					{
						$user->addUserToBase ();
					}
				}

				echo <<<HTML
<div class = "well">
	<form method = "post" action = "index.php?new_user">

		<tr>
			<td>Имя</td>
			<td><input type = "text" maxlength = "32" name = "firstname" value = "$forename"><br></td>
		</tr>
		<tr>
			<td>Фамилия</td>
			<td><input type = "text" maxlength = "32" name = "lastname" value = "$surname"><br></td>
		</tr>
		<tr>
			<td>Логин</td>
			<td><input type = "text" maxlength = "16" name = "username" value = "$username"><br></td>
		</tr>
		<tr>
			<td>Пароль</td>
			<td><input type = "text" maxlength = "12" name = "password" value = "$password"><br></td>
		</tr>

		<span class = 'fieldname'>&nbsp;</span>
		<input type = 'submit' value = 'Sign up'>
	</form>
</div><br>

HTML;
				require_once 'footer.php';
				die;
			}
		}
		break;
	}
	case 'view':
	{
		$user = new User('DB',$actionAdd);
		$username = $user->firstName;
		$login = $user->login;
		$loginID = $user->id;
		$lastname = $user->lastName;
		$date = date ('l jS \of F Y h:i:s A');

		$techFullName = preg_replace ('~^(\S++)\s++(\S)\S++\s++(\S)\S++$~u', '$1 $2.$3.', $lastname . ' ' . $username);

		echo <<<HTML
<div class = "col-md-10">
	<div class = "container-fluid">
	<strong><i class = "glyphicon glyphicon-dashboard"></i> Пользователь $username</strong>
	<hr>
<div class = "col-md-3">
			<div class = "panel panel-info">
				<div class = "panel-heading">Данные о пользователе</div>
								<div class="panel-body">
<a href="#" class="list-group-item ">ID <span class="pull-right"><strong>$loginID</strong></span></a>
<a href="#" class="list-group-item ">Логин <span class="pull-right"><strong>$login</strong></span></a>
<a href="#" class="list-group-item">Фамилия <span class="pull-right"><strong>$lastname</strong></span></a>
<a href="#" class="list-group-item">Имя <span class="pull-right"><strong>$username</strong></span></a>
<a href="#" class="list-group-item">ФИО <span class="pull-right"><strong>$techFullName</strong></span></a>

				</div>
			</div>
</div>

<div class = "col-md-9">

HTML;
		$user->echoUserHistory($loginID);

		echo <<<HTML

</div>
</div>


HTML;
		break;
	}
	default:
	{
		echo <<<HTML
        <div class="col-sm-9 col-sm-9">
            <div id="content">
                <h1>Добро пожаловать!</h1>
                <strong>Ignite 2.0112</strong>
                <div class='main'><h3>Вход для администраторов <a href='/user/login'>тут</a></h3></div>
                <div class='main'><h3>Вход для Клиентов <a href='/user/login'>здесь</a></h3></div>
            </div>
HTML;
	}
}


