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
    protected $tableName;
    public function save()
    {
        if ($this->id = '') {
            $sql = $this->insert();
        } else {
            $sql = $this->update();
        }
        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $statement->execute();
        $tableName = get_called_class();
        $array = get_object_vars($this);
        $columnString = implode(',', $array);
        $valueString = ":".implode(',:', $array);
       // echo "INSERT INTO $tableName (" . $columnString . ") VALUES (" . $valueString . ")</br>";
        echo 'I just saved record: ' . $this->id;
    }
    private function insert() {
        $sql = 'something';
        return $sql;
    }
    private function update() {
        $sql = 'something';
        return $sql;
        echo 'I just updated record' . $this->id;
    }
    public function delete() {

        $db = dbConn::getConnection();
        //$tableName = get_called_class();
        $sql = 'DELETE FROM ' . $this->tableName.' WHERE id=' . $this->id;
        //echo $sql;

        $statement = $db->prepare($sql);
        $statement->execute();

        echo 'The row with id ' . $this->id. ' has been deleted.';


    }
}
class account extends model {
	public $id;
	public $email;
	public $fname;
	public $lname;
	public $phone;
	public $birthday;
	public $gender;
	public $password;
	
	public function __construct()
    {
        $this->tableName = 'accounts';
	    $this->id = '9';
    }
}
class todo extends model {
    public $id;
    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;
	
	public function __construct()
    {
        $this->tableName = 'todos';
        $this->id = '4';
	
    }
}


class tableFunctions {

   public static function createTable($result) {
       echo '<table>';
        foreach ($result as $column) {
            echo '<tr>';
            foreach ($column as $row) {
                echo '<td>' . $row . '<td>';
            }
            echo '<tr>';
        }
        echo '<table>';
   }


}
// this would be the method to put in the index page for accounts
$records = accounts::findAll();
//print_r($records);
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
$obj = new todo;
$obj->delete();

echo '<br>';
$newobj = new account;
$newobj->delete();

?>