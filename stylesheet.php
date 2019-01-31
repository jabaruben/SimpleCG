<?PHP
#ADD PAGE
echo "Downloading stylesheet...".PHP_EOL;
$stylesheet = file_get_contents("https://tuxnull.com/CDN/bootstrap.min.css");
file_put_contents("./output/style.css",$stylesheet);
?>