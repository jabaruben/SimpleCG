<?PHP
# LIST PAGE
echo "Creating list page...".PHP_EOL;
$list_page = '<?PHP
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
      <li class="nav-item active">
        <a class="nav-link" href="./list.php">List <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="./add.php">Add</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="./admin.php">Admin</a>
      </li>
    </ul>
  </div>
</nav>

<h1 class="display-3">List</h1>

<?PHP
$auth_config = json_decode(file_get_contents("./config/auth.json"), true);
$field_config = json_decode(file_get_contents("./config/fields.json"), true);
$sql_config = json_decode(file_get_contents("./config/sql.json"),true);


if(isset($_SESSION["username"]) && isset($_SESSION["password"])){
    if($_SESSION["username"] == $auth_config["admin_user"] && $_SESSION["password"] == $auth_config["admin_pw"]){
        $authok = true;
    }
}

if($authok != true){ #Redirect non-authenticated users to authentication page
    echo "<meta http-equiv=refresh content='."'".'0;URL=auth.php'."'".' />";
}

$mylink = mysqli_connect($sql_config["host"], $sql_config["username"], $sql_config["password"], $sql_config["database"]);
if (!$mylink) {
    echo "Error: Could not connect to MySQL Server." . PHP_EOL;
    echo "Debug-ErrNo: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debug-ErrMsg: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

if(isset($_GET["action"])){
	$action = trim($_GET["action"]);
	if($action == "1"){
		$count = 1;
		$totalstring = "";
		while(array_key_exists($count,$field_config)){
			$totalstring = $totalstring . strtolower($field_config[$count]["name"]) . " = " . $_GET[$count-1] . " AND ";
			$count = $count + 1;
		}
		$totalstring = $totalstring . "1 = 1";
		mysqli_query($mylink,"DELETE FROM " . $sql_config["table"] . " WHERE ". $totalstring);
		echo \'<div class="alert alert-danger" role="alert"><b>Data entry deleted!</b><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>\';
	}
}



echo \'<table class="table table-dark"><thead><tr><th scope="col">#</th>\';

$count = 1;
while(array_key_exists($count,$field_config)){
    echo "<th scope=\'col\'>".$field_config[$count]["name"]."</th>";
    $count = $count + 1;
}

echo \'<th scope="col">Actions</th></tr></thead><tbody><tr>\';

$result = mysqli_query($mylink, "SELECT * FROM ".$sql_config["table"]);
$rowc = 0;
$totalstring = "";
while ($row = mysqli_fetch_row($result)){
	$rowc = $rowc + 1;
    $counter = 0;
	echo "<th scope=\'row\'>".$rowc."</th>";
	$totalstring = "";
    while(trim($row[$counter]) != ""){
        echo "<td>".$row[$counter]."</td>";
		$totalstring = $totalstring . strtolower($counter) . "=\"" . $row[$counter] . "\"&";
        $counter = $counter + 1;
    }
	echo "<td><a href=\'list.php?".$totalstring."action=1\'>Delete</a></td></tr><tr>";
	
}
echo "</tr></tbody></table>";
?>';
file_put_contents("./output/list.php",$list_page);
?>
