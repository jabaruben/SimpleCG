<?PHP
# LIST PAGE
echo "Creating list page...".PHP_EOL;
$list_page = '<?PHP
session_start();
$style_config = json_decode(file_get_contents("./config/style.json"), true);
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="style.css" />
<script src="./script.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#"><?PHP echo $style_config["org"] ?></a>
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
    die("<meta http-equiv=refresh content='."'".'0;URL=auth.php'."'".' />");
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
		mysqli_query($mylink,"DELETE FROM " . $sql_config["table"] . " WHERE crud_overhead=\'".trim(filter_var($_GET["delstr"],FILTER_SANITIZE_STRING))."\'" );
		echo \'<div class="alert alert-danger" role="alert"><b>Data entry deleted!</b><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>\';
	}
    if($action == "2"){
        if($_GET["table"] == "new_t"){
            die("<meta http-equiv=refresh content='."'".'0;URL=create.php'."'".' />");
        }else{
            $result = mysqli_query($mylink, "SHOW TABLES LIKE \'".$_GET["table"]."\'");
            $table = mysqli_fetch_row($result)[0];
            if($table != null){
                file_put_contents("./config/fields_".$sql_config["table"].".json", json_encode($field_config));
                if(file_exists("./config/fields_".$table.".json")){
                    echo "Config found";
                    $field_config = json_decode(file_get_contents("./config/fields_".$table.".json"), true);
                    file_put_contents("./config/fields.json", json_encode($field_config));
                }else{
                    echo \'<div class="alert alert-warning" role="alert"><b>Warning!</b> Could not load table-specific field configuration file.</div>\';
                }
                $sql_config["table"] = $table;
                file_put_contents("./config/sql.json",json_encode($sql_config));
                
            }else{
                echo \'<div class="alert alert-danger" role="alert"><b>Error</b> Could not switch table</div>\';
            }
        }
    }
}

echo \'<div class="modal" tabindex="-1" role="dialog" id="deletedialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Deletion</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>This action can not be reverted!</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
        <a href="" id="deleteconfirm" class="btn btn-danger">Confirm</a>
      </div>
    </div>
  </div>
</div>\';

?>
<form action="list.php" method="get">
<div class="form-group">
    <label for="exampleFormControlSelect1">Table Selector</label>
    <select class="form-control" id="exampleFormControlSelect1" name="table">
<?PHP

$answer = mysqli_query($mylink, "show tables");
echo \'<option value="\'.$sql_config["table"].\'">Current: \'.$sql_config["table"].\'</option>\';
while ($row = mysqli_fetch_row($answer)){
    echo \'<option>\'.$row[0].\'</option>\';
}
echo \'<option value="new_t">Create...</option>\';
?>
    </select>
<div class="input-group-append">
    <button class="btn btn-outline-secondary" id="button-addon2">Switch Table</button>
  </div>
</div>
<input type="hidden" name="action" value="2">
</form>

<?PHP

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
    while(array_key_exists($counter,$row)){
            if(substr(trim($row[$counter]), 0, 8 ) === "delhash_"){
                $deletestr = $row[$counter];
            }else{
                echo "<td>".$row[$counter]."</td>";
		        $totalstring = $totalstring . strtolower($counter) . "=\"" . $row[$counter] . "\"&";
            }
        $counter = $counter + 1;
    }
	echo "<td><a class=\'badge badge-danger\' href=\'javascript:warndelete(\"".$deletestr."\")\'>Delete</a> <a class=\'badge badge-light\' href=\'edit.php?dt_tkn=".$deletestr."\'>Update</a></td></tr><tr>";
	
}
echo "</tr></tbody></table>";
?>';
file_put_contents("./output/list.php",$list_page);
?>
