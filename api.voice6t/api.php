<?php
//require_once ("Rest.inc.php");
// slave 1

define('SITE_ROOT', '/var/www/html/voice.baseapi/');
require_once (SITE_ROOT. "Rest.inc.php");




//php.ini
//my.cnf
//426
//server : 6 : 192.168.52.27:20003

class API extends REST

{
	
		public $data = "";
    const DB_SERVER = "localhost";
    const DB_USER = "root";
    const DB_PASSWORD = "123456cH+";
    const DB = "voice";
		private $db = NULL;
		

		
		public

		function __construct()
		{
				parent::__construct(); // Init parent contructor
				$this->dbConnect(); // Initiate Database connection
		}

		/*
		*  Database connection
		*/
		private
		function dbConnect()
		{
				$this->db = mysql_connect(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD);
				if ($this->db) mysql_select_db(self::DB, $this->db);
				 mysql_query("SET NAMES UTF8");
		}

		/*
		* Public method for access api.
		* This method dynmically call the method based on the query string
		*
		*/
		
		
		
	
		public

		function processApi()
		{
				$func = strtolower(trim(str_replace("/", "", $_REQUEST['rquest'])));
				if ((int)method_exists($this, $func) > 0) $this->$func();
				else $this->response('', 404); // If the method not exist with in this class, response would be "Page not found".
		}
		
		
		public

		function get_server(&$dumy)
		{
			
				$server = "192.168.52.27:20004";
								
				return $server;
				
				
		}
		
		
			
		

		/*
		/*	I love log
		*/
		public

		function log_table(&$function_called, &$user_id_log, &$message)
		{
				$query = mysql_query("INSERT INTO `voice`.`log_table` 
									(`function_called`, `userid`, `agent`, `time_stamp`,`message`) 
									VALUES
									('$function_called', '$user_id_log', 'Ali Server 6', replace(replace(replace(replace(sysdate(3),'-',''),':',''),' ',''),'.','') , '$message');") or die(mysql_error());
				$query1 = mysql_query("commit; ") or die(mysql_error());
		}

		private
		function tv_registration()
		{

				// Cross validation if the request method is POST else it will return "Not Acceptable" status
				
				

				if ($this->get_request_method() != "POST")
				{
						
						$error = array(
										'status' => "Failed",
										"msg" => "Meathod POST is required"
								);
								$this->response($this->json($error) , 406);
						
				}

				$tvid = trim($this->_request['tv_id']);
				$user_phone = trim($this->_request['phone']);
				$user_fullname = trim($this->_request['name']);
				$application_id = trim($this->_request['application_id']);
				
				
				$application_id ='001';
				
				
					if (strlen($application_id)==0)
				{
					$error = array(
										'status' => "Failed",
										"msg" => "Application ID is needed"
								);
								$this->response($this->json($error) , 425);
					
				}
				
					if (strlen($application_id)>45)
				{
					$error = array(
										'status' => "Failed",
										"msg" => "Maximum length of application id is 45 "
								);
								$this->response($this->json($error) , 428);
					
				}

				// Input validations

				
				/* if (!filter_var($email, FILTER_VALIDATE_EMAIL))
						{
								$error = array(
										'status' => "Failed",
										"msg" => "Invalid Email address"
								);
								$this->response($this->json($error) , 419);
						} 

					 	if (!preg_match("/^[a-zA-Z ]*$/", $user_fullname))
						{
								$error = array(
										'status' => "Failed",
										"msg" => "Only letters and white space allowed"
								);
								$this->response($this->json($error) , 418);
						}  */
						
						$user_phone = '13990132840';
				if (!empty($user_phone)  and !empty($user_fullname))
				{
					

				
										
														
														if( strlen(utf8_decode($user_fullname)) > 25)
														{
															$function_called = 'register';
								$user_id_log = $tvid;
								$message = 'user registreaion name'.strlen($user_fullname);
								$this->log_table($function_called, $user_id_log, $message);
															$error = array(
																'status' => "Failed",
																"msg" => "maximum length of user name is 50 "
														);
														$this->response($this->json($error) , 431);
															
														}

						$sql = mysql_query("SELECT tvid FROM voice.tvsets WHERE tvid = '$tvid' and application_id = '$application_id'  LIMIT 1", $this->db);
						
						$function_called = 'register';
								$user_id_log = $tvid;
								$message = 'user registreaion';
								$this->log_table($function_called, $user_id_log, $message);
						if (mysql_num_rows($sql) > 0)
						{
							
								$function_called = 'register';
								$user_id_log = $tvid;
								$message = 'user with application id already exist';
								$this->log_table($function_called, $user_id_log, $message);
								$error = array(
										'status' => "Failed",
										"msg" => "TV with this application id already exist"
								);
								$this->response($this->json($error) , 426);
						}
						else
						{
							$function_called = 'register';
								$user_id_log = $tvid;
								$message = 'user registreaion';
								$this->log_table($function_called, $user_id_log, $message);
								
								$query = mysql_query("INSERT INTO voice.tvsets 
													(`tvid`,`user_fullname`,  `user_phone`, `registered_on`, `user_password`,`application_id`) 
													VALUES
													('$tvid','$user_fullname',  '$user_phone',  substring(replace(replace(replace(now(),'-',''),':',''),' ',''),1,8) , '123456', '$application_id');") or die(mysql_error());
								$query1 = mysql_query("commit; ") or die(mysql_error());
								$function_called = 'register';
								$user_id_log = $tvid;
								$message = 'user created';
								$this->log_table($function_called, $user_id_log, $message);
								$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
								$charactersLength = strlen($characters);
								$randomString = '';
								for ($i = 0; $i < 10; $i++)
								{
										$randomString.= $characters[rand(0, $charactersLength - 1) ];
								}

								$randnumber = $randomString;
								$query12 = mysql_query("UPDATE  voice.tvsets
                                                        SET key_val ='$randnumber'
                                                        WHERE
                                                        user_phone = '$user_phone'  and application_id ='$application_id' ") or die(mysql_error());
								$characters1 = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
								$charactersLength1 = strlen($characters1);
								$randomString1 = '';
								for ($i = 0; $i < 20; $i++)
								{
										$randomString1.= $characters1[rand(0, $charactersLength1 - 1) ];
								}

								$randnumber1 = $randomString1;
								$query12 = mysql_query("UPDATE  voice.tvsets
                                                        SET secret_val ='$randnumber1'
                                                        WHERE
                                                       user_phone = '$user_phone'  and application_id ='$application_id' ") or die(mysql_error());
								$query2 = mysql_query("SELECT tvid,application_id 
                               FROM voice.tvsets 
                               WHERE user_phone = '$user_phone' and application_id ='$application_id' ") or die(mysql_error());
								$row2 = mysql_fetch_array($query2) or die(mysql_error());
								$userIdforfile = $row2['tvid'];
															
								/* $serverIP4 = "anyone/$userIdforfile/";
								$serverIP2 = "anyone/$userIdforfile/register";
								$serverIP3 = "anyone/$userIdforfile/recognize";
								
								$old_umask = umask(0);
								mkdir($serverIP4, 0777, true);
								$old_umask = umask(0);
								mkdir($serverIP2, 0777, true);
								$old_umask = umask(0);
								mkdir($serverIP3, 0777, true);
								$function_called = 'register';
								$user_id_log = $tvid;
								$message = 'folders created'; */
								//$this->log_table($function_called, $user_id_log, $message);
								$success = array(
										'status' => "success",
										"msg" => "New Application created",
										"key" => $randnumber,
										"secret" => $randnumber1
										
								);
								$this->response($this->json($success) , 201);
						}

						$this->response('', 204); // If no records "No Content" status
				}

				// If invalid inputs "Bad Request" status message and reason

				$error = array(
						'status' => "Failed",
						"msg" => "Invalid Email address or Password or name"
				);
				$this->response($this->json($error) , 400);
		}



	


		private
		function user_registration()
		{

		$function_called = ' register voice  ';
														$user_id_log = $inputtvid;
														$message = 'internal process starts 1';
														//$this->log_table($function_called, $user_id_log, $message);
		
														
														
				// Cross validation if the request method is GET else it will return "Not Acceptable" status

				if ($this->get_request_method() != "POST")
				{
						$error = array(
										'status' => "Failed",
										"msg" => "Meathod POST is required"
								);
								$this->response($this->json($error) , 406);
				}
				
				$role = $this->_request['user_role'];

				$_mode = $this->_request['mode'];
				$_job = $this->_request['job'];
				$_key = $this->_request['key'];
				$_secret = $this->_request['secret'];
				$reg_name = $this->_request['username'];
				$job = $this->_request['job'];
				$uploadfile = $this->_request['audiofile'];
				$distance = $this->_request['distance'];
				//'distance' => $distance
				//$out = trim($this->_request['uploadfile']);
				
				//anyone/test1/register/11/M/wk_train.wav_20170818092845535685.wav
				//../voice.baseapi
			$uploadfile = '../voice.baseapi/'. $uploadfile;
				
					
				
			
				 
				
						$inputtvid = trim($this->_request['tv_id']);
						$reg_name = trim($this->_request['username']);
						//$_job = 'add';
						//$job = 'add';
						
						
						/* $query3 = mysql_query("SELECT count(*) as count
																			   FROM voice.tvsets 
																			   WHERE tvid = '$inputtvid'
																				") or die(mysql_error());
														$row3 = mysql_fetch_array($query3) or die(mysql_error());
														$count3 = $row3['count'];
														
														
														
															if($count3 == 0)
															{
																		$function_called = 'register face';
														$user_id_log = $inputtvid;
														$message = 'TV ID does not exist';
														$this->log_table($function_called, $user_id_log, $message);
																$error = array(
																'status' => "Failed",
																"msg" => "TV ID does not exist"
														);
														
														$this->response($this->json($error) , 459);
														
															} */
																
				
				/* $query10 = mysql_query("SELECT key_val,secret_val
																			   FROM voice.tvsets 
																			   WHERE tvid = '$inputtvid'") or die(mysql_error());
												$row10 = mysql_fetch_array($query10) or die(mysql_error());
												$keydb = $row10['key_val'];
												$secretdb = $row10['secret_val'];
				 */
				
				
		/* 		$_mode = $this->_request['mode'];
				$_job = 'add';
				$_key = 'vHP3c8DiBj';
				$_secret = 'THCm5U7VWRBF7xVQ2ECl';
				$reg_name = 'test2';
				$job = 'add';
				
				$randomString2 = '';
				$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
								$charactersLength = strlen($characters);	
				for ($i = 0; $i < 3; $i++)
								{
										$randomString2.= $characters[rand(0, $charactersLength - 1) ];
								}
								
							
					$reg_name = 'test'.$randomString2;		 */	
					
					
					
					// chcek for user
					
					/* $query02 = mysql_query("SELECT tvid as tvid,user_status
																			   FROM voice.tvsets 
																			   WHERE key_val = '$keydb'
																				AND secret_val = '$secretdb'") or die(mysql_error());
														$row02 = mysql_fetch_array($query02) or die(mysql_error());
														$tvid = $row02['tvid'];
														 */
					
				
															
															
			/* 	$query3 = mysql_query("SELECT count(*) as count
																			   FROM voice.voice_register_tbl 
																			   WHERE tvid = '$inputtvid'
																				") or die(mysql_error());
														$row3 = mysql_fetch_array($query3) or die(mysql_error());
														$count3 = $row3['count'];
														
														
														
															if($count3 == 0)
															{
																		$function_called = 'register face';
														$user_id_log = $userIdforfile;
														$message = 'TV ID does not exist';
														$this->log_table($function_called, $user_id_log, $message);
																$error = array(
																'status' => "Failed",
																"msg" => "TV ID does not exist"
														);
														
														$this->response($this->json($error) , 428);
														
															} */
																								
													
					
					
				
				if($_job == 'add' )
				
				{
					
					$function_called = 'register voice  ';
														$user_id_log = $inputtvid;
														$message = 'internal process starts ';
														$this->log_table($function_called, $user_id_log, $message);
														
					$t = microtime(true);
														$micro = sprintf("%06d",($t - floor($t)) * 1000000);
														$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );

														$useridphp = $d->format("YmdHisu");
														
						$query2 = mysql_query("SELECT count(*) as count
																			   FROM voice.voice_register_tbl 
																			   WHERE tvid = '$inputtvid'
																				AND user_name = '$reg_name'
																				and user_role ='$role'") or die(mysql_error());
														$row2 = mysql_fetch_array($query2) or die(mysql_error());
														$count = $row2['count'];
														
														
														
															if($count > 0)
															{
																		$function_called = ' register voice';
														$user_id_log = $inputtvid;
														$message = 'user already exist';
														$this->log_table($function_called, $user_id_log, $message);
																$error = array(
																'status' => "Failed",
																"msg" => "User already exist"
														);
														
														$this->response($this->json($error) , 420);
														
															}
					
					
				
				
				
						
						
								
								
								
						
						
								
										
										//if (!empty($keydb) && !empty($secretdb))
											
												//if (1==1)
												
														
													
													
													//$dttm_now = $dtttm->format('YmdHis');
														/* $query2 = mysql_query("SELECT tvid as tvid,user_status
																			   FROM voice.tvsets 
																			   WHERE key_val = '$keydb'
																				AND secret_val = '$secretdb'") or die(mysql_error());
														$row2 = mysql_fetch_array($query2) or die(mysql_error()); */
														$userIdforfile = $inputtvid;
														
														
													
													
														
													$serverIP2 = "anyone/";
													$old_umask = umask(0);
													mkdir($serverIP2, 0777, true);
								
														
														
														
														
																
																
																$userid = $userIdforfile;
																
																
																
																$ch = curl_init();
																
															
																
																
																//$useridforrole = $role. '_'.trim($userIdforfile);
																
																	
														
																$data = array(
																		'job' => $_job,
																		'id' =>   $useridphp, // $role. '_'.trim($reg_name),//; $reg_name,
																		/* 'pictureuid' => $wav_uid, */
																		'userid' => $userIdforfile,
																		'source' => '@' . $uploadfile,
																		'distance' => $distance
																);
															
																		$servername = $this->get_server($userIdforfile);
																														
														
																			curl_setopt($ch, CURLOPT_URL, 'http://'.$servername.'/voiceapi/register');

														/* 	curl_setopt($ch, CURLOPT_URL, 'http://10.7.83.151:20003/voiceapi/register'); */
																curl_setopt($ch, CURLOPT_POST, 1);
																
																
															
																

																// CURLOPT_SAFE_UPLOAD defaulted to true in 5.6.0
																// So next line is required as of php >= 5.6.0
																curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);

																curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
																		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
																		// cURL obeys the RFCs as it should. Meaning that for a HTTP/1.1 backend if the POST size is above 1024 bytes
																		// cURL sends a 'Expect: 100-continue' header. The server acknowledges and sends back the '100' status code.
																		// cuRL then sends the request body. This is proper behaviour. Nginx supports this header.
																		// This allows to work around servers that do not support that header.
																		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
															$output = curl_exec($ch);
															
														//	unlink($uploadfile);
															
															
															
														
														
															$json = json_decode($output);
																			$function_called = 'recognize voice';
														$user_id_log = $userIdforfile;
														$message = 'Internal process ends'.$output;
														$this->log_table($function_called, $user_id_log, $message);
														
														
														$text = json_encode($json, JSON_PRETTY_PRINT);
														$fff = str_replace('[', '', $output);
														$fff = str_replace(']', '', $fff);
														$json2 = json_decode($fff, true);
														$status = $json2['status'];
														$name = $json2['name'];
														
														
														
														if(!empty($status) && !empty($name) )
															
															{
																
															if (strpos((strtolower($status)), 'register success') !== false)
															{
																
																$query3 = mysql_query("INSERT INTO voice.voice_register_tbl
																					(`tvid`,  `userid`,`user_name`, `registered_on`, `wav_full_path`,`processed_flg`,`user_role`) 
																					VALUES
																					('$userIdforfile', '$useridphp', '$reg_name',  substring(replace(replace(replace(now(),'-',''),':',''),' ',''),1,14) , '$uploadfile','N','$role');") or die(mysql_error());
																					
														$function_called = 'register voice';
														$user_id_log = $userIdforfile;
														$message = 'Insert Face Register Table done';
														$this->log_table($function_called, $user_id_log, $message);
														
																$query6 = mysql_query("commit; ") or die(mysql_error());
																
																
																
																$success = array(
																		'status' => "success",
																		"msg" => "Success" ,
																		"name" => $reg_name,//substr_replace($name, '', 0 , 2) , //; $name
																		"user_id" => $useridphp,//substr_replace($name, '', 0 , 2) , //; $name
																		
															);
															$this->response($this->json($success) , 200); 
																
																
																
																
															}
															}
															
															if(strpos((strtolower($status)), 'register failed') !== false)
															
															{
																$function_called = 'register voice';
														$user_id_log = $userIdforfile;
														$message = 'Internal API error Registration Failed'. $status ;
														$this->log_table($function_called, $user_id_log, $message);
																$error = array(
																'status' => "Failed",
																'errorcode' => "450",
																"msg" => "Registration Failed");
															$this->response($this->json($error) , 450); 
																
																
															}
															
															
															 if(strpos((strtolower(trim($status))), 'invalidwav') !== false)
															
															{
																
																$function_called = 'register voice';
														$user_id_log = $userIdforfile;
														$message = 'Internal API error Registration Failed invalidwav ' . $status;
														$this->log_table($function_called, $user_id_log, $message);
																$error = array(
																'status' => "Failed",
																'errorcode' => "486",
																"msg" => "Invalid audio file");
															$this->response($this->json($error) , 486); 
																
																
															} 
															
															if(strpos((strtolower(trim($status))), 'internaldberror') !== false)
															
															{
																
																$function_called = 'register voice';
														$user_id_log = $userIdforfile;
														$message = 'Internal API error Registration Failed internaldberror ' . $status ;
														$this->log_table($function_called, $user_id_log, $message);
																$error = array(
																'status' => "Failed",
																'errorcode' => "450",
																"msg" => "Registration Failed");
															$this->response($this->json($error) , 450); 
																
																
															}
															
																if(strpos((strtolower(trim($status))), 'internaleferror') !== false)
															
															{
																
																$function_called = 'register voice';
														$user_id_log = $userIdforfile;
														$message = 'Internal API error Registration Failed internaleferror ' . $status;
														$this->log_table($function_called, $user_id_log, $message);
																$error = array(
																'status' => "Failed",
																'errorcode' => "450",
																"msg" => "Registration Failed");
															$this->response($this->json($error) , 450); 
																
																
															}
															
															
															
															
															
															
															
															
															
														
															
															if(empty($output))
															{
																		$function_called = 'register voice';
														$user_id_log = $userIdforfile;
														$message = 'Internal API error output';
														$this->log_table($function_called, $user_id_log, $message);
																$error = array(
																'status' => "Failed",
																'errorcode' => "500",
																"msg" => "Internal Server Error"
														);
														$this->response($this->json($error) , 500);
														
																
															}
															
															
															
																if(empty($name) )
															
															{
																
															
																$function_called = 'register voice';
														$user_id_log = $userIdforfile;
														$message = 'Internal API error Registration Failed';
														$this->log_table($function_called, $user_id_log, $message);
																$error = array(
																'status' => "Failed",
																"msg" => "Registration Failed");
															$this->response($this->json($error) , 500); 
																
																
																
																
															
															}
															
															
															
															
																/* $error = array('status' => "success", "msg" => "done ");
																$this->response($this->json($error), 400); */
														
												
												
								
						
				

		}
		
		else if($_job == 'del' )
		{
			
			$function_called = 'register voice del ';
														$user_id_log = $inputtvid;
														$message = 'process starts';
														$this->log_table($function_called, $user_id_log, $message);
								$query2 = mysql_query("SELECT count(*) as count
																			   FROM voice.voice_register_tbl 
																			   WHERE tvid = '$inputtvid'
																				AND user_name = '$reg_name'
																				and user_role ='$role'") or die(mysql_error());
														$row2 = mysql_fetch_array($query2) or die(mysql_error());
														$count = $row2['count'];
														
														
														
															if($count == 0)
															{
																		$function_called = 'register voice';
														$user_id_log = $inputtvid;
														$message = 'user doesnot exist';
														$this->log_table($function_called, $user_id_log, $message);
																$error = array(
																'status' => "Failed",
																"msg" => "User does not exist"
														);
														
														$this->response($this->json($error) , 428);
														
															}	
										if (1==1)
										{
												//$reg_name = $this->_request['username'];
												
												$query6 = mysql_query("SELECT userid as userid
																			   FROM voice.voice_register_tbl 
																			   WHERE tvid = '$inputtvid'
																				AND user_name = '$reg_name'
																				and user_role ='$role'") or die(mysql_error());
														$row6 = mysql_fetch_array($query6) or die(mysql_error());
														$useridphp = $row6['userid'];
												
												
												
												
												$job = $_job;
												//if (1==1)
													if (!empty($useridphp))
												{
														
													
													
													//$dttm_now = $dtttm->format('YmdHis');
														
														
														$function_called = 'register voice del ';
														$user_id_log = $inputtvid;
														$message = 'preprocess'.$inputtvid.$reg_name.$role;
														$this->log_table($function_called, $user_id_log, $message);
														
														$sql = mysql_query("SELECT * FROM voice.voice_register_tbl Where tvid = '$inputtvid' and userid ='$useridphp' AND user_name = '$reg_name' and user_role = '$role' ", $this->db);
															if (mysql_num_rows($sql) > 0)
															{
																					
														
																$ch = curl_init();
																
																$function_called = 'register voice del ';
														$user_id_log = $inputtvid;
														$message = 'Internal API call started '. $useridphp;
														$this->log_table($function_called, $user_id_log, $message);
																
																$data = array(
																		'job' => 'del',
																		'id' => $useridphp,//$role. '_'.$reg_name,
																		'userid' => $inputtvid,
																		'source' => '01' //. 'test.wav'
																		
																);
																
																$servername = $this->get_server($userIdforfile);
																														
														
																			curl_setopt($ch, CURLOPT_URL, 'http://'.$servername.'/voiceapi/register');
																			
															/* 	curl_setopt($ch, CURLOPT_URL, 'http://10.7.83.151:20003/voiceapi/register'); */
																curl_setopt($ch, CURLOPT_POST, 1);
																

																// CURLOPT_SAFE_UPLOAD defaulted to true in 5.6.0
																// So next line is required as of php >= 5.6.0
																 curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);

																curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
																	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
																	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
															$output = curl_exec($ch);
															
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
														
														
														if (strpos((strtolower($valuet)), 'del success') !== false)
															{
																 $query32 = mysql_query("delete from voice.voice_register_tbl
																				where tvid = '$inputtvid' and userid ='$useridphp' AND user_name = '$reg_name' and user_role ='$role' ") or die(mysql_error()); 
																				
																				
																				$function_called = 'register voice del';
														$user_id_log = $inputtvid;
														$message = 'whole process ends'.$useridphp.'name '.$reg_name .'role '.$role;
														$this->log_table($function_called, $user_id_log, $message);
																
																
																$success = array(
																		'status' => "success",
																		"msg" => "Success" ,
																		
																		
															);
															$this->response($this->json($success) , 200); 
																
																
																
																
															}
															else
															{
																$function_called = 'register voice';
														$user_id_log = $userIdforfile;
														$message = 'Internal API error 2';
														$this->log_table($function_called, $user_id_log, $message);
																$error = array(
																'status' => "Failed",
																"msg" => "Internal Server Error"
														);
														$this->response($this->json($error) , 500);
																
															}
														
														
														
															if(empty($output))
															{
																		$function_called = 'register voice del';
														$user_id_log = $userIdforfile;
														$message = 'Internal API error';
														$this->log_table($function_called, $user_id_log, $message);
																$error = array(
																'status' => "Failed",
																"msg" => "Internal Server Error"
														);
														$this->response($this->json($error) , 500);
														
																
															}
														
														
															
														
																/* $error = array('status' => "success", "msg" => "done ");
																$this->response($this->json($error), 400); */
														}
															
															else
															{
																$error = array(
																'status' => "Failed",
																"msg" => "Forbidden"
														);
														$this->response($this->json($error) , 403);
																
															}
														
												}
												
												else
												{
																$function_called = 'register voice';
														$user_id_log = $userIdforfile;
														$message = 'validation error';
														$this->log_table($function_called, $user_id_log, $message);
														$error = array(
																'status' => "Failed",
																"msg" => "Registered name is required"
														);
														$this->response($this->json($error) , 402);
												}
										}
										else
										{
												$error = array(
														'status' => "Failed",
														"msg" => "Key Value and Secret code is required "
												);
												$this->response($this->json($error) , 400);
										}
			
			
		}
		
		else
		{
			$error = array(
								'status' => "Failed",
								"msg" => "Invalid job value"
						);
						$this->response($this->json($error) , 457);
			
		}
		
		
		
		
		
		}

		private
		function user_recognition()
		{
							
			
				if ($this->get_request_method() != "POST")
				{
						$error = array(
										'status' => "Failed",
										"msg" => "Meathod POST is required"
								);
								$this->response($this->json($error) , 406);
				}

				$inputtvid = trim($this->_request['tv_id']);
				$threshold = trim($this->_request['threshold']);
				$uploadfile = trim($this->_request['audiofile']);
				$distance = trim($this->_request['distance']);
				
				
				$uploadfile = '../voice.baseapi/'. $uploadfile;
				
														
														
														//anyone/test1/recognize/temp/20170817173307911752.wav"
														
														//../voice.baseapi/anyone/test1/recognize/temp/20170817173307911752.wav
														
				
				/* $query10 = mysql_query("SELECT key_val,secret_val
																			   FROM voice.tvsets 
																			   WHERE tvid = '$inputtvid'") or die(mysql_error());
												$row10 = mysql_fetch_array($query10) or die(mysql_error());
												$keydb = $row10['key_val'];
												$secretdb = $row10['secret_val']; */
				
			
																
				
		
			
				
				
						
						
							
										
										
												
												/* $query2 = mysql_query("SELECT tvid as tvid,user_status
																			   FROM voice.tvsets 
																			   WHERE key_val = '$_key'
																				AND secret_val = '$_secret'") or die(mysql_error());
												$row2 = mysql_fetch_array($query2) or die(mysql_error()); */
												$userIdforfile = $inputtvid;
												
											
														//$statuscode = $row2['user_status'];
														
														
														
														
												
														
												
											
													
														/* $function_called = 'recognize voice';
														$user_id_log = $userIdforfile;
														$message = 'Move file done';
														$this->log_table($function_called, $user_id_log, $message); */
														/* $query3 = mysql_query("INSERT INTO voice.voice_recognition_tbl
																			(`tvid`,  `recognized_on`, `picture_full_path`,`processed_flg`) 
																			VALUES
																			('$userIdforfile',  '$dttm_now' , '$uploadfile','N');") or die(mysql_error());
														$query6 = mysql_query("commit; ") or die(mysql_error()); */
														
														
														
													/* 	$query8 = mysql_query("SELECT max(fileno) as fileno
																			   FROM voice.voice_recognition_tbl 
																			   WHERE tvid = '$userIdforfile'
																			   AND picture_full_path = '$uploadfile' 
																			   AND processed_flg= 'N'") or die(mysql_error());
														$row8 = mysql_fetch_array($query8) or die(mysql_error());
														
														$fileno = $row8['fileno'];
														$gen_file_no = $dttm_now . str_pad($fileno, 8, '0', STR_PAD_LEFT);
														$query30 = mysql_query("Update voice.voice_recognition_tbl SET `gen_fileno` = ('$gen_file_no')
																				WHERE fileno = '$fileno'") or die(mysql_error());
														$query60 = mysql_query("commit; ") or die(mysql_error()); */
														
														
														
														//if(empty($_mode) || $_mode == 'sync')
																	//{
																		/* $error = array('status' => "File uploaded for asyncronous recognition","msg" => " Filenumber:" );
														$this->response($this->json($error) , 201); */
														
														$function_called = 'recognize voice';
														$user_id_log = $userIdforfile;
														$message = 'recognize voice starts '. 'user_id : ' . $userIdforfile  .'threshold :' . $threshold .'distance' . $distance;
														$this->log_table($function_called, $user_id_log, $message); 
														
														
																		$ch = curl_init();
																		$data = array(
																				'source' => '@' . $uploadfile,
																				//'source' =>  $uploadfile,
																				//'pictureuid' => $fileno,
																				//'gen_file_no' => $gen_file_no,
																				'userid' => $userIdforfile,
																				//'id' => $_facemacthing,
																			'threshold' => $threshold,
																			'distance' => $distance
																		);
													
														$servername =  $this->get_server($userIdforfile); //'10.7.83.151:20003';
																														
														
																			curl_setopt($ch, CURLOPT_URL, 'http://'.$servername.'/voiceapi/recognize');
																		curl_setopt($ch, CURLOPT_POST, 1);

																		// CURLOPT_SAFE_UPLOAD defaulted to true in 5.6.0
																		// So next line is required as of php >= 5.6.0
																		 curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
																

																		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
																		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
																		// cURL obeys the RFCs as it should. Meaning that for a HTTP/1.1 backend if the POST size is above 1024 bytes
																		// cURL sends a 'Expect: 100-continue' header. The server acknowledges and sends back the '100' status code.
																		// cuRL then sends the request body. This is proper behaviour. Nginx supports this header.
																		// This allows to work around servers that do not support that header.
																		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
// We're emptying the 'Expect' header, saying to the server: please accept the body right now.
																		
																		
																		//file 
																	/* 	$myfile = fopen("logs.txt", "a") or die("Unable to open file!");
																		$txt = "user id date";
																		fwrite($myfile, "\n". $txt);
																		fclose($myfile) */
																		
																		//file close
																		//request text
																		
																	//	ob_start();  
																	//	$out = fopen('php://output', 'w');
																		
																		//new values
																		
																	//	curl_setopt($ch, CURLOPT_VERBOSE, true);  
																		//curl_setopt($ch, CURLOPT_STDERR, $out);  
																		
																		
																		//curl_setopt($curlHandle, CURLINFO_HEADER_OUT, true); // enable tracking
																		//curl_exec($ch);
																		/* $error = array('status' => "success", "msg" => "done ");
																		$this->response($this->json($error), 400); */
																		$output = curl_exec($ch);
																		//$info = curl_getinfo($ch);
																		//print_r($info);
																	/* 	$function_called = 'recognize face';
														$user_id_log = $userIdforfile;
														$message = $info;
														$this->log_table($function_called, $user_id_log, $message); */
																		
																	//	unlink($uploadfile);
																		$json = json_decode($output);
																		
																		
																		
														
														
														$text = json_encode($json, JSON_PRETTY_PRINT);
														$fff = str_replace('[', '', $output);
														$fff = str_replace(']', '', $fff);
														$json2 = json_decode($fff, true);
														$name = $json2['name'];
														$confidence = $json2['confidence'];
														$recogvalid = $json2['recogvalid'];
														$status = $json2['status'];
														
														
														
														
														$function_called = 'recognize voice';
														$user_id_log = $userIdforfile;
														$message = 'recognize voice ends '. 'output : ' . $fff;
														$this->log_table($function_called, $user_id_log, $message); 
																			
														
													
														 
														
														if(!empty(trim($name)) && !empty(trim($confidence)) )
														{
															
															$function_called = 'recognize face';
														$user_id_log = $userIdforfile;
														$message = 'Internal process ends'.$userIdforfile.'    '.$name;
														$this->log_table($function_called, $user_id_log, $message);
														
														$query6 = mysql_query("SELECT user_name as user_name,user_role as user_role
																			   FROM voice.voice_register_tbl 
																			   WHERE tvid = '$userIdforfile'
																				AND userid = '$name' limit 1
																				") or die(mysql_error());
														$row6 = mysql_fetch_array($query6) or die(mysql_error());
														
														
														
														$usernamedb = $row6['user_name'];
														$userroledb = $row6['user_role'];
															
															
															$success = array(
																		'status' => "success",
																		"msg" => "Success" ,
																		"user_id" => $name ,
																		"name" => $usernamedb , 
																		"role" => $userroledb , 
																		"confidence" => $confidence ,
																		"recogvalid" => $recogvalid ,
																		
															);
															$this->response($this->json($success) , 200); 
														}
														
														
														
														if (strcmp(trim(strtolower($status)),strtolower('UNError'))==0)
														
														{
															
																$function_called = 'recognize face';
														$user_id_log = $userIdforfile;
														$message = 'Internal server error outout';
														$this->log_table($function_called, $user_id_log, $message);
																			$error = array(
																			'status' => "Failed",
																			"msg" => "Internal Server Error" . $status
																			);
																			$this->response($this->json($error) , 500);
															
															
														}
														
														if (strcmp(trim(strtolower($status)),strtolower('InternalEFError'))==0)
														{
															
																$function_called = 'recognize face';
														$user_id_log = $userIdforfile;
														$message = 'Internal server error outout';
														$this->log_table($function_called, $user_id_log, $message);
																			$error = array(
																			'status' => "Failed",
																			"msg" => "Internal Server Error" . $status
																			);
																			$this->response($this->json($error) , 500);
															
														}
														
														if (strcmp(trim(strtolower($status)),strtolower('"InternalDBError'))==0)
														{
																$function_called = 'recognize face';
														$user_id_log = $userIdforfile;
														$message = 'Internal server error outout';
														$this->log_table($function_called, $user_id_log, $message);
																			$error = array(
																			'status' => "Failed",
																			"msg" => "Internal Server Error" . $status
																			);
																			$this->response($this->json($error) , 500);
															
															
														}
														
														
															if(empty(trim($status)) )
															{
																
																					$function_called = 'recognize face';
														$user_id_log = $userIdforfile;
														$message = 'Internal server error ststus null';
														$this->log_table($function_called, $user_id_log, $message);
																			$error = array(
																			'status' => "Failed",
																			"msg" => "Internal Server Error"
																			);
																			$this->response($this->json($error) , 500);
																
															}
														
														
														
															if(empty(trim($output)) )
															{
																
																					$function_called = 'recognize face';
														$user_id_log = $userIdforfile;
														$message = 'Internal server error outout';
														$this->log_table($function_called, $user_id_log, $message);
																			$error = array(
																			'status' => "Failed",
																			"msg" => "Internal Server Error"
																			);
																			$this->response($this->json($error) , 500);
																
															}
															
															if(empty(trim($name)) )
															{
																
																					$function_called = 'recognize face';
														$user_id_log = $userIdforfile;
														$message = 'Internal server error name' . $confidence ;
														$this->log_table($function_called, $user_id_log, $message);
														
														
														$error = array(
																			'status' => "Failed",
																			"msg" => "Internal Server Error"
																			);
																			$this->response($this->json($error) , 500);
														
														
														/* $success = array(
																		'status' => "success",
																		"msg" => "Success" ,
																		"user_id" => "XXX" ,
																		"name" => "XXX" , 
																		"role" => "M" , 
																		"confidence" => $confidence ,
																		"recogvalid" => $recogvalid ,
															);
															$this->response($this->json($success) , 200); 
															 */
															
																			/* $error = array(
																			'status' => "Failed",
																			"msg" => "Internal Server Error"
																			);
																			$this->response($this->json($error) , 437); */
																
															}
															
															
															
															
															
														
															
																		//$text = json_encode($json, JSON_PRETTY_PRINT);
																		//$json['file_number'] = $gen_file_no;
																		$this->response($this->json($json) , 200);
																			//$headerSent = curl_getinfo($ch); // request headers
																			
																			
																			// new part for request
																			//fclose($out);  
																			//$debug = ob_get_clean();
																		
																		
																		
															
														
															
															
																	//}
																	
																
				
														
												
										
						
				

				/* 	print_r($_FILES);
				$error = array('status' => "Failed", "msg" => "Invalid Email address or Password");
				$this->response($this->json($error), 400);
				*/
		}
		
		
	
		
	

		
		
		
		
		
		
		
		
		
		private
		function api_check()
		{

				// Cross validation if the request method is DELETE else it will return "Not Acceptable" status

				if ($this->get_request_method() != "GET")
				{
						$error = array(
										'status' => "Failed",
										"msg" => "Meathod GET is required"
								);
								$this->response($this->json($error) , 406);
				}

				
						
						$success = array(
								'status' => "Success",
								"msg" => "API is working fine on local server"
						);
						$this->response($this->json($success),200);
				
				
		}
		
		
		
		/*
		*	Encode array into JSON
		*/
		private
		function json($data)
		{
				if (is_array($data))
				{
						return json_encode($data);
				}
		}
}

// Initiiate Library

$api = new API;
$api->processApi();
?>
