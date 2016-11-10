<?php
/**
 * Created by PhpStorm.
 * User: Karmadon
 * Date: 06.12.2015
 * Time: 22:07
 */

global $action;

switch ($action)
{
	case 'view':
	{
		$username = $_SESSION ['login'];
		$userID = $_SESSION['loginId'];

		$counterArray = getIndexCounters ();

		$messagesCount = 2;
		$inboxMessagesCount = 3;
		$ordersRate = floor ($counterArray['orderCompletePrec']);
		$clientsRate = floor ($counterArray['clientsCompletePrec']);
		$devicesRate = floor ($counterArray['devicesCompletePrec']);
		$RealterRate = floor ($counterArray['realSumRatio']);
		$orderPayedPrec = floor ($counterArray['orderPayedPrec']);
		$fullSumAccounts = floor ($counterArray['fullSumAccounts']);
		$getFullMinusSum = floor ($counterArray['getFullMinusSum']);
		$fullSumAccount = floor ($counterArray['fullSumAccount']);
		$fullcashSumAccount = floor ($counterArray['fullcashSumAccount']);
		$fullnocashSumAccount = floor ($counterArray['fullnocashSumAccount']);


		echo <<<HTML
			<div class="col-sm-10">
<!-- column 2 -->
            <ul class="list-inline pull-right">
                <li><a href="#"><i class="glyphicon glyphicon-cog"></i></a></li>
                <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-comment"></i><span class="count"> $messagesCount</span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#">1. Тестовое сообщение ..</a></li>
                        <li><a href="#">2. Еще одно тестовое сообщение..</a></li>
                        <li><a href="#"><strong>Все сообщения</strong></a></li>
                    </ul>
                </li>
                <li><a href="#"><i class="glyphicon glyphicon-user"></i></a></li>
                <li><a title="Добавить виджет" data-toggle="modal" href="#addWidgetModal"><span class="glyphicon glyphicon-plus-sign"></span> Добавить Виджет</a></li>
            </ul>
            <a href="#"><strong><i class="glyphicon glyphicon-dashboard"></i> Рабочий стол</strong></a>
            <hr>

            <div class="row">
                <!-- center left-->
                <div class="col-sm-6">


                    <div class="btn-group btn-group-justified">
                        <a href="/order/new" class="btn btn-primary col-sm-3">
                            <i class="glyphicon glyphicon-plus"></i>
                            <br> + Заявка
                        </a>
                        <a href="/device/new" class="btn btn-primary col-sm-3">
                            <i class="glyphicon glyphicon-floppy-disk"></i>
                            <br> + Устройство
                        </a>
                        <a href="/client/new" class="btn btn-primary col-sm-3">
                            <i class="glyphicon glyphicon-user"></i>
                            <br> + Клиент
                        </a>
                        <a href="/payment/new" class="btn btn-primary col-sm-3">
                            <i class="glyphicon glyphicon-cog"></i>
                            <br> + Платеж
                        </a>
                    </div>

                    <hr>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4>Текущие Отчеты</h4></div>
                        <div class="panel-body">

                            <small>Выполнение заявок</small>
                            <div class="progress">
                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="$ordersRate" aria-valuemin="0" aria-valuemax="100" style="width: $ordersRate%">
                                    $ordersRate %
                                </div>
                            </div><small>Закончены Клиенты</small>
                            <div class="progress">
                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="$clientsRate" aria-valuemin="0" aria-valuemax="100" style="width: $clientsRate%">
                                    $clientsRate %
                                </div>
                            </div><small>Закончены Устройства</small>
                            <div class="progress">
                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="$devicesRate" aria-valuemin="0" aria-valuemax="100" style="width: $devicesRate%">
                                    $devicesRate %
                                </div>
                            </div>
                            <hr>
                            <small>Заработано на аренду</small>
                            <div class="progress">
                                <div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="$RealterRate" aria-valuemin="0" aria-valuemax="100" style="width: $RealterRate%">
                                    <span class="sr-only">$RealterRate%</span>
                                </div>
                            </div>
                            <small>Оплачено заявок</small>
                            <div class="progress">
                                <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="$orderPayedPrec" aria-valuemin="0" aria-valuemax="100" style="width: $orderPayedPrec%">
                                    <span class="sr-only">$orderPayedPrec%</span>
                                </div>
                            </div>
                            <small>Долги</small>
                            <div class="progress">
                                <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 10%">
                                    <span class="sr-only">80% Complete</span>
                                </div>
                            </div>
                        </div>
                        <!--/panel-body-->
                    </div>
                    <!--/panel-->

                    <hr>

                    <!--tabs-->
                    <div class="panel">
                        <ul class="nav nav-tabs" id="myTab">
                            <li class="active"><a href="#profile" data-toggle="tab">Профиль</a></li>
                            <li><a href="#messages" data-toggle="tab">Сообщения</a></li>
                            <li><a href="#settings" data-toggle="tab">Настройки</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active well" id="profile">
                                <h4><i class="glyphicon glyphicon-user"></i></h4>

                                <a href="#" title="Header" data-toggle="popover" data-trigger="hover" data-content="Some content">Hover over me</a>

                                Lorem profile dolor sit amet, consectetur adipiscing elit. Duis pharetra varius quam sit amet vulputate.
                                <p>Quisque mauris augue, molestie tincidunt condimentum vitae, gravida a libero. Aenean sit amet felis dolor, in sagittis nisi.</p>
                            </div>
                            <div class="tab-pane well" id="messages">
                                <h4><i class="glyphicon glyphicon-comment"></i></h4> Message ipsum dolor sit amet, consectetur adipiscing elit. Duis pharetra varius quam sit amet vulputate.
                                <p>Quisque mauris augu.</p>
                            </div>
                            <div class="tab-pane well" id="settings">
                                <h4><i class="glyphicon glyphicon-cog"></i></h4> Lorem settings dolor sit amet, consectetur adipiscing elit. Duis pharetra varius quam sit amet vulputate.
                                <p>Quisque mauris augue, molestie.</p>
                            </div>
                        </div>

                    </div>
                    <!--/tabs-->

                    <hr>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4>Новые Заявки</h4></div>
                        <div class="panel-body">
                            <div class="list-group">
                                <a href="#" class="list-group-item active">Это активная заявка..</a>
                                <a href="#" class="list-group-item">Созданная заявка..</a>
                                <a href="#" class="list-group-item">И еще одна заявка..</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/col-->
                <div class="col-sm-6">
HTML;

		Order::echoMYListFromDBSmall ('DATE DESC', '50');
		Order::echoListFromDBSmall ('DATE DESC', 50, "ORDER_STATE_ID = '1' AND ORDER_TECHNICIAN_ID != '$userID'");

		echo <<<HTML

             <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Касса (нал.)</th>
                                    <th>Касса (безнал.)</th>
                                    <th>Касса (общая)</th>
                                    <th>Нам должны</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>$fullcashSumAccount грн.</td>
                                    <td>$fullnocashSumAccount грн.</td>
                                    <td><a href="/payment/account/7">$fullSumAccount грн.</td>
                                    <td>$getFullMinusSum грн.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <i class="glyphicon glyphicon-wrench pull-right"></i>
                                <h4>Быстрая заявка</h4>
                            </div>
                        </div>
                        <div class="panel-body">
                            <form class="form form-vertical">
                                <div class="control-group">
                                    <label>Название заявки</label>
                                    <div class="controls">
                                        <input type="text" class="form-control" placeholder="Название заявки">
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label>Описание заявки</label>
                                    <div class="controls">
                                        <textarea class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label>Категория</label>
                                    <div class="controls">
                                        <select class="form-control">
                                            <option>Заправка</option>
                                             <option>Продажа</option>
                                              <option>Ремонт</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label></label>
                                    <div class="controls">
                                        <button type="submit" class="btn btn-primary">
                                            Создать
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!--/panel content-->
                    </div>


                </div>
                <!--/col-span-6-->

            </div>
            <!--/row-->


        </div>
        <!--/col-span-9-->
    </div>
</div>
HTML;

		break;
	}
	default:
	{
		break;
	}
}