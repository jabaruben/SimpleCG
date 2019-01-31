<?PHP
# LIST PAGE
echo "Creating javascript...".PHP_EOL;
$script_file = '
function warndelete(key){
    $("#deleteconfirm").attr("href", "list.php?action=1&delstr="+key);
    $("#deletedialog").modal("toggle");
}



';
file_put_contents("./output/script.js",$script_file);
?>
