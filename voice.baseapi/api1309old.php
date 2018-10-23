<?php

// Main
require_once("Rest.inc.php");
//php.ini
//my.cnf
// server time
class API extends REST
{
    public $data = "";
    const DB_SERVER = "localhost";
    const DB_USER = "root";
    const DB_PASSWORD = "123456cH+";
    const DB = "voice";
    private $db = NULL;
    public function __construct()
    {
        parent::__construct(); // Init parent contructor
        $this->dbConnect(); // Initiate Database connection
    }
    /*
     *  Database connection
     */
    private function dbConnect()
    {
        $this->db = mysql_connect(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD);
        if ($this->db)
            mysql_select_db(self::DB, $this->db);
        mysql_query("SET NAMES UTF8");
    }
    /*
     * Public method for access api.
     * This method dynmically call the method based on the query string
     *
     */
    public function processApi()
    {
        $func = strtolower(trim(str_replace("/", "", $_REQUEST['rquest'])));
        if ((int) method_exists($this, $func) > 0)
            $this->$func();
        else
            $this->response('', 404); // If the method not exist with in this class, response would be "Page not found".
    }
    /*
    /*    I love log
    */
    public function log_table(&$function_called, &$user_id_log, &$message)
    {
        $query = mysql_query("INSERT INTO `voice`.`log_table` 
                                    (`function_called`, `userid`, `agent`, `time_stamp`,`message`) 
                                    VALUES
                                    ('$function_called', '$user_id_log', 'Main Server', replace(replace(replace(replace(sysdate(3),'-',''),':',''),' ',''),'.','') , '$message');") or die(mysql_error());
        $query1 = mysql_query("commit; ") or die(mysql_error());
    }
	
	public function multiRequest($data, $options = array())
		{
		 
		  // array of curl handles
		  $curly = array();
		  // data to be returned
		  $result = array();
		 
		  // multi handle
		  $mh = curl_multi_init();
		 
		  // loop through $data and create curl handles
		  // then add them to the multi-handle
		  foreach ($data as $id => $d) {
		 
			$curly[$id] = curl_init();
		 
			$url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
			curl_setopt($curly[$id], CURLOPT_URL,            $url);
			curl_setopt($curly[$id], CURLOPT_HEADER,         0);
			curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
		 
			// post?
			if (is_array($d)) {
			  if (!empty($d['post'])) {
				curl_setopt($curly[$id], CURLOPT_POST,       1);
				curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
			  }
			}
		 
			// extra options?
			if (!empty($options)) {
			  curl_setopt_array($curly[$id], $options);
			}
		 
			curl_multi_add_handle($mh, $curly[$id]);
		  }
		 
		  // execute the handles
		  $running = null;
		  do {
			curl_multi_exec($mh, $running);
		  } while($running > 0);
		 
		 
		  // get content and remove handles
		  foreach($curly as $id => $c) {
			$result[$id] = curl_multi_getcontent($c);
			curl_multi_remove_handle($mh, $c);
		  }
		 
		  // all done
		  curl_multi_close($mh);
		 
		  return $result;
		}
	
	
	
	
	public function move_file(&$filetomove, &$inputtvid,&$nametomove,&$roletomove,&$distance,&$confidence,&$threshold,&$output)
    {
		$function_called = 'move_file';
								$user_id_log = $inputtvid;
								$message = 'process starts with name: ' . $nametomove .'role : '. $roletomove .'distance : '. $distance ;
								$this->log_table($function_called, $user_id_log, $message);	
								
								
								
								$query2 = mysql_query("SELECT userid as userid FROM voice.voice_register_tbl
														where tvid = '$inputtvid'
														and user_name = '$nametomove'
														and user_role = '$roletomove'") or die(mysql_error());
								$row2 = mysql_fetch_array($query2) or die(mysql_error());
								$userid = $row2['userid'];

								
								if((empty($nametomove))|| (empty($roletomove)) )
								{
									$serverIP3 = "anyone/$inputtvid/recognize/$inputtvid/error/$distance/";
								$old_umask = umask(0);
								if (!is_dir($serverIP3)) {
								mkdir($serverIP3, 0777, true);
								}
								}
								else
								{
								
								$serverIP3 = "anyone/$inputtvid/recognize/$userid/$distance/";
								$old_umask = umask(0);
								if (!is_dir($serverIP3)) {
								mkdir($serverIP3, 0777, true);
								}
								}
								 $t              = microtime(true);
            $micro          = sprintf("%06d", ($t - floor($t)) * 1000000);
            $d              = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
			 $file_name      = basename($_FILES['audiofile']['name']);
            $dttm1          = $d->format("YmdHisu");
            $uploadfile     = $serverIP3 . "_". $dttm1 . ".wav" ;
			
			
			
															 $query3 = mysql_query("INSERT INTO voice.voice_recognition_tbl
(`tvid`,  `user_name`, `response`,  `recognized_on`, `wav_full_path`,`user_role`,`confidence`,`distance`,`threshold`,`userid`) 
VALUES
('$inputtvid',  '$nametomove', '$output' ,
substring(replace(replace(replace(now(),'-',''),':',''),' ',''),1,14) , '$uploadfile','$roletomove','$confidence','$distance','$threshold','$userid');") or die(mysql_error()); 
			
			
								
								//move_uploaded_file( $filetomove , $uploadfile );
								
								 if(rename($filetomove, $uploadfile))
								 {
									  $function_called = 'move_file';
								$user_id_log = $inputtvid;
								$message = 'user success'  ;
								$this->log_table($function_called, $user_id_log, $message);	
								 }
								 else
								 {
									 $function_called = 'move_file';
								$user_id_log = $inputtvid;
								$message = 'user failed'  ;
								$this->log_table($function_called, $user_id_log, $message);	
									 
								 }
								
								
								
								
								
		
	}
	
	
	public function move_file_failed(&$filetomove, &$inputtvid,&$distance)
    {
		
								
								
								$serverIP3 = "anyone/$inputtvid/recognize/$inputtvid/failed/$distance/";
								$old_umask = umask(0);
								if (!is_dir($serverIP3)) {
								mkdir($serverIP3, 0777, true);
								}
								
								 $t              = microtime(true);
            $micro          = sprintf("%06d", ($t - floor($t)) * 1000000);
            $d              = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
			 $file_name      = basename($_FILES['audiofile']['name']);
            $dttm1          = $d->format("YmdHisu");
            $uploadfile     = $serverIP3 . "_". $dttm1 . ".wav" ;
			
			$function_called = 'move_file_failed';
								$user_id_log = $inputtvid;
								$message = 'user registreaion'. $uploadfile  ;
								$this->log_table($function_called, $user_id_log, $message);	
								
								//move_uploaded_file( $filetomove , $uploadfile );
								
								
								
								
								
								 if(rename($filetomove, $uploadfile))
								 {
									  $function_called = 'move_file_failed';
								$user_id_log = $inputtvid;
								$message = 'user success'  ;
								$this->log_table($function_called, $user_id_log, $message);	
								 }
								 else
								 {
									 $function_called = 'move_file_failed';
								$user_id_log = $inputtvid;
								$message = 'user failed'  ;
								$this->log_table($function_called, $user_id_log, $message);	
									 
								 }
								
								
								
								
								
		
	}
		public function move_file_error(&$filetomove, &$inputtvid,&$distance)
    {
		
								
								
								$serverIP3 = "anyone/$inputtvid/recognize/$inputtvid/error/$distance/";
								$old_umask = umask(0);
								if (!is_dir($serverIP3)) {
								mkdir($serverIP3, 0777, true);
								}
								
								 $t              = microtime(true);
            $micro          = sprintf("%06d", ($t - floor($t)) * 1000000);
            $d              = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
			 $file_name      = basename($_FILES['audiofile']['name']);
            $dttm1          = $d->format("YmdHisu");
            $uploadfile     = $serverIP3 . "_". $dttm1 . ".wav" ;
			
			$function_called = 'move_file_error';
								$user_id_log = $inputtvid;
								$message = 'user registreaion'. $uploadfile  ;
								$this->log_table($function_called, $user_id_log, $message);	
								
								//move_uploaded_file( $filetomove , $uploadfile );
								
								 if(rename($filetomove, $uploadfile))
								 {
									  $function_called = 'move_file_error';
								$user_id_log = $inputtvid;
								$message = 'user success'  ;
								$this->log_table($function_called, $user_id_log, $message);	
								 }
								 else
								 {
									 $function_called = 'move_file_error';
								$user_id_log = $inputtvid;
								$message = 'user failed'  ;
								$this->log_table($function_called, $user_id_log, $message);	
									 
								 }
								
								
								
								
								
		
	}
	
	
	public function register_tv(&$inputtvid)
    {
							
						
								
		$sql = mysql_query("SELECT tvid FROM voice.tvsets WHERE tvid = '$inputtvid' LIMIT 1", $this->db);
						
						
						if (mysql_num_rows($sql) == 1)
						{
							
								$function_called = 'register_tv';
								$user_id_log = $inputtvid;
								$message = 'TV already exist';
								$this->log_table($function_called, $user_id_log, $message);
								$success= '1';
								return $success;
								
						}
						else if (mysql_num_rows($sql) == 0)
						{
							$characters1 = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
								$charactersLength1 = strlen($characters1);
								$randomString1 = '';
								for ($i = 0; $i < 10; $i++)
								{
										$randomString1.= $characters1[rand(0, $charactersLength1 - 1) ];
								}

								$randnumber1 = $randomString1;
							
									$query = mysql_query("INSERT INTO voice.tvsets 
													(`tvid`,`user_fullname`,   `registered_on`) 
													VALUES
													('$inputtvid','$randnumber1',  substring(replace(replace(replace(now(),'-',''),':',''),' ',''),1,8) );") or die(mysql_error());
								
								$function_called = 'register_tv';
								$user_id_log = $inputtvid;
								$message = 'TV created';
								$this->log_table($function_called, $user_id_log, $message);
								
								$serverIP4 = "anyone/$inputtvid/";
								$serverIP2 = "anyone/$inputtvid/register";
								$serverIP3 = "anyone/$inputtvid/recognize";
								
								$old_umask = umask(0);
								if (!is_dir($serverIP4)) {
								mkdir($serverIP4, 0777, true);
								}
								$old_umask = umask(0);
								if (!is_dir($serverIP2)) {
								mkdir($serverIP2, 0777, true);
								}
								$old_umask = umask(0);
								if (!is_dir($serverIP3)) {
								mkdir($serverIP3, 0777, true);
								}
								$function_called = 'register';
								$user_id_log = $inputtvid;
								$message = 'folders created from desired';
								$this->log_table($function_called, $user_id_log, $message);
								
								
								$success= '0';
								return $success;
					
							
						}
						else
						{
							$success= '-1';
								return $success;
							
						} 
						
						
		
	}
	
	public function recognition(&$inputtvid, &$uploadfile,&$url,&$threshold,&$distance)
    {
		
			chmod($uploadfile,0777);
		
				$function_called = 'recognize voice';
                $user_id_log     = $inputtvid;
                $message         = 'Recognition starts';
                $this->log_table($function_called, $user_id_log, $message);
                $ch           = curl_init();
                $data         = array(
                    'tv_id' => $inputtvid,
					'threshold' => $threshold,
                    /* 'pictureuid' => $wav_uid, */
                    'audiofile' =>   $uploadfile,
					 'distance' =>   $distance
                );
			//$urls = 'localhost/api.voice/v0/user_recognition';
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
				
				//time out 
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0.1); 
				curl_setopt($ch, CURLOPT_TIMEOUT, 0.4); //timeout in seconds
				
				
                // CURLOPT_SAFE_UPLOAD defaulted to true in 5.6.0
                // So next line is required as of php >= 5.6.0
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // cURL obeys the RFCs as it should. Meaning that for a HTTP/1.1 backend if the POST size is above 1024 bytes
                // cURL sends a 'Expect: 100-continue' header. The server acknowledges and sends back the '100' status code.
                // cuRL then sends the request body. This is proper behaviour. Nginx supports this header.
                // This allows to work around servers that do not support that header.
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Expect:'
                ));
                $output   = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				
				
                    
                $function_called = 'recognize voice';
                $user_id_log = $inputtvid;
                $message = 'Internal API call end: '.$output;
                $this->log_table($function_called, $user_id_log, $message); 
                $json     = json_decode($output);
                $text     = json_encode($json, JSON_PRETTY_PRINT);
                $fff      = str_replace('[', '', $output);
                $fff      = str_replace(']', '', $fff);
                $json2    = json_decode($fff, true);
                $status   = $json2['status'];
				$name  	  = $json2['name'];
				$role  	  = $json2['role'];
				$confidence  	  = $json2['confidence'];
                /*        $function_called = 'recognize voice';
                $user_id_log = $userIdforfile;
                $message = 'Internal API call end with status : '.$status;
                $this->log_table($function_called, $user_id_log, $message);
                */
				// return $status;\
				
				
		/* 		$function_called = 'recognize voice';
                $user_id_log = $inputtvid;
                $message = 'Internal API call end: '.$status;
                $this->log_table($function_called, $user_id_log, $message); 
				
				
				$function_called = 'recognize voice';
                $user_id_log = $inputtvid;
                $message = 'Internal API call end: '.$name;
                $this->log_table($function_called, $user_id_log, $message); 
				
				
				$function_called = 'recognize voice';
                $user_id_log = $inputtvid;
                $message = 'Internal API call end: '.$role;
                $this->log_table($function_called, $user_id_log, $message);  */
				 
				 
				  return array('status' => $status,
                 'output' => $output,
				 'name' => $name,
				 'role' => $role,
				 'confidence' => $confidence,
                 'httpcode' => $httpcode);
				
				 
	}
	
   
   private function base_get_user_list()
    {
        // Cross validation if the request method is DELETE else it will return "Not Acceptable" status
       if ($this->get_request_method() != "POST")
				{
						$this->response('Meathod POST is required', 406);
				}
				
				 $inputtvid     = trim($_GET["tv_id"]);
				 
				 
				 $sql = mysql_query("SELECT tvid FROM voice.tvsets WHERE tvid = '$inputtvid' ", $this->db);
						
						$function_called = 'base_get_user_list';
								$user_id_log = $tvid;
								$message = 'user registration started';
								$this->log_table($function_called, $user_id_log, $message);
						if (mysql_num_rows($sql) == 0)
						{
							
								$function_called = 'register';
								$user_id_log = $tvid;
								$message = 'user with application id already exist in main';
								$this->log_table($function_called, $user_id_log, $message);
								$error = array(
										'status' => "Failure",
										"msg" => "TV is not registered"
								);
								$this->response($this->json($error) , 459);
						}
				 
				 
				 
				 //$tvregistrationflag = $this->register_tv($inputtvid);
		
	
				
				 
				 $function_called = 'get_user_details';
						$user_id_log = $inputtvid;
						$message = 'User data received' . $inputtvid;
						/* 	$query = mysql_query("INSERT INTO `face_reco`.`log_table`
						(`function_called`, `userid`, `agent`, `time_stamp`,`message`)
						VALUES
						('$function_called', '$user_id_log', 'API', substring(replace(replace(replace(now(),'-',''),':',''),' ',''),1,14) , '$message');") or die(mysql_error()); */
						$this->log_table($function_called, $user_id_log, $message);
						//$this->response($this->json($result) , 200);

				$sql = mysql_query("SELECT user_name as user_name,user_role,userid as user_id FROM voice.voice_register_tbl where tvid = '$inputtvid'");
				if (mysql_num_rows($sql) > 0)
				{
					 $function_called = 'get_user_details';
						$user_id_log = $inputtvid;
						$message = 'User data received length more than 0' . $inputtvid;
						
						$result = array();
						while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
						{
								$result[] = $rlt;
						}
$this->response($this->json($result) , 200);
						// If success everythig is good send header as "OK" and return list of users in JSON format

						
				}
				else
				{
					
					$function_called = 'get_user_details';
						$user_id_log = $inputtvid;
						$message = 'User data received length equals 0' . $inputtvid;
						
						
					$success = array(
                'status' => "Success",
                "msg" => "No user exist"
            );
            $this->response($this->json($success), 461);
					//$this->response('', 204); // If no records "No Content" status
				}

				
    }

   private function base_tv_registration()
    {
        // Cross validation if the request method is POST else it will return "Not Acceptable" status
        if ($this->get_request_method() != "POST")
        {
            $error = array(
                'status' => "Failed",
                "msg" => "Meathod POST is required"
            );
            $this->response($this->json($error), 406);
        }

		
		
		
		

		//$this->get_request_method() != "POST"
        $tvid            = trim($this->_request['tv_id']);
        $user_phone      = trim($this->_request['phone']);
        $user_fullname   = trim($this->_request['name']);
        $application_id  = trim($this->_request['application_id']);
        $ch              = curl_init();
        $function_called = 'register voice';
        $user_id_log     = $userIdforfile;
        $message         = 'Internal API call started test' . $reg_name . "job" . $job . "$uploadfile" . $uploadfile . "user id" . $userIdforfile;
        $this->log_table($function_called, $user_id_log, $message);
        /* $tvid           = trim($this->_request['tv_id']);
        $user_phone     = trim($this->_request['phone']);
        $user_fullname  = trim($this->_request['name']);
        $application_id = trim($this->_request['application_id']); */
		
		
		 $tvid     = trim($_GET["tv_id"]);
		 $user_fullname = trim($_GET["name"]);
		 
		 
		 if(empty($tvid))
		 {
			 $error = array(
                'status' => "Failed",
                "msg" => "TV id is required"
            );
            $this->response($this->json($error), 451);
			 
		 }
		 
		  if(empty($user_fullname))
		 {
			 $error = array(
                'status' => "Failed",
                "msg" => "TV name is required"
            );
            $this->response($this->json($error), 452);
			 
		 }
		 
		
		 //$tvid           = trim($_GET["tv_id"] trim($this->_request['tv_id']);
   
        //$user_fullname  = trim($this->_request['name']);
        $application_id = '1111';
		
		
		
		 $user_phone     = '13990132840';
		
		if (strlen($tvid) > 28)
		{
			$error = array(
                'status' => "Failed",
                "msg" => "TV ID too long"
            );
            $this->response($this->json($error), 447);
			
		}
		
		$sql = mysql_query("SELECT tvid FROM voice.tvsets WHERE tvid = '$tvid' LIMIT 1", $this->db);
						
						$function_called = 'register';
								$user_id_log = $tvid;
								$message = 'user registration started';
								$this->log_table($function_called, $user_id_log, $message);
						if (mysql_num_rows($sql) > 0)
						{
							
								$function_called = 'register';
								$user_id_log = $tvid;
								$message = 'user with application id already exist in main';
								$this->log_table($function_called, $user_id_log, $message);
								$error = array(
										'status' => "Failed",
										"msg" => "TV ID already exist"
								);
								$this->response($this->json($error) , 426);
						}
		
        $data           = array(
            'tv_id' => $tvid,
            'phone' => $user_phone,
            /* 'pictureuid' => $wav_uid, */
            'name' => $user_fullname,
            'application_id' => $application_id
        );
        curl_setopt($ch, CURLOPT_URL, 'localhost/api.voice1/v0/tv_registration');
        curl_setopt($ch, CURLOPT_POST, 1);
        $function_called = 'register voice';
        $user_id_log     = $tvid;
        $message         = 'Internal API call middle: ' . $reg_name . " job :" . $job . "$uploadfile" . $uploadfile . "user id" . "$userIdforfile";
        $this->log_table($function_called, $tvid, $message);
        // CURLOPT_SAFE_UPLOAD defaulted to true in 5.6.0
        // So next line is required as of php >= 5.6.0
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // cURL obeys the RFCs as it should. Meaning that for a HTTP/1.1 backend if the POST size is above 1024 bytes
        // cURL sends a 'Expect: 100-continue' header. The server acknowledges and sends back the '100' status code.
        // cuRL then sends the request body. This is proper behaviour. Nginx supports this header.
        // This allows to work around servers that do not support that header.
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Expect:'
        ));
        $output          = curl_exec($ch);
        $httpcode        = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $json            = json_decode($output);
        $text            = json_encode($json, JSON_PRETTY_PRINT);
        $fff             = str_replace('[', '', $output);
        $fff             = str_replace(']', '', $fff);
        $json2           = json_decode($fff, true);
        $status          = $json2['status'];
        $function_called = 'folder creation';
        $user_id_log     = $tvid;
        $message         = 'Internal API call end: status 1 =' . $output;
        $this->log_table($function_called, $user_id_log, $message);
        if ($status == 'success')
        {
			
			$serverIP4 = "anyone/$tvid/";
								$serverIP2 = "anyone/$tvid/register";
								$serverIP3 = "anyone/$tvid/recognize";
								
								$old_umask = umask(0);
								if (!is_dir($serverIP4)) {
								mkdir($serverIP4, 0777, true);
								}
								$old_umask = umask(0);
								if (!is_dir($serverIP2)) {
								mkdir($serverIP2, 0777, true);
								}
								$old_umask = umask(0);
								if (!is_dir($serverIP3)) {
								mkdir($serverIP3, 0777, true);
								}
								$function_called = 'register';
								$user_id_log = $tvid;
								$message = 'folders created from desired';
								$this->log_table($function_called, $user_id_log, $message);
								
								
								$success = array(
										'status' => "success",
										"msg" => "New TV Created",
										
										
								);
								$this->response($this->json($success) , 201);
								
								
            /* $function_called = 'folder creation';
            $user_id_log     = $userIdforfile;
            $message         = 'inside folder creation =' . $output;
            $this->log_table($function_called, $user_id_log, $message);
            $userIdforfile   = $tvid;
            $urls1           = 'localhost/api.voice/v0/create_folders';
            $urls2           = 'localhost/api.voice2/v0/create_folders';
            $txt             = $this->folder_management($userIdforfile, $urls1);
            $txt1            = $this->folder_management($userIdforfile, $urls2);
            $function_called = 'folder creation';
            $user_id_log     = $userIdforfile;
            $message         = 'inside folder creation txts=' . $txt1 . "   " . $txt;
            $this->log_table($function_called, $user_id_log, $message);
            $stack = array(
                "success"
            );
            array_push($stack, $txt);
            array_push($stack, $txt1);
            if (count(array_keys($stack, 'success')) == count($stack))
            {
                $successfinal = array(
                    'status' => "success",
                    "msg" => "tv registration successful"
                );
                $this->response($this->json($successfinal), 201);
            } //count(array_keys($stack, 'success')) == count($stack)
            else
            {
                $query = mysql_query("delete from voice.tvsets 
                                            where tvid = '$tvid' ") or die(mysql_error());
                $error = array(
                    'status' => "Failed",
                    "msg" => "TV Registartion Failed because of server problem"
                );
                $this->response($this->json($error), 446);
            } */
        } //$status == 'success'
        else
        {
            $function_called = 'folder creation';
            $user_id_log     = $userIdforfile;
            $message         = 'Internal API call else =' . $output;
            $this->log_table($function_called, $user_id_log, $message);
            $this->response($output, $httpcode);
        }
        //$this->response($this->json($output1),200);
    }
 
private function base_user_deletion()

    {
		if ($this->get_request_method() != "POST")
        {
			
			
            $error = array(
                'status' => "Failed",
                "msg" => "Meathod POST is required"
            );
            $this->response($this->json($error), 406);
        } //$this->get_request_method() != "POST"
       
		
		$inputtvid     = trim($_GET["tv_id"]);
		$user_id      = trim($_GET['user_id']);
		
		
		 if(empty($inputtvid))
		 {
			 $error = array(
                'status' => "Failed",
                "msg" => "TV ID is required"
            );
            $this->response($this->json($error), 451);
			 
		 }
		 
		  if(empty($user_id))
		 {
			 $error = array(
                'status' => "Failed",
                "msg" => "User ID is required"
            );
            $this->response($this->json($error), 484);
			 
		 }
		
		
		 $query2 = mysql_query("SELECT count(*) as count
													   FROM voice.tvsets 
													   WHERE tvid = '$inputtvid'") or die(mysql_error());
								$row2 = mysql_fetch_array($query2) or die(mysql_error());
								$count = $row2['count'];



									if($count == 0)
									{
										$error = array(
											'status' => "Failed",
											"msg" => "TV ID does not exist"
										);
										$this->response($this->json($error), 459);
										
										
									}
									
									
		
		$query2 = mysql_query("SELECT count(*) as count
										   FROM voice.voice_register_tbl 
										   WHERE userid = '$user_id'") or die(mysql_error());
											$row2 = mysql_fetch_array($query2) or die(mysql_error());
											$count1 = $row2['count']; 
											
											
											if($count1==0)

											{
												$error = array(
                'status' => "Failed",
                "msg" => "User does not exist"
            );
            $this->response($this->json($error), 428);
												
												
											}
											
											
				$query2 = mysql_query("SELECT user_name,user_role 
										   FROM voice.voice_register_tbl 
										   WHERE userid = '$user_id'") or die(mysql_error());
											$row2 = mysql_fetch_array($query2) or die(mysql_error());
											$reg_name = $row2['user_name']; 
											$role = $row2['user_role']; 
											
											
											if($count1==0)

											{
												$error = array(
                'status' => "Failed",
                "msg" => "User does not exist"
            );
            $this->response($this->json($error), 428);
												
												
											}			


$query2 = mysql_query("SELECT tvid as tv_id
										   FROM voice.voice_register_tbl 
										   WHERE userid = '$user_id'") or die(mysql_error());
											$row2 = mysql_fetch_array($query2) or die(mysql_error());
											$tviddb = $row2['tv_id']; 
										
											
											
											if (!(strcmp($tviddb,$inputtvid)==0))

											{
												$error = array(
                'status' => "Failed",
                "msg" => "TV ID and User ID does not match"
				
            );
            $this->response($this->json($error), 485);
												
												
											}											
											
											
											
											
											$function_called = 'base_user_deletion';
                $user_id_log     = $userIdforfile;
                $message         = 'process started ' . $reg_name . $role ;
                $this->log_table($function_called, $user_id_log, $message);
                $ch   = curl_init();
                $data = array(
                    'username' => $reg_name,
                    'tv_id' => $inputtvid,
					'job' => 'del',
                    /* 'pictureuid' => $wav_uid, */
                    'audiofile' => '@' . 'test.wav',
					'user_role' => $role
                );
                curl_setopt($ch, CURLOPT_URL, 'localhost/api.voice2/v0/user_registration');
                curl_setopt($ch, CURLOPT_POST, 1);
                $httpcode        = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $function_called = 'base_user_deletion';
                $user_id_log     = $userIdforfile;
                $message         = 'Internal API call middle del: ' . $reg_name . "$uploadfile : " . $uploadfile . "user id : " . "$userIdforfile";
                $this->log_table($function_called, $user_id_log, $message);
                // CURLOPT_SAFE_UPLOAD defaulted to true in 5.6.0
                // So next line is required as of php >= 5.6.0
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // cURL obeys the RFCs as it should. Meaning that for a HTTP/1.1 backend if the POST size is above 1024 bytes
                // cURL sends a 'Expect: 100-continue' header. The server acknowledges and sends back the '100' status code.
                // cuRL then sends the request body. This is proper behaviour. Nginx supports this header.
                // This allows to work around servers that do not support that header.
                //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
                $output          = curl_exec($ch);
				
				 $json     = json_decode($output);
                $text     = json_encode($json, JSON_PRETTY_PRINT);
                $fff      = str_replace('[', '', $output);
                $fff      = str_replace(']', '', $fff);
                $json2    = json_decode($fff, true);
                $status   = $json2['status'];
				$msg  	  = $json2['msg'];
               
			
											
				
                $function_called = 'register voice';
                $user_id_log     = $userIdforfile;
                $message         = 'Internal API call end: ' . $fff;
                $this->log_table($function_called, $user_id_log, $message);
				
				
                $function_called = 'register voice';
                $user_id_log     = $userIdforfile;
                $message         = 'Internal API call del end: ' . $this->json($output);
                $this->log_table($function_called, $user_id_log, $message);
                
				$success = array(
											'status' => $status,
											"msg" => $msg,
											"user_id" => $user_id,
											
										);
										$this->response($this->json($success), $httpcode);
				
				
			//	$this->response($this->json($output), $httpcode);
				
				
											
											
											
											
											
		
		
		
		

	
	}

 private function base_user_registration()
    {
		
		
			
			
        // Cross validation if the request method is GET else it will return "Not Acceptable" status
        if ($this->get_request_method() != "POST")
        {
			
			
            $error = array(
                'status' => "Failed",
                "msg" => "Meathod POST is required"
            );
            $this->response($this->json($error), 406);
        } //$this->get_request_method() != "POST"
       
		
		$inputtvid     = trim($_GET["tv_id"]);
		$reg_name      = trim($_GET['username']);
		$job   		   = trim($_GET['job']);
		$role      = strtoupper(trim($_GET['role']));
		$distance     = strtolower(trim($_GET["distance"]));

			
			if ((strcmp($distance,'n')==0) || empty($distance) )
			{
				$distance = 'n';
			}
			else if (strcmp($distance,'f')==0)
			{
				$distance = 'f';
			}

			else{
				 $error = array(
                'status' => "Failed",
                "msg" => "Invalid distance value"
            );
            $this->response($this->json($error), 483);
			}
		
		
		
		if(empty($role))
		 {
			$error = array(
                'status' => "Failed",
                "msg" => "Role is required"
            );
            $this->response($this->json($error), 454);
		 }
		
		 if(empty($inputtvid))
		 {
			 $error = array(
                'status' => "Failed",
                "msg" => "TV ID is required"
            );
            $this->response($this->json($error), 451);
			 
		 }
		 
		  if(empty($reg_name))
		 {
			 $error = array(
                'status' => "Failed",
                "msg" => "User name is required"
            );
            $this->response($this->json($error), 453);
			 
		 }
		
		
	//	$inputtvid     = 'changhongtest1234';
      //  $reg_name      = 'zhh4563';
		//
		
		if (strlen($reg_name) > 20)
		{
			$error = array(
                'status' => "Failed",
                "msg" => "Registered Name too long"
            );
            $this->response($this->json($error), 427);
			
		}
		
		
		$rowcount =0;
	
			if (($role == 'M') || ($role == 'F') || ($role == 'K'))
			{
				$rowcount =1;
			}
	
		
		if ($rowcount ==0)
		{
			$error = array(
                'status' => "Failed",
                "msg" => "Invalid role value"
            );
            $this->response($this->json($error), 455);
			
		}
		
		//$job = 'add';
		

		if(empty($job))
		 {
			 $error = array(
                'status' => "Failed",
                "msg" => "Job is required"
            );
            $this->response($this->json($error), 456);
			 
		 }
		
		/*  $error = array(
                'status' => "Failed",
                "msg" => "debug"  . $job
            );
            $this->response($this->json($error), 400); */
		
		
		$function_called = 'user registration';
								$user_id_log = $inputtvid;
								$message = 'process started';
								$this->log_table($function_called, $user_id_log, $message);
		
		if($job == 'add')
		{
			
		$tvregistrationflag = $this->register_tv($inputtvid);
		
        $userIdforfile = $inputtvid;
        if (isset($_FILES["audiofile"]))
        {
            $serverIP2 = "anyone/$userIdforfile/register/$reg_name/$role/$distance/";
            $old_umask = umask(0);
		
            mkdir($serverIP2, 0777, true);
			
          
            $uploaddir      = "anyone/$userIdforfile/register/$reg_name/$role/$distance/";
            $extension_test = end(explode(".", $_FILES["audiofile"]["name"]));
            $extension_test = 'wav';
            $t              = microtime(true);
            $micro          = sprintf("%06d", ($t - floor($t)) * 1000000);
            $d              = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
			 $file_name      = basename($_FILES['audiofile']['name']);
            $dttm1          = $d->format("YmdHisu");
            $uploadfile     = $uploaddir . $file_name. "_". $dttm1 . "." . $extension_test;
           
            $namepicture    = $dttm1 . "." . $extension_test;
            if (move_uploaded_file($_FILES['audiofile']['tmp_name'], $uploadfile))
            {
            	chmod($uploadfile,0777);
                
                $ch   = curl_init();
                $data = array(
                    'username' => $reg_name,
                    'tv_id' => $inputtvid,
					'job' => $job,
					//'out' => $uploadfile,
                    /* 'pictureuid' => $wav_uid, */
                    'audiofile' => $uploadfile,
					'user_role' => $role,
					'distance' => $distance
                );
				
				
										
										
                curl_setopt($ch, CURLOPT_URL, 'localhost/api.voice2/v0/user_registration');
                curl_setopt($ch, CURLOPT_POST, 1);
                $httpcode        = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                
				
                // CURLOPT_SAFE_UPLOAD defaulted to true in 5.6.0
                // So next line is required as of php >= 5.6.0
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // cURL obeys the RFCs as it should. Meaning that for a HTTP/1.1 backend if the POST size is above 1024 bytes
                // cURL sends a 'Expect: 100-continue' header. The server acknowledges and sends back the '100' status code.
                // cuRL then sends the request body. This is proper behaviour. Nginx supports this header.
                // This allows to work around servers that do not support that header.
                //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
                $output          = curl_exec($ch);
				$tyyy = curl_error($ch);
			
              $json     = json_decode($output);
                $text     = json_encode($json, JSON_PRETTY_PRINT);
                $fff      = str_replace('[', '', $output);
                $fff      = str_replace(']', '', $fff);
                $json2    = json_decode($fff, true);
                $status   = $json2['status'];
				$name  	  = $json2['name'];
				$msg  	  = $json2['msg'];
				$errorcode  	  = $json2['errorcode'];
				$user_id  	  = $json2['user_id'];
               
				
				
                $function_called = 'register voice';
                $user_id_log     = $userIdforfile;
                $message         = 'Internal API call end: ' . $fff .  $tyyy ;
                $this->log_table($function_called, $user_id_log, $message);
				
				
				if($status== 'success')
				{
					$success = array(
											'status' => $status,
											"msg" => $msg,
											"user_id" => $user_id,
											"name" => $name
											
										);
										$this->response($this->json($success), $httpcode);
					
				}
				
				else
				{
					
					$success = array(
											'status' => $status,
											"msg" => $msg,
											
										);
										$this->response($this->json($success), $errorcode);
					
				}
				
				
				
              //  $this->response($this->json($output), $httpcode);
            } //move_uploaded_file($_FILES['audiofile']['tmp_name'], $uploadfile)
        } //isset($_FILES["audiofile"])
        else
        {
            $error = array(
                'status' => "Failed",
                "msg" => "audio file is required"
            );
            $this->response($this->json($error), 400);
        }
		
		}
		else if ($job == 'del')
		{
					
		
        $userIdforfile = $inputtvid;
		
		
		$query2 = mysql_query("SELECT count(userid) as userid
										   FROM voice.voice_register_tbl 
										   WHERE tvid = '$inputtvid'
											AND user_name = '$reg_name'
											and user_role ='$role'") or die(mysql_error());
											$row2 = mysql_fetch_array($query2) or die(mysql_error());
											$count1 = $row2['userid']; 
											
											
											if($count1==0)

											{
												$error = array(
                'status' => "Failed",
                "msg" => "User does not exist"
            );
            $this->response($this->json($error), 428);
												
												
											}												
												
											
		
		
        	 $query2 = mysql_query("SELECT userid as userid
										   FROM voice.voice_register_tbl 
										   WHERE tvid = '$inputtvid'
											AND user_name = '$reg_name'
											and user_role ='$role'") or die(mysql_error());
											$row2 = mysql_fetch_array($query2) or die(mysql_error());
											$userid = $row2['userid']; 
											
            
            
                $function_called = 'register voice';
                $user_id_log     = $userIdforfile;
                $message         = 'Picture moved ';
                $this->log_table($function_called, $user_id_log, $message);
                $ch   = curl_init();
                $data = array(
                    'username' => $reg_name,
                    'tv_id' => $inputtvid,
					'job' => 'del',
                    /* 'pictureuid' => $wav_uid, */
                    'audiofile' => '@' . 'test.wav',
					'user_role' => $role
                );
                curl_setopt($ch, CURLOPT_URL, 'localhost/api.voice2/v0/user_registration');
                curl_setopt($ch, CURLOPT_POST, 1);
                $httpcode        = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $function_called = 'register voice';
                $user_id_log     = $userIdforfile;
                $message         = 'Internal API call middle del: ' . $reg_name . "$uploadfile : " . $uploadfile . "user id : " . "$userIdforfile";
                $this->log_table($function_called, $user_id_log, $message);
                // CURLOPT_SAFE_UPLOAD defaulted to true in 5.6.0
                // So next line is required as of php >= 5.6.0
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // cURL obeys the RFCs as it should. Meaning that for a HTTP/1.1 backend if the POST size is above 1024 bytes
                // cURL sends a 'Expect: 100-continue' header. The server acknowledges and sends back the '100' status code.
                // cuRL then sends the request body. This is proper behaviour. Nginx supports this header.
                // This allows to work around servers that do not support that header.
                //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
                $output          = curl_exec($ch);
				
				 $json     = json_decode($output);
                $text     = json_encode($json, JSON_PRETTY_PRINT);
                $fff      = str_replace('[', '', $output);
                $fff      = str_replace(']', '', $fff);
                $json2    = json_decode($fff, true);
                $status   = $json2['status'];
				$msg  	  = $json2['msg'];
               
			
											
				
                $function_called = 'register voice';
                $user_id_log     = $userIdforfile;
                $message         = 'Internal API call end: ' . $fff;
                $this->log_table($function_called, $user_id_log, $message);
				
				
                $function_called = 'register voice';
                $user_id_log     = $userIdforfile;
                $message         = 'Internal API call del end: ' . $this->json($output);
                $this->log_table($function_called, $user_id_log, $message);
                
				$success = array(
											'status' => $status,
											"msg" => $msg,
											"user_id" => $userid,
											
										);
										$this->response($this->json($success), $httpcode);
				
				
			//	$this->response($this->json($output), $httpcode);
				
				
				
           
        
		
		}
		else
		{
			$error = array(
                'status' => "Failed",
                "msg" => "Invalid job value"
            );
            $this->response($this->json($error), 457);
		}
		
		
		
    }
    private function base_user_recognition()
    {
       
        if ($this->get_request_method() != "POST")
        {
            $error = array(
                'status' => "Failed",
                "msg" => "Meathod POST is required"
            );
            $this->response($this->json($error), 406);
        } //$this->get_request_method() != "POST"
        //$inputtvid     = trim($this->_request['tv_id']);
			$inputtvid     = trim($_GET["tv_id"]);
			$dataflag     = strtolower(trim($_GET["dataflag"]));
			$threshold     = strtolower(trim($_GET["threshold"]));
			$distance     = strtolower(trim($_GET["distance"]));
			
	/* 		
			//new test
			$success = array(
            'status' => "Success",
            "msg" => "API is working fine on main server"
        );
        $this->response($this->json($success), 200); */

			
			if ((strcmp($distance,'n')==0) || empty($distance) )
			{
				$distance = 'n';
			}
			else if (strcmp($distance,'f')==0)
			{
				$distance = 'f';
			}

			else{
				 $error = array(
                'status' => "Failed",
                "msg" => "Invalid distance value"
            );
            $this->response($this->json($error), 483);
			}


			
        $userIdforfile = $inputtvid;
		if(empty($inputtvid))
		 {
			 $error = array(
                'status' => "Failed",
                "msg" => "TV ID is required"
            );
            $this->response($this->json($error), 451);
			 
		 }
		 
		 
		  
		if(strcmp($inputtvid,'testfornopid')==0)
		 {
			 
			 $function_called = 'base_user_recognition';
                $user_id_log     = $inputtvid;
                $message         = 'bye bye' . $count3 ;
                $this->log_table($function_called, $user_id_log, $message);
			 $error = array(
                'status' => "Failed",
                "msg" => "bye bye"
            );
            $this->response($this->json($error), 451);
			 
		 }
		 		 
		 $query3 = mysql_query("SELECT count(userid) as count FROM voice.voice_register_tbl where tvid ='$inputtvid'") or die(mysql_error());
														$row3 = mysql_fetch_array($query3) or die(mysql_error());
														$count3 = $row3['count'];
														
														
														  $function_called = 'base_user_recognition';
                $user_id_log     = $inputtvid;
                $message         = 'Data received with flag count' . $count3 ;
                $this->log_table($function_called, $user_id_log, $message);
														
		 
	if($count3 == 0)
	{
	
		  $function_called = 'base_user_recognition';
                $user_id_log     = $inputtvid;
                $message         = 'Data received with flag false' ;
                $this->log_table($function_called, $user_id_log, $message);
		
		
		
		
            $serverIP2 = "anyone/$userIdforfile/data/$distance/";
            $old_umask = umask(0);
			
            mkdir($serverIP2, 0777, true);
					
          //  mkdir($serverIP2, 0777, true);
            $uploaddir      = "anyone/$userIdforfile/data/$distance/";
            //$uploadfile = $uploaddir . basename($_FILES['imagefile']['name']);
            //$extension_test = end(explode(".", $_FILES["imagefile"]["name"]));
                       $extension_test = end(explode(".", $_FILES["audiofile"]["name"]));
            $extension_test = 'wav';
            $t              = microtime(true);
            $micro          = sprintf("%06d", ($t - floor($t)) * 1000000);
            $d              = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
			 $file_name      = basename($_FILES['audiofile']['name']);
            $dttm1          = $d->format("YmdHisu");
            $uploadfile     = $uploaddir . $file_name. "_". $dttm1 . "." . $extension_test;
           
            $namepicture    = $dttm1 . "." . $extension_test;
            if (move_uploaded_file($_FILES['audiofile']['tmp_name'], $uploadfile))
            {
				$error = array(
											'status' => "Failed",
											"msg" => "No user exist"
										);
										$this->response($this->json($error), 461);
				
			}
			
			else
			{
				$error = array(
											'status' => "Failed",
											"msg" => "No user exist"
										);
										$this->response($this->json($error), 461);
				
			}
		
		
		
		
	}
	
		 
		 
		
		
			
		if(strcmp($dataflag,'true')==0)
		{
			 $function_called = 'recognize voice';
        $user_id_log     = $userIdforfile;
        $message         = 'process starts for uploading'. $inputtvid ;
        $this->log_table($function_called, $user_id_log, $message);
		
		$userIdforfile = $inputtvid;
		if(empty($inputtvid))
		 {
			 $error = array(
                'status' => "Failed",
                "msg" => "TV ID is required"
            );
            $this->response($this->json($error), 451);
			 
		 }
		 
		  if (isset($_FILES["audiofile"]))
        {
			
			 $function_called = 'base_user_recognition';
                $user_id_log     = $inputtvid;
                $message         = 'Data received with flag true' ;
                $this->log_table($function_called, $user_id_log, $message);
				
				
            $serverIP2 = "anyone/$userIdforfile/data/$distance/";
            $old_umask = umask(0);
			
            mkdir($serverIP2, 0777, true);
					
          //  mkdir($serverIP2, 0777, true);
            $uploaddir      = "anyone/$userIdforfile/data/$distance/";
            //$uploadfile = $uploaddir . basename($_FILES['imagefile']['name']);
            //$extension_test = end(explode(".", $_FILES["imagefile"]["name"]));
                       $extension_test = end(explode(".", $_FILES["audiofile"]["name"]));
            $extension_test = 'wav';
            $t              = microtime(true);
            $micro          = sprintf("%06d", ($t - floor($t)) * 1000000);
            $d              = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
			 $file_name      = basename($_FILES['audiofile']['name']);
            $dttm1          = $d->format("YmdHisu");
            $uploadfile     = $uploaddir . $file_name. "_". $dttm1 . "." . $extension_test;
           
            $namepicture    = $dttm1 . "." . $extension_test;
            if (move_uploaded_file($_FILES['audiofile']['tmp_name'], $uploadfile))
            {
				$success = array(
											'status' => "success",
											"msg" => "Audio file upload successful"
										);
										$this->response($this->json($success), 209);
				
			}
			
			else
			{
				$success = array(
											'status' => "failure",
											"msg" => "Audio Files not uploaded "
										);
										$this->response($this->json($success), 481);
				
			}
		}
		 
		 else
		 {
			 $error = array(
                'status' => "Failed",
                "msg" => "Audio file is required"
            );
            $this->response($this->json($error), 400);
			 
		 }
			
			
		}
		
		else if ((strcmp($dataflag,'false')==0) || empty($dataflag) )
		{
			 $function_called = 'recognize voice';
        $user_id_log     = $userIdforfile;
        $message         = 'process starts'. $inputtvid ;
        $this->log_table($function_called, $user_id_log, $message);
		
			 if(empty($threshold))
		 {
			 $thresholdforapi = '0';
			 
		 }
		 
		 else
		 {
			  $thresholdforapi = $threshold;
		 }
		
		
		if (isset($_FILES["audiofile"]))
        {
            $serverIP2 = "anyone/$userIdforfile/recognize/temp/";
            $old_umask = umask(0);
			
			if (!is_dir($serverIP2)) {
            mkdir($serverIP2, 0777, true);
			}
			
           // mkdir($serverIP2, 0777, true);
            $uploaddir      = "anyone/$userIdforfile/recognize/temp/";
            //$uploadfile = $uploaddir . basename($_FILES['imagefile']['name']);
            //$extension_test = end(explode(".", $_FILES["imagefile"]["name"]));
            $extension_test = 'wav';
            $t              = microtime(true);
            $micro          = sprintf("%06d", ($t - floor($t)) * 1000000);
            $d              = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
            $dttm1          = $d->format("YmdHisu");
            $uploadfile     = $uploaddir . $dttm1 . "." . $extension_test;
            $file_name      = basename($_FILES['audiofile']['name']);
            $dttm_now       = date("YmdHis");
            if (move_uploaded_file($_FILES['audiofile']['tmp_name'], $uploadfile))
            {
				//tv_id, uploadfile
				
				
					/* $randomnumber = rand(1, 10);
 
					$servers= array('localhost/api.voice/v0/user_recognition');
 
					 for ($i= 1; $i<10; $i++)
					 {
						 
						 array_push($servers,'localhost/api.voice'.$i.'/v0/user_recognition');
					 }
 
 					$url =$servers[$randomnumber]; 
					 */
					 
					 // pump it up
					 
					 $randomnumber = rand(0, 1);
 
					$servers= array('localhost/api.voice1/v0/user_recognition');
 
					
						 
						 array_push($servers,'localhost/api.voice2/v0/user_recognition');
					 
 
 					$url =$servers[$randomnumber];
					
					$function_called = 'recognize voice server';
                    $user_id_log     = $userIdforfile;
                    $message         = 'recognition at server  '. $randomnumber;
                    $this->log_table($function_called, $user_id_log, $message);
				
				
				
			//	 $urls = 'localhost/api.voice2/v0/user_recognition';
				//$arrayoutput             = $this->recognition($inputtvid, $uploadfile, $urls);
				
					$arrayoutput             = $this->recognition($inputtvid, $uploadfile, $url,$thresholdforapi,$distance);
					
					$status = $arrayoutput['status'];
					$output = $arrayoutput['output'];
					$httpcode = $arrayoutput['httpcode'];
					$name = $arrayoutput['name'];
					$role = $arrayoutput['role'];
					$confidence = $arrayoutput['confidence'];
				
               
                /* $randomnumber = rand(1, 2);
                if ($randomnumber == 1)
                {
                    $function_called = 'recognize voice server';
                    $user_id_log     = $userIdforfile;
                    $message         = 'recognition at server 1';
                    $this->log_table($function_called, $user_id_log, $message);
                    $urls = 'localhost/api.voice/v0/user_recognition';
                } //$randomnumber == 1
                else
                {
                    $function_called = 'recognize voice server';
                    $user_id_log     = $userIdforfile;
                    $message         = 'recognition at server 2';
                    $this->log_table($function_called, $user_id_log, $message);
                    $urls = 'localhost/api.voice2/v0/user_recognition';
                }
				 */
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
               
                if ($status == 'success')
                {
                    $function_called = 'recognize voice';
                    $user_id_log     = $userIdforfile;
                    $message         = 'process ends '. $status .'output   ' .$output .' name : ' .$name. 'role :' .$role;
                    $this->log_table($function_called, $user_id_log, $message);
					
					// unlink($uploadfile);
					 $this->move_file($uploadfile,$userIdforfile,$name,$role,$distance,$confidence,$thresholdforapi,$output);
					 
                    $this->response($output, $httpcode);
					
					
					
					
					
                } //$status == 'success'
				/* else if ($status == 'Failed')
                {
                    $function_called = 'recognize voice';
                    $user_id_log     = $userIdforfile;
                    $message         = 'process ends failuyre' .$status;
                    $this->log_table($function_called, $user_id_log, $message);
					$this->move_file_failed($uploadfile,$userIdforfile);
					
					// unlink($uploadfile);
					 
                    $this->response($output, $httpcode);
                }  */
                else
                {
				/* 	//test
					$function_called = 'recognize voice';
									$user_id_log     = $userIdforfile;
									$message         = 'process ends failure' .$status;
									$this->log_table($function_called, $user_id_log, $message);
									 
									//$this->move_file_failed($uploadfile,$userIdforfile,$distance);
									
									// unlink($uploadfile);
									 
									$this->response($output, $httpcode);
									
									//test end */
									
									
					$function_called = 'recognize voice';
                    $user_id_log     = $userIdforfile;
                    $message         = 'process ends other'.$status;
                    $this->log_table($function_called, $user_id_log, $message);
					
					$function_called = 'recognize voice';
                    $user_id_log     = $userIdforfile;
                    $message         = 'sending to new server'.$status;
                    $this->log_table($function_called, $user_id_log, $message);
					
					
					/* $newurlnumber = ($randomnumber + 1)%11 ;
					$url =$servers[$newurlnumber]; 
					
					
					$function_called = 'recognize voice server';
                    $user_id_log     = $userIdforfile;
                    $message         = 'recognition at server  '. $newurlnumber;
                    $this->log_table($function_called, $user_id_log, $message); */
					
					
					if($randomnumber==0)
					{
						$newurl =$servers[1]; 
						$function_called = 'recognize voice server';
                    $user_id_log     = $userIdforfile;
                    $message         = 'recognition at server 2 ' .$newurl ;
                    $this->log_table($function_called, $user_id_log, $message); 
					
					
					
				$arrayoutput             = $this->recognition($inputtvid, $uploadfile, $url,$thresholdforapi,$distance);
					
					$status = $arrayoutput['status'];
					$output = $arrayoutput['output'];
					$httpcode = $arrayoutput['httpcode'];
					$name = $arrayoutput['name'];
					$role = $arrayoutput['role'];
					
									if ($status == 'success')
								{
									$function_called = 'recognize voice';
									$user_id_log     = $userIdforfile;
									$message         = 'process ends' .$status;
									$this->log_table($function_called, $user_id_log, $message);
									// unlink($uploadfile);
									  $this->move_file($uploadfile,$userIdforfile,$name,$role,$distance,$confidence,$thresholdforapi,$output);
									$this->response($output, $httpcode);
								} //$status == 'success'
								else if ($status == 'Failed')
								{
									$function_called = 'recognize voice';
									$user_id_log     = $userIdforfile;
									$message         = 'process ends failure' .$status;
									$this->log_table($function_called, $user_id_log, $message);
									 
									$this->move_file_failed($uploadfile,$userIdforfile,$distance);
									
									// unlink($uploadfile);
									 
									$this->response($output, $httpcode);
									
									
								} //$status == 'failure'
								else
								{
									$function_called = 'recognize voice';
									$user_id_log     = $userIdforfile;
									$message         = 'process ends other'.$status;
									$this->log_table($function_called, $user_id_log, $message);
									
									$function_called = 'recognize voice';
									$user_id_log     = $userIdforfile;
									$message         = 'stopping now'.$status;
									$this->log_table($function_called, $user_id_log, $message);
									

									$this->move_file_error($uploadfile,$userIdforfile,$distance);
									
									$error = array(
										   'status' => "failure",
														"msg" => "Multiple server failure" 
										);
										$this->response($this->json($error), 501);
									
									
								}
									
									
									
						
					
					
					
						
					}
					else
					{
						
						
						$newurl =$servers[0]; 
						$function_called = 'recognize voice server';
                    $user_id_log     = $userIdforfile;
                    $message         = 'recognition at server  1 . '. $servers[0];
                    $this->log_table($function_called, $user_id_log, $message); 
					
					$arrayoutput             = $this->recognition($inputtvid, $uploadfile, $newurl,$thresholdforapi);
					
					$status = $arrayoutput['status'];
					$output = $arrayoutput['output'];
					$httpcode = $arrayoutput['httpcode'];
					
									if ($status == 'success')
								{
									$function_called = 'recognize voice';
									$user_id_log     = $userIdforfile;
									$message         = 'process ends' .$status;
									$this->log_table($function_called, $user_id_log, $message);
									$this->response($output, $httpcode);
								} //$status == 'success'
								else if ($status == 'Failed')
								{
									$function_called = 'recognize voice';
									$user_id_log     = $userIdforfile;
									$message         = 'process ends failuyre' .$status;
									$this->log_table($function_called, $user_id_log, $message);
									$this->response($output, $httpcode);
								} //$status == 'failure'
								else
								{
									$function_called = 'recognize voice';
									$user_id_log     = $userIdforfile;
									$message         = 'process ends other'.$status;
									$this->log_table($function_called, $user_id_log, $message);
									
									$function_called = 'recognize voice';
									$user_id_log     = $userIdforfile;
									$message         = 'stopping now'.$status;
									$this->log_table($function_called, $user_id_log, $message);
									
									$error = array(
										   'status' => "failure",
														"msg" => "Multiple server failure" 
										);
										$this->response($this->json($error), 501);
									
									
								}
									
									
									
									
									
									}
					
					
				//	$url =$servers[$newurlnumber]; 
					
					
					
					
					
					
					
                   
                }
            } //move_uploaded_file($_FILES['audiofile']['tmp_name'], $uploadfile)
            else
            {
                $error = array(
                    'status' => "Failed",
                    "msg" => "File cannot be uploaded"
                );
                $this->response($this->json($error), 422);
            }
        } //isset($_FILES["audiofile"])
        else
        {
            $error = array(
                'status' => "Failed",
                "msg" => "Audio file is required"
            );
            $this->response($this->json($error), 400);
        }
		
		
		
			
			
		}
		else
		{
			$error = array(
                'status' => "Failed",
                "msg" => "Invalid flag value"
            );
            $this->response($this->json($error), 482);
			
		}
		
		
        
        /*     print_r($_FILES);
        $error = array('status' => "Failed", "msg" => "Invalid Email address or Password");
        $this->response($this->json($error), 400);
        */
    }
	
	
	
	 private function base_clear_users()
    {
        // Cross validation if the request method is DELETE else it will return "Not Acceptable" status
        if ($this->get_request_method() != "POST")
        {
            $error = array(
                'status' => "Failed",
                "msg" => "Meathod GET is required"
            );
            $this->response($this->json($error), 406);
        } //$this->get_request_method() != "GET"
        
		$inputtvid     = trim($_GET["tv_id"]);
		
		if(empty($inputtvid))
		 {
			 $error = array(
                'status' => "Failed",
                "msg" => "TV ID is required"
            );
            $this->response($this->json($error), 451);
			 
		 }
		 
		 $function_called = 'base_clear_users';
									$user_id_log     = $inputtvid;
									$message         = 'process del started'.$status;
									$this->log_table($function_called, $user_id_log, $message);
		 
		 $query2 = mysql_query("SELECT count(*) as count
													   FROM voice.tvsets
													   WHERE tvid = '$inputtvid'") or die(mysql_error());
								$row2 = mysql_fetch_array($query2) or die(mysql_error());
								$count = $row2['count'];



									if($count == 0)
									{
										$error = array(
											'status' => "Failed",
											"msg" => "TV ID does not exist"
										);
										$this->response($this->json($error), 459);
										
										
									}
									
									
									$query3 = mysql_query("SELECT count(userid) as count FROM voice.voice_register_tbl where tvid ='$inputtvid'") or die(mysql_error());
														$row3 = mysql_fetch_array($query3) or die(mysql_error());
														$count3 = $row3['count'];
														
		 
	if($count3 == 0)
	{
		$error = array(
											'status' => "Failed",
											"msg" => "No user exist"
										);
										$this->response($this->json($error), 461);
	}
		 
		 
		 
		
		$data = array(array(),array());
		$i = 0;		
		
		$status = 0;
		
		$query_select = "SELECT user_name,user_role FROM voice.voice_register_tbl where tvid = '$inputtvid'";
		$result_select = mysql_query($query_select) or die(mysql_error());
		$rows = array();
		while($row = mysql_fetch_array($result_select))
		$rows[] = $row;
			foreach($rows as $row)
				{ 
				$texting ='';
				 $ename = stripslashes($row['user_name']);
				 $erole = stripslashes($row['user_role']);
				 $tv_idurl = $inputtvid;
				 

 
 
 
 
				$ch   = curl_init();
                $data = array(
                    'username' => $ename,
                    'tv_id' => $tv_idurl,
					'job' => 'del',
                    /* 'pictureuid' => $wav_uid, */
                    'audiofile' => '@' . 'test.wav',
					'user_role' => $erole
                );
                curl_setopt($ch, CURLOPT_URL, 'localhost/api.voice1/v0/user_registration');
                curl_setopt($ch, CURLOPT_POST, 1);
                $httpcode        = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $function_called = 'register voice';
                $user_id_log     = $userIdforfile;
                $message         = 'Internal API call middle del: ' . $reg_name . "$uploadfile : " . $uploadfile . "user id : " . "$userIdforfile";
                $this->log_table($function_called, $user_id_log, $message);
                // CURLOPT_SAFE_UPLOAD defaulted to true in 5.6.0
                // So next line is required as of php >= 5.6.0
                // curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // cURL obeys the RFCs as it should. Meaning that for a HTTP/1.1 backend if the POST size is above 1024 bytes
                // cURL sends a 'Expect: 100-continue' header. The server acknowledges and sends back the '100' status code.
                // cuRL then sends the request body. This is proper behaviour. Nginx supports this header.
                // This allows to work around servers that do not support that header.
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $output          = curl_exec($ch);
				$fff = str_replace('[', '', $output);
                                    $fff = str_replace(']', '', $fff);
                                    $json = json_decode($fff, true);
                                    $valuet = $json['status'];
									
																	$function_called = 'register voice del';
														$user_id_log = $inputtvid;
														$message = 'Internal API call ended'.$fff;
														$this->log_table($function_called, $user_id_log, $message);
														
														$function_called = 'register voice del';
														$user_id_log = $inputtvid;
														$message = 'Internal API call ended'.$valuet;
														$this->log_table($function_called, $user_id_log, $message);
														
														$i = $i +1;
														
														
														if (strpos((strtolower($valuet)), 'success') !== false)
															{
																$status = $status + 1 ;
																
																$function_called = 'register voice del';
														$user_id_log = $inputtvid;
														$message = 'querry : '.$query_delete;
														$this->log_table($function_called, $user_id_log, $message);
																
															}
															else
															{
																	$status =$status ;
																	
															}
				
							
				
				}
				
				$function_called = 'register voice del';
														$user_id_log = $inputtvid;
														$message = 'total : '.$i;
														$this->log_table($function_called, $user_id_log, $message);
														
														
														$function_called = 'register voice del';
														$user_id_log = $inputtvid;
														$message = 'status : '.$status;
														$this->log_table($function_called, $user_id_log, $message);
				
				if($status == $i)
				{
					$success = array(
											'status' => "success",
											"msg" => "Delete successful" 
										);
										$this->response($this->json($success), 210);
		
					
				}
				else
				{
					$failure = array(
											'status' => "failure",
											"msg" => "Delete unsuccessful" 
										);
										$this->response($this->json($failure), 460);
				}
					
				
				
				
				
		
	
		
		
		
	}
	
	
	
	
	 private function base_user_management()
    {
        // Cross validation if the request method is DELETE else it will return "Not Acceptable" status
        if ($this->get_request_method() != "POST")
        {
            $error = array(
                'status' => "Failed",
                "msg" => "Meathod GET is required"
            );
            $this->response($this->json($error), 406);
        } //$this->get_request_method() != "GET"
        
		$inputtvid     = trim($_GET["tv_id"]);
		$job     = trim($_GET["job"]); //name or role or both
		$old_name     = trim($_GET["old_name"]);
		$old_role     = trim($_GET["old_role"]);
		$new_name     = trim($_GET["new_name"]);
		$new_role     = trim($_GET["new_role"]);
		
		
		//validations
		
								$query2 = mysql_query("SELECT count(*) as count
													   FROM voice.voice_register_tbl 
													   WHERE tvid = '$inputtvid'
														AND user_name = '$old_name'
														and user_role ='$old_role'") or die(mysql_error());
								$row2 = mysql_fetch_array($query2) or die(mysql_error());
								$count = $row2['count'];



									if($count == 0)
									{
												$function_called = 'register voice';
								$user_id_log = $userIdforfile;
								$message = 'user doesnot exist';
								$this->log_table($function_called, $user_id_log, $message);
										$error = array(
										'status' => "Failed",
										"msg" => "User does not exist"
								);
								$this->response($this->json($error) , 428);

									}
									
									
			/// copied validations
			
			if (strlen($new_name) > 20)
		{
			$error = array(
                'status' => "Failed",
                "msg" => "Registered Name too long"
            );
            $this->response($this->json($error), 427);
			
		}

			
			if(empty($old_role))
		 {
			$error = array(
                'status' => "Failed",
                "msg" => "Role is required"
            );
            $this->response($this->json($error), 454);
		 }
		
		 if(empty($inputtvid))
		 {
			 $error = array(
                'status' => "Failed",
                "msg" => "TV ID is required"
            );
            $this->response($this->json($error), 451);
			 
		 }
		 
		  if(empty($old_name))
		 {
			 $error = array(
                'status' => "Failed",
                "msg" => "User name is required"
            );
            $this->response($this->json($error), 453);
			 
		 }
		
		
		
		$rowcount =0;
	
			if (($old_role == 'M') || ($old_role == 'F') || ($old_role == 'K'))
			{
				$rowcount =1;
			}
	
		
		if ($rowcount ==0)
		{
			$error = array(
                'status' => "Failed",
                "msg" => "Invalid role value"
            );
            $this->response($this->json($error), 455);
			
		}
			
			
	
		
		
		
		
		if(strcmp($job,'name')==0)
		{
			
			  if(empty($new_name))
		 {
			 $error = array(
                'status' => "Failed",
                "msg" => "User name is required"
            );
            $this->response($this->json($error), 453);
			 
		 }
		 
			if(!empty($old_name) && !empty($old_role) && !empty($new_name) )
			{
				
						$query2 = mysql_query("SELECT count(*) as count
										   FROM voice.voice_register_tbl 
										   WHERE tvid = '$inputtvid'
											AND user_name = '$new_name'
											and user_role ='$old_role'") or die(mysql_error());
					$row2 = mysql_fetch_array($query2) or die(mysql_error());
					$count = $row2['count'];
														
														
														
						if($count > 0)
						{
							$function_called = 'base_user_management';
							$user_id_log = $inputtvid;
							$message = 'user already exist';
							$this->log_table($function_called, $user_id_log, $message);
									$error = array(
									'status' => "Failed",
									"msg" => "User already exist"
							);
							
							$this->response($this->json($error) , 420);
														
						}
				
				$function_called = 'base_user_management';
								$user_id_log = $inputtvid ;
								$message = 'change name '. $old_name.$inputtvid.$new_name ;
								$this->log_table($function_called, $user_id_log, $message);
								
								
				$query = mysql_query("Update voice.voice_register_tbl 
										set user_name = '$new_name'
										where tvid = '$inputtvid'
										and user_name ='$old_name'
										and user_role ='$old_role'") or die(mysql_error());
								$query1 = mysql_query("commit; ") or die(mysql_error());
								
								
								$query2 = mysql_query("SELECT userid as userid
										   FROM voice.voice_register_tbl 
										   WHERE tvid = '$inputtvid'
											AND user_name = '$old_name'
											and user_role ='$old_role'") or die(mysql_error());
											$row2 = mysql_fetch_array($query2) or die(mysql_error());
											$userid = $row2['userid'];
								
								$success = array(
											'status' => "Success",
											"msg" => "Update successful",
											"user_id" => $userid
										);
										$this->response($this->json($success), 202);
				
				
				
			}
			else
			{
				$error = array(
                'status' => "Failed",
                "msg" => "Validation error"
            );
            $this->response($this->json($error), 458);
				
			}
			
			
		}
		else if(strcmp($job,'role')==0)
		{
			
			$rowcount =0;
	
			if (($new_role == 'M') || ($new_role == 'F') || ($new_role == 'K'))
			{
				$rowcount =1;
			}
	
		
		if ($rowcount ==0)
		{
			$error = array(
                'status' => "Failed",
                "msg" => "Invalid role value"
            );
            $this->response($this->json($error), 455);
			
		}
		
		
			if(!empty($old_name) && !empty($old_role) && !empty($new_role) )
			{
			
			
			$query2 = mysql_query("SELECT count(*) as count
										   FROM voice.voice_register_tbl 
										   WHERE tvid = '$inputtvid'
											AND user_name = '$old_name'
											and user_role ='$new_role'") or die(mysql_error());
					$row2 = mysql_fetch_array($query2) or die(mysql_error());
					$count = $row2['count'];
														
														
														
						if($count > 0)
						{
							$function_called = 'base_user_management';
							$user_id_log = $inputtvid;
							$message = 'user already exist';
							$this->log_table($function_called, $user_id_log, $message);
									$error = array(
									'status' => "Failed",
									"msg" => "User already exist"
							);
							
							$this->response($this->json($error) , 420);
														
						}
			
			
			
			
			$function_called = 'base_user_management';
								$user_id_log = $inputtvid ;
								$message = 'change role '. $old_name.$inputtvid.$new_name ;
								$this->log_table($function_called, $user_id_log, $message);
								
								
				$query = mysql_query("Update voice.voice_register_tbl 
										set user_role = '$new_role'
										where tvid = '$inputtvid'
										and user_name ='$old_name'
										and user_role ='$old_role'") or die(mysql_error());
								$query1 = mysql_query("commit; ") or die(mysql_error());
								
								$query2 = mysql_query("SELECT userid as userid
										   FROM voice.voice_register_tbl 
										   WHERE tvid = '$inputtvid'
											AND user_name = '$old_name'
											and user_role ='$old_role'") or die(mysql_error());
											$row2 = mysql_fetch_array($query2) or die(mysql_error());
											$userid = $row2['userid'];
			
			
			$success = array(
											'status' => "Success",
											"msg" => "Update successful",
											"user_id" => $userid
											
										
										);
										$this->response($this->json($success), 202);
			
			}
			else
			{
				$error = array(
                'status' => "Failed",
                "msg" => "Validation error"
            );
            $this->response($this->json($error), 458);
				
			}
			
		}
		else if(strcmp($job,'both')==0)
		{
			
			
				  if(empty($new_name))
		 {
			 $error = array(
                'status' => "Failed",
                "msg" => "User name is required"
            );
            $this->response($this->json($error), 453);
			 
		 }
		 
			$rowcount =0;
	
			if (($new_role == 'M') || ($new_role == 'F') || ($new_role == 'K'))
			{
				$rowcount =1;
			}
	
		
		if ($rowcount ==0)
		{
			$error = array(
                'status' => "Failed",
                "msg" => "Invalid role value"
            );
            $this->response($this->json($error), 455);
			
		}
		
			if(!empty($old_name) && !empty($old_role) && !empty($new_name) && !empty($new_role) )
			{
			
			
			$query2 = mysql_query("SELECT count(*) as count
										   FROM voice.voice_register_tbl 
										   WHERE tvid = '$inputtvid'
											AND user_name = '$new_name'
											and user_role ='$new_role'") or die(mysql_error());
					$row2 = mysql_fetch_array($query2) or die(mysql_error());
					$count = $row2['count'];
														
														
														
						if($count > 0)
						{
							$function_called = 'base_user_management';
							$user_id_log = $inputtvid;
							$message = 'user already exist';
							$this->log_table($function_called, $user_id_log, $message);
									$error = array(
									'status' => "Failed",
									"msg" => "User already exist"
							);
							
							$this->response($this->json($error) , 420);
														
						}
			
			
			
			$function_called = 'base_user_management';
								$user_id_log = $inputtvid ;
								$message = 'change both '. $old_name.$inputtvid.$new_name ;
								$this->log_table($function_called, $user_id_log, $message);
								
								
				$query = mysql_query("Update voice.voice_register_tbl 
										set user_role = '$new_role',
										user_name = '$new_name'										
										where tvid = '$inputtvid'
										and user_name ='$old_name'
										and user_role ='$old_role'") or die(mysql_error());
								$query1 = mysql_query("commit; ") or die(mysql_error());
								
								$query2 = mysql_query("SELECT userid as userid
										   FROM voice.voice_register_tbl 
										   WHERE tvid = '$inputtvid'
											AND user_name = '$new_name'
											and user_role ='$new_role'") or die(mysql_error());
											$row2 = mysql_fetch_array($query2) or die(mysql_error());
											$userid = $row2['userid'];
					
					
								
								$success = array(
											'status' => "Success",
											"msg" => "Update successful",
											"user_id" => $userid
										);
										$this->response($this->json($success), 202);
			
			
			}
			else
			{
				$error = array(
                'status' => "Failed",
                "msg" => "Validation error"
            );
            $this->response($this->json($error), 458);
				
			}
			
			
		}
		else
		{
			$error = array(
                'status' => "Failed",
                "msg" => "Invalid job value"
            );
            $this->response($this->json($error), 457);
			
		}
		
		
		
		
    }
	
    private function api_check()
    {
        // Cross validation if the request method is DELETE else it will return "Not Acceptable" status
        if ($this->get_request_method() != "GET")
        {
            $error = array(
                'status' => "Failed",
                "msg" => "Meathod GET is required"
            );
            $this->response($this->json($error), 406);
			
								
			
			
        } //$this->get_request_method() != "GET"
		
		$function_called = 'api_check';
								$user_id_log = 'rishu';
								$message = 'API Check ';
								$this->log_table($function_called, $user_id_log, $message);
								
        $success = array(
            'status' => "Success",
            "msg" => "API is working fine on main server"
        );
        $this->response($this->json($success), 200);
    }
	
	private function api_check_post()
    {
        // Cross validation if the request method is DELETE else it will return "Not Acceptable" status
        
								
        $success = array(
            'status' => "Success",
            "msg" => "API is working fine on main server"
        );
        $this->response($this->json($success), 200);
    }
	
	  private function get_time()
    {
        // Cross validation if the request method is DELETE else it will return "Not Acceptable" status
        if ($this->get_request_method() != "POST")
        {
            $error = array(
                'status' => "Failed",
                "msg" => "Meathod GET is required"
            );
            $this->response($this->json($error), 406);
        } //$this->get_request_method() != "GET"
		
		$t              = microtime(true);
            $micro          = sprintf("%06d", ($t - floor($t)) * 1000000);
            $d              = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
			 $file_name      = basename($_FILES['audiofile']['name']);
            $dttm1          = $d->format("YmdHisu");
			
			
        $success = array(
           
            "msg" => $dttm1
        );
        $this->response($this->json($success), 200);
    }
	
	
	
	 
    /*
     *    Encode array into JSON
     */
    private function json($data)
    {
        if (is_array($data))
        {
            return json_encode($data);
        } //is_array($data)
    }
}
// Initiiate Library
$api = new API;
$api->processApi();
?>