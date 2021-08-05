<?php
//to deal wil big imange (not used)
$a="";
if (isset($_GET['a'])) {
  $a= $_GET["a"];
}
$type = 'gif';
$width=800;
$height=400;
$w=$width;
$step=1;$z=0;
header("Content-type: image/".$type);
$im = @imagecreate($width,$height);
$bk=250;//背景色
$backColor = imagecolorallocate($im,$bk,$bk,$bk);//第一次调用即为画布设置背景颜色
imageantialias($im,true);//抗锯齿
$linecolor = imagecolorallocate($im, 20, 130, 220);//线颜色
$arraylist=explode(',',$arr); 
$c=count($arraylist);
if(($w/$c)>1){$step=ceil(($w-$c)/$c);}
for($i=0;$i<($c-2);$i++)
{
$z++;
imageline($im, $i*$step,$arraylist[$i], ($i+1)*$step,$arraylist[$i+1], $linecolor);//画线段
if($z>0){zhu($im,$i*$step,$arraylist[$i],2,$arraylist[$i+1]);$z=0;}
}




$ImageFun='Image'.$type;//用字符串做函数名 字符串()
$ImageFun($im);
imagedestroy($im);

function zhu($im,$x,$y,$h,$w){//柱线
$red = imagecolorallocate($im,255,0,0);//创建一个颜色
$green= imagecolorallocate($im,0,255,0);//创建一个颜色
$w=$w-$y;
if($w<0){$color=$red;}else{$color=$green;$w=abs($w);}
//$w=floor($w/2);
imagefilledrectangle($im,$x,$y+$w,$x+$h,$y-$w,$color);//填充的矩形
imageline($im, $x+floor($h/2),$y+$w+10,$x+floor($h/2),$y-$w-5, $color);//画线段
}
?>