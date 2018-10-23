<?php
//require_once ("Rest.inc.php");
//slave 2
				$serverIP2 = "testfolder/";
													$old_umask = umask(0);
													mkdir($serverIP2, 0777, true);
													
													$serverIP3 = "testfolder/test";
													$old_umask = umask(0);
													mkdir($serverIP3, 0777, true);
													
													
													echo "done";
								

?>