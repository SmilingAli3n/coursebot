<?php
namespace Repository;

use Db\MysqlConnector;

class UserRepository
{
    public static function setMainCurrency(string $currencyShortName, int $chatId): void {
        $db = MysqlConnector::getInstance();
		$PDO = $db->getConnection();
		$stmt = $PDO->prepare("SELECT * FROM currencies WHERE short_name=?");
		$stmt->bindValue(1, $currencyShortName, \PDO::PARAM_STR);
		$stmt->execute();
		$res = $stmt->fetchAll();
		if (count($res) !== 0) {
		    $stmt = $PDO->prepare("INSERT INTO users (telegram_user_id, base_currency) VALUES (?, ?) ON DUPLICATE KEY UPDATE base_currency=?");
		    $stmt->bindValue(1, $chatId, \PDO::PARAM_INT);
		    $stmt->bindValue(2, $res[0]["id"], \PDO::PARAM_INT);
		    $stmt->bindValue(3, $res[0]["id"], \PDO::PARAM_INT);
		    $stmt->execute();
        }
    }
}
