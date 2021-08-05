<?php
require("sql.php");	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<title>Loading...</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="favicon.png" />
		<link href="styles.css" type="text/css" rel="stylesheet" />
   
</head>
<body>
<?php require('top.php');?>
<div id="body1">
  <div style="padding:10px;"> </div>
  
  <table width="100%" border="0" cellspacing="20" cellpadding="0" >
    <tr>
      <td height="411" align="center">
      <p><img src="imgs/loading.gif" alt="loading" width="32" height="32" /></p>
    <p>Loading...</p></td>
    </tr>
  </table>
</div>
<?php require('bot.php')?>
</body>
</html>
<?php
//getprices();
function getprices(){//fetch json save it to db
$res = file_get_contents("https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=25&page=1&sparkline=true&price_change_percentage=1h");
//$res=json_decode($res,true);
$fp = fopen("json/prices.txt",'w');
fwrite($fp,$res);
   fclose($fp);
   return $res;
}
$ps=getprices();
$ps=json_decode($ps,true);

for($i=0;$i<count($ps);$i++){
$cz=NDF_dq("select count(id) as totals from currency where id='".$ps[$i]["id"]."'","totals");
		if($cz==0){//identify new coin
		NDFsql("INSERT INTO currency (id,name,symbol)VALUES ('".$ps[$i]["id"]."','".$ps[$i]["name"]."','".$ps[$i]["symbol"]."')");
		}	
if(file_exists("logos/".$ps[$i]["id"].".png")==false){GetR($ps[$i]["image"],"logos/".$ps[$i]["id"].".png");}//save icons
NDFsql("update currency set price='".$ps[$i]["current_price"]."',change='".$ps[$i]["price_change_percentage_24h"]."' where id='".$ps[$i]["id"]."' ");
}
echo "<meta http-equiv=refresh content=0;URL=prices.php>";
?>
