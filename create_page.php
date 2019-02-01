<?PHP
#ADD PAGE
echo "Creating table creation page...".PHP_EOL;
$create_page = '<?PHP
session_start();
$style_config = json_decode(file_get_contents("./config/style.json"), true);
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="style.css" />
<script src="./script.js"></script>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#"><?PHP echo $style_config["org"] ?></a>
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
      <li class="nav-item active">
        <a class="nav-link" href="#">Create Table <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="./admin.php">Admin</a>
      </li>
    </ul>
  </div>
</nav>

<h1 class="display-3">Create Table</h1>

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
    die("<meta http-equiv=refresh content='."'".'0;URL=auth.php'."'".' />");
}

$fields = array();
$count = 0;
$sql_field_list = "";
$temparr = array();
while(isset($_POST["f_".$count])){
    unlink($temparr);
    $temparr["name"] = strtolower(filter_var($_POST["f_".$count], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH));
    $temparr["type"] = strtolower(filter_var($_POST["f_".$count."_type"], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH));
    $sql_field_list = $sql_field_list . "   " . filter_var(strtolower($_POST["f_".$count]),FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH) . " TEXT,\n";
    $fields[$count+1] = $temparr;
    $count = $count + 1;
}
if(isset($_POST["f_0"])){
    $result = mysqli_query($mylink, \'CREATE TABLE IF NOT EXISTS \'.strtolower($_POST["tablename"]).\' (
    \'.$sql_field_list.\'    crud_overhead TEXT
    )\');
    if($result == false){
        echo "Error: ".mysqli_error($mylink);
    }
    file_put_contents("./config/fields_".$_POST["tablename"].".json", json_encode($fields));
    $sql_config["table"] = $_POST["tablename"];
    file_put_contents("./config/sql.json", json_encode($sql_config));
}


?>
<form action="create.php" method="post">
<div class="form-group">
<label for="exampleInputEmail1">Table Name</label>
<input type="text" class="form-control" name="tablename" placeholder="Enter Table Name" required>
<small id="emailHelp" class="form-text text-muted">Please only enter small latin characters and numbers.</small>
</div>
<div id="inputs">
</div>
<a href="javascript:addField(0)" class="btn btn-secondary" id="addbtn">Add Field</a>
<button type="submit" class="btn btn-primary">Submit</button>
</form>

';
file_put_contents("./output/create.php",$create_page);
?>
