$(function () {
	$('textarea').summernote({
		   height: 100,
		   toolbar: [
		    //['style', ['style']], // no style button
		    ['style', ['bold', 'italic', 'underline', 'clear']],
		    ['fontsize', ['fontsize']],
		    ['color', ['color']],
		    ['para', ['ul', 'ol', 'paragraph']],
		    ['height', ['height']],
		    //['insert', ['picture', 'link']], // no insert buttons
		    //['table', ['table']], // no table button
		    //['help', ['help']] //no help button
		  ]
		});
    $(window).scroll(function(){
        // add navbar opacity on scroll
        if ($(this).scrollTop() > 100) {
            $(".navbar.navbar-fixed-top").addClass("scroll");
        } else {
            $(".navbar.navbar-fixed-top").removeClass("scroll");
        }

        // global scroll to top button
        if ($(this).scrollTop() > 300) {
            $('.scrolltop').fadeIn();
        } else {
            $('.scrolltop').fadeOut();
        }        
    });

    $('#myCarousel').carousel('cycle');

    // scroll back to top btn
    $('.scrolltop').click(function(){
        $("html, body").animate({ scrollTop: 0 }, 700);
        return false;
    });
    
    // scroll navigation functionality
    $('.scroller').click(function(){
    	var section = $($(this).data("section"));
    	var top = section.offset().top;
        $("html, body").animate({ scrollTop: top }, 700);
        return false;
    });

    // FAQs
    var $faqs = $("#faq .faq");
    $faqs.click(function () {
        var $answer = $(this).find(".answer");
        $answer.slideToggle('fast');
    });

    if (!$.support.leadingWhitespace) {
        //IE7 and 8 stuff
        $("body").addClass("old-ie");
    }
    
});

function contentvote(module,updown,content,id,showresult)
{
	$.ajax({
		url: $('#url').val()+module+'/contentvote',
		type: "GET",
		data: { updown: updown,content:content, id: id },
		success: function(data){
			$( "#"+showresult ).text(data);
		}
	});
}