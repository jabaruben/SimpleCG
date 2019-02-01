<?PHP
# LIST PAGE
echo "Creating javascript...".PHP_EOL;
$script_file = '
function warndelete(key){
    $("#deleteconfirm").attr("href", "list.php?action=1&delstr="+key);
    $("#deletedialog").modal("toggle");
}

function addField(id){
    $("#inputs").append(\'<div class="form-group"><label for="exampleInputEmail1">Field Name</label><input required type="text" class="form-control" name="f_\'+id+\'" placeholder="Enter Field Name"></div>\')
    $("#inputs").append(\'<div class="form-group"><label for="exampleFormControlSelect1">Field Type</label><select class="form-control" name="f_\'+id+\'_type" required><option>text</option></select></div>\')
    $("#addbtn").attr("href", "javascript:addField("+(id+1)+")")
}



'.file_get_contents("https://code.jquery.com/jquery-3.3.1.min.js");

file_put_contents("./output/script.js",$script_file);
?>
