<?php


//
//                       _oo0oo_
//                      o8888888o
//                      88" . "88
//                      (| -_- |)
//                      0\  =  /0
//                    ___/`---'\___
//                  .' \\|     |// '.
//                 / \\|||  :  |||// \
//                / _||||| -:- |||||- \
//               |   | \\\  -  /// |   |
//               | \_|  ''\---/''  |_/ |
//               \  .-\__  '-'  ___/-. /
//             ___'. .'  /--.--\  `. .'___
//          ."" '<  `.___\_<|>_/___.' >' "".
//         | | :  `- \`.;`\ _ /`;.`/ - ` : | |
//         \  \ `_.   \_ __\ /__ _/   .-` /  /
//     =====`-.____`.___ \_____/___.-`___.-'=====
//                       `=---='
//
//
//     ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
//               佛祖保佑         永无BUG
//
	
	class REST {
		
		public $_allow = array();
		public $_content_type = "application/json";
		public $_request = array();
		
		private $_method = "";		
		private $_code = 200;
		
		public function __construct(){
			$this->inputs();
		}
		
		public function get_referer(){
			return $_SERVER['HTTP_REFERER'];
		}
		
		public function response($data,$status){
			$this->_code = ($status)?$status:200;
			$this->set_headers();
			echo $data;
			exit;
		}
		
		private function get_status_message(){
			$status = array(
						100 => 'Continue',  
						101 => 'Switching Protocols',  
						200 => 'OK',
						201 => 'Created',  
						202 => 'Accepted',  
						203 => 'Non-Authoritative Information',  
						204 => 'No Content',  
						205 => 'Reset Content',  
						206 => 'Partial Content',
						207 => 'New version avilable',
						208 => 'Already have new version',							
						300 => 'Multiple Choices',  
						301 => 'Moved Permanently',  
						302 => 'Found',  
						303 => 'See Other',  
						304 => 'Not Modified',  
						305 => 'Use Proxy',  
						306 => '(Unused)',  
						307 => 'Temporary Redirect',  
						400 => 'Bad Request',  
						401 => 'Unauthorized',  
						402 => 'Payment Required',  
						403 => 'Forbidden',  
						404 => 'Not Found',  
						405 => 'Method Not Allowed',  
						406 => 'Not Acceptable',  
						407 => 'Proxy Authentication Required',  
						408 => 'Request Timeout',  
						409 => 'Conflict',  
						410 => 'Gone',  
						411 => 'Length Required',  
						412 => 'Precondition Failed',  
						413 => 'Request Entity Too Large',  
						414 => 'Request-URI Too Long',  
						415 => 'Unsupported Media Type',  
						416 => 'Requested Range Not Satisfiable',  
						417 => 'Expectation Failed',  
						419 => 'Invalid Email Address',  
						418 => 'Invalid Name',  
						420 => 'User already exist',  
						421 => 'Unexpected File error',  
						422 => 'File cannot be uploaded',  
						423 => 'Audio File error',  
						424 => 'User already exist',
						425 => 'Application ID is needed',
						426 => 'TV ID already exist',
						427 => 'Registered Name too long',  
						428 => 'User does not exist', 
						429 => 'Incorrect Password',
						430 => 'Checkin Time was not retrived',  
						431 => 'code doesnt match',  	
						432 => 'User doesnt opted for changing password',  							
						433 => 'Company code is needed',  	
						434 => 'Invalid Company code ',  
						435 => 'No name from server ',  						
						436 => 'Result mismatch ',  	
						437 => 'Recognition Failed ',  
						438 => 'Company already exist ',  						
						439 => 'Pre registration failed ',  
						440 => 'Company Registration Failed',	
						441 => 'Company does not exist',	
						442 => 'Telephone already exist',	
						443 => 'Email already exist',	
						444 => 'Code does not match',	
						445 => 'Deletion Failed',
						446 => 'Folder Management error',
						447 => 'TV ID too long',	
						450 => 'Registration Failed ',  
						451 => 'TV ID is required',
						452 => 'TV name is required',
						453 => 'User name is required',
						454 => 'Role is required',
						455 => 'Invalid role value',
						456 => 'Job is required',
						457 => 'Invalid job value',
						458 => 'Validation error',
						459 => 'TV ID does not exist',
						480 => 'Quota Reached',							
						500 => 'Internal Server Error',  
						501 => 'Not Implemented',  
						502 => 'Bad Gateway',  
						503 => 'Service Unavailable',  
						504 => 'Gateway Timeout',  
						505 => 'HTTP Version Not Supported');
			return ($status[$this->_code])?$status[$this->_code]:$status[500];
		}
		
		public function get_request_method(){
			return $_SERVER['REQUEST_METHOD'];
		}
		
		private function inputs(){
			switch($this->get_request_method()){
				case "POST":
					$this->_request = $this->cleanInputs($_POST);
					break;
				case "GET":
				case "DELETE":
					$this->_request = $this->cleanInputs($_GET);
					break;
				case "PUT":
					parse_str(file_get_contents("php://input"),$this->_request);
					$this->_request = $this->cleanInputs($this->_request);
					break;
				default:
					$this->response('',406);
					break;
			}
		}		
		
		private function cleanInputs($data){
			$clean_input = array();
			if(is_array($data)){
				foreach($data as $k => $v){
					$clean_input[$k] = $this->cleanInputs($v);
				}
			}else{
				if(get_magic_quotes_gpc()){
					$data = trim(stripslashes($data));
				}
				$data = strip_tags($data);
				$clean_input = trim($data);
			}
			return $clean_input;
		}		
		
		private function set_headers(){
			header("HTTP/1.1 ".$this->_code." ".$this->get_status_message());
			header("Content-Type:".$this->_content_type);
		}
	}	
?>