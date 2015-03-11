<?php
error_reporting(0);
session_start();
header("ContentType:text/html");
define("SCRIPT", $_SERVER['SCRIPT_NAME']);
define("CHAT_NOTE", "./chat.txt");
define("ONLINE_LIST", "./online.txt");
define("REF_TIME",2);
define("CHAT_NAME", "Illni Exchange Chat Room");
define("AD_MSG", "Welcome Back");

if(!$_SESSION["currentuser"])
{
   header("Location: index.html");
}
else {
	include "dbconfig.php";
	$username=$_SESSION["currentuser"];
}

   
if (isset($_GET['action']) && !empty($_GET['action'])) {
	$action = $_GET['action'];
	
	if (isset($_GET['find'])) {
		$itemlook = $_GET['find'];
	}else{
		$itemlook = -1;
	}
}

//save_online($username, get_client_ip());


//start chatting
if ($action=="chat")
{
include "navigation.php";

echo "	<head>
		<title>[ ".CHAT_NAME." ]</title>
      	</head>
      
      	<center><body bgcolor=#F5F5F5 style='font-size:12px;'>
      	<div style='border:1px solid #999966; width:802px;height:450'>
      	<iframe  src='".SCRIPT."?action=show' name=show_win width=800 height=450 scrolling=auto frameborder=0></iframe>  	
      	</div><br>
	<marquee width=70% scrollamount=2> ".AD_MSG." </marquee>&nbsp;&nbsp; 
	<iframe src='".SCRIPT."?action=say' name=say_win width=800 height=60 scrolling=no frameborder=0></iframe>
	<iframe id='I2' src='".SCRIPT."?action=showitem&find=".$itemlook."' name=showitem_win width=100% height=100 scrolling=auto frameborder=1></iframe>
     ";
}

//action=say
if ($action=="say")
{
echo "<head><title>[ ".CHAT_NAME." ]</title></head><center><body bgcolor=#F5F4F6 style='font-size:12px;'>

<form action=".SCRIPT."?action=save method=post name=chat onSubmit='return check()'>
[".$_SESSION['currentuser']."]says:<input type=text size=80 maxlength=500 name=chatmsg style=' background-color:#99CC99; width:550px; height:22px; border:1px solid:#000000'>
<select name=usercolor>
<OPTION selected style='COLOR: #000000' value='000000'>Default colour(Black)</OPTION>
<option style='COLOR: #ff9900' value='#FF9900'>Yellow</option>
<option style='COLOR: #ff0000' value='#FF0000'>Red</option> 
<option style='COLOR: #000099' value='#000099'>Blue</option>

</select>
<input type=submit value='say it!' style='background-color:#F5F5F5'> 
<a href=".SCRIPT."?action=logoff title=Exit Room target=_top>Quit</a>
</form>
<script>function check(){if(document.chat.chatmsg.value==''){;alert('Please input message!');return false;}return true;}</script>
";
}

if ($action=="showitem")
{

echo "<body style='font-size:12px' onload='scrollit()'>";

$my_sql1="SELECT * FROM itemSell WHERE `itemId`='$itemlook'";

$myitem1= mysql_query($my_sql1);
	
	echo "<table width=100% border=1px>";
	 if(mysql_num_rows($myitem1)>0){
	  print("
                    <tr>
                    <th> ItemId </th> <th> Post Date </th> <th> Name </th> <th> Category </th>
                    <th> Description </th> <th> For Exchange</th>
                    </tr>
                    ");
		while($data1=mysql_fetch_assoc($myitem1))
                    {
                      print("<tr>");
                        print("<th>".$data1["itemID"]."</th>");
                        print("<th>".$data1["date"]."</th>");
                        print("<th>".$data1["item_name"]."</th>");
                        print("<th>".$data1["category"]."</th>");
                        print("<th>".$data1["description"]."</th>");
                        print("<th>".$data1["category_want"]."</th>");
			print("</tr>");  
                    }

	}else
	{
		echo "No such item";
	}

}

//save chat
if ($action=="save")
{
if ($_POST['chatmsg']!="") {
save_chat($_POST['chatmsg'], $_SESSION['currentuser'], $_POST['usercolor']);
}
header("location:".SCRIPT."?action=say");
}


//show chat
if ($action=="show")
{
echo "<body style='font-size:12px' onload='scrollit()'>";
echo "<META HTTP-EQUIV=REFRESH CONTENT='".REF_TIME.";URL=".SCRIPT."?action=show'>";
if (file_exists(CHAT_NOTE)) {
$chat_msg = @file_get_contents(CHAT_NOTE);

echo $chat_msg;

} else {
echo "No body is saying";
}
}

//exit
if ($action=="logoff")
{
header("location:account.php");
}


//save chat
function save_chat($msg, $user, $color)
{
if (!$fp = fopen(CHAT_NOTE, "a ")) {
die('Error.');
}
$msg = htmlspecialchars($msg);
$msg = preg_replace('/([http|ftp:\/\/])*([a-zA-]) \.([a-zA-Z0-9_-]) \.([a-zA-Z0-9_-]) (a-zA-Z0-9_)*/', '<a href=\\0 target=_blank>\\0</a>', $msg);
$msg = preg_replace('/([a-zA-Z0-9_\.]) @([a-zA-Z0-9-]) \.([a-zA-Z0-9-]{2,4}) /', '<a href=mailto:\\0>\\0</a>', $msg);
$msg = date('H:i:s')." [".$user."]says: <font color='".$color."'>".$msg."</font><br>\r\n";

$temp=explode("**",$msg);
//$msg= "<a href=\"javascript:window.top.location='chatroom.php?action=chat'\">www</a>";

if(count($temp)==3){
   $temp1=$temp[0];
   $temp2=$temp[1];
   $temp3=$temp[2];
   $msg= $temp1."<a href=\"javascript:window.top.location='chatroom.php?action=chat&find=".$temp2."'\" target='I2'>".$temp2."</a>".$temp3;
//	
//onclick="parent.document.I2.location

}

if (!fwrite($fp, $msg)) {
die('Error in writing history.');
}
fclose($fp);
}

?>