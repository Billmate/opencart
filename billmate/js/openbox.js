$(".billmate_terms").bind('click',function(event){event.preventDefault();});
$(function(){
	if( $(".billmate_terms").hasClass('superbinded') ) return false;
	$.superbox();
	$(".billmate_terms").addClass('superbinded');
	$('#superbox').find('p.close').css({'float':'right'});
});
