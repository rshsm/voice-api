<?php
 //php.ini
//my.cnf
function multiRequest($data, $options = array()) {
 
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
	curl_setopt($curly[$id], CURLOPT_SAFE_UPLOAD, false);
	//time out 
				curl_setopt($curly[$id], CURLOPT_CONNECTTIMEOUT ,10); 
				curl_setopt($curly[$id], CURLOPT_TIMEOUT, 400); //timeout in seconds
 
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

$data = array(array(),array());

$i = 0;		

	$inputdir =	'recognizebulk/';

foreach(glob($inputdir.'*.*') as $file) {
	
	//echo $file;
	
	$data[$i]['url']  = 'http://116.62.184.12/voice.baseapi/v0/base_user_recognition?tv_id=testfornopid&dataflag=false&distance=n';
$data[$i]['post'] = array();
$data[$i]['post']['audiofile']   = '@'.$file;
	
	$i = $i+1;
	
	
	
}

$time_start = microtime(true); 
$r = multiRequest($data);
echo 'Total execution time in seconds: ' . (microtime(true) - $time_start);
echo '</br>';

 echo '</br>';
print_r($r);
echo 'Total execution time for whole process in seconds: ' . (microtime(true) - $time_start);
 
?>