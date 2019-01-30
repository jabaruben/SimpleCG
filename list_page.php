<?PHP
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
    while(trim($row[$counter]) != ""){
        echo $row[$counter];
        $counter = $counter + 1;
    }
}
?>';
file_put_contents("./output/list.php",$list_page);
?>
