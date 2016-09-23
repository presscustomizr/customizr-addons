<?php
/**
* Plugin Name: Customizr Addons
* Plugin URI: http://presscustomizr.com
* Description: Lightweight addons plugin for the Customizr WordPress theme.
* Version: 1.0.0
* Text Domain: customizr-addons
* Author: Press Customizr
* Author URI: http://presscustomizr.com
* License: GPLv2 or later
*/


/**
* Fires the plugin
* @author Nicolas GUILLAUME
* @since 1.0
*/
if ( ! class_exists( 'CZR_addons_plugin' ) ) :
class CZR_addons_plugin {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    public static $theme;
    public static $theme_name;
    public $is_customizing;


    function __construct() {
      self::$instance =& $this;

      //checks if is customizing : two context, admin and front (preview frame)
      $this -> is_customizing = $this -> tc_is_customizing();

      self::$theme          = $this -> tc_get_theme();
      self::$theme_name     = $this -> tc_get_theme_name();

      //stop execution if not Customizr
      if ( false === strpos( self::$theme_name, 'customizr' ) ) {
        add_action( 'admin_notices', array( $this , 'tc_admin_notice' ) );
        return;
      }

      //TEXT DOMAIN
      //adds plugin text domain
      add_action( 'plugins_loaded', array( $this , 'tc_plugin_lang' ) );


      //SHARRRE
      add_action( 'wp_head', array( $this, 'tc_write_sharre_style') );
      add_action( 'wp_enqueue_scripts', array( $this, 'tc_addons_style_scripts' ) );
      add_filter( 'tc_single_post_option_map', array( $this, 'tc_register_sharrre_settings'));
      add_action( 'wp', array($this, 'tc_sharrre_front_actions') );

      //CUSTOMIZER PANEL JS
      //add_action( 'customize_controls_print_footer_scripts', array( $this, 'tc_extend_visibilities' ), 100 );
    }//end of construct


    //hook : wp_head
    function tc_write_sharre_style() {
      ?>
        <style type="text/css">
          .entry-content.share { padding-right: 100px; min-height: 354px; /* share buttons height */ position: relative; }
          .entry.share .entry-inner { float: left; width: 100%; }

          /*  single : sharrre
          /* ------------------------------------ */
          .sharrre-container { float: right; width: 50px; padding: 0 10px; margin-right: -100px;
          -webkit-border-radius: 4px; border-radius: 4px; }
          .sharrre-container span { color: #aaa; display: block; text-align: center; text-transform: uppercase; font-size: 11px; }
          .sharrre { padding: 10px 0 0; }
          .sharrre .box { width: 50px; display: block; text-decoration: none;}
          .sharrre .count { background: #eee; color: #333; display: block; font-size: 15px; font-weight: 600; line-height: 30px; position: relative; text-align: center;
          -webkit-border-radius: 4px; border-radius: 4px; }
          .sharrre .count:after { content:''; display: block; position: absolute; left: 49%; width: 0; height: 0; border: solid 6px transparent; border-top-color: #eee; margin-left: -6px; bottom: -12px; }
          .sharrre .share { display: block; font-size: 28px; font-weight: 600; line-height: 32px; margin-top: 12px; padding: 0; text-align: center; text-decoration: none; }
          .sharrre .box .share,
          .sharrre .box .count { -webkit-transition: all .3s ease; transition: all .3s ease; }
          .sharrre .box:hover .share,
          .sharrre .box:hover .count { color: #444!important; }
          .sharrre#twitter .share,
          .sharrre#twitter .box .count { color: #00acee; }
          .sharrre#facebook .share,
          .sharrre#facebook .box .count { color: #3b5999; }
          .sharrre#googleplus .share,
          .sharrre#googleplus .box .count { color: #cd483c; }
          .sharrre#pinterest .share,
          .sharrre#pinterest .box .count { color: #ca2128; }

          @media only screen and (max-width: 767px) {
            /*.sharrre-container { position: relative; float: left; width: auto; padding: 0; margin: 20px 0 0; }
            .sharrre-container span { text-align: left; }
            .sharrre-container > div { float: left; margin-right: 10px; }*/
          }
          @media only screen and (max-width: 479px) {
            /* Don't display the sharre bar */
            .sharrre-container { display: none;}
            .entry-content.share { padding-right: 0; }
          }
        </style>
      <?php
    }





    //hook : tc_single_post_option_map
    function tc_register_sharrre_settings( $settings ) {
      $sharrre_settings = array(
        'tc_sharrre' => array(
              'default'   => 1,
              'control'   => 'TC_controls',
              'label'     => __('Display social sharing buttons in your single posts', 'customizr-addons'),
              'title'     => __('Social Sharring Bar Setttings', 'customizr-addons'),
              'notice'    => __('Display social sharing buttons in each single articles.', 'customizr-addons'),
              'section'   => 'single_posts_sec',
              'type'      => 'checkbox',
              'priority'  => 40
        ),
        'tc_sharrre-scrollable' => array(
              'default'   => 1,
              'control'   => 'TC_controls',
              'label'     => __('Make the Share Bar "sticky"', 'customizr-addons'),
              'notice'    => __('Make the social share bar stick to the browser window when scrolling down a post.', 'customizr-addons'),
              'section'   => 'single_posts_sec',
              'type'      => 'checkbox',
              'priority'  => 50
        ),
        'tc_sharrre-twitter-on' => array(
              'default'   => 1,
              'control'   => 'TC_controls',
              'label'     => __('Enable Twitter Button', 'customizr-addons'),
              'section'   => 'single_posts_sec',
              'type'      => 'checkbox',
              'notice'    => __('Since Nov. 2015, Twitter disabled the share counts from its API. If you want to get the display count anyway, you can create an account for free (as of Feb. 2016) on [https://opensharecount.com/]. The Customizr Addons plugin is configured to use opensharecount.', 'customizr-addons'),
              'priority'  => 60
        ),
        'tc_twitter-username' => array(
              'default'   => '',
              'control'   => 'TC_controls',
              'label'     => __('Twitter Username (without "@")', 'customizr-addons'),
              'notice'    => __('Simply enter your username without the "@" prefix. Your username will be added to share-tweets of your posts (optional).', 'customizr-addons'),
              'section'   => 'single_posts_sec',
              'type'      => 'text',
              'transport' => 'postMessage',
              'priority'  => 70
        ),
        'tc_sharrre-facebook-on' => array(
              'default'   => 1,
              'control'   => 'TC_controls',
              'label'     => __('Enable Facebook Button', 'customizr-addons'),
              'section'   => 'single_posts_sec',
              'type'      => 'checkbox',
              'priority'  => 80
        ),
        'tc_sharrre-google-on' => array(
              'default'   => 1,
              'control'   => 'TC_controls',
              'label'     => __('Enable Google Plus Button', 'customizr-addons'),
              'section'   => 'single_posts_sec',
              'type'      => 'checkbox',
              'priority'  => 90
        ),
        'tc_sharrre-pinterest-on' => array(
              'default'   => 0,
              'control'   => 'TC_controls',
              'label'     => __('Enable Pinterest Button', 'customizr-addons'),
              'section'   => 'single_posts_sec',
              'type'      => 'checkbox',
              'priority'  => 100
        ),
        'tc_sharrre-linkedin-on' => array(
              'default'   => 0,
              'control'   => 'TC_controls',
              'label'     => __('Enable LinkedIn Button', 'customizr-addons'),
              'section'   => 'single_posts_sec',
              'type'      => 'checkbox',
              'priority'  => 100
        )
      );

      return array_merge( $sharrre_settings, $settings );
    }

    function tc_plugin_lang() {
      load_plugin_textdomain( 'customizr-addons' , false, basename( dirname( __FILE__ ) ) . '/lang' );
    }


    /**************************************************************
    ** SHARRRE
    **************************************************************/
    function tc_sharrre_front_actions() {
      if ( ! is_single() )
        return;

      //alter the single entry wrapper class
      add_filter( 'tc_single_post_section_class', array($this, 'tc_maybe_add_sharrre_class'));

      //hook the sharrre content to the single post template
      add_action( '__after_single_entry_inner', array($this, 'tc_maybe_print_sharrre_template') );
    }


    //@param $classes = array of classes
    //hook : hu_single_entry_class
    function tc_maybe_add_sharrre_class( $classes ) {
      if ( ! tc_are_share_buttons_enabled() )
        return $classes;
      $classes[] = 'share';
      return $classes;
    }

    //hook : hu_after_single_entry_inner
    function tc_maybe_print_sharrre_template() {
      if ( ! tc_are_share_buttons_enabled() )
        return;

      load_template( dirname( __FILE__ ) . '/inc/sharrre-template.php' );
    }






    //hook : wp_enqueue_scripts
    function tc_addons_style_scripts() {
      if ( ! is_single() )
        return;

      //can be dequeued() if already loaded by a plugin.
      //=> wp_dequeue_style( 'czr-font-awesome' )
      wp_enqueue_style(
          'czr-font-awesome',
          sprintf('%1$s/assets/front/css/%2$s',
              plugins_url( basename( __DIR__ ) ),
              'font-awesome.min.css'
          ),
          array(),
          ( defined('WP_DEBUG') && true === WP_DEBUG ) ? time() : HUEMAN_VER,
          'all'
      );

      wp_enqueue_script(
        'sharrre',
        sprintf( '%1$s/assets/front/js/jQuerySharrre%2$s', plugins_url( basename( __DIR__ ) ), (defined('TC_DEV') && true === TC_DEV) ? '.js' : '.min.js' ),
        array( 'jquery' ),
        '',
        true
      );
    }


    //hook : 'customize_controls_enqueue_scripts'
    function tc_extend_visibilities() {
      ?>
      <script type="text/javascript">
        (function (api, $, _) {
          var _oldDeps = api.CZR_visibilities.prototype.controlDeps;
          console.log('_oldDeps', _oldDeps);
          api.CZR_visibilities.prototype.controlDeps = _.extend( _oldDeps, {
              'tc_sharrre' : {
                  controls: [
                    'tc_sharrre-scrollable',
                    'tc_sharrre-twitter-on',
                    'tc_twitter-username',
                    'tc_sharrre-facebook-on',
                    'tc_sharrre-google-on',
                    'tc_sharrre-pinterest-on',
                    'tc_sharrre-linkedin-on'
                  ],
                  callback : function (to) {
                    return '0' !== to && false !== to && 'off' !== to;
                  }
              },
              'tc_sharrre-twitter-on' : {
                  controls: [
                    'tc_twitter-username'
                  ],
                  callback : function (to) {
                    return '0' !== to && false !== to && 'off' !== to;
                  }
              }
          });
        }) ( wp.customize, jQuery, _);
      </script>
      <?php
    }



    /**
    * @uses  wp_get_theme() the optional stylesheet parameter value takes into account the possible preview of a theme different than the one activated
    *
    * @return  the (parent) theme object
    */
    function tc_get_theme(){
      // Return the already set theme
      if ( self::$theme )
        return self::$theme;
      // $_REQUEST['theme'] is set both in live preview and when we're customizing a non active theme
      $stylesheet = $this -> is_customizing && isset($_REQUEST['theme']) ? $_REQUEST['theme'] : '';

      //gets the theme (or parent if child)
      $tc_theme               = wp_get_theme($stylesheet);

      return $tc_theme -> parent() ? $tc_theme -> parent() : $tc_theme;

    }

    /**
    *
    * @return  the theme name
    *
    */
    function tc_get_theme_name(){
      $tc_theme = $this -> tc_get_theme();

      return sanitize_file_name( strtolower( $tc_theme -> Name ) );
    }



    function tc_admin_notice() {
        $what = __( 'works only with the Customizr theme', 'customizr-addons' );

       ?>
        <div class="error">
            <p>
              <?php
              printf( __( 'The <strong>%1$s</strong> plugin %2$s.' ,'customizr-addons' ),
                'Customizr Addons',
                $what
              );
              ?>
            </p>
        </div>
        <?php
    }


    /**
    * Returns a boolean on the customizer's state
    * @since  3.2.9
    */
    function tc_is_customizing() {
      //checks if is customizing : two contexts, admin and front (preview frame)
      global $pagenow;
      $bool = false;
      if ( is_admin() && isset( $pagenow ) && 'customize.php' == $pagenow )
        $bool = true;
      if ( ! is_admin() && isset($_REQUEST['wp_customize']) )
        $bool = true;
      if ( $this -> tc_doing_customizer_ajax() )
        $bool = true;
      return $bool;
    }

    /**
    * Returns a boolean
    * @since  3.3.2
    */
    function tc_doing_customizer_ajax() {
      return isset( $_POST['customized'] ) && ( defined( 'DOING_AJAX' ) && DOING_AJAX );
    }

} //end of class

//Creates a new instance of front and admin
new CZR_addons_plugin;

endif;


//@return bool
function tc_are_share_buttons_enabled() {
  if ( ! tc_is_checked('tc_sharrre') )
    return;
  if ( ! tc_is_checked('tc_sharrre-twitter-on') && ! tc_is_checked('tc_sharrre-facebook-on') && ! tc_is_checked('tc_sharrre-google-on') && ! tc_is_checked('tc_sharrre-pinterest-on') && ! tc_is_checked('tc_sharrre-linkedin-on') )
    return;
  return true;
}


function tc_is_checked( $opt_name ) {
  return 1 == esc_attr( TC_utils::$inst->tc_opt( $opt_name ) );
}