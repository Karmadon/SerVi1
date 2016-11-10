<?php
/**
 * Created by PhpStorm.
 * User: strem
 * Date: 30.10.2015
 * Time: 21:46
 */
global $action, $actionAdd;

if (isset($action))
{
	switch ($action)
	{
		case 'client':
		{

			break;
		}
		case 'device':
		{

			break;
		}
		case 'order':
		{

			break;
		}
		case 'all':
		{

			$queryStr = sanitizeString (post ('q'));

			if (isset ($queryStr))

			{
				if (!empty($queryStr))
				{
					if (strlen ($queryStr) < 2)
					{
						$text = '<p>Слишком короткий поисковый запрос.</p>';
					}
					else if (strlen ($queryStr) > 128)
					{
						$text = '<p>Слишком длинный поисковый запрос.</p>';
					}
					else
					{
						$search = new Search();

						echo <<<HTML
						<div class="col-sm-10">
							<a href="#"><strong><i class="glyphicon glyphicon-dashboard"></i> Поиск "$queryStr" по всем разделам</strong></a>
                        <hr>
HTML;
						$search->searchInClients ($queryStr);
						$search->searchInDevices ($queryStr);
						$search->searchInOrders ($queryStr);
						echo <<<HTML
						</div>
HTML;
					}

				}
				else
				{
					$text = '<p>Задан пустой поисковый запрос.</p>';
				}

				if (isset($text))
				{
					Echo <<<HTML

	<div class = 'col-lg-7'>
<div id = "myAlert" class = "alert alert-danger">
   <a href = "/index.php" class = "close" data-dismiss = "alert">&times;</a>
   <strong>$text</strong>.
</div>
</div>


<script type = "text/javascript">
   $(function(){
      $("#myAlert").bind('closed.bs.alert', function () {
         location.replace('/index.php');
      });
   });
</script>
HTML;
				}

			}
			else
			{

			}
			break;
		}
		default:
		{
		}
	}
}
