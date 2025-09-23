(function($){
   let mm = gsap.matchMedia();
   
   $(function(){
      /**** Pinned Scroll Trigger Sections ****/
      if( $(".featured-cards-full-wrapper").length ) {
         mm.add("(min-width: 991px)", () => {
            $(".featured-cards-full-wrapper").each(function() {
               let $cards = $(this).find(".featured-cards-full__card"),
                  sectionHeight = $(this).innerHeight();
               if( $cards.length ) {
                  const cards = gsap.utils.toArray($cards);

                  cards.forEach((card, index) => {
                     let cardHeight = card.clientHeight;
                     console.log( "clientHeight", cardHeight * (index) );
                     if (index === 0) return;

                     gsap.set(card, { top: ((cardHeight * index) + 65) });

                     gsap.to(card, {
                        top: 0,
                        scrollTrigger: {
                           trigger: card,
                           start: `top bottom-=${ cardHeight }`,
                           end: `+=${ cardHeight }`,
                           endTrigger: card,
                           scrub: true,
                           invalidateOnRefresh: true
                        }
                     })
                  });
               }


               ScrollTrigger.create({
                  trigger: $(this),
                  start: `top top+=90`,
                  end: `+=${ $cards.length ? $cards.length * $cards[0].clientHeight : 0 }`,
                  pin: true,
                  pinSpacing: true,
                  invalidateOnRefresh: true,
               });
            });
         });
      }


      //Scrolling Banner Animation
      if( $(".banner-image-scroll-main").length ) {
         mm.add("(min-width: 991px)", () => {
            let homeTl = gsap.timeline({
                 scrollTrigger: {
                     trigger: '.banner-image-scroll-main .container',
                     pin: true,
                     pinSpacing: true,
                     start: 'top-=90',
                     end: 'bottom',
                     scrub: 1.2,
                 },
            });

            homeTl.to('.banner-image-scroll-media img', { left: 0, right: 0, top: 0, bottom: 60 });
            homeTl.to('.banner-image-scroll-text', { opacity: 0, y: '-50' }, "<");
            homeTl.to('.banner-image-scroll-row-2', { opacity: 0, y: '-50' }, "<");
             
            homeTl.to('.banner-image-scroll-media img', { width: '100%', height: 'calc(100% - 120px)' });
         });
      }

      /**** Pinned Scroll Trigger Sections ****/


      //Wrap the heading content in a span for informational content section
      if( $(".content-no-media").length ) {
         $(".content-no-media .wp-block-heading").each(function() {
            mySplitText = new SplitText( this, {type:"lines", tag: "span"} );
         });
      }
      
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
         });
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

            gsap.to( $(this).find(".wp-block-media-text__content"), {
               x: 0,
               opacity: 1,
               duration: 1,
               delay: 0.5, 
               scrollTrigger: this,
               ease: "power1.out",
            });
         });
      }

      if( $(".feature-details-columns").length ) {
         $(".feature-details-columns").each(function() {
            gsap.to( $(this).find(".feature-detail-column__left, .feature-detail-column__right") , {
               x: 0,
               opacity: 1,
               delay: 0.5, 
               duration: 1,
               scrollTrigger: this,
               ease: "power1.out",
            });
         });
      }

   });


   $(window).on("load", () => {
     ScrollTrigger.refresh();
   });
})(jQuery);