<div class="sharrre-container">
	<span><?php _e('Share','customizr-addons'); ?></span>
  <?php if ( tc_is_checked('tc_sharrre-twitter-on') ) : ?>
	   <div id="twitter" data-url="<?php echo the_permalink(); ?>" data-text="<?php echo the_title(); ?>" data-title="<?php _e('Tweet', 'customizr-addons'); ?>"><a class="box" href="#"><div class="count" href="#"><i class="fa fa-plus"></i></div><div class="share"><i class="fa fa-twitter"></i></div></a></div>
  <?php endif; ?>
  <?php if ( tc_is_checked('tc_sharrre-facebook-on') ) : ?>
	   <div id="facebook" data-url="<?php echo the_permalink(); ?>" data-text="<?php echo the_title(); ?>" data-title="<?php _e('Like', 'customizr-addons'); ?>"></div>
  <?php endif; ?>
  <?php if ( tc_is_checked('tc_sharrre-google-on') ) : ?>
	   <div id="googleplus" data-url="<?php echo the_permalink(); ?>" data-text="<?php echo the_title(); ?>" data-title="<?php _e('+1', 'customizr-addons'); ?>"></div>
  <?php endif; ?>
  <?php if ( tc_is_checked('tc_sharrre-pinterest-on') ) : ?>
	   <div id="pinterest" data-url="<?php echo the_permalink(); ?>" data-text="<?php echo the_title(); ?>" data-title="<?php _e('Pin It', 'customizr-addons'); ?>"></div>
  <?php endif; ?>
  <?php if ( tc_is_checked('tc_sharrre-linkedin-on') ) : ?>
    <div id="linkedin" data-url="<?php echo the_permalink(); ?>" data-text="<?php echo the_title(); ?>" data-title="<?php _e('Publish on Linked In', 'customizr-addons'); ?>"></div>
  <?php endif; ?>
</div><!--/.sharrre-container-->

<script type="text/javascript">
	// Sharrre
	jQuery(function($){
    <?php if ( tc_is_checked('tc_sharrre-twitter-on') ) : ?>
    		$('#twitter').sharrre({
    			share: {
    				twitter: true
    			},
    			template: '<a class="box" href="#"><div class="count" href="#">{total}</div><div class="share"><i class="fa fa-twitter"></i></div></a>',
    			enableHover: false,
    			enableTracking: true,
    			buttons: { twitter: {via: '<?php echo esc_attr( TC_utils::$inst->tc_opt( "twitter-username" ) ); ?>'}},
    			click: function(api, options){
    				api.simulateClick();
    				api.openPopup('twitter');
    			}
    		});
    <?php endif; ?>
		<?php if ( tc_is_checked('tc_sharrre-facebook-on') ) : ?>
        $('#facebook').sharrre({
    			share: {
    				facebook: true
    			},
    			template: '<a class="box" href="#"><div class="count" href="#">{total}</div><div class="share"><i class="fa fa-facebook-square"></i></div></a>',
    			enableHover: false,
    			enableTracking: true,
          buttons:{layout: 'box_count'},
    			click: function(api, options){
    				api.simulateClick();
    				api.openPopup('facebook');
    			}
    		});
    <?php endif; ?>
    <?php if ( tc_is_checked('tc_sharrre-google-on') ) : ?>
    		$('#googleplus').sharrre({
    			share: {
    				googlePlus: true
    			},
    			template: '<a class="box" href="#"><div class="count" href="#">{total}</div><div class="share"><i class="fa fa-google-plus-square"></i></div></a>',
    			enableHover: false,
    			enableTracking: true,
          buttons:{size: 'tall'},
    			urlCurl: '<?php echo plugins_url( basename( dirname( dirname( __FILE__ ) ) ) ) . "/assets/front/js/sharrre.php"; ?>',
    			click: function(api, options){
    				api.simulateClick();
    				api.openPopup('googlePlus');
    			}
    		});
    <?php endif; ?>
    <?php if ( tc_is_checked('tc_sharrre-pinterest-on') ) : ?>
    		$('#pinterest').sharrre({
    			share: {
    				pinterest: true
    			},
    			template: '<a class="box" href="#" rel="nofollow"><div class="count" href="#">{total}</div><div class="share"><i class="fa fa-pinterest"></i></div></a>',
    			enableHover: false,
    			enableTracking: true,
    			buttons: {
    			pinterest: {
    				description: '<?php echo the_title(); ?>'<?php if( has_post_thumbnail() ){ ?>,media: '<?php echo wp_get_attachment_url( get_post_thumbnail_id() ); ?>'<?php } ?>
    				}
    			},
    			click: function(api, options){
    				api.simulateClick();
    				api.openPopup('pinterest');
    			}
    		});
    <?php endif; ?>
    <?php if ( tc_is_checked('tc_sharrre-linkedin-on') ) : ?>
        $('#linkedin').sharrre({
          share: {
            linkedin: true
          },
          template: '<a class="box" href="#" rel="nofollow"><div class="count" href="#">{total}</div><div class="share"><i class="fa fa-linkedin"></i></div></a>',
          enableHover: false,
          enableTracking: true,
          buttons: {
          linkedin: {
            description: '<?php echo the_title(); ?>'<?php if( has_post_thumbnail() ){ ?>,media: '<?php echo wp_get_attachment_url( get_post_thumbnail_id() ); ?>'<?php } ?>
            }
          },
          click: function(api, options){
            api.simulateClick();
            api.openPopup('linkedin');
          }
        });
    <?php endif; ?>
		<?php if ( tc_is_checked( 'tc_sharrre-scrollable' ) ) : ?>

			// Scrollable sharrre bar, contributed by Erik Frye. Awesome!
			var $_shareContainer = $(".sharrre-container"),
			    $_header         = $('.tc-header'),
			    $_postEntry      = $('.entry-content'),
    			$window          = $(window),
    			startSharePosition = $_shareContainer.offset(),//object
    			contentBottom    = $_postEntry.offset().top + $_postEntry.outerHeight(),
    			topOfTemplate    = $_header.offset().top,
          topSpacing       = _setTopSpacing();

      //triggered on scroll
			shareScroll = function(){
        if( ! ( $window.width() > 480) )
          return;

				var scrollTop     = $window.scrollTop() + topOfTemplate,
				    stopLocation  = contentBottom - ($_shareContainer.outerHeight() + topSpacing);

				if(scrollTop > stopLocation){
					$_shareContainer.offset({top: contentBottom - $_shareContainer.outerHeight(),left: startSharePosition.left});
				}
				else if(scrollTop >= $_postEntry.offset().top - topSpacing){
					$_shareContainer.offset({top: scrollTop + topSpacing, left: startSharePosition.left});
				}else if(scrollTop < startSharePosition.top+(topSpacing-1)){
					$_shareContainer.offset({top: $_postEntry.offset().top,left:startSharePosition.left});
				}
			},

      //triggered on resize
			shareMove = function() {
				startSharePosition = $_shareContainer.offset();
				contentBottom = $_postEntry.offset().top + $_postEntry.outerHeight();
				topOfTemplate = $_header.offset().top;
				_setTopSpacing();
			};

			/* As new images load the page content body gets longer. The bottom of the content area needs to be adjusted in case images are still loading. */
			setTimeout(function() {
				contentBottom = $_postEntry.offset().top + $_postEntry.outerHeight();
			}, 2000);

      //setup event listeners
			if (window.addEventListener) {
				window.addEventListener('scroll', shareScroll, false);
				window.addEventListener('resize', shareMove, false);
			} else if (window.attachEvent) {
				window.attachEvent('onscroll', shareScroll);
				window.attachEvent('onresize', shareMove);
			}

			function _setTopSpacing(){
        var distanceFromTop  = 20;

				if($window.width() > 1024)
					topSpacing = distanceFromTop;
				else
					topSpacing = distanceFromTop;
        return topSpacing;
			}
		<?php endif; ?>

	});
</script>