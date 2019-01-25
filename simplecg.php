<?PHP

$fields[0] = "";
$fieldtype[0] = "";
$field = "";
$fieldcounter = 0;
$table1 = "";
if(file_exists("config.yaml")){
    $handle = fopen("config.yaml", "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            echo $line."\n";
            if (strpos($line, 'db_host') !== false) {
                $db_host = str_replace("db_host:","",$line);
            }else if (strpos($line, 'db_user') !== false) {
                $db_user = str_replace("db_user:","",$line);
            }else if (strpos($line, 'db_pw') !== false) {
                $db_pw = str_replace("db_pw:","",$line);
            }else if (strpos($line, 'db_name') !== false) {
                $db_name = str_replace("db_name:","",$line);
            }else if (strpos($line, 'admin_user') !== false) {
                $admin_user = str_replace("admin_user:","",$line);
            }else if (strpos($line, 'admin_pass') !== false) {
                $admin_pass = str_replace("admin_pass:","",$line);
            }else if (strpos($line, 'table1') !== false) {
                $table1 = "true";
            }
            if($table1 == "true"){
                if($field == ""){
                    if (strpos($line, 'name') !== false) {
                        $table_name = str_replace("name:","",$line);
                    }
                    if (strpos($line, 'field') !== false) {
                        $field = "true";
                        $fieldcounter = $fieldcounter + 1;
                    }
                }else{
                    if (strpos($line, 'field') !== false) {
                        $field = "true";
                        $fieldcounter = $fieldcounter + 1;
                    }
                    if (strpos($line, 'name') !== false) {
                        $fields[$fieldcounter] = str_replace("name:","",$line);
                    }
                    if (strpos($line, 'type') !== false) {
                        $fieldtype[$fieldcounter] = str_replace("type:","",$line);
                        if($fieldtype[$fieldcounter] == "dropdown"){
                            $fieldtype[$fieldcounter] = str_replace("type:","",trim(fgets($handle)));
                        }
                    }
                }
            }
        }

        fclose($handle);
    } else {
        // error opening the file.
    } 
    var_dump($fields);
    var_dump($fieldtype);
}else{
    die('Configuration File "config.yml" not found!');
}

?>
