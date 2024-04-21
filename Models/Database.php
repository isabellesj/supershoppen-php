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

    function initIfNotInitialized()
    {

        static $initialized = false;
        if ($initialized)
            return;

        $this->usersDatabase->setupUsers();
        $this->usersDatabase->seedUsers();

        $initialized = true;
    }

    function getAllUsers()
    {
        return $this->pdo->query('SELECT * FROM users')->fetchAll(PDO::FETCH_CLASS, 'User');

    }

    // function getUser($id)
    // {
    //     $prep = $this->pdo->prepare('SELECT * FROM products where id=:id');
    //     $prep->setFetchMode(PDO::FETCH_CLASS, 'User');
    //     $prep->execute(['id' => $id]);
    //     return $prep->fetch();
    // }
    // function getProductByUsername($username)
    // {
    //     $prep = $this->pdo->prepare('SELECT * FROM products where username=:username');
    //     $prep->setFetchMode(PDO::FETCH_CLASS, 'User');
    //     $prep->execute(['username' => $username]);
    //     return $prep->fetch();
    // }
}
?>