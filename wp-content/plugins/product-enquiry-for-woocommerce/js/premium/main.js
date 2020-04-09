$(window).load(function() {

	$(".content-wrapper, .image-wrapper").viewportChecker({
		classToAdd: "visible",
		classToAddForFullView: "",
		offset: 150,
		invertBottomOffset: 0,
		repeat: false
	});
});
function scrollDown(){
	var nextElemTop = $('.add-enquiry').offset().top;
	$('body').on('click', '#scroll-down', function(){
		$('html,body').animate({scrollTop : nextElemTop},800);
	});
}
$(document).ready(function(){
	scrollDown();
});
$(window).on("debouncedresize", scrollDown);
