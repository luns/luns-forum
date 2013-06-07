function click_button_up()
{
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
}

$(function() {
 $.fn.scrollToTop = function() {
  $(this).hide().removeAttr("href");
  if ($(window).scrollTop() >= "250") $(this).fadeIn("slow")
  var scrollDiv = $(this);
  $(window).scroll(function() {
   if ($(window).scrollTop() <= "250") $(scrollDiv).fadeOut("slow")
   else $(scrollDiv).fadeIn("slow")
  });
  $(this).click(function() {
   $("html, body").animate({scrollTop: 0}, "slow")
  })
 }
});
 
$(function() {
 $("#Go_Top").scrollToTop();
});


onload = function() {

//document.body.firstChild.data = "";

}


