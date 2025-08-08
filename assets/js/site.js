(function($){
   $(function(){
        $('.header-menu-toggle').click(function() {
            var bsTarget = $(this).data('bs-target');
            $(bsTarget).toggleClass('active');
        });
   });


})(jQuery);