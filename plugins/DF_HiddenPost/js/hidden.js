
function Gdn_Hide(){

Gdn_Hide.prototype.Prepare = function() {
   
      $('span.CommentHide a').livequery('click', jQuery.proxy(function(event){
         var HideLink = $(event.target);
         var ObjectID = HideLink.attr('href').split('/').pop();
         this.Hide(ObjectID);
         return false;
      },this));
            
   }

   Gdn_Hide.prototype.Hide = function(ObjectID) {
      
	  alert(ObjectID);
	  
   }
   
}

var GdnHide = null;
jQuery(document).ready(function(){
   GdnHide = new Gdn_Hide();
   GdnHide.Prepare();
});