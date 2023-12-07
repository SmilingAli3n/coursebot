<?php
declare(strict_types=1);

set_include_path(getcwd() . "/");
spl_autoload_extensions(".php");
spl_autoload_register(function ($class) {
    require_once str_replace("\\", "/", $class) . ".php";
});

use Controller\CourseBot;

//setlocale(LC_TIME, "ru_RU");

$input = file_get_contents("php://input");
if (empty($input))
{
	exit();
}

$input = json_decode($input, true);

if (isset($input["callback_query"]))
{
	$callbackQuery = $input["callback_query"];
	$data = $callbackQuery["data"];
}
//exit();
$bot = new CourseBot(getenv("TG_COURSEBOTKZ_TOKEN"));
if (!empty($input["message"]["text"]))
{
	$chatId = $input["message"]["chat"]["id"];
	$incomingText = $input["message"]["text"];
    if ($incomingText === "/start")
	{
		$bot->showStartMenu($chatId);
	}
}

if(!empty($callbackQuery))
{
	$chatId = $callbackQuery["message"]["chat"]["id"];
	$id = $callbackQuery["id"];
	$whatToCall = $callbackQuery["data"];
	$whatToCall = explode("_", $whatToCall);
	$func = strval($whatToCall[0]);
	$params = array_slice($whatToCall, 1);
	$bot->{$func}($chatId, $params);
	exit();
}
