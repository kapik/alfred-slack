<?php

//error_reporting(E_ALL);

require_once 'SlackRouter.php';
require_once 'SlackController.php';

$route = SlackRouter::getAction($input, $query);
$controller = new SlackController();
$params = empty($route->params) ? [] : $route->params;
call_user_func_array(array($controller, $route->action.'Action'), $params);