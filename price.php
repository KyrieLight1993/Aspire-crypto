<?php require("sql.php");
$id=@$_GET["id"];
define("CID",$id);
define("DAYS",180);//天数
//define("PR",60);//概率值
$a="";
if (isset($_GET['a'])) {
  $a= $_GET["a"];
}

if($user==""){header("location:login.php");exit;}
if($a=="do"){//购买处理
$qty=NDFint(@$_POST["qty"]);
$p_price=NDFint(@$_POST["price"]);
$id=@$_POST["id"];
$price=NDF_dq("select * from currency where id='".$id."'","price");
$balance=NDF_dq("select * from users where user='".$user."'","balance");
if($p_price>0){
$qty=round($p_price/$price,5);
$c=$p_price;
}else{
$c=$price*$qty;
}

	if($qty>0){
	if($balance<($c)){
	msg("Sorry, your balance is not enough! Please recharge first!");
	exit;
	}
		if(NDF_dq("select count(currency) as total from orderlist where user='".$user."' and currency='".$id."'","total")==0){
		NDFsql("insert into orderlist (user,currency,qty,price,times)values('".$user."','".$id."',$qty,$c,'".NDF_time()."')");
		NDFsql("update users set balance=balance-$c where user='".$user."' ");
	echo "<script language=javascript>alert('The purchase was successful!');</script>";
	echo "<meta http-equiv=refresh content=0;URL=account.php>";
	exit;
		}else{
		NDFsql("update orderlist set qty=qty+$qty,price=price+$c where user='".$user."' and currency='".$id."' ");
		NDFsql("update users set balance=balance-$c where user='".$user."' ");
	echo "<script language=javascript>alert('The purchase was successful!');</script>";
	echo "<meta http-equiv=refresh content=0;URL=account.php>";
	exit;
		}
	}else{
	msg("The purchase quantity needs to be greater than 0 !");
	}
}

function jspic($str){//格式化k线像素
$arr="";
for($i=0;$i<count($str)-1;$i++){
$date1=date("Y-m-d",$str[$i][0]/1000);//时间转化
$price=$str[$i][1];
$price2=$str[$i+1][1];
$price3=$price*(1-UD($price));
$price4=$price2*(1+UD($price2));
$arr.="['".$date1."',".$price.",".($price2).",".$price3.",".$price4."],";//这里的数据格式参考下面JS开头的解释文字
}
return $arr;
}
function aik($str){//AI预测函数，根据以往走势数据进行一系列计算并无缝衔接真实k线，进行一系列平滑处理(smoothing)降低较大振幅的波动提高真实性
$arr="";$n=1;
$all=count($str);
for($i=$all/2;$i<$all-1;$i++){
$date1=date("Y-m-d",$str[$i][0]/1000);//时间转化
$price=$str[$i][1];
$price2=$str[$i+1][1];
$price3=$price*(1-UD($price));
$price4=$price2*(1+UD($price2));
$arr.="['".$date1."',".$price.",".($price2).",".$price3.",".$price4."],";//这里的数据格式可参考下面JS开头的解释文字
$lastprice=$price2;
}
$js=0.99;//大面额货币处理
for($i=$all-2;$i>($all*0.6);$i--){
$n++;
$date1=date("Y-m-d",time()+(86400*$n));
$ph=($str[$i][1]-$lastprice)/2;//平滑基数
if($price<20){$js=1;}
$price=($str[$i][1]-$ph)*$js;
$price2=($str[$i+1][1]-$ph);
$price3=$price*(1-UD($price));
$price4=$price2*(1+UD($price2));
$arr.="['".$date1."',".$price.",".($price2).",".$price3.",".$price4."],";
}
return $arr;
}
function PR($str){//概率值计算
$zx=zxz2($str,zdz2($str));
$zd=zdz2($str);
$gl=$zx/((5*$zx+$zd)/6);
if($gl<0.6){//对概率过高和过低的进行折中处理
$gl=rand(61,63)/100;
}elseif($gl>0.8){
$gl=rand(77,78)/100;
}
return round($gl,3)*100;
}
function UD($price){//k线上下出头长度
if($price>10){
return substr($price,strlen($price)-1,1)/200;
}else{
return substr($price,strlen($price)-1,1)/4000;
}
}
function zdz2($str){//取最大值
$zd=0;
for($i=0;$i<count($str);$i++){
	if($str[$i][1]>$zd){$zd=$str[$i][1];}
}
return $zd;
}
function zxz2($str,$zd){//取最小值
$zx=$zd;
for($i=0;$i<count($str);$i++){
	if($str[$i][1]<$zx){$zx=$str[$i][1];}
}
return $zx;
}
function us($volumeE){//单位换算
$symbleE ="";
if ($volumeE>=1000000 and $volumeE<1000000000){
                    $volumeE/=1000000.0;
                    $symbleE= "M";
}
else if($volumeE>=1000000000){
                    $volumeE/=1000000000.0;
                    $symbleE= "B";
}
return round($volumeE,2).$symbleE;
}
function rg($v){//红绿色呈现
if($v<0){
return "<span class='Green'>".$v."%</span>";
}else{
return "<span class='Red'>+".$v."%</span>";}
}
function getprice(){//获取json
$res = file_get_contents("https://api.coingecko.com/api/v3/coins/".CID."/market_chart?vs_currency=usd&days=".DAYS."&interval=daily");
//https://api.coingecko.com/api/v3/coins/bitcoin/market_chart/range?vs_currency=usd&from=1609462800&to=1626407513
$fp = fopen("json/".CID.".txt",'w');
fwrite($fp,$res);
   fclose($fp);
   return $res;
}
function GetPrice2($filename){//本地缓存
$filename="json/".$filename.".txt";
if(file_exists($filename)){
$etime=filemtime($filename);//缓存当天数据加速
	if(date("d",$etime)==date("d",time())){
    $handle = fopen($filename,"r");
    $contents = fread($handle, filesize($filename));
    fclose($handle);
	return $contents;
	}else{return getprice();}
}else{
return getprice();
}
}
//$ps=getprice();
$ps=getprice();//GetPrice2(CID);
$ps=json_decode($ps,true);

$rs=NDFdqs("select * from currency where id='".$id."'");
$price=$rs["price"];
$change=$rs["change"];
//echo var_dump($ps["prices"]);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<title><?=$rs["name"]?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="favicon.png" />
		<link href="styles.css" type="text/css" rel="stylesheet" />
        <!-- <script type="text/javascript" src="js/echarts.min.js"></script> -->
        <script type="text/javascript" src="js/newEcharts.min.js"></script>
        <script type="text/javascript" src="js/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php require('top.php');?>
<div id="body1">
  <div style="padding:10px;"> </div>
  
  <table width="100%" border="0" cellspacing="20" cellpadding="0" >
    <tr>
      <td>
      <form action="?a=do" method="post" id="form1" name="form1" style="display:inline">
      <table width="520" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td width="520" class="T150">
         
          <img src="logos/<?=$id?>.png" width="100" hspace="20" align="left" class="Img_m" />
              <?=$rs["name"]?>
            /
            <?=strtoupper($rs["symbol"])?><br />
<span id="ttime"><?=NDF_time()?></span>
            <br />
            <span class="US" id="tprice">$
              <?=$price?>
              </span>
            <span id="tchange"><?=rg($change)?></span> <br />
QTY：
            <input name="qty" type="text" id="qty" size="5" placeholder="0" onkeyup="gl('price')" />
            US:
            <input name="price" type="text" id="price" size="5" placeholder="0" onkeyup="gl('qty')" />
            <input name="id" type="hidden" id="id" value="<?=$id?>" />
            <input type="submit" name="button" id="button" value="Buy" /> 
            <a href="account.php">[Recharge]</a></td>
        </tr>
        <tr>
          <td></td>
        </tr>
      </table>
      </form>
      <table width="100%" border="0" cellspacing="10" cellpadding="0">
        <tr>
          <td width="50%"><div id="container" style="width:100%;height:500px;"></div></td>
          <td align="center"><div id="container2" style="width:100%;height:500px;"></div></td>
        </tr>
      </table></td>
    </tr>
  </table>
  
</div>
<script type="text/javascript">
var dom = document.getElementById("container");
var myChart = echarts.init(dom);
var app = {};
var option;
var dom2 = document.getElementById("container2");
var myChart2 = echarts.init(dom2);
var app2 = {};
var option2;
 // 数据意义：开盘(open)，收盘(close)，最低(lowest)，最高(highest)he
/*var rawData= [数据参考：
            ['2015/1/31',10, 20, 30,40],
            ['2015/2/31',40, 35, 30, 50]
        ];*/
var rawData= [<?=jspic($ps["prices"])?>];
var aiData= [<?=aik($ps["prices"])?>];
function calculateMA(dayCount, data) {
    var result = [];
    for (var i = 0, len = data.length; i < len; i++) {
        if (i < dayCount) {
            result.push('-');
            continue;
        }
        var sum = 0;
        for (var j = 0; j < dayCount; j++) {
            sum += data[i - j][1];
        }
        result.push(sum / dayCount);
    }
    return result;
}
var dates = rawData.map(function (item) {
    return item[0];
});
var data = rawData.map(function (item) {
    return [+item[1], +item[2], +item[3], +item[4]];
});
option = {
 title: { //标题
  text: '<?=strtoupper($rs["symbol"])?> Real trend chart',
  left:60,top:0,
  },
grid: { //直角坐标系
  show:true,
  left: '50px', //grid组件离容器左侧的距离
  right: '5',
  bottom: '15%',
  top:'40px',
  backgroundColor:'#fff'//k线图标背景色
  },
    xAxis: {
	 type: 'category',
        data:dates,
		axisLine: { lineStyle: { color: '#8392A5' } }
    },
    yAxis: { scale: true, //坐标刻度不强制包含零刻度
  splitArea: {
   show: true //显示分割区域
  }},
	  dataZoom: [{//下方滑块
        textStyle: {
            color: '#8392A5'
        },
		start: 25,//数据窗口范围的起始百分比
   		end: 100,
        /*handleIcon: 'path://M10.7,11.9v-1.3H9.3v1.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4v1.3h1.3v-1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',*/
        dataBackground: {
            areaStyle: {
                color: '#8392A5'
            },
            lineStyle: {
                opacity: 0.8,
                color: '#8392A5'
            }
        },
        brushSelect: true
    }, {
        type: 'inside',
	 	
    }],
	 /*鼠标滑过显示十字
	 tooltip: {
        trigger: 'axis',
        axisPointer: {
            animation: false,
            type: 'cross',
            lineStyle: {
                color: '#376df4',
                width: 2,
                opacity: 1
            }
        }
    },*/
    series: [{
        type: 'k',
        data: data,
		
		 itemStyle: {
                color: '#eb5454',
                color0: '#00a800',
                borderColor: '#eb5454',
                borderColor0: '#00a800'
            }
    },
	{
            name: 'Line',
            type: 'line',
            data: calculateMA(2, rawData),
            smooth: true,
            showSymbol: false,
            lineStyle: {
			color: '#5c7bd9',
                width: 1
            }
        }
	]
};

if (option && typeof option === 'object') {
    myChart.setOption(option);
}

//------以下为右侧AI预测K线程序----
var dates2 = aiData.map(function (item) {
    return item[0];
});
var data2 = aiData.map(function (item) {
    return [+item[1], +item[2], +item[3], +item[4]];
});
option2 = {
title: { //标题 概率值可以去掉
  text: '<?=strtoupper($rs["symbol"])?> AI forecast chart (PR:<?=PR($ps["prices"])?>%)',
  left:60,
  textStyle:{color:'#357afa'}//标题文字颜色
  },
grid: { //直角坐标系
  show:true,
  left: '50px', //grid组件离容器左侧的距离
  right: '5',
  bottom: '15%',
  top:'40px',
  backgroundColor:'#f2fee1'//k线图标背景色
  },
    xAxis: {
	 type: 'category',
        data:dates2,
		axisLine: { lineStyle: { color: '#8392A5' } }
    },
    yAxis: { scale: true, //坐标刻度不强制包含零刻度
  splitArea: {
   show: true //显示分割区域,false只有横线
  }},
	  dataZoom: [{//下方滑块
        textStyle: {
            color: '#8392A5'
        },
		start:10,//数据窗口范围的起始百分比
   		end: 100,
        /*handleIcon: 'path://M10.7,11.9v-1.3H9.3v1.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4v1.3h1.3v-1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',*/
        dataBackground: {
            areaStyle: {
                color: '#8392A5'
            },
            lineStyle: {
                opacity: 0.8,
                color: '#8392A5'
            }
        },
        brushSelect: true
    }, {
        type: 'inside',
	 	
    }],
	 /*鼠标滑过显示十字
	 tooltip: {
        trigger: 'axis',
        axisPointer: {
            animation: false,
            type: 'cross',
            lineStyle: {
                color: '#376df4',
                width: 2,
                opacity: 1
            }
        }
    },*/
    series: [{
        type: 'k',
        data: data2,
		
		 itemStyle: {
                color: '#eb5454',
                color0: '#47b262',
                borderColor: '#eb5454',
                borderColor0: '#47b262'
            }
    },
	{
            name: 'Line',
            type: 'line',
            data: calculateMA(5, aiData),
            smooth: true,
            showSymbol: false,
            lineStyle: {
			color: '#fe1901',//走势曲线颜色
                width: 1
            }
        }
	]
};
if (option2 && typeof option2 === 'object') {
    myChart2.setOption(option2);
}
//自动更新数据：
var myFunction = function() { 
   htmlobj=$.ajax({url:"ajax.php?id=<?=$id?>",async:true,type:"GET",
   success:function(data){
   var obj = JSON.parse(data);
  $("#tprice").text("$"+obj["price"]);
  $("#tchange").html(rg(obj["change"]));
   }
   });
 
}; 
function myFunction2(){
$("#ttime").text(dateFormat("YYYY-mm-dd HH:MM:SS", Date()));
}
var timeOut = setInterval(myFunction, 15000); 
var timeOut2 = setInterval(myFunction2, 1000); 

function rg(v){//红绿色呈现
if(v<0){
return "<span class='Green'>"+v+"%</span>";
}else{
return "<span class='Red'>+"+v+"%</span>";}
}
function dateFormat(fmt, date) {//时间格式化
    let ret;
	var date = new Date(date);
    const opt = {
        "Y+": date.getFullYear().toString(), // 年
        "m+": (date.getMonth() + 1).toString(),     // 月
        "d+": date.getDate().toString(),            // 日
        "H+": date.getHours().toString(),           // 时
        "M+": date.getMinutes().toString(),         // 分
        "S+": date.getSeconds().toString()          // 秒
        // 有其他格式化字符需求可以继续添加，必须转化成字符串
    };
    for (let k in opt) {
        ret = new RegExp("(" + k + ")").exec(fmt);
        if (ret) {
            fmt = fmt.replace(ret[1], (ret[1].length == 1) ? (opt[k]) : (opt[k].padStart(ret[1].length, "0")))
        };
    };
    return fmt;
}
function gl(str){
document.getElementById(str).value="";
}
</script>
<?php require('bot.php')?>

</body>
</html>