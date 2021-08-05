<?php require("sql.php");
$psize=15;
$page=@$_GET["page"];
if($page=="")$page=1;
$pcount=6;
function rurl($str0,$str)
{
return $str0.$str;
}

function topic($str){//格式化像素
$h=32;$w=100;//像素预设
$zd=zdz($str);
$zx=zxz($str,$zd);
$zd=$zd-$zx;//最大值减最小值确定区间
$step=floor(count($str)/$w);
$arr="";
for($i=0;$i<$w;$i+=$step){
$arr.=($h-floor(($str[$i]-$zx)*$h/$zd)).",";
}
return $arr;
}
function zdz($str){//取最大值
$zd=0;
for($i=0;$i<count($str);$i++){
	if($str[$i]>$zd){$zd=$str[$i];}
}
return $zd;
}
function zxz($str,$zd){//取最小值
$zx=$zd;
for($i=0;$i<count($str);$i++){
	if($str[$i]<$zx){$zx=$str[$i];}
}
return $zx;
}
function us($volumeE){//单位换算
$symbleE ="";
if ($volumeE>=1000000 and $volumeE<1000000000){
                    $volumeE/=1000000.0;
                    $symbleE= "M";
}
elseif($volumeE>=1000000000){
                    $volumeE/=1000000000.0;
                    $symbleE= "B";
}
return round($volumeE,2).$symbleE;
}
function rg($v){//红绿色
if($v<0){
return "<span class='Green'>".$v."%</span>";
}else{
return "<span class='Red'>+".$v."%</span>";}
}
function pricer($price){
if($price>0.0001){
return round($price,2);
}
else{return $price;}
}
function getprices($page){//获取json
//header("location:load.php");exit;
//echo "<meta http-equiv=refresh content=0;URL=load.php>";exit;
$res = file_get_contents("https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=15&page=".$page."&sparkline=true&price_change_percentage=1h%2C24h%2C7d%2C30d%2C1y");
//$res=json_decode($res,true);
$fp = fopen("json/prices.txt",'w');
fwrite($fp,$res);
   fclose($fp);
   return $res;
}
//getprices();
function GetPrice1(){//本地测试用
$filename="json/prices.txt";
if(file_exists($filename)){
$etime=filemtime($filename);//缓存当天数据加速
	if(date("d",$etime)==date("d",time())){
    $handle = fopen($filename,"r");
    $contents = fread($handle, filesize($filename));
    fclose($handle);
	return $contents;
	}else{ getprices();exit;}
}else{
 getprices();exit;
}
}
$ps=getprices($page);//GetPrice1();
$ps=json_decode($ps,true);
//var_dump( $ps[1]["sparkline_in_7d"]["price"]);
//echo $ps[1]["sparkline_in_7d"]["price"][0];
//echo base64_encode(topic($ps[0]["sparkline_in_7d"]["price"]));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<title>Prices</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="favicon.png" />
		<link href="styles.css" type="text/css" rel="stylesheet" />
        <script type="text/javascript" src="js/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php require('top.php');?>
<div id="body1">
  <div style="padding:10px;"> </div>
  
  <table width="100%" border="0" cellspacing="20" cellpadding="0" >
    <tr>
      <td><table width="100%" border="0" cellspacing="0" cellpadding="10" id="paper">
        <tr>
          <td class="Bold">&nbsp;&nbsp;Name</td>
          <td class="Bold">Price</td>
          <td class="Bold">Change</td>
          <td class="Bold">Volume</td>
          <td class="Bold">Market Cap</td>
          <td class="Bold">Supply</td>
          <td align="center" class="Bold">Price Chart</td>
        </tr>
        <?php for($i=0;$i<count($ps);$i++){
		NDFsql("update currency set price='".$ps[$i]["current_price"]."',change='".$ps[$i]["price_change_percentage_24h"]."' where id='".$ps[$i]["id"]."' ");
		$cz=NDF_dq("select count(id) as totals from currency where id='".$ps[$i]["id"]."'","totals");
		if(file_exists("logos/".$ps[$i]["id"].".png")==false or $cz==0){
		if($cz==0){//自动识别新货币
		NDFsql("INSERT INTO currency (id,name,symbol)VALUES ('".$ps[$i]["id"]."','".$ps[$i]["name"]."','".$ps[$i]["symbol"]."')");
		GetR($ps[$i]["image"],"logos/".$ps[$i]["id"].".png");
		}}
		?>
        <tr>
          <td>
        <a href="price.php?id=<?=$ps[$i]["id"]?>" ><img src="logos/<?=$ps[$i]["id"]?>.png" width="32" height="32" hspace="3" class="Img_m" /><?=$ps[$i]["name"]?></a> <?=strtoupper($ps[$i]["symbol"])?></td>
          <td><span class="US">$ <?=pricer($ps[$i]["current_price"],2)?></span></td>
          <td><?=rg(round($ps[$i]["price_change_percentage_24h"],2))?></td>
          <td class="US">US$ <?=us($ps[$i]["total_volume"])?></td>
          <td class="US">US$ <?=us($ps[$i]["market_cap"])?></td>
          <td><?=us($ps[$i]["total_supply"])?></td>
          <td align="center">
            <a href="price.php?id=<?=$ps[$i]["id"]?>"><img src="img.php?arr=<?=topic($ps[$i]["sparkline_in_7d"]["price"]);?>" width="105" height="33" hspace="3" class="Img_m" /></a></td>
        </tr>
        <?php }?>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="80" align="center" class="Bold"> Page: <b><?php echo $page; ?></b> &nbsp;&nbsp;&nbsp;
          <?php if ($page>1)
			 {?>
          <a id="P1" href="<?php echo rurl("?","page=1");?>" >Home</a> [<a id="P2" href="<?php echo rurl("?","page=".($page-1));?>" >Previous</a>]
          <?php
            }
if($page<$pcount)
{?>
[<a id="P3" href="<?php echo rurl("?","page=".($page+1));?>" >Next</a>] <a id="P4" href="<?php echo rurl("?","page=".$pcount);?>" >Last</a>
<?php }?></td>
  </tr>
</table>

      </td>
    </tr>
  </table>
  
</div>
<script type="text/javascript">
var myFunction = function() { 
   htmlobj=$.ajax({url:"ajax.php",async:true});
  if(htmlobj.responseText!=""){
  location.reload();
  }
}; 

var timeOut = setInterval(myFunction, 30000); 
</script>
<?php require('bot.php')?>
</body>
</html>