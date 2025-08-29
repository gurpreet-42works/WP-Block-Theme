(function($){
   $(function(){
      
      if( $(".inner-page-hero, .banner-content-centered").length ) {
         $(".inner-page-hero, .banner-content-centered").each(function() {
            gsap.to( $(this).find(".wp-block-heading") , {
               y: 0,
               opacity: 1,
               stagger: 0.1,
               duration: 0.8
            });

            gsap.to( $(this).find("p") , {
               y: 0,
               opacity: 1,
               duration: 0.8,
               stagger: 0.1
            });

            if( $(this).find(".wp-block-buttons").length ) {
               gsap.to( $(this).find(".wp-block-buttons") , {
                  y: 0,
                  opacity: 1,
                  duration: 0.8,
                  stagger: 0.1
               });
            }
         })
      }

      $(".section-heading-wrap").each(function() {
         let elem = this, 
            heading = $(this).find(".wp-block-heading"),
            content = $(this).find("p");
         gsap.to(heading, { y: 0, opacity: 1, delay: 0.5, duration: 1, scrollTrigger: elem });
         gsap.to(content, { y: 0, opacity: 1, delay: 0.5, duration: 1, scrollTrigger: elem });
      });

      if( $(".feature-cards-grid").length ) {
         $(".feature-cards-grid").each(function() {
            gsap.to( $(this).find(".feature-card") , {
               y: 0,
               opacity: 1,
               stagger: 0.1,
               delay: 0.5, 
               scrollTrigger: this
            });
         })
      }

      if( $(".listing-cards-grid").length ) {
         $(".listing-cards-grid").each(function() {
            gsap.to( $(this).find(".card") , {
               y: 0,
               opacity: 1,
               stagger: 0.1,
               delay: 0.5,
               duration: 1, 
               scrollTrigger: this
            });
         })
      }

      if( $(".testimonial-grid-container").length ) {
         $(".testimonial-grid-container").each(function() {
            gsap.to( $(this).find(".testimonial-grid-block") , {
               y: 0,
               opacity: 1,
               stagger: 0.1,
               delay: 0.5,
               duration: 1, 
               scrollTrigger: this
            });
         })
      }

      if( $(".wp-block-post-template").length ) {
         $(".wp-block-post-template").each(function() {
            gsap.to( $(this).find(".card") , {
               y: 0,
               opacity: 1,
               stagger: 0.1,
               delay: 0.5,
               duration: 1, 
               scrollTrigger: this
            });
         })
      }

      if( $(".our-team-grid").length ) {
         $(".our-team-grid").each(function() {
            gsap.to( $(this).find(".social-card") , {
               y: 0,
               opacity: 1,
               stagger: 0.2,
               delay: 0.5,
               duration: 1,
               scrollTrigger: this,
               ease: "power1.out",
            });
         })
      }

      if( $(".about-journery-content").length ) {
         $(".about-journery-content").each(function() {
            gsap.to( $(this).find(".wp-block-image") , {
               x: 0,
               opacity: 1,
               delay: 0.5, 
               duration: 1,
               scrollTrigger: this,
               ease: "power1.out",
            });

            gsap.to( $(this).find(".wp-block-heading, p") , {
               y: 0,
               opacity: 1,
               delay: 0.5,
               scrollTrigger: this,
            });

            gsap.to( $(this).find(".about-stats-grid > .wp-block-group") , {
               y: 0,
               opacity: 1,
               delay: 0.5,
               stagger: 0.1,
               scrollTrigger: this,
            });
         })
      }

      if( $(".content-with-media-section").length ) {
         $(".content-with-media-section").each(function() {
            gsap.to( $(this).find("img") , {
               x: 0,
               opacity: 1,
               delay: 0.5, 
               duration: 1,
               scrollTrigger: this,
               ease: "power1.out",
            });

            gsap.to( $(this).find(".wp-block-media-text__content") , {
               x: 0,
               opacity: 1,
               duration: 1,
               delay: 0.5, 
               scrollTrigger: this,
               ease: "power1.out",
            });
         })
      }



   });
})(jQuery);