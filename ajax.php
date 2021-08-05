<?php
/**
 * we use AJAX to refresh price of all coins without reload the whole page
 */
require("sql.php");
$fromurl=@$_GET["url"];
$id=@$_GET["id"];
if($fromurl=="")$fromurl="account";

function getprices(){//Get all prices
$res = file_get_contents("https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=25&page=1&sparkline=true&price_change_percentage=1h");
$fp = fopen("json/prices.txt",'w');
fwrite($fp,$res);
   fclose($fp);
   $ps=json_decode($res,true);
    for($i=0;$i<count($ps);$i++){
	NDFsql("update currency set price='".$ps[$i]["current_price"]."',change='".$ps[$i]["price_change_percentage_24h"]."' where id='".$ps[$i]["id"]."' ");
	echo '[{"id":"'.$ps[$i]["id"].'","price":"'.$ps[$i]["current_price"].'","change":"'.$ps[$i]["price_change_percentage_24h"].'"}]';
	}
}

function getprice($id){//get one price
$res = file_get_contents("https://api.coingecko.com/api/v3/simple/price?ids=".$id."&vs_currencies=usd&include_market_cap=true&include_24hr_vol=true&include_24hr_change=true&include_last_updated_at=true");
 $ps=json_decode($res,true);
echo '{"price":"'.$ps[$id]["usd"].'","change":"'.round($ps[$id]["usd_24h_change"],5).'","last_updated":"'.$ps[$id]["last_updated_at"].'","market_cap":"'.$ps[$id]["usd_market_cap"].'"}';
NDFsql("update currency set price='".$ps[$id]["usd"]."',change='".round($ps[$id]["usd_24h_change"],5)."' where id='".$id."' ");
}

if($id<>""){
getprice($id);
}else{
getprices();
}
?>