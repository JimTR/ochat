<?php
include "inc/functions.php";
 $function = $_POST['function'];
$file = "data/".$_POST['file'];
$msgs = $_POST['msgs'];
$user= $_POST['user'];
$chat_area = $_POST['chatarea'];
$date = date("d-m-y H:i:s");
$state = $_POST['state'];
$nickname ='';
$log = array();
switch($function) {
	case('getState'):
		log_to ("debug.log",print_r($_POST,true));
		if(file_exists($file)){
			$lines = file($file);
			log_to ("state.log","file count for $file = ".count($lines)); 
		}
		else{
			//log_to("new.log","Could not find $file");
			$nickname = "Chat Admin";
			$message = "New Chat started $date";
			$chat_text = "<div class='user'> $nickname</div><div class='msg'>$message</div>";
			log_to($file,$chat_text);
			$lines[]= $chat_text;
		} 
		$log['state'] = count($lines); 
		break;	
		case('update'):
			if(file_exists($file)){
				$lines = file($file);
				$state = count($lines); // check if someone else has posted 
			}
			if($state == $msgs){
				$log['state'] = $state;
				$log['text'] = false;
				$log['user'] = $user;
				$log['msgs'] = $msgs;
			}
			elseif($msgs <  $state){
				log_to("update.log",date("d-m-y h:i:s")."  msgs ($msgs) is smaller than state ($state)");
				log_to ("update.log",print_r($lines,true));
				foreach ($lines as $key => $line){
					// populate
					if( $key+1 > $msgs  ){
						log_to("loop.log", "key = $key state = $state msgs = $msgs line = $line");
						$text[] = trim($line);
						$msgs ++;
					} 
				}
				$last_line = end($lines); // get the last line
				$end_user = strpos($last_line,"</div>");
				$nick =  trim(strip_tags(substr($last_line,0,$end_user)));
				$log['user'] = $nick;
				$log['state'] = $state;
				$log['text'] = $text;
				$log['chatArea'] = $chat_area;
				$log['msgs'] = count($lines); // update the number of messages
			}	
			else{
				log_to("update.log",date("d-m-y H:i:s")." there is a difference state ($state) msgs ($msgs)");
				$text= array();
				$log['state'] = $state;// + count($lines) - $state;
				$last_line = end($lines); // get the last line
				$end_user = strpos($last_line,"</div>");
				$nick =  trim(strip_tags(substr($last_line,0,$end_user)));
				$log['user'] = $nick;
				foreach ($lines as $line_num => $line){
					if($line_num >= $state){
						$text[] = trim($line);
					}
				}
				$log['text'] = $text; 
				$log['chatArea'] = $chat_area;
				$log['state'] = $state;
				$log['msgs'] = $state;
			}
			break;
			case('send'):
				$log['chatArea'] = $chat_area;
				$nickname = htmlentities(strip_tags($_POST['nickname']));
				$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
				$message = strip_tags($_POST['message'],"<span>");
				if(($message) != "\n"){
					if(preg_match($reg_exUrl, $message, $url)) {
						$message = preg_replace($reg_exUrl, '<a href="'.$url[0].'" target="_blank">'.$url[0].'</a>', $message);
						log_to ("url.log",print_r($url,true));
					}
					else{
						$message= trim($message);
					}
					$message = str_replace("\n",  "", $message) .
					$chat_text = "<div class='user' title='$date'> $nickname</div><div class='msg'>$message</div>";
					$chat_text = preg_replace('/[\x00-\x1F\x7F]/u', '', $chat_text)."\n";
					file_put_contents($file,$chat_text,FILE_APPEND);
					$log['user'] = $nickname;
				}
				break;
}
echo json_encode($log);