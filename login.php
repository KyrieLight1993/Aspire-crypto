<?php require("sql.php");
$a="";
if (isset($_GET['a'])) {
  $a= $_GET["a"];
}
if($a=="reg"){//register
$user=$_POST["user"];
$psw1=$_POST["psw1"];
$psw2=$_POST["psw2"];
if($user<>"" and $psw1<>"" and $psw2<>"" and $psw1==$psw2){
if(ctype_alnum($user)==false or strlen($user)>30)//user name should be 30 letters+ numbers
{
msg("Sorry!Usernames can only be combined with letters and numbers below 30 characters!");$user="";exit;
}
	if(NDF_dq("select count(user) as total from users where user='".$user."'","total")==0){
	NDFsql("insert into users (user,psw)values('".$user."','".MD16($psw1)."')");//encrypt user password with MD5 encryption before save to our database
	$_SESSION["user"]=$user;
	echo "<script language=javascript>alert('Success to register!');</script>";
	echo "<meta http-equiv=refresh content=0;URL=account.php>";
	exit;
	}else{
	msg("Sorry!This userid has existed!");$user="";exit;
	}
}else{
msg("Sorry! Fills in not completely!");$user="";exit;
}

}

if($a=="login"){//log in
$user=$_POST["user"];
$psw=$_POST["psw"];
$code=$_POST["code"];
if($user=="" or $psw=="" and $code==""){
msg("Please input complete!");
$user="";
}
if($code<>$_SESSION["SafeCode"]){
msg("Verification code error!");
$user="";
}
	if(NDF_dq("select psw from users where user='".$user."'","psw")==MD16($psw)){
	$_SESSION["user"]=$user;
	echo "<script language=javascript>alert('Successful login!');</script>";
	echo "<meta http-equiv=refresh content=0;URL=load.php?url=prices>";
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<title>Sign in or Register</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="favicon.png" />
		<link href="styles.css" type="text/css" rel="stylesheet" />
   
</head>
<body>
<?php require('top.php');?>
<div id="body1">

  
  <table width="100%" border="0" cellspacing="20" cellpadding="0" >
    <tr>
      <td><table width="100%" border="0" cellpadding="0" cellspacing="10">
<tr>
        <td height="24" align="left" scope="col"><a href="./prices.php"><img src="imgs/X3_homebreadcrumb_111406.gif" alt="return the homepage" width="11" height="9" border="0" /> Home</a> &nbsp;<img src="imgs/jiantour.gif" width="6" height="7" />  My account</td>
    </tr>
      <tr>
        <td align="left" valign="top"  scope="col"><table width="100%" border="0" cellpadding="0" cellspacing="10">
            <tr>
              <td width="50%" valign="top"><form action="?a=login" method="post" id="form1" name="form1">
                  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="5">
                    <tr>
                      <td bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="5" cellspacing="0">
                      <tr>
                            <td height="30" align="center" class="Bai Bold"><img src="imgs/login.png" width="252" height="51" /></td>
                        </tr>
                        </table>
                  <table width="100%" border="0" cellspacing="10" cellpadding="0">
                            
                            <tr>
                              <td width="28%" align="right" class="Fv">Username:</td>
                              <td colspan="2"><label>
                                <input name="user" type="text" id="user" size="30" maxlength="50" />
                              </label></td>
                            </tr>
                            <tr>
                              <td align="right" class="Fv">Password:</td>
                              <td colspan="2" class="Fv"><input name="psw" type="password" id="psw" size="30" maxlength="50" /></td>
                            </tr>
                            <tr>
                              <td align="right" class="Fv">Verification Code:</td>
                              <td width="7%" class="Fv"><input name="code" type="text" id="code" size="4" maxlength="4" />                              </td>
                              <td width="65%" class="Fv"><img src="code.php" alt="Power By KernalLumina Corporation" width="64" height="24" hspace="3" vspace="4" /></td>
                            </tr>
                            <tr>
                              <td align="right" class="Fv" >&nbsp;</td>
                              <td colspan="2" class="Fv">&nbsp;</td>
                            </tr>
                            <tr>
                              <td height="44" align="right"><input name="url" type="hidden" id="url" value="<?=@$_SERVER["HTTP_REFERER"]?>" /></td>
                              <td colspan="2" valign="bottom">
                              <input type="submit" name="button" id="button" value="Sign In" />
                            </td>
                            </tr>
                            <tr>
                              <td>&nbsp;</td>
                              <td colspan="2"></td>
                            </tr>
                      </table></td>
                    </tr>
                  </table>
              </form></td>
            <td valign="top"><form action="?a=reg" method="post" id="form2" name="form2">
                  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="5">
                    <tr>
                      <td bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="5" cellspacing="0">
                          <tr>
                            <td align="center"><span class="Bai Bold"><img src="imgs/register.png" width="240" height="51" /></span></td>
                        </tr>
                        </table>
                  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="10">
            <tr>
                              <td width="171" align="right" class="Fv">Username:</td>
                    <td width="423" class="Fv">&nbsp;
          <input name="user"  type="text" id="user"  size="30" maxlength="30" />
                                  <span class="STYLE13" id="cks">*</span></td>
                        </tr>
                            <tr>
                              <td align="right" class="Fv">Password:</td>
                              <td class="Fv">&nbsp;
                                  <input name="psw1" type="password" id="psw1" size="30" maxlength="30" />
                                  <span class="STYLE13">*</span></td>
                            </tr>
                            <tr>
                              <td align="right" class="Fv">Repeat Password:</td>
                              <td class="Fv">&nbsp;
                                  <input name="psw2" type="password" id="psw2" size="30" maxlength="30" />
                                  <span class="STYLE13">*</span></td>
                            </tr>
                            <tr>
                              <td align="right" class="Fv">&nbsp;</td>
                              <td rowspan="2" class="F10">
                            &nbsp;</td>
                            </tr>
                            <tr>
                              <td>&nbsp;</td>
                              </tr>
                            <tr>
                              <td height="47"><input name="url" type="hidden" id="url" value="<?php echo @$_SERVER['HTTP_REFERER']; ?>" /></td>
                              <td valign="top"><input type="submit" name="button2" id="button2" value="Submit" /></td>
                            </tr>
                        </table></td>
                    </tr>
                  </table>
<script>   


function G(o){
return document.getElementById(o);
}
</script>
              </form></td>
            </tr>
          </table>
            <p>&nbsp;</p></td>
    </tr>
    </table></td>
    </tr>
  </table>
  
</div>

<?php require('bot.php')?>
</body>
</html>