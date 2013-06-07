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
//document.body.scrollBottom = 0;

$('#Form_Search.form-text').val('Поиск');
 
$('#Form_Search.form-text').blur(function(){
     if(this.value==''){
       this.value='Поиск';
     }
  });   
  $('#Form_Search.form-text').focus(function(){
    if(this.value=='Поиск'){
      this.value='';
    }
  });

//autoupdate
    
    
    
   

}

