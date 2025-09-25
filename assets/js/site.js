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

        //Testimonials Slider
        if( $(".testimonials-slider__wrapper").length ){
            $(".testimonials-slider__wrapper").slick({
              slidesToShow: 1,
              slidesToScroll: 1,
              autoplay: true,
              autoplaySpeed: 3000,
              fade: true,
              speed: 700
            });
        }

        if( $(".listing-cards-grid.slider-grid").length ){
            $(".listing-cards-grid.slider-grid").slick({
              slidesToShow: 3,
              slidesToScroll: 1,
              autoplay: true,
              autoplaySpeed: 3000,
              speed: 700,
              dots: true,
              arrows: false,
              responsive: [
                {
                  breakpoint: 991,
                  settings: {
                    slidesToShow: 2
                  }
                },
                {
                  breakpoint: 650,
                  settings: {
                    slidesToShow: 1
                  }
                }
              ]
            });
        }

        //Logos Slider
        if( $(".social-logos-gallery").length ){
            jQuery('.social-logos-gallery').slick({
                speed: 5000,
                autoplay: true,
                autoplaySpeed: 0,
                centerMode: true,
                cssEase: 'linear',
                slidesToShow: 6,
                slidesToScroll: 1,
                infinite: true,
                initialSlide: 1,
                arrows: false,
                buttons: false,
                responsive: [
                {
                  breakpoint: 1600,
                  settings: {
                    slidesToShow: 5
                  }
                },
                {
                  breakpoint: 1200,
                  settings: {
                    slidesToShow: 4
                  }
                },
                {
                  breakpoint: 768,
                  settings: {
                    slidesToShow: 3
                  }
                }
              ]
            });
        }


        // Init fancybox
        Fancybox.bind("[data-fancybox='gallery']", {
            Toolbar: {
                display: ["close"]
            }
        });
        
   });


})(jQuery);