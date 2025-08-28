(function($){
   $(function(){
        $('.header-menu-toggle').click(function() {
            var bsTarget = $(this).data('bs-target');
            $(bsTarget).toggleClass('active');
        });

        $(".accordion-wrapper .wp-block-details").click( function(e) {
            e.preventDefault();
            var attr = $(this).attr('open');
            $(".accordion-wrapper .wp-block-details").not( $(this) ).removeAttr("open");
            typeof attr == 'undefined' || attr == false ? $(this).attr("open", true) : $(this).removeAttr("open");
        } )


        //Add a class if there are no social icons in bottom footer
        if( $("#footer-row-bottom").find(".footer-social-nav").length == 0 ){
            $("#footer-row-bottom").addClass('no-social');
        }

        //Add Lightbox effect to WP gallery
        $('.fancybox-gallery a').each(function(i, el) {
            el.setAttribute('data-fancybox', 'gallery');
        });



        // Init fancybox
        Fancybox.bind("[data-fancybox='gallery']", {
            Toolbar: {
                display: ["close"]
            }
        });
        
   });


})(jQuery);