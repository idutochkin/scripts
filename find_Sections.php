<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$find_Redirects = explode("\n", file_get_contents("find_Redirects.csv"));
$nextSection = explode(":", explode(";", end(explode("\n", file_get_contents("find_Sections.tmp"))))[2])[1];
$nextSection = $nextSection ? $nextSection : 0;
$replace = true;

CModule::IncludeModule("iblock");
$res = CIBlockSection::GetList(Array("ID"=>"ASC"), Array(">=ID" => $nextSection), true);
while($arFields = $res->GetNext()) {
	foreach($find_Redirects as $key => $arFindString) {
		if($key == 0)
			continue;
		
		$arFindString = explode(";", $arFindString);
		$findString = trim($arFindString[0])."\"";
		$replaceString = trim($arFindString[1])."\"";	
		
		if($replace) {
			if(stripos($arFields["DESCRIPTION"], $findString) !== false) {
				$bs = new CIBlockSection;
				$bs->Update($arFields["ID"], array("DESCRIPTION" => str_replace($findString, $replaceString, $arFields["DESCRIPTION"])));
		
				file_put_contents("find_SectionsReplaced.tmp", "\nKEY:".$key.";IBLOCK_ID:".$arFields["IBLOCK_ID"].";SECTION_ID:".$arFields["ID"], FILE_APPEND);
			}
		}
	}
	
	file_put_contents("find_Sections.tmp", "\nKEY:".$key.";IBLOCK_ID:".$arFields["IBLOCK_ID"].";SECTION_ID:".$arFields["ID"], FILE_APPEND);
}
?>