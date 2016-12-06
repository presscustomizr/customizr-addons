<?php
class CZRA_Czr {
  static $instance;
  function __construct() {
    self::$instance =& $this;

    //CUSTOMIZER PANEL JS
    add_action( 'customize_controls_print_footer_scripts'    , array( $this, 'czra_extend_visibilities' ), 100 );
    //Various DOM ready actions + print rate link + template
    add_action( 'customize_controls_print_footer_scripts'    , array( $this, 'czra_various_dom_ready' ) );
    //control style
    add_action( 'customize_controls_enqueue_scripts'         , array( $this, 'czra_customize_controls_js_css' ) );
  }

  /**************************************************************
  ** CUSTOMIZER
  **************************************************************/
  /**
   * Add script to controls
   * Dependency : customize-controls located in wp-includes/script-loader.php
   * Hooked on customize_controls_enqueue_scripts located in wp-admin/customize.php
   * @package Customzir Addons
   * @since Customizr Addons 1.0.1
   */
  function czra_customize_controls_js_css() {

    wp_enqueue_style(
      'czra-czr-controls-style',
      sprintf( '%1$sassets/czr/css/czr-control-footer.css', CZRA_BASE_URL ),
      array( 'customize-controls' ),
      time(),
      $media = 'all'
    );
    //INCLUDED IN THE THEME CUSTOMIZR FMK - TO REMOVE
    wp_enqueue_script(
      'czra-czr-footer-script',
      sprintf( '%1$sassets/czr/js/czr-control-footer.js', CZRA_BASE_URL ),
      array( 'customize-controls', 'underscore' ),
      time(),
      $media = 'all'
    );
  }




  //hook : customize_controls_print_footer_scripts
  function czra_various_dom_ready() {
    ?>
    <script id="rate-tpl" type="text/template" >
      <?php
        printf( '<span class="czr-rate-link">%1$s %2$s, <br/>%3$s <a href="%4$s" title="%5$s" class="czr-stars" target="_blank">%6$s</a> %7$s</span>',
          __( 'If you like' , 'customizr-addons' ),
          __( 'the Customizr theme' , 'customizr-addons'),
          __( 'we would love to receive a' , 'customizr-addons' ),
          'https://' . 'wordpress.org/support/view/theme-reviews/customizr?filter=5',
          __( 'Review the Customizr theme' , 'customizr-addons' ),
          '&#9733;&#9733;&#9733;&#9733;&#9733;',
          __( 'rating. Thanks :) !' , 'customizr-addons')
        );
      ?>
    </script>
    <script id="rate-theme" type="text/javascript">
      (function ($) {
        $( function($) {
          //Render the rate link
          _render_rate_czr();
          function _render_rate_czr() {
            var _cta = _.template(
                  $( "script#rate-tpl" ).html()
            );
            $('#customize-footer-actions').append( _cta() );
          }
        });
      })(jQuery)
    </script>
    <?php
  }


  //hook : 'customize_controls_enqueue_scripts'
  function czra_extend_visibilities() {
    ?>
    <script type="text/javascript">
        (function (api, $, _) {
            if ( ! _.has( api, 'CZR_ctrlDependencies') )
              return;
            //@return boolean
            var _is_checked = function( to ) {
                return 0 !== to && '0' !== to && false !== to && 'off' !== to;
            };
            api.CZR_ctrlDependencies.prototype.dominiDeps = _.extend(
                  api.CZR_ctrlDependencies.prototype.dominiDeps,
                  [
                      {
                          dominus : 'sharrre',
                          servi : [
                            'tc_sharrre-scrollable',
                            'tc_sharrre-twitter-on',
                            'tc_twitter-username',
                            'tc_sharrre-facebook-on',
                            'tc_sharrre-google-on',
                            'tc_sharrre-pinterest-on',
                            'tc_sharrre-linkedin-on'
                          ],
                          visibility : function (to) {
                              return _is_checked(to);
                          }
                      },
                       {
                          dominus : 'tc_sharrre-twitter-on',
                          servi : ['tc_twitter-username'],
                          visibility : function (to) {
                              return _is_checked(to);
                          }
                      },
                  ]
            );
        }) ( wp.customize, jQuery, _);
    </script>
    <?php
  }
}//end of class