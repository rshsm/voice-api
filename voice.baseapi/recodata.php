
<?php


$link = mysql_connect('localhost', 'root', '123456cH+');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
echo 'Connected successfully';

$shots = mysql_query("SELECT wav_full_path FROM voice.voice_recognition_tbl where tvid not in ('D75501022400H4370700001J','NOPID','D7W500943800G04701000002','20187724','wayne','testfornopid')
and substring(recognized_on,1,8) = DATE_SUB(CURRENT_DATE(),INTERVAL 1 DAY) limit 10;") or die(mysql_error());
echo 'Connected successfully';
 while($row = mysql_fetch_assoc($shots)) {
	
	echo $row["wav_full_path"];
	$wav =  $row["wav_full_path"] ;//'anyone/D75501022400H4370700000U/recognize/20170922105515349400/f/_20170922105522953486.wav';//$row["wav_full_path"];
	
	$wav2 =  str_replace('/','-',$wav);//'anyone/D75501022400H4370700000U/recognize/20170922105515349400/f/_20170922105522953486.wav';//$row["wav_full_path"];
	
	
	
	$foldername =  'anyone/zhhrecodata/'. 'zhh' .  date("dmY");
	
	
								$old_umask = umask(0);
								if (!is_dir($foldername)) {
								mkdir($foldername, 0777, true);
								}
	$src = $wav;  // source folder or file
$dest =$foldername.'/'.$wav2; //"zhh1/".$wav2;   // destination folder or file  

shell_exec("cp -r $src $dest");


} 







?>