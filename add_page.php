<?PHP
#ADD PAGE
echo "Creating add page...".PHP_EOL;
$add_page = '<?PHP
session_start();



?>
<!DOCTYPE html>
<html lang="en">
<?PHP
$authok = false;
$auth_config = json_decode(file_get_contents("./config/auth.json"), true);
$field_config = json_decode(file_get_contents("./config/fields.json"), true);
$sql_config = json_decode(file_get_contents("./config/sql.json"),true);

$mylink = mysqli_connect($sql_config["host"], $sql_config["username"], $sql_config["password"], $sql_config["database"]);
if (!$mylink) {
    echo "Error: Could not connect to MySQL Server." . PHP_EOL;
    echo "Debug-ErrNo: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debug-ErrMsg: " . mysqli_connect_error() . PHP_EOL;
    exit;
}



if(isset($_SESSION["username"]) && isset($_SESSION["password"])){
    if($_SESSION["username"] == $auth_config["admin_user"] && $_SESSION["password"] == $auth_config["admin_pw"]){
        $authok = true;
    }
}

if($authok != true){ #Redirect non-authenticated users to authentication page
    echo "<meta http-equiv=refresh content='."'".'0;URL=auth.php'."'".' />";
}

$count = 1;
$buildstr = "";
$valuestr = "";
while(array_key_exists($count,$field_config)){
    if(isset($_POST[$count])){
        $valuestr = $valuestr . "\'" . $_POST[$count] . "\',";
        $buildstr = strtolower($buildstr . $field_config[$count]["name"] . ", ");
    }
    $count = $count + 1;
}

if(strlen($valuestr)>0){
    mysqli_query($mylink, "INSERT INTO ".$sql_config["table"]." (".$buildstr." crud_overhead) VALUES (".$valuestr." \' \')");
    echo "INSERT INTO ".$sql_config["table"]." (".$buildstr." crud_overhead) VALUES (".$valuestr.", \' \')";
}



?>
<form action="add.php" method="post">
<?PHP
$count = 1;
while(array_key_exists($count,$field_config)){
    echo "<b>".$field_config[$count]["name"]."</b><input type=text name=".$count." />";
    $count = $count + 1;
}
?>
<button>Submit</button>
</form>
';
file_put_contents("./output/add.php",$add_page);
?>
