<?php require("sql.php");
$a="";
if (isset($_GET['a'])) {
  $a= $_GET["a"];
}

if($user==""){header("location:login.php");exit;}
if($a=="recharge"){
$price=NDFint($_POST["price"]);
NDFsql("update users set balance=balance+$price where user='".$user."'");
msg("Recharge success!");
exit;
}
if($a=="sell"){
$id=$_GET["id"];
$rs=NDFdqs("select * from currency where id='".$id."'");
$price=$rs["price"];
$rs2=NDFdqs("select * from orderlist where currency='".$id."' and user='".$user."'");
$qty=$rs2["qty"];
$market=$qty*$price;
NDFsql("update users set balance=balance+$market where user='".$user."'");
NDFsql("delete from orderlist where currency='".$id."' and user='".$user."'");
msg("Sell successfully!");
exit;
}

function rg2($v){//red color
if($v<0){
return "<span class='Green'>".$v."</span>";
}else{
return "<span class='Red'>".$v."</span>";}
}
function rg($v){//red color
if($v<0){
return "<span class='Green'>".$v."%</span>";
}else{
return "<span class='Red'>".$v."%</span>";}
}

$balance=NDF_dq("select * from users where user='".$user."'","balance");

$marks=0;
$profits=0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<title>Account</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="favicon.png" />
		<link href="styles.css" type="text/css" rel="stylesheet" />
        <script type="text/javascript" src="js/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php require('top.php');?>
<div id="body1">

  
  
  <table width="100%" border="0" cellspacing="20" cellpadding="0" >
    <tr>
      <td height="400" valign="top"><table width="800" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="50"> <form action="?a=recharge" method="post" id="form1" name="form1" style="display:inline">
      User ID:<b><?=$user?></b> Balance:<span class="Orange">$<?=$balance?></span> , 
 &nbsp;&nbsp;Recharge:<input name="price" type="text" id="price" value="0" size="6" />

 USD
 <input type="submit" name="button" id="button" value="submit" />
          </form></td>
        </tr>
      </table>
        <table width="100%" border="0" cellpadding="10" cellspacing="1" bgcolor="#E6E6E6" id="paper">
        <tr>
          <td width="5%" align="center" class="Bold">No.</td>
          <td width="11%" class="Bold">Currency</td>
          <td width="11%" class="Bold">QTY</td>
          <td width="9%" class="Bold">Price</td>
          <td width="10%" class="Bold">Market Value</td>
          <td width="15%" class="Bold">Change</td>
          <td width="12%" class="Bold">Profit</td>
          <td width="9%" align="center" class="Bold">&nbsp;</td>
        </tr>
        <?php
		$n=0;
		$db = new SQLite3(DBfile);
$stmt = $db->prepare("SELECT * FROM orderlist WHERE user='".$user."' limit 100");
$result = $stmt->execute();
while ($row = $result->fetchArray())
{
$n++;
$id=$row["currency"];
$rs=NDFdqs("select * from currency where id='".$id."'");
$price=$rs["price"];
//$profit=bcsub(($row["qty"]*$price),$row["price"],2)+0;
$profit=floor((($row["qty"]*$price)-$row["price"])*100)/100;
$change=round($profit/$row["price"],3);

$profits+=$profit;
$marks+=($row["qty"]*$price);
?>
        <tr>
          <td align="center" bgcolor="#FFFFFF"><?=$n?>.</td>
          <td bgcolor="#FFFFFF"><a href="price.php?id=<?=$id?>" ><img src="logos/<?=$id?>.png" width="32" height="32" hspace="3" class="Img_m" /><?=$rs["name"]?></a> <?=strtoupper($rs["symbol"])?></td>
          <td bgcolor="#FFFFFF">
            <?=$row["qty"]?>        </td>
          <td bgcolor="#FFFFFF" class="US">$ <?=$rs["price"]?></td>
          <td bgcolor="#FFFFFF" class="US">$ <?=$row["qty"]*$rs["price"]?></td>
          <td bgcolor="#FFFFFF" class="US"><?=rg($change)?></td>
          <td bgcolor="#FFFFFF">$ <?=rg2($profit)?></td>
          <td align="center" bgcolor="#FFFFFF" class="buttonA"><a href="?a=sell&id=<?=$id?>" onclick="return confirm('Are you sure?');">Sell</a></td>
        </tr>
        <?php }?>
      </table>
      <table width="100%" border="0" cellpadding="0" cellspacing="1">
    <tr>
      <td height="50" >
     Market value:$
     <?=$marks?> 
     , Profit:$
     <?=rg2($profits)?></td>
      </tr>
  </table>
      </td>
    </tr>
  </table>
  
</div>
<script type="text/javascript">
var timeOut = setInterval(update, 15000);//update the time

function update(){//use API to update price 
  htmlobj=$.ajax({url:"https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=90&page=1&sparkline=true&price_change_percentage=1h",async:true,type:"GET",dataType:"html",
   success:function(data){
   //$("#load").text(data);
		post1(data);
   }
   });
}  
function post1(strs){
htmlobj2=$.ajax({url:"pfile.php?f=allprices.txt",async:true,type:"POST",data: {"str":encodeURIComponent(strs), "f":"allprices.txt"},
  		 success:function(data1){
		 //$("#load").html(data1);
		 location.reload();
		 //window.location.href="account.php";
   		}
  		 });
}
</script>
<?php require('bot.php')?>

</body>
</html>