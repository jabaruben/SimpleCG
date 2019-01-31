<?PHP
#ADD PAGE
echo "Creating add page...".PHP_EOL;
$add_page = '<?PHP
session_start();



?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="style.css" />

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#">CRUD</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="./list.php">List</a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="./add.php">Add <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="./admin.php">Admin</a>
      </li>
    </ul>
  </div>
</nav>

<h1 class="display-3">Add</h1>

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
$hashstr = "";
while(array_key_exists($count,$field_config)){
    if(isset($_POST[$count])){
        if(trim($_POST[$count]) != ""){
            $valuestr = $valuestr . "\'" . $_POST[$count] . "\',";
            $buildstr = strtolower($buildstr . $field_config[$count]["name"] . ", ");
            $hashstr = $hashstr . $_POST[$count];
        }else{
            $valuestr = $valuestr . "\' \',";
            $buildstr = strtolower($buildstr . $field_config[$count]["name"] . ", ");
        }
    }
    $count = $count + 1;
}
$hashstr = $hashstr . random_int(1,10000);
$hashstr = hash("whirlpool",$hashstr);

if(strlen($valuestr)>0){
    mysqli_query($mylink, "INSERT INTO ".$sql_config["table"]." (".$buildstr." crud_overhead) VALUES (".$valuestr." \'delhash_".$hashstr."\')");
	echo \'<div class="alert alert-success" role="alert">Data added successfully!</div>\';
}



?>
<form action="add.php" method="post">
<?PHP
$count = 1;
while(array_key_exists($count,$field_config)){
    echo \'<div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text" id="basic-addon1">\'.$field_config[$count]["name"].\'</span>
      </div>
      <input type="text" class="form-control" placeholder="\'.$field_config[$count]["name"].\'" name="\'.$count.\'">
    </div>
    \';
    $count = $count + 1;
}
?>
<button type="submit" class="btn btn-primary">Submit</button>
</form>
';
file_put_contents("./output/add.php",$add_page);
?>
