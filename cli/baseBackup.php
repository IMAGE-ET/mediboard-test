<?php 
require_once ("utils.php");

function baseBackup($method, $username, $password, $hostname, $port, $database, $backupPath, $time, $binary, $loginUsername, $loginPassword) {
    
    $currentDir = dirname(__FILE__);
    
    announce_script("Database daily backup");
    
    if ($hostname === "") {
    	
		$hostname = "localhost";
    }
	if ($port === "") {
		
		$port = "3306";
	}
	if ($time === "") {
    
        $time = "7";
    }
    if ($binary === "y") {
    
        $binary = true;
    }
	else {
		
		$binary = false;
	}
    if ($loginUsername === "") {
    
        $loginUsername = "";
        $loginPassword = "";
    }

    info_script("Backuping ".$database." database");
    
    // Make complete path //
    // Make shell path
    $SHELL_PATH = $currentDir;
    
    // Make backup path
    force_dir($backupPath);
    
    // Make database path
    $BASE_PATH = $backupPath."/".$database."-db";
    force_dir($BASE_PATH);
    chdir($BASE_PATH);
    
    // If no enough free disk space (1.5 * size of database), send mail if provided and quit
    $mysql_conf = shell_exec("find /etc -name my.cnf 2>/dev/null|head -n 1");
    $mysql_conf = trim($mysql_conf);
    
    $mysql_data_root = "";
    $lines = file($mysql_conf);
    foreach ($lines as $line_num=>$line) {
    
        if (preg_match("/^(datadir)/m", $line)) {
        
            $datadirPOS = strpos($line, "=");
            $mysql_data_root = trim(substr($line, $datadirPOS + 1));
        }
    }
    $dir = opendir($mysql_data_root);
    check_errs($dir, false, "Unable to determine MySQL data root", "MySQL data root found!");
    closedir($dir);
    
    $mysql_data_base = $mysql_data_root."/".trim($database);

    $database_size = 0;
    $database_files = getFiles($mysql_data_base."/");
    
    if ($database_files) {
    
        foreach ($database_files as $one_database_file) {
        
            $database_size += filesize($one_database_file);
        }
    }
    
    // Expanded size (database + tar)
    $needed_size = $database_size * 3 / 2;
    $available_size = disk_free_space($backupPath);
    
    if ($available_size < $needed_size) {
    
        if ($loginUsername != "") {
        
            info_script("Send a mail using ".$loginUsername." login");
            // Name of the instance of mediboard
            $instance = basename(dirname($currentDir));
            file_get_contents("http://localhost/".$instance."/?login=".$loginUsername.":".$loginPassword."&m=system&a=ajax_send_mail_diskfull");
        }
    }
    check_errs(($available_size < $needed_size), 1, "Needed space ".formatSize($needed_size)." exceeds available space ".formatSize($available_size), "Enough available space!");
    
    // Male MySQL method //
    
    // removes previous hotcopy/dump if something went wrong
    rrmdir($database);
    
    $DATETIME = date('Y-m-d\TH-i-s');
    
    switch ($method) {
    
        case "hotcopy":
        
            $result = $database."/";
            
            $mysqlhotcopy = shell_exec("mysqlhotcopy -h " . $hostname . " -P " . $port . " -u ".$username." -p ".$password." ".$database." ".$BASE_PATH);
            check_errs($mysqlhotcopy, NULL, "Failed to create MySQL hot copy", "MySQL hot copy done!");
            
            if ($binary) {
            
                $databasebinlog = $database . "-" . $DATETIME . ".binlog.position";
                $link = mysql_connect($hostname . ":" . $port, $username, $password);
                check_errs($link, false, "Could not connect : ".mysql_error(), "Connected!");
				
				if (!($link)) {
					
					return 0;
				}
				
				$query = "SHOW MASTER STATUS";
				$res = mysql_query($query);
				mysql_close($link);
				
				$row = 0;
				if ($res) {
					
					$row = mysql_fetch_object($res);
				}
				
				$a = 0;
				if ($row) {
					
					$file = fopen($backupPath . "/binlog-" . $DATETIME . ".index", "w");

	                if ($file) {
	                	
						fwrite($file, "File            Position	Binlog_Do_DB	Binlog_Ignore_DB
");
						fwrite($file, $row->File . "	" . $row->Position . "	        " . $row->Binlog_Do_DB . "	" . $row->Binlog_Ignore_DB . "\n");
						fclose($file);
						$a = 1;
	                }
				}
				check_errs($a, 0, "Failed to create MySQL Binary log index", "MySQL Binary log index done!");
            }
            
            break;
            
        case "dump":
        
            $result = $database . ".sql";
			
			$mysqldump = shell_exec("mysqldump --opt -u " . $username . " -p" . $password . " -h " . $hostname . " -P " . $port . " " . $database);
			check_errs($mysqldump, NULL, "Failed to create MySQL dump", "MySQL dump done!");
			
			$file = fopen($result, "w");
			$a = 0;
			if ($file) {
				
				fwrite($file, $mysqldump);
				fclose($file);
				$a = 1;
			}
			check_errs($a, 0, "Failed to save MySQL dump file", "MySQL dump file saved!");
			
            break;
			
		default:
			$result = $database . "/";
			echo "Choose hotcopy or dump method\n";
			return 0;
    }
	
	// Rotating files older than n days, all files if 0
	if (!($time)) {
		
		exec("find " . $BASE_PATH . " -name '" . $database . "*.tar.gz'", $find, $return_var);

		foreach ($find as $one_file) {
			
			unlink($one_file);
		}
		check_errs($return_var, true, "Failed to rotate files", "Files rotated");
	}
	else {
		
		$filter = "-ctime +" . $time;
		exec("find " . $BASE_PATH . " -name '" . $database . "*.tar.gz' " . $filter, $find, $return_var);
		
		foreach ($find as $one_file) {
			
			unlink($one_file);
		}
		check_errs($return_var, true, "Failed to rotate files", "Files rotated");
	}
	
	// Compress archive and remove file //
	
	// Make the tarball
	$tarball = $database . "-" . $DATETIME . ".tar.gz";
	exec("tar cfz " . $tarball . " " . $result, $tar, $return_var);
	check_errs($return_var, true, "Failed to create backup tarball", "Tarball packaged!");
	
	// Create a symlink
	@unlink($database . "-latest.tar.gz");
	$res = symlink($tarball, $database . "-latest.tar.gz");
	check_errs($res, false, "Failed to create symlink", "Symlink created!");
	
	// Remove temporay files
	$a = 0;
	if (is_dir($result)) {
		
		if (rrmdir($result)) {
			
			$a = 1;
		}
	}
	else {
		
		if (unlink($result)) {
			
			$a = 1;
		}
	}
	check_errs($a, false, "Failed to clean MySQL files", "MySQL files cleaning done!");	
}

// From http://fr2.php.net/manual/fr/function.rmdir.php
function rrmdir($dir) {
	$a = 0;
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir."/".$object) == "dir")
                    rrmdir($dir."/".$object);
                else
                    unlink($dir."/".$object);
            }
        }
        reset($objects);
        if (rmdir($dir)) {
        	
			$a = 1;
        }
    }
	return $a;
}

// From http://fr.php.net/manual/fr/function.opendir.php
function getFiles($directory, $exempt = array('.', '..', '.ds_store', '.svn'), &$files = array()) {
    $handle = opendir($directory);
    while (false !== ($resource = readdir($handle))) {
        if (!in_array(strtolower($resource), $exempt)) {
            if (is_dir($directory.$resource.'/'))
                array_merge($files, self::getFiles($directory.$resource.'/', $exempt, $files));
            else
                $files[] = $directory.$resource;
        }
    }
    closedir($handle);
    return $files;
}

// From http://www.php.net/manual/fr/function.disk-total-space.php
function formatSize($size) {
    switch (true) {
        case ($size > 1099511627776):
            $size /= 1099511627776;
            $suffix = 'TB';
            break;
        case ($size > 1073741824):
            $size /= 1073741824;
            $suffix = 'GB';
            break;
        case ($size > 1048576):
            $size /= 1048576;
            $suffix = 'MB';
            break;
        case ($size > 1024):
            $size /= 1024;
            $suffix = 'KB';
            break;
        default:
            $suffix = 'B';
    }
    return round($size, 2).$suffix;
}
?>
