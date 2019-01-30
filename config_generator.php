<?PHP

$quit = false;

$fieldarr = array();

$fieldcount = 0;

$main = array();

echo "Enter db_host:";
$main["db_host"] = rtrim(fgets(STDIN));
echo "Enter db_user:";
$main["db_user"] = rtrim(fgets(STDIN));
echo "Enter db_pw:";
$main["db_pw"] = rtrim(fgets(STDIN));
echo "Enter db_name:";
$main["db_name"] = rtrim(fgets(STDIN));
echo "Enter db_table:";
$main["db_table"] = rtrim(fgets(STDIN));
echo "Enter admin_name:";
$main["admin_name"] = rtrim(fgets(STDIN));
echo "Enter admin_pass:";
$main["admin_pass"] = rtrim(fgets(STDIN));

while($quit == false){
    $fieldcount = $fieldcount + 1;
    echo "Enter Field ".$fieldcount." Name:";
    $fieldarr["name"] = rtrim(fgets(STDIN));
    echo "Enter Field ".$fieldcount." Type:";
    $fieldarr["type"] = rtrim(fgets(STDIN));
    if($fieldarr["type"] == "dropdown"){
        echo "Enter Dropdown seperator:";
        $fieldarr["seperator"] = rtrim(fgets(STDIN));
    }
    $main["field".$fieldcount] = $fieldarr;
    unset($fieldarr);
    $fieldarr = array();
    echo "Add another field? (y/n)";
    $input = rtrim(fgets(STDIN));
    if($input != "y"){
        $quit = true;
    }
}
echo json_encode($main);
file_put_contents("config.json",json_encode($main));



?>
