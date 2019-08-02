<?php
$url = "./";
$lj = opendir($url);
while(($filename = readdir($lj)) != false){
echo $filname;
if(is_dir($url.$filename)){
echo "是目录"."";
}
echo "";
}
closedir($lj);
?>