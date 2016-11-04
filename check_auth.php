<?php
session_start();
/**
 @Filename: check_auth.php
 @Version: 0.1
 @Author: Aihara Anwaru
 @Blog: http://rezerolab.blogspot.com 
 @E-mail: anwaru@yandex.com
 
 @deskripsi: file ini berfungsi untuk check 2FA code yang terkirim ke e-mail user
**/
 //@check sudah login atau belum 
 if(@empty($_SESSION['rzlab_uname']) || empty($_SESSION['rzlab_mail'])) 
 {
 die("No direct script allowed!");
 }
 if(@$_SESSION['rzlab_level'] == "admin"){
 echo "<script>window.location='./admin_dashboard.php'</script>";
 }elseif(@$_SESSION['rzlab_level'] == "member"){
 echo "<script>window.location='./member_dashboard.php'</script>";  
 }
 //@setting connection ke databse
$con = new mysqli('localhost', 'root', '', 'demo_login2fa');
if($con->connect_errno > 0) {
	die('Could not connect: ' . connect_error());
}
?>
<html>
<head>
<title>2FA Auth Check</title>
<link href='http://d2f0ora2gkri0g.cloudfront.net/bkasia47535_favicon.ico?v=1474960911' rel='icon' type='image/x-icon'/>
<link href='https://plus.google.com/110358378598572679031/posts' rel='publisher'/>
<link href='https://plus.google.com/110358378598572679031/about' rel='author'/>
<link href='https://plus.google.com/110358378598572679031' rel='me'/>
<meta content='LlbnsWclpd4kvm3UoaTcB1Wi033-vYqxDRylELAz4HQ' name='google-site-verification'/>
<meta content='9B7052F906A8B4A4D601D2C9EB2813C4' name='msvalidate.01'/>
<meta content='Indonesia' name='geo.placename'/>
<meta content='Aihara Anwaru' name='Author'/>
<meta content='general' name='rating'/>
<meta content='id' name='geo.country'/>
<meta content='https://www.facebook.com/tinkere21' property='article:author'/>
<meta content='https://www.facebook.com/rezerolab' property='article:publisher'/>
<meta content='en_US' property='og:locale'/>
<meta content='en_GB' property='og:locale:alternate'/>
<meta content='id_ID' property='og:locale:alternate'/><style type="text/css">
body{
  background:#000;
  color:#00ff00;
  border-style: dashed;
}
h1{
	text-align: center;
}
#login-form{
  text-align: center;
}
input{
	border: 1;
	border-color: #df0000;
	background: #000;
	color: #00ff00; 
  border-style: dashed;
}
#footer{
  text-align: center;
  color:#00f;
  text-transform: none;
  text-decoration: none;
}
a{
  color:#fff;
  text-transform: none;
  text-decoration: none;
}
a:hover{
  color:#00ff00;
  text-transform: none;
  text-decoration: none;
}
#alert{
  text-align: center;
  color:#df0000;
}
#info{
  text-align: center;
  color:#00ff00;
}
#flag{
  text-align: center;
  color:#00f
}
</style>
</head>
<body>
<h1><a href="http://rezerolab.blogspot.com">Re Zero Labs</a> 2FA Auth Check</h1>
<div id="info">
<?php
if(@$_GET['st'] == "info"){
 echo "<p>Your 2FA code was send to your mail</p>";
}
?>
<div id="alert">
<?php
if(@$_GET['st'] == "2fa_code_empty"){
 echo "<p>Please input 2FA!</p>";
}elseif(@$_GET['st'] == "2fa_not_match"){
  echo "<p>Your 2FA not Match, Please Request Again <a href='#'>Resend 2FA</a>!</p>";
}elseif(@$_GET['st'] == "account_inactive"){
  echo "<p>Your account not actived yet! contact admin or <a href='./request_code.php?user=test&mail=test@mail.com'>Resend activation code!</a></p>";
}
?>
</div>
</div>
<form id="login-form" action="?do=check&act=2fa&st=true" method="POST" /> 
<input type="text" name="2fa" placeholder="RZLab-123456"/> = <input type="submit" name="go" value="Check" />
</form>
<div id="footer">
<p>Copyright &copy; 2016 <a href="http://rezerolab.blogspot.com">Re Zero Labs</a></p>
</div>
<?php
$do = @$_REQUEST['do'] == "check";
$gate =@$_REQUEST['act'] == "2fa";
$status =@$_REQUEST['st'] == "true";
$fa = @$_POST['2fa'];
$user = @$_SESSION['rzlab_uname'];
if($do){
  if($fa == ''){
    echo "<script>window.location='?st=2fa_code_empty'</script>";
  }else{
  $sqli_query = $con->query("SELECT * FROM tbl_users WHERE username='$user' AND auth_2fa='$fa'");
  $sqli_row=$sqli_query->fetch_array();
  $sqli_count = $sqli_query->num_rows; 
  echo $sqli_count;
  if($sqli_count == "0"){
    echo "<script>window.location='?st=2fa_not_match'</script>";
  }elseif($sqli_count == "1"){
     if($sqli_row['auth_level'] == "admin"){
      @$_SESSION['rzlab_token'] = $sqli_row['auth_token'];
      @$_SESSION['rzlab_level'] = $sqli_row['auth_level'];
      @$_SESSION['rzlab_uid'] = $sqli_row['uid'];
      $query_up = $con->query("UPDATE tbl_users SET auth_2fa='0' WHERE username='$user'") or die(mysqli_error());
      echo "<script>window.location='./admin_dashboard.php?st=login_sukses'</script>"; 
    }elseif($sqli_row['auth_level'] == "member"){
      @$_SESSION['rzlab_token'] = $sqli_row['auth_token'];
      @$_SESSION['rzlab_level'] = $sqli_row['auth_level'];  
      @$_SESSION['rzlab_uid'] = $sqli_row['uid']; 
      $query_up = $con->query("UPDATE tbl_users SET auth_2fa='0' WHERE username='$user'") or die(mysqli_error());
      echo "<script>window.location='./member_dashboard.php?st=login_sukses'</script>";
    }
   }
  }
}
?>
</body>
</html>