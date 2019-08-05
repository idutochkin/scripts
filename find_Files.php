<?
$arFiles = DirFilesR(".");
$find_Redirects = explode("\n", file_get_contents("find_Redirects.csv"));
$skipArray = array(".mp4", ".png", ".jpg", ".JPG", ".pdf", ".gif", ".swf");
$nextKey = explode(":", explode(";", end(explode("\n", file_get_contents("find_Files.tmp"))))[0])[1];
$nextKey = $nextKey ? $nextKey : 0;
$replace = true;

foreach($find_Redirects as $key => $arFindString) {
	if($key == 0 || $key < $nextKey)
		continue;
	
	$arFindString = explode(";", $arFindString);
	$findString = trim($arFindString[0])."\"";
	$replaceString = trim($arFindString[1])."\"";
	
	foreach($arFiles as $file) {
		if(in_array(substr($file, -4), $skipArray))
			continue;
		
		if($replace) {
			$content = file_get_contents($file);
			if(strpos($content, $findString) !== false) {
				file_put_contents(substr($file, 2), str_replace($findString, $replaceString, $content));
				
				file_put_contents("find_FilesReplaced.tmp", "\nKEY:".$key.";FILE:".$file, FILE_APPEND);
			}
		}
	}
	
	file_put_contents("find_Files.tmp", "\nKEY:".$key.";FILE:".$file, FILE_APPEND);
}

function DirFilesR($dir) {
	$handle = opendir($dir) or die("Can't open directory ".$dir);
	$files = Array();
	$subfiles = Array();
	while(false !== ($file = readdir($handle))) {
		if($file != "." && $file != "..") {
			if(is_dir($dir."/".$file)) {
				if(
					$dir."/".$file != "./.git" && 
					$dir."/".$file != "./.git.bak" && 
					$dir."/".$file != "./1_files" && 
					$dir."/".$file != "./_images" && 
					$dir."/".$file != "./upload" && 
					$dir."/".$file != "./_upload" && 
					$dir."/".$file != "./_upload_old" && 
					$dir."/".$file != "./webstat" && (
						substr($dir."/".$file, 0, 9) != "./bitrix/" || ( 
							substr($dir."/".$file, 0, 9) == "./bitrix/" && (
								substr($dir."/".$file, 0, 18) == "./bitrix/templates" || 
								substr($dir."/".$file, 0, 22) == "./bitrix/templates.old"
							)
						)
					)
				)
					$subfiles = DirFilesR($dir."/".$file);
				$files = array_merge($files,$subfiles);
			} else {
				$files[] = $dir."/".$file;
			}
		}
	}
	closedir($handle);
	return $files;
}
?>