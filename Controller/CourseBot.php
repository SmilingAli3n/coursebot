<?php
namespace Controller;

use Repository\ExchangeRatesRepository;
use Repository\UserRepository;

class CourseBot extends TelegramBot
{
    public function __construct($token) {
        //file_put_contents(__DIR__ . "/logs/log.txt", "Курсобот тут ёпта", FILE_APPEND);
        parent::__construct($token);
    }

	public function showStartMenu($chatId) {
		$text = "Выберите основную валюту:";
		$button1 = array("text" => "Казахстанский тенге", "callback_data" => "setMainCurrency_KZT");
		$button2 = array("text" => "Узбекский сум", "callback_data" => "setMainCurrency_UZS");
		$button3 = array("text" => "Российский рубль", "callback_data" => "setMainCurrency_RUB");
		$this->sendMessage($chatId, $text, $button1, $button2, $button3);
	}

	public function setMainCurrency(int $chatId, array $params) {
		if (count($params) === 0 || count($params) > 1)
			return;
		$currency = $params[0];
        UserRepository::setMainCurrency($currency, $chatId);
		$this->sendMessage($chatId, "Настройки сохранены");
        $this->showUserActions($chatId, "Выберите действие:");
	}

    public function showUserActions($chatId, $text) {
        $button1 = array("text" => "Получить актуальные курсы", "callback_data" => "sendCourses_{$chatId}");
        $button2 = array("text" => "In progress", "callback_data" => "");
        $this->sendMessage($chatId, $text, $button1);
    }

	public function returnToMainMenu($text, $chatId) {
		$inline_button = array("text"=>"Вернуться назад", "callback_data"=>"back");
		$this->sendMessage($chatId, $text, $inline_button);
	}

    public function sendCourses($chatId) {
        $res = ExchangeRatesRepository::getAllCourses($chatId);
        $dateArr = date_parse_from_format("Y-m-d", $res[0]["date"]);
        $dateStr = "{$dateArr['day']}.{$dateArr['month']}.{$dateArr['year']}";
        // "j F Y"
        $ratesList = "<b>Курсы валют за {$dateStr}:</b>\n\n";
        foreach ($res as $key => $value) {
            $quant =  $res[$key]["quant"] == 0 ? 1 : $res[$key]["quant"];
            $rate =  round($res[$key]["rate"] / $quant, 4);
            /*$n = 50 - mb_strlen(strval($res[$key]["currency"])) - strlen(strval($rate));
            $dots = "";
            for ($i = 0; $i <= $n; $i++) {
                $dots .= ".";
            }*/
            $ratesList .= sprintf("%s: %s\n", $res[$key]["currency"], $rate);
        }
        //$ratesList .= "</ul></pre>";
        $this->sendMessage($chatId, $ratesList);
    }
}
