<?PHP

$fields[0] = "";
$fieldtype[0] = "";
$field = "";
$fieldcounter = 0;
$table1 = "";
$sql_field_list = "";
if(file_exists("config.json")){
    $json_config = file_get_contents("config.json");
    $config = json_decode($json_config, true);
    echo "Creating output directory".PHP_EOL;
    mkdir("output");
    echo "Creating config directory".PHP_EOL;
    mkdir("output/config");
    echo "Populating SQL Config".PHP_EOL;
    $sql_config = array();
    $sql_config["host"] = $config["db_host"];
    $sql_config["username"] = $config["db_user"];
    $sql_config["password"] = $config["db_pw"];
    $sql_config["database"] = $config["db_name"];
    $sql_config["table"] = $config["db_table"];
    file_put_contents("./output/config/sql.json", json_encode($sql_config));
    echo "Populating Authentication Config".PHP_EOL;
    $auth_config = array();
    $auth_config["admin_user"] = $config["admin_name"];
    $auth_config["admin_pw"] = hash("whirlpool", $config["admin_pass"]);
    file_put_contents("./output/config/auth.json", json_encode($auth_config));
    echo "Populating Field Config".PHP_EOL;
    $field_config = array();
    $count = 1;
    while(array_key_exists("field".$count,$config)){
        $field_config[$count] = $config["field".$count];
        if($config["field".$count]["type"] == "text"){
            $sql_field_list = $sql_field_list . "   " . strtolower($config["field".$count]["name"]) . " TEXT,\n";
        }
        if($config["field".$count]["type"] == "dropdown"){
            $sql_field_list = $sql_field_list . "   " .strtolower($config["field".$count]["name"]) . " TEXT,\n";
        }
        $count = $count + 1;
    }
    file_put_contents("./output/config/fields.json", json_encode($field_config));
    echo "Populating SQL Server".PHP_EOL;
    $mylink = mysqli_connect($config["db_host"], $config["db_user"], $config["db_pw"], $config["db_name"]);
    if (!$mylink) {
        echo "Error: Could not connect to MySQL Server." . PHP_EOL;
        echo "Debug-ErrNo: " . mysqli_connect_errno() . PHP_EOL;
        echo "Debug-ErrMsg: " . mysqli_connect_error() . PHP_EOL;
        exit;
    }
    $result = mysqli_query($mylink, 'CREATE TABLE IF NOT EXISTS '.$sql_config["table"].' (
    '.$sql_field_list.'    crud_overhead TEXT
    )');
    if($result == false){
        echo "Error: ".mysqli_error($mylink);
    }
# LIST PAGE
echo "Creating list page...".PHP_EOL;
$list_page = '<?PHP
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<?PHP
$sql_config = json_decode(file_get_contents("./config/sql.json"),true);
$mylink = mysqli_connect($sql_config["host"], $sql_config["username"], $sql_config["password"], $sql_config["database"]);
if (!$mylink) {
    echo "Error: Could not connect to MySQL Server." . PHP_EOL;
    echo "Debug-ErrNo: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debug-ErrMsg: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
$result = mysqli_query($mylink, "SELECT * FROM ".$sql_config["table"]);
while ($row = mysqli_fetch_row($result)){
    $counter = 0;
    while($row[$counter] != ""){
        echo $row[$counter];
        $counter = $counter + 1;
    }
}
?>';
file_put_contents("./output/list.php",$list_page);


#AUTHENTICATION PAGE
echo "Creating auth page...".PHP_EOL;
$auth_page = '<?PHP
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<?PHP
$auth_config = json_decode(file_get_contents("./config/auth.json"), true);
if(isset($_SESSION["username"]) && isset($_SESSION["password"])){
    if($_SESSION["username"] == $auth_config["admin_user"] && $_SESSION["password"] == $auth_config["admin_pw"]){
        if(isset($_GET["redir"])){
            echo "<meta http-equiv=refresh content='."'".'0;URL=".$_GET["redir"]."'."'".' /> ";
        }else{
            echo "<meta http-equiv=refresh content='."'".'0;URL=list.php'."'".' /> ";
        }
    }
}
if(isset($_POST["username"]) && isset($_POST["password"])){
    if($_POST["username"] == $auth_config["admin_user"]){
        if(hash("whirlpool",$_POST["password"]) == $auth_config["admin_pw"]){
            $_SESSION["username"] = $_POST["username"];
            $_SESSION["password"] = $auth_config["admin_pw"];
        }else{
            echo "<b>Wrong Password</b>";
        }
    }else{
        echo "<b>Wrong Password</b>";
    }
}



?>
<form action="auth.php" method="post">
<input type="text" name="username" />
<br>
<input type="password" name="password" />
<br>
<button>Submit</button>
</form>
';
file_put_contents("./output/auth.php",$auth_page);

}else{
    die('Configuration File "config.json" not found!');
}

?>
