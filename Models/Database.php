<?php
require_once ('vendor/autoload.php');
require_once ('Models/UserDatabase.php');
require_once ('Models/User.php');

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
        $dsn = "mysql:host=$host;dbname=$db";
        $this->pdo = new PDO($dsn, $user, $pass);
        $this->usersDatabase = new UserDatabase($this->pdo);
        $this->initIfNotInitialized();
    }

    function addUser($UserName, $Name, $StreetAddress, $City, $ZipCode, $EmailAddress)
    {
        // $userId = $this->usersDatabase->getAuth()->admin()->createUser($UserName, $Password, $EmailAddress);


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

    function getAllUsers()
    {
        return $this->pdo->query('SELECT * FROM users')->fetchAll(PDO::FETCH_CLASS, 'User');

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

        $this->pdo->exec($sql);

        $this->usersDatabase->setupUsers();
        $this->usersDatabase->seedUsers();

        $initialized = true;
    }
}
?>