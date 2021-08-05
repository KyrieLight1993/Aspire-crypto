<?php 
/**
 * this is the admin account to manage users in frontend
 * 
 */
require("sql.php");
$a="";
if (isset($_GET['a'])) {
  $a= $_GET["a"];
}

if($user==""){header("location:login.php");exit;}
if($a=="del"){
$userid=$_GET["userid"];
NDFsql("delete from orderlist where user='".$userid."'");
NDFsql("delete from users where user='".$userid."'");
msg("The member has been deleted!");
exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<title>Administrator</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="favicon.png" />
		<link href="styles.css" type="text/css" rel="stylesheet" />
</head>
<body>
<?php require('top.php');?>
<div id="body1">

  
  
  <table width="100%" border="0" cellspacing="20" cellpadding="0" >
    <tr>
      <td height="400" valign="top"><table width="800" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="50"> <form action="?a=recharge" method="post" id="form1" name="form1" style="display:inline">
      User ID:<b><?=$user?></b>
          </form></td>
        </tr>
      </table>
        <table width="100%" border="0" cellpadding="10" cellspacing="1" bgcolor="#E6E6E6" id="paper">
        <tr>
          <td width="5%" align="center" class="Bold">No.</td>
          <td width="11%" class="Bold">User ID</td>
          <td width="11%" class="Bold">Balance</td>
          <td width="9%" class="Bold">&nbsp;</td>
          <td width="10%" class="Bold">&nbsp;</td>
          <td width="15%" class="Bold">&nbsp;</td>
          <td width="12%" class="Bold">&nbsp;</td>
          <td width="9%" align="center" class="Bold">Manage</td>
        </tr>
        <?php
		$n=0;
		$db = new SQLite3(DBfile);
$stmt = $db->prepare("SELECT * FROM users WHERE user<>'admin' limit 100");
$result = $stmt->execute();
while ($row = $result->fetchArray())
{
$n++;
?>
        <tr>
          <td align="center" bgcolor="#FFFFFF"><?=$n?>.</td>
          <td bgcolor="#FFFFFF"><?=$row["user"]?></td>
          <td bgcolor="#FFFFFF">$ <?=$row["balance"]?></td>
          <td bgcolor="#FFFFFF" class="US">&nbsp;</td>
          <td bgcolor="#FFFFFF" class="US">&nbsp;</td>
          <td bgcolor="#FFFFFF" class="US">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td align="center" bgcolor="#FFFFFF" class="buttonA"><a href="?a=del&userid=<?=$row["user"]?>" onclick="return confirm('Are you sure?');"><img src="imgs/del.gif" width="14" height="14" class="Img_m" />Delete</a></td>
        </tr>
        <?php }?>
      </table>
      </td>
    </tr>
  </table>
  
</div>
<?php require('bot.php')?>
</body>
</html>