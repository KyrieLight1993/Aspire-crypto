<?php
require("sql.php");
$f=@$_POST["f"];
$sqltxt="";
$res =urldecode(@$_POST["str"]);
$fp = fopen("json/".$f,'w');
fwrite($fp,$res);
fclose($fp);

$ps=$res;
$ps=json_decode($ps,true);
$db = new SQLite3(DBfile);
$db->exec("begin;");
for($i=0;$i<count($ps);$i++){
$db->exec("update currency set price='".$ps[$i]["current_price"]."',change='".$ps[$i]["price_change_percentage_24h"]."' where id='".$ps[$i]["id"]."' ");
}
$db->exec("commit;");
echo "......";
?>