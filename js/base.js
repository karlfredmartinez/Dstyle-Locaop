
$(function () {
/**page top**/
var topBtn = $('#pagetop');
topBtn.hide();
//スクロールが100に達したらボタン表示
$(window).scroll(function () {
if ($(this).scrollTop() > 100) {
topBtn.fadeIn();
} else {
topBtn.fadeOut();
}
});
//スクロールしてトップ
topBtn.click(function () {
$('body,html').animate({
scrollTop: 0
}, 500);
return false;
});

//ページ内リンク
$('a[href^="#"]').click(function () {
var href = $(this).attr("href");
var target = $(href == "#" || href == "" ? 'html' : href);
var hheight = $("#header").height();
var position = target.offset().top - hheight;

$("html, body").animate({
scrollTop: position
}, 500, "swing");
return false;
});


//


/** 電話 **/
$(".tellink").css("cursor", "pointer");
$(".tellink").removeAttr("href");
$(".tellink").click(function () {
$("#telpopup-frame").show();
});
$("#telpopup-close a").click(function () {
$("#telpopup-frame").hide();
});


/** スマホグローバルメニュー **/
var state = false;
var scrollpos;
function smenuopen() {
	if (state == true) {
	return false;
	}
	scrollpos = $(window).scrollTop();
	$('body').addClass('open');
	state = true;
}
function smenuclose() {
	if (state == false) {
		return false;
	}
	$("body").removeClass("open");
	state = false;
	window.scrollTo(0, scrollpos);
}

$('.smbtn').click(function () {
	if (state == false) {
		smenuopen();
	} else {
		smenuclose();
	}
});
$("#smenu .close").click(function () {
	smenuclose();
});
$(window).on('resize', function () {
	if ($(window).width() < 1024) {
		return;
	}
	smenuclose();
	$("#telpopup-frame").hide();
});





/** function end **/
});
