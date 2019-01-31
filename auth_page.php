<?PHP
#AUTHENTICATION PAGE
echo "Creating auth page...".PHP_EOL;
$auth_page = '<?PHP
session_start();

function generateRandomString($length = 10) {
    $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $charactersLength = strlen($characters);
    $randomString = "";
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="style.css" />

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">CRUD</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="./auth.php">Auth <span class="sr-only">(current)</span></a>
      </li>
    </ul>
  </div>
</nav>

<h1 class="display-3">Auth</h1>

<?PHP
$auth_config = json_decode(file_get_contents("./config/auth.json"), true);
if(isset($_SESSION["username"]) && isset($_SESSION["password"])){
    if($_SESSION["username"] == $auth_config["admin_user"] && $_SESSION["password"] == $auth_config["admin_pw"]){
        if(isset($_GET["redir"])){
            echo "<meta http-equiv=refresh content=\'0;URL=".$_GET["redir"]."\' /> ";
        }else{
            echo "<meta http-equiv=refresh content=\'0;URL=list.php\' /> ";
        }
    }
}

$csrf_token = generateRandomString(16);



if(isset($_POST["username"]) && isset($_POST["password"])){
    if($_POST["username"] == $auth_config["admin_user"]){
        if(hash("whirlpool",$_POST["password"]) == $auth_config["admin_pw"] && $_SESSION["csrf_token"] == $_POST["csrf_token"]){
            $_SESSION["username"] = $_POST["username"];
            $_SESSION["password"] = $auth_config["admin_pw"];
			echo "<meta http-equiv=refresh content=\'0;URL=".$_GET["redir"]."\' /> ";
        }else{
            echo "<b>Wrong Password</b>";
            echo $_SESSION["csrf_token"];
            echo $_POST["csrf_token"];
        }
    }else{
        echo "<b>Wrong Password</b>";
    }
}

$_SESSION["csrf_token"] = $csrf_token;

?>
<form action="auth.php" method="post">
<input type="hidden" name="csrf_token" value="<?PHP echo $csrf_token; ?>" />
<input type="text" name="username" />
<br>
<input type="password" name="password" />
<br>
<button>Submit</button>
</form>
';
file_put_contents("./output/auth.php",$auth_page);
?>
