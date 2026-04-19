<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class AccountModel {
    public static function getAllAccounts() {
        $db = Database::getMariaDb();
        $stmt = $db->query("SELECT id, username, email, role, created_at FROM accounts ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public static function getAccountByUsername($username) {
        $db = Database::getMariaDb();
        $stmt = $db->prepare("SELECT * FROM accounts WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public static function getAccountById($id) {
        $db = Database::getMariaDb();
        $stmt = $db->prepare("SELECT * FROM accounts WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function addAccount($username, $email, $password, $role) {
        $db = Database::getMariaDb();
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT INTO accounts (username, email, password, role) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$username, $email, $hash, $role]);
    }

    public static function updateAccount($id, $username, $email, $role, $password = null) {
        $db = Database::getMariaDb();
        if ($password) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare("UPDATE accounts SET username = ?, email = ?, role = ?, password = ? WHERE id = ?");
            return $stmt->execute([$username, $email, $role, $hash, $id]);
        } else {
            $stmt = $db->prepare("UPDATE accounts SET username = ?, email = ?, role = ? WHERE id = ?");
            return $stmt->execute([$username, $email, $role, $id]);
        }
    }

    public static function deleteAccount($id) {
        $db = Database::getMariaDb();
        $stmt = $db->prepare("DELETE FROM accounts WHERE id = ?");
        return $stmt->execute([$id]);
    }
}