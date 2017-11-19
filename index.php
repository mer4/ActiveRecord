<?php

//turn on debugging messages
ini_set('display_errors', 'On');
error_reporting(E_ALL);

define('DATABASE', 'mer4');
define('USERNAME', 'mer4');
define('PASSWORD', '2kXQOxHZC');
define('CONNECTION', 'sql2.njit.edu');

class dbConn{
    //variable to hold connection object.
    protected static $db;
    //private construct - class cannot be instatiated externally.
    public function __construct() {
        try {
            // assign PDO object to db variable
            self::$db = new PDO( 'mysql:host=' . CONNECTION .';dbname=' . DATABASE, USERNAME, PASSWORD );
            self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			echo 'Connected successfully<br>';
        }
        catch (PDOException $e) {
            //Output error - would normally log this to error file rather than output to user.
            echo "Connection Error: " . $e->getMessage();
        }
    }
    // get connection function. Static method - accessible without instantiation
    public static function getConnection() {
        //Guarantees single instance, if no connection object exists then create one.
        if (!self::$db) {
            //new connection object.
            new dbConn();
        }
        //return connection.
        return self::$db;
    }
}
class collection {
    static public function create() {
      $model = new static::$modelName;
      return $model;
    }
    static public function findAll() {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
    }
    static public function findOne($id) {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE id = ' . $id;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
    }
}
class accounts extends collection {
    protected static $modelName = 'accounts';
}
class todos extends collection {
    protected static $modelName = 'todos';
}
class model {
    protected static $columnString;
    protected static  $valueString;
    public function save()
    {

        if (static::$id == '') {

            $db = dbConn::getConnection();
            $array = get_object_vars($this);
            self::$valueString = implode(', ', array_fill(0,count($array),'?'));

            //  print_r($array);

            $array = array_flip($array);
            self::$columnString = implode(', ', $array);
            $sql = $this->insert();
            $statement = $db->prepare($sql);
            $statement->execute(static::$data);
            //  echo $sql;
        } else {
            $this->update();
        }

    }
    private function insert() {

        $sql =  "INSERT INTO ". static::$tableName. " (" . self::$columnString . ") VALUES (" . self::$valueString . ")";

        return $sql;
    }
    private function update() {
        $sql = 'something';
        return $sql;
        echo 'I just updated record' . $this->id;
    }
    public function delete() {

        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'DELETE FROM ' . static::$tableName.' WHERE id=' . static::$id;
        //echo $sql;

        $statement = $db->prepare($sql);
        $statement->execute();

        echo 'The row with id ' . static::$id . ' has been deleted from ' . static::$tableName . '.<br>';

    }
}
class account extends model {
	public static $id = '';

	public $email;
	public $fname;
	public $lname;
	public $phone;
	public $birthday;
	public $gender;
	public $password;

    public static $tableName = 'accounts';

    static $data = array('whatever@gmail.com','First','Last','888-777-6666','1991-05-05','male','987654');

    public function __construct()
    {

        $this->email = 'whatever@gmail.com';
        $this->fname = 'First';
        $this->lname = 'Last';
        $this->phone = '888-777-6666';
        $this->birthday = '1991-05-05';
        $this->gender = 'male';
        $this->password = '987654';

    }
}
class todo extends model {
    public static $id = '18';

    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;

    public static $tableName = 'todos';

    static $data = array('whatev@gmail.com','4','2017-10-24 00:00:00','2017-11-25 00:00:00','new test','0');
	
	public function __construct()
    {
        $this->owneremail = 'whatev@gmail.com';
        $this->ownerid = '4';
        $this->createddate = '2017-10-24 00:00:00';
        $this->duedate = '2017-11-25 00:00:00';
        $this->message = 'new test';
        $this->isdone = '0';

    }
}

class tableFunctions {

   public static function createTable($result) {
        echo '<style>table { border-collapse: collapse; } table, tr { border: 1px solid black; }</style>';
        echo '<table>';
        foreach ($result as $row) {
            echo '<tr>';
            foreach ($row as $column) {
                echo '<td>' . $column . '<td>';
            }
            echo '<tr>';
        }
        echo '<table>';
   }
}

class stringFunctions {

    static public function headingOne($text) {
        return '<h1>' . $text . '</h1>';
    }

    static public function printThis($inputText) {
        return print($inputText);
    }
}

// this would be the method to put in the index page for accounts
$records = accounts::findAll();
tableFunctions::createTable($records);
echo '<br>';
$records = accounts::findOne(1);
tableFunctions::createTable($records);
echo '<br>';
$records = accounts::findOne(3);
tableFunctions::createTable($records);
echo '<br>';
// this would be the method to put in the index page for todos
$records = todos::findAll();
//print_r($records);
tableFunctions::createTable($records);

echo '<br>';
$records = todos::findOne(1);
tableFunctions::createTable($records);

echo '<br>';
$records = todos::findOne(3);
tableFunctions::createTable($records);

echo '<br>';

$obj = new todo;
$obj->delete();

echo '<br>';
//$newobj = new account;
//$newobj->delete();
/*
$obj = new account;
$obj->save();
$records = accounts::findAll();
tableFunctions::createTable($records);
*/
/*
$newobj = new todo;
$newobj->save();
*/
$records = todos::findAll();
tableFunctions::createTable($records);

?>