<?php
/* <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> */
session_start();

mb_language("ja");
mb_regex_encoding("UTF-8");
mb_internal_encoding("UTF-8");


$jqsc="";
$cga="";

$disp="";
$disp1="";
$disp2="";
$disp3="";
$dmode="";


include("mail.php");
include("config.php");

gpcInit();


$_POST["cmail"] = mb_convert_kana($_POST["cmail"],"arn");
$_POST["ctel"] = mb_convert_kana($_POST["ctel"],"arn");

$pdata = $_POST = initStr($_POST);
$_GET = initStr($_GET);
$_REQUEST = initStr($_REQUEST);









if((isset($pdata['check']) && $pdata['check'] == 1) or (isset($pdata['send']) && $pdata['send'] == 1)){

	$errflg=false;
	
/******エラーチェック********************************************/

if(errchk($pdata["ctype1"])){
	$pdata['err']['ctype1']=errtxt("お問い合わせ項目",1);
	$errflg=true;
}
if(errrdo($pdata["ctype1"])){
	$pdata['err']['ctype1']=errtxt("お問い合わせ項目",1);
	$errflg=true;
}

if(errblank($pdata["cname"])){
	$pdata['err']['cname']=errtxt("お名前");
	$errflg=true;
}

if(errblank($pdata["cname2"])){
	$pdata['err']['cname2']=errtxt("ふりがな");
	$errflg=true;
}

if (errblank($pdata["cmail"])){
	$pdata['err']['cmail']=errtxt("メールアドレス");
	$errflg=true;
} elseif ( !preg_match("/^[a-z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-z0-9-]+(?:\.[a-z0-9-]+)*$/ims",$pdata['cmail']) ) {
	$pdata['err']['cmail']=errtxt("メールアドレス",2);
	$errflg=true;
}

if(errblank($pdata["cmail2"])){
	$pdata['err']['cmail2']=errtxt("メールアドレス確認");
	$errflg=true;
}
if($pdata["cmail"] != $pdata["cmail2"]){
	$pdata['err']['cmail2']=errtxt("メールアドレス確認",4);
	$errflg=true;
}


if(errblank($pdata["ccom"])){
	$pdata['err']['ccomp']=errtxt("会社名");
	$errflg=true;
}

if($pdata["cpref"] <= 0){
	$pdata['err']['cpref']=errtxt("都道府県",1);
	$errflg=true;
}
if(errblank($pdata["cadd1"])){
	$pdata['err']['cadd1']=errtxt("市区町村");
	$errflg=true;
}
if(errblank($pdata["cadd2"])){
	$pdata['err']['cadd2']=errtxt("丁目番地");
	$errflg=true;
}

if(errblank($pdata["ctext"])){
	$pdata['err']['ctext']=errtxt("お問い合わせ内容");
	$errflg=true;
}


/****************************************************************************/


}


if (isset($pdata['check']) && $pdata['check'] ==1 && !$errflg){
	$dmode="conform";
}

if (isset($pdata['send']) && $pdata['send'] ==1 && !$errflg){
	$dmode="send";
}


//if (isset($pdata['back']) || $pdata['back'] !== false) {
if (isset($pdata['back']) || isset($pdata['back_x']) ) {
	$dmode="";
}

/*******************************/
$disp="";
switch ($dmode){

case "conform" :

$ctext=str_replace("\n","<br />",$pdata['ctext']);

$pb="";

foreach($pdata as $key => $val){
if(preg_match("/^(x|y|check)$/",$key) or ($val=="")){
	continue;
}
if(!is_array($val)){
$pb .= "<input type=\"hidden\" name=\"{$key}\" value=\"{$val}\" />\n";
}else{
foreach($val as $key2 => $val2){
$pb .= "<input type=\"hidden\" name=\"{$key}[{$key2}]\" value=\"{$val2}\" />\n";
}
}
}


/******確認画面********************************************/
$dctype1=mkrdotxt($def_ctype1,"ctype1",$pdata["ctype1"]);
$dctype1=mkchktxt($def_ctype1,"ctype1",$pdata["ctype1"]);
$dcpref=mkopttxt($def_cpref,"cpref",$pdata["cpref"]);
$disp .= <<< EOF

<div id="conf">
<form action="../inq/index.php#mf1" method="post">
<input type="hidden" name="send" value="1">
{$pb}

<dl>
<dt>お名前<span>必須</span></dt>
<dd>{$pdata["cname"]}</dd>
</dl>

<dl>
<dt>ふりがな<span>必須</span></dt>
<dd>{$pdata["cname2"]}</dd>
</dl>

<dl>
<dt>お問い合わせ内容<span>必須</span></dt>
<dd>{$dctype1}</dd>
</dl>

<dl>
<dt>メールアドレス<span>必須</span></dt>
<dd>{$pdata["cmail"]}</dd>
</dl>

<dl>
<dt>メールアドレス確認<span>必須</span></dt>
<dd>{$pdata["cmail2"]}</dd>
</dl>

<dl>
<dt>会社名<span>必須</span></dt>
<dd>{$pdata["ccomp"]}</dd>
</dl>

<dl>
<dt>電話番号</dt>
<dd>{$pdata["ctel"]}</dd>
</dl>

<dl>
<dt>郵便番号</dt>
<dd>{$pdata["cpost"]}</dd>
</dl>

<dl>
<dt>ご住所<span>必須</span></dt>
<dd>{$dcpref}{$pdata["cadd1"]}{$pdata["cadd2"]}</dd>
</dl>

<dl>
<dt>お問い合わせ内容</dt>
<dd>{$ctext}</dd>
</dl>


<div class="sbtn">
<p><input type="submit" value="修正する" name="back"></p>
<p><input type="submit" value="この内容で送信する"></p>
</div>

</form>
</div>

EOF;

break;


/******送信内容********************************************/

case "send":

$dctype1=mkrdotxt($def_ctype1,"ctype1",$pdata["ctype1"]);
$dctype1=mkchktxt($def_ctype1,"ctype1",$pdata["ctype1"]);
$dcpref=mkopttxt($def_cpref,"cpref",$pdata["cpref"]);

$body = <<< EOF
■お問い合わせ項目
{$dctype1}

■お名前
{$pdata["cname"]}

■ふりがな
{$pdata["cname2"]}

■メールアドレス
{$pdata["cmail"]}

■会社名
{$pdata["ccomp"]}

■電話番号
{$pdata["ctel"]}

■郵便番号
{$pdata["cpost"]}

■ご住所
{$dcpref}
{$pdata["cadd1"]}{$pdata["cadd2"]}

■お問い合わせ内容
{$pdata["ctext"]}
EOF;

$body = <<< EOF
{$smailheader}
{$body}
{$smailfooter}
EOF;


/*
echo <<< EOF
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
EOF;

echo "<pre>";
echo $body;
echo "</pre>";

die();
*/

MailSender($sendmailfrom,$sendmailto1,$sendmailto2,$sendmailto,$sendmailtitle,$body);


if($pdata['cmail']){
MailSender($sendmailfrom,$pdata['cmail'],"",$sendmailtitle,$body);
}


header("Location: thanks.html");
exit();




break;

/******入力画面********************************************/
default :

$disp1 = <<< EOF
EOF;


$dctype1=mkrdo($def_ctype1,"ctype1",$pdata["ctype1"]);
$dctype1=mkchk($def_ctype1,"ctype1",$pdata["ctype1"]);
$dcpref=mkopt($def_cpref,"cpref",$pdata["cpref"]);

$disp2 = <<< EOF
<div id="input">

<form action="../inq/index.php#mf1" method="post">
<input type="hidden" name="check" value="1">

<dl>
<dt>お問い合わせ項目<span>必須</span></dt>
<dd>{$pdata["err"]["ctype1"]}{$dctype1}</dd>
</dl>

<dl>
<dt>お名前<span>必須</span></dt>
<dd>{$pdata["err"]["cname"]}<input type="text" name="cname" value="{$pdata["cname"]}" placeholder="山田太郎" class="w50"></dd>
</dl>

<dl>
<dt>ふりがな<span>必須</span></dt>
<dd>{$pdata["err"]["cname2"]}<input type="text" name="cname2" value="{$pdata["cname2"]}" placeholder="やまだたろう" class="w50"></dd>
</dl>

<dl>
<dt>メールアドレス<span>必須</span></dt>
<dd>{$pdata["err"]["cmail"]}<input type="email" name="cmail" value="{$pdata["cmail"]}" placeholder=" 例）test@admin.com.jp"></dd>
</dl>

<dl>
<dt>メールアドレス確認<span>必須</span></dt>
<dd>{$pdata["err"]["cmail2"]}<input type="email" name="cmail2" value="{$pdata["cmail2"]}" placeholder=" 例）test@admin.com.jp"></dd>
</dl>

<dl>
<dt>会社名<span>必須</span></dt>
<dd>{$pdata["err"]["ccomp"]}<input type="text" name="ccomp" value="{$pdata["ccomp"]}" placeholder="島産業株式会社"></dd>
</dl>

<dl>
<dt>電話番号</dt>
<dd><input type="tel" name="ctel" value="{$pdata["ctel"]}" placeholder="例）00-1234-5678"></dd>
</dl>

<dl>
<dt>郵便番号</dt>
<dd><input type="text" name="cpost" value="{$pdata["cpost"]}" placeholder="000-0000"></dd>
</dl>

<dl>
<dt>ご住所<span>必須</span></dt>
<dd>
<span>
{$pdata["err"]["cpref"]}<select name="cpref" class="w30">
<option value="" selected>選択してください</option>
{$dcpref}
</select>
</span>
<span>{$pdata["err"]["cadd1"]}<input type="text" name="cadd1" value="{$pdata["cadd1"]}" placeholder="市区町村"></span>
<span>{$pdata["err"]["cadd2"]}<input type="text" name="cadd2" value="{$pdata["cadd2"]}" placeholder="丁目番地"></span>
</dd>
</dl>

<dl>
<dt>お問い合わせ内容<span>必須</span></dt>
<dd>{$pdata["err"]["ctext"]}<textarea name="ctext" cols="" rows="10" placeholder="例）お問い合わせ内容をこちらに記載してください。">{$pdata["ctext"]}</textarea></dd>
</dl>

<div class="sbtn">
<p><input type="submit" value="個人情報の取り扱いに同意して次へ"></p>
</div>

</form>

<div class="cbox" id="pp">
<div class="cbox-inner">
<h2>個人情報保護方針</h2>
<p>●●●●会社（以下、当方）では、お客様のプライバシーを尊重し、お客様の個人情報を大切に保護することを重要な責務と考えております。当ウェブサイトでは、個人情報保護に関する法令を遵守するとともに、個人情報の取り扱いに関して次のような姿勢で行動しています。
</p>
<p>【個人情報の利用目的】<br> お客様から個人情報をご提供いただく場合、その情報は、お客様からのお問い合わせおよびご要望に対して回答または対応する目的、または個人情報をご提供いただく際に予め明示する目的のみに利用いたします。
お客様の個人情報をこれら正当な目的以外に無断で利用することはありません。</p>
<p>【第三者への提供】
<br>お客様からご提供いただいた個人情報は、以下のそれぞれの場合を除き、如何なる第三者にも開示または提供することはありません。
・お問い合わせまたはご要望に対し、適切な回答または対応をさせていただく為に、お問い合わせ・ご要望に関連する内容を開示する場合
・個人情報をご提供いただく際に予め明示した第三者に提供する場合
・法令に基づく場合その他個人情報保護法により第三者への提供が認められている場合
・お客様にご承諾頂いた場合</p>
<p>【個人情報の開示、訂正、削除について】<br> お客様がご提供された個人情報の開示、訂正、削除を希望される場合、合理的な範囲で速やかに対応いたします。
その場合は、個人情報をご提供いただいた個々のウェブページにてお知らせした窓口までご連絡ください。</p>
<p> 【安全対策】
<br>お客様の個人情報を安全に管理・運営するよう鋭意努力しており、個人情報への外部からの不正なアクセス、個人情報の紛失・破壊・改ざん・漏えいなどへの危険防止に対する合理的かつ適切な安全対策を行っています。
また個人情報を取り扱う部門ごとに情報管理責任者を置き、個人情報の適切な管理に努めるとともに、情報セキュリティに関する規程を設けて社員への周知徹底を実施しています。</p>
<p>【特定または不特定情報の収集】
<br>当ウェブサイトでは、アクセスされたお客様個人を特定できる情報（お名前、ご住所、お電話番号、電子メールアドレス等）を、お客様のご承諾なく収集することはありません。
その一方、当ウェブサイトでは、お客様個人を特定できない情報を収集することがあります。
このタイプの情報の例としては、お客様が当ウェブサイトのどのページにご訪問されたのか、またどのドメイン名のウェブサイトから当ウェブサイトにアクセスされたのかの記録等があります。
これらの情報は、当ウェブサイトの内容改善等に用いられることがあります。</p>
<p>【サイト利用状況を把握するためのGoogle Analyticsの利用について】<br> 本サイトは、サイトの利用状況を把握するためにGoogle Analyticsによって提供されるサービスを利用しております。
Google Analyticsではクッキー（cookie）を使用し個人を特定する情報を含まずにログを収集します。
なお、収集されるログはGoogle社のプライバシーポリシーに基づいて管理されます。
Google Analyticsについて、およびGoogle社のプライバシーポリシーについては以下をご覧ください。</p>
<p> Google Analytics | 公式ウェブサイト <a href="https://marketingplatform.google.com/about/analytics/" target="_blank">https://marketingplatform.google.com/about/analytics/</a><br />
プライバシー ポリシー - Google ポリシーと規約<a href="https://policies.google.com/privacy" target="_blank">https://policies.google.com/privacy</a></p>
<p>【本ポリシーの適用範囲】
<br>本ポリシーの適用範囲は当ウェブサイト内とします。
当ウェブサイトからリンクの張られている他のウェブサイトのプライバシー保護についての責任は負いかねますので、それぞれのウェブサイトのプライバシーポリシーをご確認ください。</p>

<!--/cbox-inner--></div>
<!--/cbox--></div>


</div>


EOF;



$disp3 = <<< EOF


EOF;


if($errflg){
	$disp=$disp2;
}else{
	$disp=$disp1.$disp2.$disp3;
}





}

//print_r($_POST);
//print_r($pdata);


include_once("temp.html");
?>
