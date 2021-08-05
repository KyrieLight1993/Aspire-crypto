<?php
/**
 * this for drawing the sparkline in prices table
 * it uses PHP_GD to generate dynamic grapics
 * we was using d3 + svg to process sparkline 
 * but since this we need PHP_GD to make the login verfication gif
 * we just use it to generate sparklines too
 * 
 */
$arr="";
if (isset($_GET['arr'])) {
  $arr= $_GET["arr"];
}

$type = 'gif';
$width= 100;
$height= 32;
header("Content-type: image/".$type);
$im = @imagecreate($width,$height);
$bk=255;//背景色
$backColor = imagecolorallocate($im,$bk,$bk,$bk);//第一次调用即为画布设置背景颜色
imageantialias($im,true);//抗锯齿 (anti_aliasing)
$linecolor = imagecolorallocate($im, 20, 130, 220);//线颜色
$arraylist=explode(',',$arr); 
for($i=0;$i<(count($arraylist)-2);$i++)
{
imageline($im, $i,$arraylist[$i], $i+1,$arraylist[$i+1], $linecolor);//画线段
}
$ImageFun='Image'.$type;//用字符串做函数名 字符串()
$ImageFun($im);
imagedestroy($im);
?>