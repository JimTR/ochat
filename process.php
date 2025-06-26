<?php
include "inc/functions.php";
    $function = $_POST['function'];
   $file = "data/".$_POST['file'];
   $msgs = $_POST['msgs'];
   $user= $_POST['user'];
   $chat_area = $_POST['chatarea'];
   $date = date("d-m-y H:i:s");
   $nickname ='';
   //if($msgs > 0 ) {$msgs = $msgs/2;}
   //log_to ("debug.log",print_r($_POST,true));
    $log = array();
    //$log['chatArea'] = $chat_area;
    
    switch($function) {
    
    	 case('getState'):
        	 if(file_exists($file)){
               $lines = file($file);
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
    	 // $msgs = $_POST['msg'];
    	 // log_to("update.log",print_r($_POST,true)."data:-  file = $file msgs = $msgs state = $state" );
	   	//$state = $_POST['state'];
		
    	  
        	if(file_exists($file)){
        	   $lines = file($file);
        	 }
        	 $state =  count($lines);
		//log_to("update.log",print_r($_POST,true)."data:-  file = $file msgs = $msgs state = $state" );
        	 if($state == $msgs){
        		 $log['state'] = $state;
        		 $log['text'] = false;
			 $log['user'] = $user;
        		 
        		 }
        	  	elseif($msgs <  $state){
					log_to("update.log",date("h:i:s")."  msgs ($msgs) is smaller than state ($state)");
					foreach ($lines as $line){
						// populate
						$text[] = trim($line);
						//$text[] =  $line = str_replace("\n", "", $line);
					}
					$last_line = end($lines); // get the last line
                               		 $end_user = strpos($last_line,"</div>");
                                	$nick =  trim(strip_tags(substr($last_line,0,$end_user)));
                                	//log_to ("update.log",strip_tags(print_r($last_line,true))." position = $end_user nick =$nick");
                                	$log['user'] = $nick;

					 $log['state'] = $state;
					 $log['text'] = $text;
					 $log['chatArea'] = $chat_area;
					 //if(!empty($nickname)){
					//$log['user'] = $nickname;//}

				}	
        	 
        		 else{
				 log_to("update.log",date("d-m-y H:i:s")." there are new messages state ($state) msgs ($msgs)");
        			 $text= array();
        			 $log['state'] = $state;// + count($lines) - $state;
				 //if(!empty($nickname)){
				//$log['user'] = $nickname;//}
				$last_line = end($lines); // get the last line
				$end_user = strpos($last_line,"</div>");
				$nick =  trim(strip_tags(substr($last_line,0,$end_user)));
				//log_to ("update.log",strip_tags(print_r($last_line,true))." position = $end_user nick =$nick");
				$log['user'] = $nick;
        			 foreach ($lines as $line_num => $line)
                       {
        				   if($line_num >= $state){
							   $text[] = trim($line);
                         //$text[] =  $line = str_replace("\n", "", $line);
        				   }
         
                        }
        			 $log['text'] = $text; 
				$log['chatArea'] = $chat_area;
        		 }
        	  //$log['text'] = $lines; 
             break;
    	 
    	 case('send'):
    	  //log_to("post.log",print_r($_POST,true));
		$log['chatArea'] = $chat_area;
		  $nickname = htmlentities(strip_tags($_POST['nickname']));
			 $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
			 //$message = htmlentities(strip_tags($_POST['message'],"<span>"));
			$message = strip_tags($_POST['message'],"<span>");
		 if(($message) != "\n"){
        	
			 if(preg_match($reg_exUrl, $message, $url)) {
				// log_to("url.log", "url found $message");
       			$message = preg_replace($reg_exUrl, '<a href="'.$url[0].'" target="_blank">'.$url[0].'</a>', $message);
			log_to ("url.log",print_r($url,true));
			//$message = str_replace('href=\"','href="',$message);
				//log_to("url.log","screwed message $message");
				}
			else{
			//$message = trim(addslashes($message));
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

?>
