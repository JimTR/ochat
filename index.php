<?php 
$file = $_GET['file'];
$user = "<span>{$_GET['user']}</span>";
//echo "$user</br>";
$e ='';
$emoji_root = "&#1285";
for ($x = 12; $x <= 91; $x++) {
  $e.= "<option class='emoji' value='$emoji_root$x;'>$emoji_root$x;</option>";
}
$c_emoji = explode(PHP_EOL,trim(file_get_contents("data/emoji.txt")));
foreach($c_emoji as $a){
$e.="<option class='emoji' value='$a'>$a</option>";
}
$html = file_get_contents("chat-box.html");
$html = str_replace("#file#",$file,$html);
$html = str_replace("#user#",$user,$html);
$html = str_replace("#emojis#",$e,$html);
//eval("\$html = \"$html\";");
//exit;
echo $html;
