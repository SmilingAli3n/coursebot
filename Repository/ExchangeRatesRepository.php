<?php
namespace Repository;

use Db\MysqlConnector;

class ExchangeRatesRepository
{
    // private $db;
    public static function getAllCourses(int $chatId): array {
        $db = MysqlConnector::getInstance();
	    $PDO = $db->getConnection();
        $baseCurrencyRate = 1.0; // если нет курса валюты на данный момент - считаем, что это тенге
        $stmt = $PDO->prepare("SELECT rates.rate as rate, rates.quant AS quant FROM users INNER JOIN exchange_rates AS rates ON base_currency = currency_id WHERE telegram_user_id = ? AND rates.date = ?");
        $stmt->bindValue(1, $chatId, \PDO::PARAM_INT);
        $stmt->bindValue(2, date("Y-m-d", time()), \PDO::PARAM_STR);
        $stmt->execute();
        $res = $stmt->fetchAll();
        if (count($res) !== 0) {
            $quant = $res[0]["quant"] === 0 ? 1 : $res[0]["quant"];
            $baseCurrencyRate = $res[0]["rate"] / $res[0]["quant"];
        }
        $stmt = $PDO->prepare("SELECT rates.rate AS rate, rates.quant AS quant, rates.date AS date, currs.full_name as currency FROM exchange_rates AS rates LEFT JOIN currencies as currs ON rates.currency_id=currs.id WHERE date = ?");
	    $date = date("Y-m-d", time());
        $stmt->bindValue(1, $date, \PDO::PARAM_STR);
	    $stmt->execute();
	    $res = $stmt->fetchAll();
	    if (count($res) === 0) {
            $date = date("Y-m-d", time() - 86400);
            $stmt->bindValue(1, $date, \PDO::PARAM_STR);
            $stmt->execute();
            $res = $stmt->fetchAll();
        }
        foreach ($res as $key => $value) {
            $res[$key]["rate"] = $res[$key]["rate"] / $baseCurrencyRate;
        }
        return $res;
    }
}
