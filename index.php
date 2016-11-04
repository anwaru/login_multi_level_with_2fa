<?php
session_start();
/**
 @Filename: index.php
 @Version: 0.1
 @Author: Aihara Anwaru
 @Blog: http://rezerolab.blogspot.com 
 @E-mail: anwaru@yandex.com
 
 @deskripsi: login multi leve dengan 2fa meningkatkan keamanana pada form login!
**/
 //@library include
 include "./library_generate_2fa.php";
 //@check sudah login atau belum
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
<title>Login witf 2FA auth</title>
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
<meta content='id_ID' property='og:locale:alternate'/>
<style type="text/css">
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
#success{
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
<h1><a href="http://rezerolab.blogspot.com">Re Zero Labs</a> Login Multi Level with 2FA</h1>
<div id="alert">
<?php
if(@$_GET['st'] == "empty_user_pass"){
 echo "<p>Your Username/Password is empty</p>";
}elseif(@$_GET['st'] == "unregister"){
  echo "<p>Username/Password not match! Try again!</p>";
}elseif(@$_GET['st'] == "account_inactive"){
  echo "<p>Your account not actived yet! contact admin or <a href='./request_code.php?user=test&mail=test@mail.com'>Resend activation code!</a></p>";
}
?>
</div>
<form id="login-form" action="?do=login&act=login&st=true" method="POST" /> 
<input type="text" name="user" /> : <input type="password" name="pass" /> = <input type="submit" name="go" value="Login" />
</form>
<div id="footer">
<p>Copyright &copy; 2016 <a href="http://rezerolab.blogspot.com">Re Zero Labs</a></p>
</div>
<?php
$do = @$_REQUEST['do'] == "login";
$gate =@$_REQUEST['act'] == "login";
$status =@$_REQUEST['st'] == "true";
$user = @$_POST['user'];
$pass = @$_POST['pass'];
if($do){
  if($user == '' || $pass == ''){
    echo "<script>window.location='?st=empty_user_pass'</script>";
  }else{
  $sqli_query = $con->query("SELECT * FROM tbl_users WHERE username='$user' and password=md5('$pass')");
  $sqli_row=$sqli_query->fetch_array();
  $sqli_count = $sqli_query->num_rows;
  if($sqli_count == "0"){
    echo "<script>window.location='?st=unregister'</script>";
  }elseif($sqli_count == "1"){
   if($sqli_row['status_register'] == "0"){
    echo "<script>window.location='?st=account_inactive'</script>"; 
   }elseif($sqli_row['status_register'] == "1"){
     if($sqli_row['auth_level'] == "admin"){ 
      $string = $sqli_row['password'];
      $key = sha1(sha1(md5(time())));
      $user = $sqli_row['username'];
      $auth_token = hash_hmac('sha256', $string, $key);
      $auth_2fa = "RZLab-".random2fa();
      @$_SESSION['rzlab_uname'] = $sqli_row['username'];
      @$_SESSION['rzlab_mail'] = $sqli_row['mail'];
      $query_up = $con->query("UPDATE tbl_users SET auth_token='$auth_token', auth_2fa='$auth_2fa' WHERE username='$user'") or die(mysqli_error());
      echo "<script>window.location='./check_auth.php?st=info'</script>"; 
    }elseif($sqli_row['auth_level'] == "member"){
      $string = $sqli_row['password'];
      $key = sha1(sha1(md5(time())));
      $user = $sqli_row['username'];
      $auth_token = hash_hmac('sha256', $string, $key);
      $auth_2fa = "RZLab-".random2fa();
      @$_SESSION['rzlab_uname'] = $sqli_row['username'];
      @$_SESSION['rzlab_mail'] = $sqli_row['mail'];
      $query_up = $con->query("UPDATE tbl_users SET auth_token='$auth_token', auth_2fa='$auth_2fa' WHERE username='$user'") or die(mysqli_error());
      echo "<script>window.location='./check_auth.php?st=info'</script>";
    }
   }
  }
  }
}
?>
</body>
</html>