<?php
/* <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> */
/**
	* @return boolean
	* @param $MailFrom, $MailTo, $Subject, $Message
	* @desc メール送信処理
*/

function gpcInit(){
if (get_magic_quotes_gpc()) {
  function strip_magic_quotes_slashes($arr){
    return is_array($arr) ?
      array_map('strip_magic_quotes_slashes', $arr) :
      stripslashes($arr);
  }
  $_GET     = strip_magic_quotes_slashes($_GET);
  $_POST    = strip_magic_quotes_slashes($_POST);
  $_REQUEST = strip_magic_quotes_slashes($_REQUEST);
  $_COOKIE  = strip_magic_quotes_slashes($_COOKIE);
}
	return;
}

function initStr($item){
	if ( is_array($item) == TRUE) {
			$item = array_map('initStr', $item);
	} else {

	$item = gaiji($item);
	$item = replaceText($item);
	$item = han_kaku_to_jen_kaku($item);

	$item = mb_convert_encoding($item, "SJIS-win", "UTF-8");
	$item = mb_convert_encoding($item, "JIS", "SJIS-win");
	$item = mb_convert_encoding($item, "UTF-8", "JIS");

			$item = trim(htmlspecialchars($item,3));
			$item = trim(mb_convert_kana( $item, "s"));
	}
	return $item;
}




function MailSender($MailFrom="", $MailTo="", $MailTo2="", $Subject="", $Message=""){
	// Subject部分を変換
	$xSubject = mb_convert_encoding($Subject, "iso-2022-jp", "UTF-8");
//	$xSubject = mb_convert_encoding($Subject, "SJIS", "UTF-8");
	$xSubject = base64_encode($xSubject);
	$xSubject = "=?iso-2022-jp?B?".$xSubject."?=";
//	$xSubject = "=?SHIFT_JIS?B?".$xSubject."?=";
	
	// Message部分を変換
	$xMessage = replaceText($Message);
	$xMessage = han_kaku_to_jen_kaku($xMessage);
	//$xMessage = $Message;
	//$xMessage = htmlspecialchars($Message);
	//$xMessage = str_replace("&amp;", "&", $xMessage);
	//if (get_magic_quotes_gpc()) $xMessage = stripslashes($xMessage);
	$xMessage = str_replace("\r\n", "\r", $xMessage);
	$xMessage = str_replace("\r", "\n", $xMessage);	
	//$xMessage = mb_convert_encoding($xMessage, "iso-2022-jp", "UTF-8");
	//$xMessage = mb_convert_encoding($xMessage, "JIS", "UTF-8");
	$xMessage = mb_convert_encoding($xMessage, "SJIS-win", "UTF-8");
	$xMessage = mb_convert_encoding($xMessage, "JIS", "SJIS-win");

	// Header部分を生成	
	$GMT = date("Z");
	$GMT_ABS  = abs($GMT);
	$GMT_HOUR = floor($GMT_ABS / 3600);
	$GMT_MIN = floor(($GMT_ABS - $GMT_HOUR * 3600) / 60);
	if ($GMT >= 0) $GMT_FLG = "+"; else $GMT_FLG = "-";
	$GMT_RFC = date("D, d M Y H:i:s ").sprintf($GMT_FLG."%02d%02d", $GMT_HOUR, $GMT_MIN);

	$Headers  = "Date: ".$GMT_RFC."\n";
	$Headers .= "From: $MailFrom\n";
	if(!$MailTo2 == ""){
	$Headers .= "Bcc: $MailTo2\n";
	}
	$Headers .= "Subject: $xSubject\n";
	$Headers .= "MIME-Version: 1.0\n";
	$Headers .= "X-Mailer: PHP/".phpversion()."\n";
//	$Headers .= "Content-type: text/plain; charset=Shift-JIS\n";
	$Headers .= "Content-type: text/plain; charset=ISO-2022-JP\n";
	$Headers .= "Content-Transfer-Encoding: 7bit";
	
	// メール送信実行
	if(mail($MailTo, $xSubject, $xMessage, $Headers)) return true;
	return false;
}


function gaiji($str){
$replace_of=array("髙","﨑","〝","〟","∮","∟","⊿","炻","仼","僴","凬","匇","匤","﨎","咊","坙","﨏","增","寬","峵","嵓","德","悅","愠","敎","昻","晥");
$replace_by=array("高","崎","\"","\"","","└","△","[火石]","[イ王]","[イ間]","風","匆","匡","[文隹又]","和","[ケイ]","[土谷]","増","寛","[山宏]","岩","徳","悦","","教","昴","");
$_result = str_replace($replace_of, $replace_by, $str);
return $_result;
}




function han_kaku_to_jen_kaku($str){
$replace_of = array('ｳﾞ','ｶﾞ','ｷﾞ','ｸﾞ',
										'ｹﾞ','ｺﾞ','ｻﾞ','ｼﾞ',
										'ｽﾞ','ｾﾞ','ｿﾞ','ﾀﾞ',
										'ﾁﾞ','ﾂﾞ','ﾃﾞ','ﾄﾞ',
										'ﾊﾞ','ﾋﾞ','ﾌﾞ','ﾍﾞ',
										'ﾎﾞ','ﾊﾟ','ﾋﾟ','ﾌﾟ','ﾍﾟ','ﾎﾟ');
$replace_by = array('ヴ','ガ','ギ','グ',
										'ゲ','ゴ','ザ','ジ',
										'ズ','ゼ','ゾ','ダ',
										'ヂ','ヅ','デ','ド',
										'バ','ビ','ブ','ベ',
										'ボ','パ','ピ','プ','ペ','ポ');
$_result = str_replace($replace_of, $replace_by, $str);

$replace_of = array('ｱ','ｲ','ｳ','ｴ','ｵ',
										'ｶ','ｷ','ｸ','ｹ','ｺ',
										'ｻ','ｼ','ｽ','ｾ','ｿ',
										'ﾀ','ﾁ','ﾂ','ﾃ','ﾄ',
										'ﾅ','ﾆ','ﾇ','ﾈ','ﾉ',
										'ﾊ','ﾋ','ﾌ','ﾍ','ﾎ',
										'ﾏ','ﾐ','ﾑ','ﾒ','ﾓ',
										'ﾔ','ﾕ','ﾖ','ﾗ','ﾘ',
										'ﾙ','ﾚ','ﾛ','ﾜ','ｦ',
										'ﾝ','ｧ','ｨ','ｩ','ｪ',
										'ｫ','ヵ','ヶ','ｬ','ｭ',
										'ｮ','ｯ','､','｡','ｰ',
										'｢','｣','ﾞ','ﾟ');
$replace_by = array('ア','イ','ウ','エ','オ',
										'カ','キ','ク','ケ','コ',
										'サ','シ','ス','セ','ソ',
										'タ','チ','ツ','テ','ト',
										'ナ','ニ','ヌ','ネ','ノ',
										'ハ','ヒ','フ','ヘ','ホ',
										'マ','ミ','ム','メ','モ',
										'ヤ','ユ','ヨ','ラ','リ',
										'ル','レ','ロ','ワ','ヲ',
										'ン','ァ','ィ','ゥ','ェ',
										'ォ','ヶ','ヶ','ャ','ュ',
										'ョ','ッ','、','。','ー',
										'「','」','"','');       
$_result = str_replace($replace_of, $replace_by, $_result);
return $_result;
}


function replaceText($str){
	$arr = array(
		/* --- 0x2100 - 0x2138 (文字種記号) --- */
		// 0x2100 - 0x210F
		"\xE2\x84\x80" => "a/c",
		"\xE2\x84\x81" => "a/s",
		"\xE2\x84\x82" => "C",
		"\xE2\x84\x83" => "?",
		"\xE2\x84\x84" => "?",
		"\xE2\x84\x85" => "c/o",
		"\xE2\x84\x86" => "c/u",
		"\xE2\x84\x87" => "?",
		"\xE2\x84\x88" => "?",
		"\xE2\x84\x89" => "F",
		"\xE2\x84\x8A" => "g",
		"\xE2\x84\x8B" => "?",
		"\xE2\x84\x8C" => "?",
		"\xE2\x84\x8D" => "?",
		"\xE2\x84\x8E" => "?",
		"\xE2\x84\x8F" => "?",
		// 0x2110 - 0x211F
		"\xE2\x84\x90" => "?",
		"\xE2\x84\x91" => "?",
		"\xE2\x84\x92" => "?",
		"\xE2\x84\x93" => "?",
		"\xE2\x84\x94" => "?",
		"\xE2\x84\x95" => "?",
		"\xE2\x84\x96" => "No.",
		"\xE2\x84\x97" => "?",
		"\xE2\x84\x98" => "?",
		"\xE2\x84\x99" => "?",
		"\xE2\x84\x9A" => "?",
		"\xE2\x84\x9B" => "?",
		"\xE2\x84\x9C" => "?",
		"\xE2\x84\x9D" => "?",
		"\xE2\x84\x9E" => "?",
		"\xE2\x84\x9F" => "?",
		// 0x2120 - 0x212F
		"\xE2\x84\xA0" => "SM",
		"\xE2\x84\xA1" => "TEL",
		"\xE2\x84\xA2" => "TM",
		"\xE2\x84\xA3" => "?",
		"\xE2\x84\xA4" => "?",
		"\xE2\x84\xA5" => "?",
		"\xE2\x84\xA6" => "?",
		"\xE2\x84\xA7" => "?",
		"\xE2\x84\xA8" => "?",
		"\xE2\x84\xA9" => "?",
		"\xE2\x84\xAA" => "?",
		"\xE2\x84\xAB" => "?",
		"\xE2\x84\xAC" => "?",
		"\xE2\x84\xAD" => "?",
		"\xE2\x84\xAE" => "?",
		"\xE2\x84\xAF" => "?",
		// 0x2130 - 0x2138
		"\xE2\x84\xB0" => "e",
		"\xE2\x84\xB1" => "?",
		"\xE2\x84\xB2" => "?",
		"\xE2\x84\xB3" => "M",
		"\xE2\x84\xB4" => "o",
		"\xE2\x84\xB5" => "?",
		"\xE2\x84\xB6" => "?",
		"\xE2\x84\xB7" => "?",
		"\xE2\x84\xB8" => "?",
 
		/* ref
		* 機種依存文字とUnicode - WebStudio
		* http://www.d-toybox.com/studio/lib/romanNumerals.html
		*/
		/* --- 0x2150 - 0x2183 (数字の形) --- */
		// 0x2153 - 0x215F (分数)
		"\xE2\x85\x93" => "1/3",
		"\xE2\x85\x94" => "2/3",
		"\xE2\x85\x95" => "1/5",
		"\xE2\x85\x96" => "2/5",
		"\xE2\x85\x97" => "3/5",
		"\xE2\x85\x98" => "4/5",
		"\xE2\x85\x99" => "1/6",
		"\xE2\x85\x9A" => "5/6",
		"\xE2\x85\x9B" => "1/8",
		"\xE2\x85\x9C" => "3/8",
		"\xE2\x85\x9D" => "5/8",
		"\xE2\x85\x9E" => "7/8",
		"\xE2\x85\x9F" => "1/ ",
 
		// 0x2160 - 0x216F (ローマ数字 : 大文字)
		"\xE2\x85\xA0" => "I",
		"\xE2\x85\xA1" => "II",
		"\xE2\x85\xA2" => "III",
		"\xE2\x85\xA3" => "IV",
		"\xE2\x85\xA4" => "V",
		"\xE2\x85\xA5" => "VI",
		"\xE2\x85\xA6" => "VII",
		"\xE2\x85\xA7" => "VIII",
		"\xE2\x85\xA8" => "IX",
		"\xE2\x85\xA9" => "X",
		"\xE2\x85\xAA" => "XI",
		"\xE2\x85\xAB" => "XII",
		"\xE2\x85\xAC" => "L",
		"\xE2\x85\xAD" => "C",
		"\xE2\x85\xAE" => "D",
		"\xE2\x85\xAF" => "M",
 
		// 0x2170 - 0x217F (ローマ数字 : 小文字)
		"\xE2\x85\xB0" => "i",
		"\xE2\x85\xB1" => "ii",
		"\xE2\x85\xB2" => "iii",
		"\xE2\x85\xB3" => "iv",
		"\xE2\x85\xB4" => "v",
		"\xE2\x85\xB5" => "vi",
		"\xE2\x85\xB6" => "vii",
		"\xE2\x85\xB7" => "viii",
		"\xE2\x85\xB8" => "ix",
		"\xE2\x85\xB9" => "x",
		"\xE2\x85\xBA" => "xi",
		"\xE2\x85\xBB" => "xii",
		"\xE2\x85\xBC" => "l",
		"\xE2\x85\xBD" => "c",
		"\xE2\x85\xBE" => "d",
		"\xE2\x85\xBF" => "m",
 
		// 0x2180 - 0x2182 (ローマ数字: 別表記)
		"\xE2\x86\x80" => "M",
		"\xE2\x86\x81" => "(5000)",
		"\xE2\x86\x82" => "(10000)",
 
		/* --- 0x2190 - 0x219F (矢印) --- */
		// 0x2190 - 0x219F
		"\xE2\x86\x90" => "->",
		"\xE2\x86\x91" => "(上矢印)",
		"\xE2\x86\x92" => "<-",
		"\xE2\x86\x93" => "(下矢印)",
		"\xE2\x86\x94" => "<->",
		"\xE2\x86\x95" => "(上下矢印)",
		"\xE2\x86\x96" => "(左上矢印)",
		"\xE2\x86\x97" => "(右上矢印)",
		"\xE2\x86\x98" => "(右下矢印)",
		"\xE2\x86\x99" => "(左下矢印)",
		"\xE2\x86\x9A" => "<-/-",
		"\xE2\x86\x9B" => "-/->",
		"\xE2\x86\x9C" => "<~",
		"\xE2\x86\x9D" => "~>",
		"\xE2\x86\x9E" => "<<--",
		"\xE2\x86\x9F" => "-->>",
 
		/* --- 0x2460 - 0x24EF (囲み英数字) --- */
		// 0x2460 - 0x246F
		"\xE2\x91\xA0" => "(1)",
		"\xE2\x91\xA1" => "(2)",
		"\xE2\x91\xA2" => "(3)",
		"\xE2\x91\xA3" => "(4)",
		"\xE2\x91\xA4" => "(5)",
		"\xE2\x91\xA5" => "(6)",
		"\xE2\x91\xA6" => "(7)",
		"\xE2\x91\xA7" => "(8)",
		"\xE2\x91\xA8" => "(9)",
		"\xE2\x91\xA9" => "(10)",
		"\xE2\x91\xAA" => "(11)",
		"\xE2\x91\xAB" => "(12)",
		"\xE2\x91\xAC" => "(13)",
		"\xE2\x91\xAD" => "(14)",
		"\xE2\x91\xAE" => "(15)",
		"\xE2\x91\xAF" => "(16)",
 
		// 0x2470 - 0x247F
		"\xE2\x91\xB0" => "(17)",
		"\xE2\x91\xB1" => "(18)",
		"\xE2\x91\xB2" => "(19)",
		"\xE2\x91\xB3" => "(20)",
		"\xE2\x91\xB4" => "(1)",
		"\xE2\x91\xB5" => "(2)",
		"\xE2\x91\xB6" => "(3)",
		"\xE2\x91\xB7" => "(4)",
		"\xE2\x91\xB8" => "(5)",
		"\xE2\x91\xB9" => "(6)",
		"\xE2\x91\xBA" => "(7)",
		"\xE2\x91\xBB" => "(8)",
		"\xE2\x91\xBC" => "(9)",
		"\xE2\x91\xBD" => "(10)",
		"\xE2\x91\xBE" => "(11)",
		"\xE2\x91\xBF" => "(12)",
 
		// 0x2480 - 0x248F
		"\xE2\x92\x80" => "(13)",
		"\xE2\x92\x81" => "(14)",
		"\xE2\x92\x82" => "(15)",
		"\xE2\x92\x83" => "(16)",
		"\xE2\x92\x84" => "(17)",
		"\xE2\x92\x85" => "(18)",
		"\xE2\x92\x86" => "(19)",
		"\xE2\x92\x87" => "(20)",
		"\xE2\x92\x88" => "1.",
		"\xE2\x92\x89" => "2.",
		"\xE2\x92\x8A" => "3.",
		"\xE2\x92\x8B" => "4.",
		"\xE2\x92\x8C" => "5.",
		"\xE2\x92\x8D" => "6.",
		"\xE2\x92\x8E" => "7.",
		"\xE2\x92\x8F" => "8.",
 
		// 0x2490 - 0x249F
		"\xE2\x92\x90" => "9.",
		"\xE2\x92\x91" => "10.",
		"\xE2\x92\x92" => "11.",
		"\xE2\x92\x93" => "12.",
		"\xE2\x92\x94" => "13.",
		"\xE2\x92\x95" => "14.",
		"\xE2\x92\x96" => "15.",
		"\xE2\x92\x97" => "16.",
		"\xE2\x92\x98" => "17.",
		"\xE2\x92\x99" => "18.",
		"\xE2\x92\x9A" => "19.",
		"\xE2\x92\x9B" => "20.",
		"\xE2\x92\x9C" => "(a)",
		"\xE2\x92\x9D" => "(b)",
		"\xE2\x92\x9E" => "(c)",
		"\xE2\x92\x9F" => "(d)",
 
		// 0x24A0 - 0x24AF
		"\xE2\x92\xA0" => "(e)",
		"\xE2\x92\xA1" => "(f)",
		"\xE2\x92\xA2" => "(g)",
		"\xE2\x92\xA3" => "(h)",
		"\xE2\x92\xA4" => "(i)",
		"\xE2\x92\xA5" => "(j)",
		"\xE2\x92\xA6" => "(k)",
		"\xE2\x92\xA7" => "(l)",
		"\xE2\x92\xA8" => "(m)",
		"\xE2\x92\xA9" => "(n)",
		"\xE2\x92\xAA" => "(o)",
		"\xE2\x92\xAB" => "(p)",
		"\xE2\x92\xAC" => "(q)",
		"\xE2\x92\xAD" => "(r)",
		"\xE2\x92\xAE" => "(s)",
		"\xE2\x92\xAF" => "(t)",
 
		// 0x24B0 - 0x24BF
		"\xE2\x92\xB0" => "(u)",
		"\xE2\x92\xB1" => "(v)",
		"\xE2\x92\xB2" => "(w)",
		"\xE2\x92\xB3" => "(x)",
		"\xE2\x92\xB4" => "(y)",
		"\xE2\x92\xB5" => "(z)",
		"\xE2\x92\xB6" => "(A)",
		"\xE2\x92\xB7" => "(B)",
		"\xE2\x92\xB8" => "(C)",
		"\xE2\x92\xB9" => "(D)",
		"\xE2\x92\xBA" => "(E)",
		"\xE2\x92\xBB" => "(F)",
		"\xE2\x92\xBC" => "(G)",
		"\xE2\x92\xBD" => "(H)",
		"\xE2\x92\xBE" => "(I)",
		"\xE2\x92\xBF" => "(J)",
 
		// 0x24C0 - 0x24CF
		"\xE2\x93\x80" => "(K)",
		"\xE2\x93\x81" => "(L)",
		"\xE2\x93\x82" => "(M)",
		"\xE2\x93\x83" => "(N)",
		"\xE2\x93\x84" => "(O)",
		"\xE2\x93\x85" => "(P)",
		"\xE2\x93\x86" => "(Q)",
		"\xE2\x93\x87" => "(R)",
		"\xE2\x93\x88" => "(S)",
		"\xE2\x93\x89" => "(T)",
		"\xE2\x93\x8A" => "(U)",
		"\xE2\x93\x8B" => "(V)",
		"\xE2\x93\x8C" => "(W)",
		"\xE2\x93\x8D" => "(X)",
		"\xE2\x93\x8E" => "(Y)",
		"\xE2\x93\x8F" => "(Z)",
 
		// 0x24D0 - 0x24DF
		"\xE2\x93\x90" => "(a)",
		"\xE2\x93\x91" => "(b)",
		"\xE2\x93\x92" => "(c)",
		"\xE2\x93\x93" => "(d)",
		"\xE2\x93\x94" => "(e)",
		"\xE2\x93\x95" => "(f)",
		"\xE2\x93\x96" => "(g)",
		"\xE2\x93\x97" => "(h)",
		"\xE2\x93\x98" => "(i)",
		"\xE2\x93\x99" => "(j)",
		"\xE2\x93\x9A" => "(k)",
		"\xE2\x93\x9B" => "(l)",
		"\xE2\x93\x9C" => "(m)",
		"\xE2\x93\x9D" => "(n)",
		"\xE2\x93\x9E" => "(o)",
		"\xE2\x93\x9F" => "(p)",
 
		// 0x24E0 - 0x24EF
		"\xE2\x93\xA0" => "(q)",
		"\xE2\x93\xA1" => "(r)",
		"\xE2\x93\xA2" => "(s)",
		"\xE2\x93\xA3" => "(t)",
		"\xE2\x93\xA4" => "(u)",
		"\xE2\x93\xA5" => "(v)",
		"\xE2\x93\xA6" => "(w)",
		"\xE2\x93\xA7" => "(x)",
		"\xE2\x93\xA8" => "(y)",
		"\xE2\x93\xA9" => "(z)",
		"\xE2\x93\xAA" => "(0)",
		"\xE2\x93\xAB" => "(11)",
		"\xE2\x93\xAC" => "(12)",
		"\xE2\x93\xAD" => "(13)",
		"\xE2\x93\xAE" => "(14)",
		"\xE2\x93\xAF" => "(15)",
 
		/* --- 0x2600 - 0x266F (その他の記号) --- */
		// 0x2600 - 0x260F
		"\xE2\x98\x80" => "(晴)",
		"\xE2\x98\x81" => "(曇)",
		"\xE2\x98\x82" => "(雨)",
		"\xE2\x98\x83" => "(雪)",
		"\xE2\x98\x84" => "?",
		"\xE2\x98\x85" => "(黒星)",
		"\xE2\x98\x86" => "(白星)",
		"\xE2\x98\x87" => "?",
		"\xE2\x98\x88" => "?",
		"\xE2\x98\x89" => "?",
		"\xE2\x98\x8A" => "?",
		"\xE2\x98\x8B" => "?",
		"\xE2\x98\x8C" => "?",
		"\xE2\x98\x8D" => "?",
		"\xE2\x98\x8E" => "(黒電話)",
		"\xE2\x98\x8F" => "(白電話)",
		// 0x2610 - 0x2613
		"\xE2\x98\x90" => "(チェックボックス 空欄)",
		"\xE2\x98\x91" => "(チェックボックス チェック)",
		"\xE2\x98\x92" => "(チェックボックス チェック)",
		"\xE2\x98\x93" => "(チェック)",
 
		// 0x261A - 0x261F
		"\xE2\x98\x9A" => "(左)",
		"\xE2\x98\x9B" => "(右)",
		"\xE2\x98\x9C" => "(左)",
		"\xE2\x98\x9D" => "(上)",
		"\xE2\x98\x9E" => "(右)",
		"\xE2\x98\x9F" => "(下)",
		// 0x2620 - 0x262F
		"\xE2\x98\xA0" => "(ドクロ)",
		"\xE2\x98\xA1" => "?",
		"\xE2\x98\xA2" => "(核)",
		"\xE2\x98\xA3" => "?",
		"\xE2\x98\xA4" => "?",
		"\xE2\x98\xA5" => "?",
		"\xE2\x98\xA6" => "?",
		"\xE2\x98\xA7" => "?",
		"\xE2\x98\xA8" => "?",
		"\xE2\x98\xA9" => "?",
		"\xE2\x98\xAA" => "?",
		"\xE2\x98\xAB" => "?",
		"\xE2\x98\xAC" => "?",
		"\xE2\x98\xAD" => "?",
		"\xE2\x98\xAE" => "?",
		"\xE2\x98\xAF" => "?",
		// 0x2630 - 0x263F
		"\xE2\x98\xB0" => "?",
		"\xE2\x98\xB1" => "?",
		"\xE2\x98\xB2" => "?",
		"\xE2\x98\xB3" => "?",
		"\xE2\x98\xB4" => "?",
		"\xE2\x98\xB5" => "?",
		"\xE2\x98\xB6" => "?",
		"\xE2\x98\xB7" => "?",
		"\xE2\x98\xB8" => "?",
		"\xE2\x98\xB9" => "?",
		"\xE2\x98\xBA" => "?",
		"\xE2\x98\xBB" => "?",
		"\xE2\x98\xBC" => "?",
		"\xE2\x98\xBD" => "?",
		"\xE2\x98\xBE" => "?",
		"\xE2\x98\xBF" => "?",
		// 0x2640 - 0x264F
		"\xE2\x99\x80" => "?",
		"\xE2\x99\x81" => "?",
		"\xE2\x99\x82" => "?",
		"\xE2\x99\x83" => "?",
		"\xE2\x99\x84" => "?",
		"\xE2\x99\x85" => "?",
		"\xE2\x99\x86" => "?",
		"\xE2\x99\x87" => "?",
		"\xE2\x99\x88" => "(おひつじ座)",
		"\xE2\x99\x89" => "(おうし座)",
		"\xE2\x99\x8A" => "(ふたご座)",
		"\xE2\x99\x8B" => "(かに座)",
		"\xE2\x99\x8C" => "(しし座)",
		"\xE2\x99\x8D" => "(おとめ座)",
		"\xE2\x99\x8E" => "(てんびん座)",
		"\xE2\x99\x8F" => "(さそり座)",
		// 0x2650 - 0x265F
		"\xE2\x99\x90" => "(いて座)",
		"\xE2\x99\x91" => "(やぎ座)",
		"\xE2\x99\x92" => "(みずがめ座)",
		"\xE2\x99\x93" => "(うお座)",
		"\xE2\x99\x94" => "(キング 白)",
		"\xE2\x99\x95" => "(クイーン 白)",
		"\xE2\x99\x96" => "(ルーク 白)",
		"\xE2\x99\x97" => "(ビショップ 白)",
		"\xE2\x99\x98" => "(ナイト 白)",
		"\xE2\x99\x99" => "(ポーン 白)",
		"\xE2\x99\x9A" => "(キング 黒)",
		"\xE2\x99\x9B" => "(クイーン 黒)",
		"\xE2\x99\x9C" => "(ルーク 黒)",
		"\xE2\x99\x9D" => "(ビショップ 黒)",
		"\xE2\x99\x9E" => "(ナイト 黒)",
		"\xE2\x99\x9F" => "(ポーン 黒)",
		// 0x2660 - 0x266F
		"\xE2\x99\xA0" => "(スペード)",
		"\xE2\x99\xA1" => "(ハード)",
		"\xE2\x99\xA2" => "(ダイヤ)",
		"\xE2\x99\xA3" => "(クラブ)",
		"\xE2\x99\xA4" => "(スペード)",
		"\xE2\x99\xA5" => "(ハード)",
		"\xE2\x99\xA6" => "(ダイヤ)",
		"\xE2\x99\xA7" => "(クラブ)",
		"\xE2\x99\xA8" => "(温泉)",
		"\xE2\x99\xA9" => "(4分音符)",
		"\xE2\x99\xAA" => "(8分音符)",
		"\xE2\x99\xAB" => "(2つの8分音符)",
		"\xE2\x99\xAC" => "(2つの16分音符)",
		"\xE2\x99\xAD" => "(フラット)",
		"\xE2\x99\xAE" => "(ナチュラル)",
		"\xE2\x99\xAF" => "(シャープ)",
 
		/* --- 0x3220 - 0x324F (囲みCJK文字/月) --- */
		// 0x3220 - 0x322F
		"\xE3\x88\xA0" => "(一)",
		"\xE3\x88\xA1" => "(二)",
		"\xE3\x88\xA2" => "(三)",
		"\xE3\x88\xA3" => "(四)",
		"\xE3\x88\xA4" => "(五)",
		"\xE3\x88\xA5" => "(六)",
		"\xE3\x88\xA6" => "(七)",
		"\xE3\x88\xA7" => "(八)",
		"\xE3\x88\xA8" => "(九)",
		"\xE3\x88\xA9" => "(十)",
		"\xE3\x88\xAA" => "(月)",
		"\xE3\x88\xAB" => "(火)",
		"\xE3\x88\xAC" => "(水)",
		"\xE3\x88\xAD" => "(木)",
		"\xE3\x88\xAE" => "(金)",
		"\xE3\x88\xAF" => "(土)",
 
		// 0x3230 - 0x323F
		"\xE3\x88\xB0" => "(日)",
		"\xE3\x88\xB1" => "(株)",
		"\xE3\x88\xB2" => "(有)",
		"\xE3\x88\xB3" => "(社)",
		"\xE3\x88\xB4" => "(名)",
		"\xE3\x88\xB5" => "(特)",
		"\xE3\x88\xB6" => "(財)",
		"\xE3\x88\xB7" => "(祝)",
		"\xE3\x88\xB8" => "(労)",
		"\xE3\x88\xB9" => "(代)",
		"\xE3\x88\xBA" => "(呼)",
		"\xE3\x88\xBB" => "(学)",
		"\xE3\x88\xBC" => "(監)",
		"\xE3\x88\xBD" => "(企)",
		"\xE3\x88\xBE" => "(資)",
		"\xE3\x88\xBF" => "(協)",
 
		// 0x3240 - 0x3243
		"\xE3\x89\x80" => "(祭)",
		"\xE3\x89\x81" => "(休)",
		"\xE3\x89\x82" => "(自)",
		"\xE3\x89\x83" => "(至)",
 
		/* --- 0x3280 - 0x33FF --- */
		// 0x3280 - 0x328F
		"\xE3\x8A\x80" => "(一)",
		"\xE3\x8A\x81" => "(二)",
		"\xE3\x8A\x82" => "(三)",
		"\xE3\x8A\x83" => "(四)",
		"\xE3\x8A\x84" => "(五)",
		"\xE3\x8A\x85" => "(六)",
		"\xE3\x8A\x86" => "(七)",
		"\xE3\x8A\x87" => "(八)",
		"\xE3\x8A\x88" => "(九)",
		"\xE3\x8A\x89" => "(十)",
		"\xE3\x8A\x8A" => "(月)",
		"\xE3\x8A\x8B" => "(火)",
		"\xE3\x8A\x8C" => "(水)",
		"\xE3\x8A\x8D" => "(木)",
		"\xE3\x8A\x8E" => "(金)",
		"\xE3\x8A\x8F" => "(土)",
		// 0x3290 - 0x329F
		"\xE3\x8A\x90" => "(日)",
		"\xE3\x8A\x91" => "(株)",
		"\xE3\x8A\x92" => "(有)",
		"\xE3\x8A\x93" => "(社)",
		"\xE3\x8A\x94" => "(名)",
		"\xE3\x8A\x95" => "(特)",
		"\xE3\x8A\x96" => "(財)",
		"\xE3\x8A\x97" => "(祝)",
		"\xE3\x8A\x98" => "(労)",
		"\xE3\x8A\x99" => "(秘)",
		"\xE3\x8A\x9A" => "(男)",
		"\xE3\x8A\x9B" => "(女)",
		"\xE3\x8A\x9C" => "(適)",
		"\xE3\x8A\x9D" => "(優)",
		"\xE3\x8A\x9E" => "(印)",
		"\xE3\x8A\x9F" => "(注)",
		// 0x32A0 - 0x32AF
		"\xE3\x8A\xA0" => "(項)",
		"\xE3\x8A\xA1" => "(休)",
		"\xE3\x8A\xA2" => "(写)",
		"\xE3\x8A\xA3" => "(正)",
		"\xE3\x8A\xA4" => "(上)",
		"\xE3\x8A\xA5" => "(中)",
		"\xE3\x8A\xA6" => "(下)",
		"\xE3\x8A\xA7" => "(左)",
		"\xE3\x8A\xA8" => "(右)",
		"\xE3\x8A\xA9" => "(医)",
		"\xE3\x8A\xAA" => "(宗)",
		"\xE3\x8A\xAB" => "(学)",
		"\xE3\x8A\xAC" => "(監)",
		"\xE3\x8A\xAD" => "(企)",
		"\xE3\x8A\xAE" => "(資)",
		"\xE3\x8A\xAF" => "(協)",
		// 0x32B0 - 0x32BF
		"\xE3\x8A\xB0" => "(夜)",
		"\xE3\x8A\xB1" => "(36)",
		"\xE3\x8A\xB2" => "(37)",
		"\xE3\x8A\xB3" => "(38)",
		"\xE3\x8A\xB4" => "(39)",
		"\xE3\x8A\xB5" => "(40)",
		"\xE3\x8A\xB6" => "(41)",
		"\xE3\x8A\xB7" => "(42)",
		"\xE3\x8A\xB8" => "(43)",
		"\xE3\x8A\xB9" => "(44)",
		"\xE3\x8A\xBA" => "(45)",
		"\xE3\x8A\xBB" => "(46)",
		"\xE3\x8A\xBC" => "(47)",
		"\xE3\x8A\xBD" => "(48)",
		"\xE3\x8A\xBE" => "(49)",
		"\xE3\x8A\xBF" => "(50)",
		// 0x32C0 - 0x32CB
		"\xE3\x8B\x80" => "1月",
		"\xE3\x8B\x81" => "2月",
		"\xE3\x8B\x82" => "3月",
		"\xE3\x8B\x83" => "4月",
		"\xE3\x8B\x84" => "5月",
		"\xE3\x8B\x85" => "6月",
		"\xE3\x8B\x86" => "7月",
		"\xE3\x8B\x87" => "8月",
		"\xE3\x8B\x88" => "9月",
		"\xE3\x8B\x89" => "10月",
		"\xE3\x8B\x8A" => "11月",
		"\xE3\x8B\x8B" => "12月",
 
		// 0x32D0 - 0x32DF
		"\xE3\x8B\x90" => "(ア)",
		"\xE3\x8B\x91" => "(イ)",
		"\xE3\x8B\x92" => "(ウ)",
		"\xE3\x8B\x93" => "(エ)",
		"\xE3\x8B\x94" => "(オ)",
		"\xE3\x8B\x95" => "(カ)",
		"\xE3\x8B\x96" => "(キ)",
		"\xE3\x8B\x97" => "(ク)",
		"\xE3\x8B\x98" => "(ケ)",
		"\xE3\x8B\x99" => "(コ)",
		"\xE3\x8B\x9A" => "(サ)",
		"\xE3\x8B\x9B" => "(シ)",
		"\xE3\x8B\x9C" => "(ス)",
		"\xE3\x8B\x9D" => "(セ)",
		"\xE3\x8B\x9E" => "(ソ)",
		"\xE3\x8B\x9F" => "(タ)",
		// 0x32E0 - 0x32EF
		"\xE3\x8B\xA0" => "(チ)",
		"\xE3\x8B\xA1" => "(ツ)",
		"\xE3\x8B\xA2" => "(テ)",
		"\xE3\x8B\xA3" => "(ト)",
		"\xE3\x8B\xA4" => "(ナ)",
		"\xE3\x8B\xA5" => "(ニ)",
		"\xE3\x8B\xA6" => "(ヌ)",
		"\xE3\x8B\xA7" => "(ネ)",
		"\xE3\x8B\xA8" => "(ノ)",
		"\xE3\x8B\xA9" => "(ハ)",
		"\xE3\x8B\xAA" => "(ヒ)",
		"\xE3\x8B\xAB" => "(フ)",
		"\xE3\x8B\xAC" => "(ヘ)",
		"\xE3\x8B\xAD" => "(ホ)",
		"\xE3\x8B\xAE" => "(マ)",
		"\xE3\x8B\xAF" => "(ミ)",
		// 0x32F0 - 0x32FE
		"\xE3\x8B\xB0" => "(ム)",
		"\xE3\x8B\xB1" => "(メ)",
		"\xE3\x8B\xB2" => "(モ)",
		"\xE3\x8B\xB3" => "(ヤ)",
		"\xE3\x8B\xB4" => "(ユ)",
		"\xE3\x8B\xB5" => "(ヨ)",
		"\xE3\x8B\xB6" => "(ラ)",
		"\xE3\x8B\xB7" => "(リ)",
		"\xE3\x8B\xB8" => "(ル)",
		"\xE3\x8B\xB9" => "(レ)",
		"\xE3\x8B\xBA" => "(ロ)",
		"\xE3\x8B\xBB" => "(ワ)",
		"\xE3\x8B\xBC" => "(ヰ)",
		"\xE3\x8B\xBD" => "(ヱ)",
		"\xE3\x8B\xBE" => "(ヲ)",
 
		/* --- 0x3300 - 0x33F0 (CJK互換文字) --- */
		// 0x3300 - 0x330F
		"\xE3\x8C\x80" => "アパート",
		"\xE3\x8C\x81" => "アルファ",
		"\xE3\x8C\x82" => "アンペア",
		"\xE3\x8C\x83" => "アール",
		"\xE3\x8C\x84" => "イニング",
		"\xE3\x8C\x85" => "インチ",
		"\xE3\x8C\x86" => "ウォン",
		"\xE3\x8C\x87" => "エスクード",
		"\xE3\x8C\x88" => "エーカー",
		"\xE3\x8C\x89" => "オンス",
		"\xE3\x8C\x8A" => "オーム",
		"\xE3\x8C\x8B" => "カイリ",
		"\xE3\x8C\x8C" => "カラット",
		"\xE3\x8C\x8D" => "カロリー",
		"\xE3\x8C\x8E" => "ガロン",
		"\xE3\x8C\x8F" => "ガンマ",
		// 0x3310 - 0x331F
		"\xE3\x8C\x90" => "ギガ",
		"\xE3\x8C\x91" => "ギニー",
		"\xE3\x8C\x92" => "キュリー",
		"\xE3\x8C\x93" => "ギルダー",
		"\xE3\x8C\x94" => "キロ",
		"\xE3\x8C\x95" => "キログラム",
		"\xE3\x8C\x96" => "キロメートル",
		"\xE3\x8C\x97" => "キロワット",
		"\xE3\x8C\x98" => "グラム",
		"\xE3\x8C\x99" => "グラムトン",
		"\xE3\x8C\x9A" => "クルゼイロ",
		"\xE3\x8C\x9B" => "クローネ",
		"\xE3\x8C\x9C" => "ケース",
		"\xE3\x8C\x9D" => "コルナ",
		"\xE3\x8C\x9E" => "コーポ",
		"\xE3\x8C\x9F" => "サイクル",
		// 0x3320 - 0x332F
		"\xE3\x8C\xA0" => "サンチーム",
		"\xE3\x8C\xA1" => "シリング",
		"\xE3\x8C\xA2" => "センチ",
		"\xE3\x8C\xA3" => "セント",
		"\xE3\x8C\xA4" => "ダース",
		"\xE3\x8C\xA5" => "デシ",
		"\xE3\x8C\xA6" => "ドル",
		"\xE3\x8C\xA7" => "トン",
		"\xE3\x8C\xA8" => "ナノ",
		"\xE3\x8C\xA9" => "ノット",
		"\xE3\x8C\xAA" => "ハイツ",
		"\xE3\x8C\xAB" => "パーセント",
		"\xE3\x8C\xAC" => "パーツ",
		"\xE3\x8C\xAD" => "バーレル",
		"\xE3\x8C\xAE" => "ピアストル",
		"\xE3\x8C\xAF" => "ピクル",
		// 0x3330 - 0x333F
		"\xE3\x8C\xB0" => "ピコ",
		"\xE3\x8C\xB1" => "ビル",
		"\xE3\x8C\xB2" => "ファラッド",
		"\xE3\x8C\xB3" => "フィート",
		"\xE3\x8C\xB4" => "ブッシェル",
		"\xE3\x8C\xB5" => "フラン",
		"\xE3\x8C\xB6" => "ヘクタール",
		"\xE3\x8C\xB7" => "ペソ",
		"\xE3\x8C\xB8" => "ペニヒ",
		"\xE3\x8C\xB9" => "ヘルツ",
		"\xE3\x8C\xBA" => "ペンス",
		"\xE3\x8C\xBB" => "ページ",
		"\xE3\x8C\xBC" => "ベータ",
		"\xE3\x8C\xBD" => "ポイント",
		"\xE3\x8C\xBE" => "ボルト",
		"\xE3\x8C\xBF" => "ホン",
		// 0x3340 - 0x334F
		"\xE3\x8D\x80" => "ポンド",
		"\xE3\x8D\x81" => "ホール",
		"\xE3\x8D\x82" => "ホーン",
		"\xE3\x8D\x83" => "マイクロ",
		"\xE3\x8D\x84" => "マイル",
		"\xE3\x8D\x85" => "マッハ",
		"\xE3\x8D\x86" => "マルク",
		"\xE3\x8D\x87" => "マンション",
		"\xE3\x8D\x88" => "ミクロン",
		"\xE3\x8D\x89" => "ミリ",
		"\xE3\x8D\x8A" => "ミリバール",
		"\xE3\x8D\x8B" => "メガ",
		"\xE3\x8D\x8C" => "メガトン",
		"\xE3\x8D\x8D" => "メートル",
		"\xE3\x8D\x8E" => "ヤード",
		"\xE3\x8D\x8F" => "ヤール",
		// 0x3350 - 0x335F
		"\xE3\x8D\x90" => "ユアン",
		"\xE3\x8D\x91" => "リットル",
		"\xE3\x8D\x92" => "リラ",
		"\xE3\x8D\x93" => "ルピー",
		"\xE3\x8D\x94" => "ルーブル",
		"\xE3\x8D\x95" => "レム",
		"\xE3\x8D\x96" => "レントゲン",
		"\xE3\x8D\x97" => "ワット",
		"\xE3\x8D\x98" => "0点",
		"\xE3\x8D\x99" => "1点",
		"\xE3\x8D\x9A" => "2点",
		"\xE3\x8D\x9B" => "3点",
		"\xE3\x8D\x9C" => "4点",
		"\xE3\x8D\x9D" => "5点",
		"\xE3\x8D\x9E" => "6点",
		"\xE3\x8D\x9F" => "7点",
		// 0x3360 - 0x336F
		"\xE3\x8D\xA0" => "8点",
		"\xE3\x8D\xA1" => "9点",
		"\xE3\x8D\xA2" => "10点",
		"\xE3\x8D\xA3" => "11点",
		"\xE3\x8D\xA4" => "12点",
		"\xE3\x8D\xA5" => "13点",
		"\xE3\x8D\xA6" => "14点",
		"\xE3\x8D\xA7" => "15点",
		"\xE3\x8D\xA8" => "16点",
		"\xE3\x8D\xA9" => "17点",
		"\xE3\x8D\xAA" => "18点",
		"\xE3\x8D\xAB" => "19点",
		"\xE3\x8D\xAC" => "20点",
		"\xE3\x8D\xAD" => "21点",
		"\xE3\x8D\xAE" => "22点",
		"\xE3\x8D\xAF" => "23点",
		// 0x3370 - 0x3376
		"\xE3\x8D\xB0" => "24点",
		"\xE3\x8D\xB1" => "hPa",
		"\xE3\x8D\xB2" => "da",
		"\xE3\x8D\xB3" => "AU",
		"\xE3\x8D\xB4" => "bar",
		"\xE3\x8D\xB5" => "oV",
		"\xE3\x8D\xB6" => "pc",
		// 0x337B - 0x337F
		"\xE3\x8D\xBB" => "平成",
		"\xE3\x8D\xBC" => "昭和",
		"\xE3\x8D\xBD" => "大正",
		"\xE3\x8D\xBE" => "明治",
		"\xE3\x8D\xBF" => "株式会社",
 
		// 0x3380 - 0x338F
		"\xE3\x8E\x80" => "pA",
		"\xE3\x8E\x81" => "nA",
		"\xE3\x8E\x82" => "マイクロA",
		"\xE3\x8E\x83" => "mA",
		"\xE3\x8E\x84" => "kA",
		"\xE3\x8E\x85" => "KB",
		"\xE3\x8E\x86" => "MB",
		"\xE3\x8E\x87" => "GB",
		"\xE3\x8E\x88" => "cal",
		"\xE3\x8E\x89" => "kcal",
		"\xE3\x8E\x8A" => "pF",
		"\xE3\x8E\x8B" => "nF",
		"\xE3\x8E\x8C" => "マイクロF",
		"\xE3\x8E\x8D" => "マイクロg",
		"\xE3\x8E\x8E" => "mg",
		"\xE3\x8E\x8F" => "kg",
		// 0x3390 - 0x339F
		"\xE3\x8E\x90" => "Hz",
		"\xE3\x8E\x91" => "kHz",
		"\xE3\x8E\x92" => "MHz",
		"\xE3\x8E\x93" => "GHz",
		"\xE3\x8E\x94" => "THz",
		"\xE3\x8E\x95" => "マイクロl",
		"\xE3\x8E\x96" => "Ml",
		"\xE3\x8E\x97" => "dl",
		"\xE3\x8E\x98" => "kl",
		"\xE3\x8E\x99" => "fm",
		"\xE3\x8E\x9A" => "nm",
		"\xE3\x8E\x9B" => "マイクロm",
		"\xE3\x8E\x9C" => "mm",
		"\xE3\x8E\x9D" => "cm",
		"\xE3\x8E\x9E" => "km",
		"\xE3\x8E\x9F" => "mm2",
		// 0x33A0 - 0x33AF
		"\xE3\x8E\xA0" => "cm2",
		"\xE3\x8E\xA1" => "m2",
		"\xE3\x8E\xA2" => "km2",
		"\xE3\x8E\xA3" => "mm3",
		"\xE3\x8E\xA4" => "cm3",
		"\xE3\x8E\xA5" => "m3",
		"\xE3\x8E\xA6" => "km3",
		"\xE3\x8E\xA7" => "m/s",
		"\xE3\x8E\xA8" => "m/s2",
		"\xE3\x8E\xA9" => "Pa",
		"\xE3\x8E\xAA" => "kPa",
		"\xE3\x8E\xAB" => "MPa",
		"\xE3\x8E\xAC" => "GPa",
		"\xE3\x8E\xAD" => "rad",
		"\xE3\x8E\xAE" => "rad/s",
		"\xE3\x8E\xAF" => "rad/s2",
		// 0x33B0 - 0x33BF
		"\xE3\x8E\xB0" => "ps",
		"\xE3\x8E\xB1" => "ns",
		"\xE3\x8E\xB2" => "マイクロs",
		"\xE3\x8E\xB3" => "ms",
		"\xE3\x8E\xB4" => "pV",
		"\xE3\x8E\xB5" => "nV",
		"\xE3\x8E\xB6" => "マイクロV",
		"\xE3\x8E\xB7" => "mV",
		"\xE3\x8E\xB8" => "kV",
		"\xE3\x8E\xB9" => "MV",
		"\xE3\x8E\xBA" => "pW",
		"\xE3\x8E\xBB" => "nW",
		"\xE3\x8E\xBC" => "マイクロW",
		"\xE3\x8E\xBD" => "mW",
		"\xE3\x8E\xBE" => "kW",
		"\xE3\x8E\xBF" => "MW",
		// 0x33C0 - 0x33CF
		"\xE3\x8F\x80" => "kオーム",
		"\xE3\x8F\x81" => "Mオーム",
		"\xE3\x8F\x82" => "a.m.",
		"\xE3\x8F\x83" => "Bq",
		"\xE3\x8F\x84" => "cc",
		"\xE3\x8F\x85" => "cd",
		"\xE3\x8F\x86" => "C/kg",
		"\xE3\x8F\x87" => "Co.",
		"\xE3\x8F\x88" => "dB",
		"\xE3\x8F\x89" => "Gy",
		"\xE3\x8F\x8A" => "ha",
		"\xE3\x8F\x8B" => "?",
		"\xE3\x8F\x8C" => "in",
		"\xE3\x8F\x8D" => "K.K.",
		"\xE3\x8F\x8E" => "KM",
		"\xE3\x8F\x8F" => "kt",
		// 0x33D0 - 0x33DF
		"\xE3\x8F\x90" => "lm",
		"\xE3\x8F\x91" => "ln",
		"\xE3\x8F\x92" => "log",
		"\xE3\x8F\x93" => "lx",
		"\xE3\x8F\x94" => "mb",
		"\xE3\x8F\x95" => "mil",
		"\xE3\x8F\x96" => "mol",
		"\xE3\x8F\x97" => "pH",
		"\xE3\x8F\x98" => "p.m.",
		"\xE3\x8F\x99" => "PPM",
		"\xE3\x8F\x9A" => "PR",
		"\xE3\x8F\x9B" => "sr",
		"\xE3\x8F\x9C" => "Sv",
		"\xE3\x8F\x9D" => "Wb",
 
		// 0x33E0 - 0x33EF
		"\xE3\x8F\xA0" => "1日",
		"\xE3\x8F\xA1" => "2日",
		"\xE3\x8F\xA2" => "3日",
		"\xE3\x8F\xA3" => "4日",
		"\xE3\x8F\xA4" => "5日",
		"\xE3\x8F\xA5" => "6日",
		"\xE3\x8F\xA6" => "7日",
		"\xE3\x8F\xA7" => "8日",
		"\xE3\x8F\xA8" => "9日",
		"\xE3\x8F\xA9" => "10日",
		"\xE3\x8F\xAA" => "11日",
		"\xE3\x8F\xAB" => "12日",
		"\xE3\x8F\xAC" => "13日",
		"\xE3\x8F\xAD" => "14日",
		"\xE3\x8F\xAE" => "15日",
		"\xE3\x8F\xAF" => "16日",
		// 0x33F0 - 0x33FF
		"\xE3\x8F\xB0" => "17日",
		"\xE3\x8F\xB1" => "18日",
		"\xE3\x8F\xB2" => "19日",
		"\xE3\x8F\xB3" => "20日",
		"\xE3\x8F\xB4" => "21日",
		"\xE3\x8F\xB5" => "22日",
		"\xE3\x8F\xB6" => "23日",
		"\xE3\x8F\xB7" => "24日",
		"\xE3\x8F\xB8" => "25日",
		"\xE3\x8F\xB9" => "26日",
		"\xE3\x8F\xBA" => "27日",
		"\xE3\x8F\xBB" => "28日",
		"\xE3\x8F\xBC" => "29日",
		"\xE3\x8F\xBD" => "30日",
		"\xE3\x8F\xBE" => "31日",
	);
	return str_replace( array_keys( $arr), array_values( $arr), $str);
}


function errtxt($str,$num=0){
	$add[0] = "を入力してください。";
	$add[1] = "を選択してください。";
	$add[2] = "の形式が不正です。";
	$add[3] = "が間違っています。";
	$add[4] = "が一致しません。";
	$err = '<p class="err">'.$str.$add[$num].'</p>';
	return $err;
}


function errblank($str){
	if($str=="" or strlen($str)==0){
		return true;
	}
	return false;
}


function errchk($arr){
	$chk=true;
	
	foreach($arr as $key => $val){
		if($val==1){
			$chk = false;
		}
	}
	
	return $chk;
}


function errrdo($int){
	$chk=true;
	
	if($int > 0){
		$chk = false;
	}
	
	return $chk;
}




function mkrdo($item,$name,$def,$st=1){

$ret="";

for($i=$st;$i<count($item);$i++){

$chk="";

if($def==$i){
	$chk="checked=\"checked\"";
}

$ret.=<<<EOF
<input type="radio" name="{$name}" id="{$name}{$i}" value="{$i}" {$chk} />
<label for="{$name}{$i}">{$item[$i]}</label>
EOF;

}

return $ret;

}

function mkrdotxt($item,$name,$def,$st=1){

return $item[$def];

}


function mkopttxt($names,$val,$def){
	$ret="";
	$sel="";	
	$ret = $names[$def];
	return $ret;
}

function mkopt($names,$val,$def){
	$ret="";
	$sel="";	
	for($i=1;$i<count($names);$i++){

	if(intval($def) == $i){
		$sel = "selected='selected'";
		//die($i);
	}else{
		$sel="";
	}


$ret .= <<< EOF
<option value="{$i}" {$sel}>{$names[$i]}</option>

EOF;
		
	}

	return $ret;
}

function mkchktxt($names,$val,$def){
$ret="";
$sel="";	
for($i=1;$i<count($names);$i++){
if(isset($def[$i]) && $def[$i]==1){
	$ret.= $names[$i]." , ";
}
}

$ret = preg_replace("/ , $/im","",$ret);

return $ret;
}

function mkchk($names,$val,$def){
	$ret="";
	$sel="";	
	for($i=1;$i<count($names);$i++){

	if(isset($def[$i]) && $def[$i]==1){
		$sel = "checked='checked'";
	}else{
		$sel="";
	}
$ret .= <<< EOF
<input type="checkbox" name="{$val}[{$i}]" id="{$val}{$i}" value="1" {$sel} /><label for="{$val}{$i}">{$names[$i]}</label>

EOF;

	}

	return $ret;
}



?>