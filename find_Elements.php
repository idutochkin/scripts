<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$find_Redirects = explode("\n", file_get_contents("find_Redirects.csv"));
$nextElement = explode(":", explode(";", end(explode("\n", file_get_contents("find_Elements.tmp"))))[2])[1];
$nextElement = $nextElement ? $nextElement : 0;
$replace = false;

CModule::IncludeModule("iblock");
$res = CIBlockElement::GetList(Array("ID"=>"ASC"), Array(">=ID" => $nextElement), false, false, array("IBLOCK_ID", "ID", "PREVIEW_TEXT", "DETAIL_TEXT"));
while($ob = $res->GetNextElement()) {
	$arFields = $ob->GetFields();
	foreach($find_Redirects as $key => $arFindString) {	
		if($key == 0)
			continue;
		
		$arFindString = explode(";", $arFindString);
		$findString = trim($arFindString[0])."\"";
		$replaceString = trim($arFindString[1])."\"";	
		
		if($replace) {
			if(stripos($arFields["PREVIEW_TEXT"], $findString) !== false) {
				$el = new CIBlockElement;
				$el->Update($arFields["ID"], array("PREVIEW_TEXT" => str_replace($findString, $replaceString, $arFields["PREVIEW_TEXT"])));		
				
				file_put_contents("find_ElementsReplaced.tmp", "\nKEY:".$key.";IBLOCK_ID:".$arFields["IBLOCK_ID"].";ELEMENT_ID:".$arFields["ID"], FILE_APPEND);
			}
			if(stripos($arFields["DETAIL_TEXT"], $findString) !== false) {
				$el = new CIBlockElement;
				$el->Update($arFields["ID"], array("DETAIL_TEXT" => str_replace($findString, $replaceString, $arFields["DETAIL_TEXT"])));
				
				file_put_contents("find_ElementsReplaced.tmp", "\nKEY:".$key.";IBLOCK_ID:".$arFields["IBLOCK_ID"].";ELEMENT_ID:".$arFields["ID"], FILE_APPEND);
			}
		}
	}
	
	file_put_contents("find_Elements.tmp", "\nKEY:".$key.";IBLOCK_ID:".$arFields["IBLOCK_ID"].";ELEMENT_ID:".$arFields["ID"], FILE_APPEND);
}
?>