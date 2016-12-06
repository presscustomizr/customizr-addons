<?php
/**
* Plugin Name: Customizr Addons
* Plugin URI: http://presscustomizr.com
* Description: Lightweight addons plugin for the Customizr WordPress theme.
* Version: 1.0.1
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

    public static function czra_get_instance() {
      if ( ! isset( self::$instance ) && ! ( self::$instance instanceof CZR_addons_plugin ) )
        self::$instance = new CZR_addons_plugin();
      return self::$instance;
    }

    function __construct() {
      self::$instance =& $this;

      //checks if is customizing : two context, admin and front (preview frame)
      $this -> is_customizing = $this -> czra_is_customizing();

      self::$theme          = $this -> czra_get_theme();
      self::$theme_name     = $this -> czra_get_theme_name();

      //stop execution if not Customizr
      if ( false === strpos( self::$theme_name, 'customizr' ) ) {
        add_action( 'admin_notices', array( $this , 'czra_admin_notice' ) );
        return;
      }

      if( ! defined( 'CZRA_BASE_PATH' ) ) define( 'CZRA_BASE_PATH' , plugin_dir_path( __FILE__ ) );
      if( ! defined( 'CZRA_BASE_URL' ) ) define( 'CZRA_BASE_URL' , trailingslashit( plugins_url( basename( __DIR__ ) ) ) );
      if( ! defined( 'CZRA_SKOP_ON' ) ) define( 'CZRA_SKOP_ON' , false );
      if( ! defined( 'CZRA_SEK_ON' ) ) define( 'CZRA_SEK_ON' , false );


      //TEXT DOMAIN
      //adds plugin text domain
      add_action( 'plugins_loaded', array( $this , 'czra_plugin_lang' ) );

      //fire
      $this -> czra_load();

      //SHARRRE


    }//end of construct

    function czra_load() {
      /* ------------------------------------------------------------------------- *
       *  Loads Features
      /* ------------------------------------------------------------------------- */
      require_once( CZRA_BASE_PATH . 'inc/sharrre/czra-sharrre.php' );
      new CZRA_Sharrre();

      /* ------------------------------------------------------------------------- *
       *  Loads Customizer
      /* ------------------------------------------------------------------------- */
      require_once( CZRA_BASE_PATH . 'inc/czr/czra-czr.php' );
      new CZRA_Czr();
    }



    function czra_plugin_lang() {
      load_plugin_textdomain( 'customizr-addons' , false, basename( dirname( __FILE__ ) ) . '/lang' );
    }



    /**
    * @uses  wp_get_theme() the optional stylesheet parameter value takes into account the possible preview of a theme different than the one activated
    *
    * @return  the (parent) theme object
    */
    function czra_get_theme(){
      // Return the already set theme
      if ( self::$theme )
        return self::$theme;
      // $_REQUEST['theme'] is set both in live preview and when we're customizing a non active theme
      $stylesheet = $this -> is_customizing && isset($_REQUEST['theme']) ? $_REQUEST['theme'] : '';

      //gets the theme (or parent if child)
      $czra_theme               = wp_get_theme($stylesheet);

      return $czra_theme -> parent() ? $czra_theme -> parent() : $czra_theme;

    }



    /**
    *
    * @return  the theme name
    *
    */
    function czra_get_theme_name(){
      $czra_theme = $this -> czra_get_theme();

      return sanitize_file_name( strtolower( $czra_theme -> Name ) );
    }



    function czra_admin_notice() {
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
    * Is the customizer left panel being displayed ?
    * @return  boolean
    * @since 1.0.1
    */
    function czra_is_customize_left_panel() {
      global $pagenow;
      return is_admin() && isset( $pagenow ) && 'customize.php' == $pagenow;
    }



    /**
    * Is the customizer preview panel being displayed ?
    * @return  boolean
    * @since  1.0.1
    */
    function czra_is_customize_preview_frame() {
      return ! is_admin() && isset($_REQUEST['wp_customize']);
    }


    /**
    * Always include wp_customize or customized in the custom ajax action triggered from the customizer
    * => it will be detected here on server side
    * typical example : the donate button
    *
    * @return boolean
    * @since  1.0
    */
    function czra_doing_customizer_ajax() {
      $_is_ajaxing_from_customizer = isset( $_POST['customized'] ) || isset( $_POST['wp_customize'] );
      return $_is_ajaxing_from_customizer && ( defined( 'DOING_AJAX' ) && DOING_AJAX );
    }


    /**
    * Are we in a customization context ? => ||
    * 1) Left panel ?
    * 2) Preview panel ?
    * 3) Ajax action from customizer ?
    * @return  bool
    * @since  1.0
    */
    function czra_is_customizing() {
      //checks if is customizing : two contexts, admin and front (preview frame)
      return $this -> czra_is_customize_left_panel() ||
        $this -> czra_is_customize_preview_frame() ||
        $this -> czra_doing_customizer_ajax();
    }

  } //end of class
endif;

function czra_is_checked( $opt_name ) {
  if ( function_exists( 'czr_fn_get_opt' ) )//c4
    return 1 == esc_attr( czr_fn_get_opt( $opt_name ) );
  return 1 == esc_attr( CZR_utils::$inst->czr_fn_opt( $opt_name ) );
}

function czra_get_opt( $opt_name ) {
  if ( function_exists( 'czr_fn_get_opt' ) )//c4
    return czr_fn_get_opt( $opt_name );
  return CZR_utils::$inst->czr_fn_opt( $opt_name );
}

//Creates a new instance
function CZR_AD() {
  return CZR_addons_plugin::czra_get_instance();
}

CZR_AD();