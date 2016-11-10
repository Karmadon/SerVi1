<?php
/**
 * Created by PhpStorm.
 * User: karmadon
 * Date: 11.12.15
 * Time: 15:41
 */

global $action,$actionAdd;

if (isset($action))
{
	switch ($action)
	{
		case 'history':
		{
echo<<<HTML

<div class = "container-fluid">
	<div class = "row">
		<div class = "col-sm-10">


            <a href="#"><strong><i class="glyphicon glyphicon-comment"></i> Активность</strong></a>

            <hr>

            <div class="row">
                <div class="col-sm-12">
                    <ul class="list-group">
HTML;

			echoLastActivity();

			echo <<<HTML

                    </ul>
                </div>
            </div>
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