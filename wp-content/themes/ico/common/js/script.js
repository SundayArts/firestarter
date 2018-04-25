$(function(){
	var $navbarH = $('#jsi-header').outerHeight();
	$('a[href^="#"]').click(function(){
		if($('.navbar-collapse.collapse').hasClass('show')){
			$('.navbar-collapse.collapse').removeClass('show');
		}
		var speed = 500;
		var href= $(this).attr("href");
		var target = $(href == '#' || href == '' ? 'html' : href);
		var position = target.offset().top - $navbarH;
		$('html, body').animate({scrollTop:position}, speed, 'swing');
		return false;
	});
});

$(function(){
	var jscNavTrigger = $('.jsc-nav-trigger'),
		jacNavmenu = $('.jac-navmenu');
	jscNavTrigger.on('click',function(){
		jacNavmenu.toggleClass('navmenu-open');
	});
});

$(window).on('scroll',function(){
	var $top = $(this).scrollTop(),
		$pageTop = $('#jsi-page-top');
	if( 200 < $top ){
		$pageTop.addClass('page-top-block');
	}else if (200 > $top){
		$pageTop.removeClass('page-top-block');
	}
});

$(function(){
	var $modalAddress = $('#modal-address');
	$modalAddress.on('shown.bs.modal', function () {
		$('body').addClass('modal-open');
	});
});

$(function(){
	$('.contribute-main input[type="checkbox"]').on('click',function(event){
		$check = $('.contribute-main input[type="checkbox"]').prop('checked');
		if ($check) {
			$('.contribute-main button[type="button"]').removeClass('inactive');
		} else {
			$('.contribute-main button[type="button"]').addClass('inactive');
		}
	});
});
$(function(){
	$('.contribute-main button[type="button"]').on('click',function(event){
		event.preventDefault();
		$check = $('.contribute-main input[type="checkbox"]').prop('checked');
		if ($check) {
			$(this).attr('data-target', '#modal-warning');
		} else {
			$(this).attr('data-target', '');
		}
	});
});

$(function(){
	$('.contribute-ico input[type="checkbox"]').on('click',function(event){
		$check = $('.contribute-ico input[type="checkbox"]').prop('checked');
		if ($check) {
			$('.contribute-ico button[type="button"]').removeClass('inactive');
		} else {
			$('.contribute-ico button[type="button"]').addClass('inactive');
		}
	});
});
$(function(){
	$('.contribute-ico button[type="button"]').on('click',function(event){
		event.preventDefault();
		$check = $('.contribute-ico input[type="checkbox"]').prop('checked');
		if ($check) {
			$(this).attr('data-target', '#modal-warning');
		} else {
			$(this).attr('data-target', '');
		}
	});
});
		
$(function(){
	countDown();
});

function countDown() {
    var left_d = Number($('.countdown-list .countdown-num').eq(0).text());
    var left_h = Number($('.countdown-list .countdown-num').eq(1).text());
    var left_m = Number($('.countdown-list .countdown-num').eq(2).text());
    var left_s = Number($('.countdown-list .countdown-num').eq(3).text());

    var left = left_d * 24 * 60 * 60
             + left_h * 60 * 60
             + left_m * 60
             + left_s;
    var a_day = 24 * 60 * 60;

	if (left > 0) {
	    left = left - 1;
	}
    
    var d = Math.floor(left / a_day);
    var h = Math.floor((left % a_day) / (60 * 60));
    var m = Math.floor((left % a_day) / 60) % 60;
    var s = Math.floor(left % a_day) % 60 % 60 ;
    
    $('.countdown-list .countdown-num').eq(0).text(d);
    $('.countdown-list .countdown-num').eq(1).text(('00' + h).slice(-2));
    $('.countdown-list .countdown-num').eq(2).text(('00' + m).slice(-2));
    $('.countdown-list .countdown-num').eq(3).text(('00' + s).slice(-2));
    setTimeout('countDown()', 1000);
}