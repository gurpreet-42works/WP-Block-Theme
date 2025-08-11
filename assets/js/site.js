(function($){
   $(function(){
        $('.header-menu-toggle').click(function() {
            var bsTarget = $(this).data('bs-target');
            $(bsTarget).toggleClass('active');
        });


        //Add a class if there are no social icons in bottom footer
        if( $("#footer-row-bottom").find(".footer-social-nav").length == 0 ){
            $("#footer-row-bottom").addClass('no-social');
        }
        
   });


})(jQuery);