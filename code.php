<?PHP 
/**
 * code will return a graph of virification code, it uses PHP GD library to draw graphics 
 */
session_start(); 
//session_register('SafeCode');
//html_entity_decode()
$type = 'gif';
$width= 64;
$height= 24;
header("Content-type: image/".$type);
srand((double)microtime()*1000000);// random generator
$randval = randStr(4,"NUMBER");//random code for verification
if($type!='gif' && function_exists('imagecreatetruecolor')){ 
     $im = @imagecreatetruecolor($width,$height);// all those fucntions are from PHP GD library to deal with graphics
}else{ 
     $im = @imagecreate($width,$height);
}
     $r = Array(225,211,255,223);
     $g = Array(225,236,237,215);
     $b = Array(225,240,216,225);

     $key = rand(0,3);
  
     $backColor = imagecolorallocate($im,$r[$key],$g[$key],$b[$key]);//randomly set up back ground color
	 //@imagesetthickness($im, 1);//
     //$borderColor = imagecolorallocate($im, 0, 0, 0);//
     $pointColor = imagecolorallocate($im, rand(160,255), rand(100,170), rand(100,255));//set up random points color
     @imageantialias($im,true);//anti-aliasing
     @imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);//draw a rectangle to the correct place
     @imagerectangle($im, 0, 0, $width-1, $height-1, $borderColor); //set up frame of rectangle
	 
for ($i = 0; $i < 3; $i++) {//make some interference lines in the verification code
    $linecolor = imagecolorallocate($im, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
    imageline($im, mt_rand(1, 59), mt_rand(1, 23), mt_rand(1, 59), mt_rand(1, 23), $linecolor);
}
     $stringColor = imagecolorallocate($im,2,18,rand(1,200));

     for($i=0;$i<=50;$i++){ //deploy interference dots color
           $pointX = rand(2,$width-2);
           $pointY = rand(2,$height-2);
           @imagesetpixel($im, $pointX, $pointY, $pointColor);
		   //@imagesetpixel($im, $pointX+1, $pointY, $pointColor);
     }
     //@imagestring($im,5, rand(5,15), rand(1,8), $randval, $stringColor);//make words
	 @imagettftext($im, 18, rand(-3,8), rand(1,10),22,  $stringColor,realpath("actionj.ttf"), $randval);//make string with tff fonts (write text into an image)
     $ImageFun='Image'.$type;
     $ImageFun($im);
	 //@imagegif($im);
     @imagedestroy($im);
     $_SESSION['SafeCode'] = $randval;
//generate random string there
function randStr($len=6,$format='ALL') { 
           switch($format) { 
                 case 'ALL':
                 $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'; break;
                 case 'CHAR':
                 $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'; break;
                 case 'NUMBER':
                 $chars='0123456789'; break;
                 default :
                 $chars='abcdefghijklmnopqrstuvwxyz0123456789'; 
                 break;
           }
     $string="";
     while(strlen($string)<$len)
     $string.=substr($chars,(mt_rand()%strlen($chars)),1);
     return $string;
}
?> 