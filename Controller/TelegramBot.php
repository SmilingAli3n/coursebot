<?php
namespace Controller;

abstract class TelegramBot
{
	protected $API = "";
	protected $method = "";
	protected $fields = [];

	function __construct($token)
	{
		$this->API = "https://api.telegram.org/bot{$token}";
	}

	private function sendToTelegram($method, $fields)
	{
		$this->method = $method;
		$this->fields = $fields;
		$ch = curl_init($this->API . "/" . $this->method);
		curl_setopt_array($ch, array(
			CURLOPT_POST           => count($this->fields),
			CURLOPT_POSTFIELDS     => http_build_query($this->fields),
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT        => 10
		));
		$res = json_decode(curl_exec($ch), true);
		curl_close($ch);

		return $res;
	}

	public function sendMessage($chatId, $text, ...$buttons)
	{
        $fields = [
            "chat_id"    => $chatId,
		    "text"	     => $text,
            "parse_mode" => "HTML" //"MarkdownV2",
        ];
        if (count($buttons) !== 0) {
		    $inlineKeyboard = [];
    		foreach($buttons as $button)
	    	{
		    	$button = array($button);
			    array_push($inlineKeyboard, $button);
		    }
		    $keyboard = array("inline_keyboard" => $inlineKeyboard);
		    $replyMarkup = json_encode($keyboard);
	        $fields["reply_markup"] = $replyMarkup;
        }
		$this->sendToTelegram("sendMessage", $fields);
	}

	public function sendKeyboardAnswer($id, $text=null)
	{
		$fields = ["callback_query_id" => $id,
				   "text" => $text];
		$this->sendToTelegram("answerCallbackQuery", $fields);
	}

	public function sendCoords($chatId, $latitude, $longitude)
	{
		$fields = ["chat_id" => $chatId,
				   "latitude" => $latitude,
				   "longitude" => $longitude];
		$this->sendToTelegram("sendLocation", $fields);
	}

	public function sendDoc($chatId)
	{
		$fields = ["chat_id" => $chatId,
				   "document" => curl_file_create(__DIR__ . "/price.xlsx")];
		$this->sendToTelegram("sendDocument", $fields);
	}

	public function sendPic($chatId)
	{
		$fields = ["chat_id" => $chatId,
				   "photo" => curl_file_create(__DIR__ . "/fy.jpg")];
		$this->sendToTelegram("sendPhoto", $fields);
	}

	public function sendNotification($text)
	{
		$fields = ["chat_id" => "283642055",
				   "text" => $text];
		$this->sendToTelegram("sendMessage", $fields);
	}
}
