<?php
require_once ('vendor/autoload.php');
require_once ('Models/UserDatabase.php');
require_once ('Models/Database.php');


class DBContext
{

    private $pdo;
    private $usersDatabase;

    function getUsersDatabase()
    {
        return $this->usersDatabase;
    }

    function __construct()
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
        $dotenv->load();
        $host = $_ENV['host'];
        $db = $_ENV['db'];
        $user = $_ENV['user'];
        $pass = $_ENV['pass'];
        $Username = $_ENV['Username'];
        $Password = $_ENV['Password'];
        $dsn = "mysql:host=$host;dbname=$db";
        $this->pdo = new PDO($dsn, $user, $pass);
        $this->usersDatabase = new UserDatabase($this->pdo);
        $this->initIfNotInitialized();
    }

    function addUser($UserName, $Name, $StreetAddress, $City, $ZipCode, $EmailAddress)
    {

        $prep = $this->pdo->prepare("INSERT INTO UserDetails
                        (FullName, StreetAddress, City, ZipCode, UserName)
                    VALUES(:FullName, :StreetAddress, :City, :ZipCode, :UserName);
        ");
        $prep->execute([
            "FullName" => $Name,
            "StreetAddress" => $StreetAddress,
            "City" => $City,
            "ZipCode" => $ZipCode,
            "UserName" => $UserName
        ]);
        return $this->pdo->lastInsertId();

    }

    function addLoginSession($userId, $timestamp, $ip)
    {

        $prep = $this->pdo->prepare("INSERT INTO LoginSession
                        (userId, timestamp, ip)
                    VALUES(:userId, :timestamp, :ip);
        ");
        $prep->execute([
            "userId" => $userId,
            "timestamp" => $timestamp,
            "ip" => $ip
        ]);
        return $this->pdo->lastInsertId();

    }

    function initIfNotInitialized()
    {

        static $initialized = false;
        if ($initialized)
            return;

        $sql = "CREATE TABLE IF NOT EXISTS `UserDetails` (
                `id` INT AUTO_INCREMENT NOT NULL,
                `FullName` varchar(200) NOT NULL,
                `StreetAddress` varchar(200) NOT NULL,
                `City` varchar(200) NOT NULL,
                `ZipCode` INT,
                `UserName` varchar(200) NOT NULL,
                PRIMARY KEY (`id`)
                ) ";

        $sql = "CREATE TABLE IF NOT EXISTS `LoginSession` (
               `id` INT AUTO_INCREMENT NOT NULL,
               `userId` INT NOT NULL,
               `timestamp` TIMESTAMP NOT NULL,
               `ip` VARBINARY(16) NOT NULL,
               PRIMARY KEY (`id`)
               ) ";

        $this->pdo->exec($sql);

        $this->usersDatabase->setupUsers();
        $this->usersDatabase->seedUsers();

        $initialized = true;
    }
}
?>