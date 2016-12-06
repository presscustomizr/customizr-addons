<?php
/* ------------------------------------------------------------------------- *
 *  Public Functions
/* ------------------------------------------------------------------------- */
//@return bool
function czra_are_share_buttons_enabled() {
  if ( ! czra_is_checked('tc_sharrre') )
    return;
  if ( ! czra_is_checked('tc_sharrre-twitter-on') && ! czra_is_checked('tc_sharrre-facebook-on') && ! czra_is_checked('tc_sharrre-google-on') && ! tc_is_checked('czra_sharrre-pinterest-on') && ! tc_is_checked('czra_sharrre-linkedin-on') )
    return;
  return true;
}


/* ------------------------------------------------------------------------- *
 *  Class
/* ------------------------------------------------------------------------- */
class CZRA_Sharrre {
  static $instance;
  function __construct() {
      self::$instance =& $this;
      //front
      add_action( 'wp'                                     , array( $this, 'czra_sharrre_front_actions') );
      //scripts
      add_action( 'wp_enqueue_scripts'                     , array( $this, 'czra_addons_style_scripts' ) );
      //customizer
      add_filter( 'czr_fn_single_post_option_map'          , array( $this, 'czra_register_sharrre_settings') );
  }



  /* ------------------------------------------------------------------------- *
   *  Front End
  /* ------------------------------------------------------------------------- */
  //hook : 'wp'
  function czra_sharrre_front_actions() {
    if ( ! is_single() )
      return;

    //SHARRRE FRONT STYLE
    add_action( 'wp_head'                                  , array( $this, 'czra_write_sharre_style') );

    //alter the single entry wrapper class
    add_filter( 'tc_single_post_section_class'             , array( $this, 'czra_maybe_add_sharrre_class') );

    //hook the sharrre content to the single post template
    add_action( '__after_single_entry_inner'               , array( $this, 'czra_maybe_print_sharrre_template') );
  }


  //@param $classes = array of classes
  //hook : tc_single_post_section_class
  function czra_maybe_add_sharrre_class( $classes ) {
    if ( ! czra_are_share_buttons_enabled() )
      return $classes;

    $classes[] = 'share';
    return $classes;
  }



  //hook : __after_single_entry_inner
  function czra_maybe_print_sharrre_template() {
    if ( ! czra_are_share_buttons_enabled() )
      return;

    require_once( CZRA_BASE_PATH . '/inc/sharrre/sharrre-template.php' );
  }



  /* ------------------------------------------------------------------------- *
   *  Scripts
  /* ------------------------------------------------------------------------- */
  //hook : wp_enqueue_scripts
  function czra_addons_style_scripts() {
    if ( ! is_single() )
      return;

    if ( ! esc_attr( czra_get_opt( 'tc_font_awesome_css' ) ) )
      //can be dequeued() if already loaded by a plugin.
      //=> wp_dequeue_style( 'czr-font-awesome' )
      wp_enqueue_style(
          'czr-font-awesome',
          sprintf('%1$s/assets/front/css/%2$s',
              CZRA_BASE_URL,
              'font-awesome.min.css'
          ),
          array(),
          ( defined('WP_DEBUG') && true === WP_DEBUG ) ? time() : CUZROMIZR_VER,
          'all'
      );

    wp_enqueue_script(
      'sharrre',
      sprintf( '%1$s/assets/front/js/jQuerySharrre%2$s', CZRA_BASE_URL, (defined('TC_DEV') && true === TC_DEV) ? '.js' : '.min.js' ),
      array( 'jquery' ),
      '',
      true
    );
  }



  /* ------------------------------------------------------------------------- *
   *  Customizer
  /* ------------------------------------------------------------------------- */
  //add customizer settings
  //hook : czr_fn_single_post_option_map
  function czra_register_sharrre_settings( $settings ) {
    $sharrre_settings = array(
      'tc_sharrre' => array(
            'default'   => 1,
            'control'   => 'CZR_controls',
            'label'     => __('Display social sharing buttons in your single posts', 'customizr-addons'),
            'title'     => __('Social Sharring Bar Setttings', 'customizr-addons'),
            'notice'    => __('Display social sharing buttons in each single articles.', 'customizr-addons'),
            'section'   => 'single_posts_sec',
            'type'      => 'checkbox',
            'priority'  => 40
      ),
      'tc_sharrre-scrollable' => array(
            'default'   => 1,
            'control'   => 'CZR_controls',
            'label'     => __('Make the Share Bar "sticky"', 'customizr-addons'),
            'notice'    => __('Make the social share bar stick to the browser window when scrolling down a post.', 'customizr-addons'),
            'section'   => 'single_posts_sec',
            'type'      => 'checkbox',
            'priority'  => 50
      ),
      'tc_sharrre-twitter-on' => array(
            'default'   => 1,
            'control'   => 'CZR_controls',
            'label'     => __('Enable Twitter Button', 'customizr-addons'),
            'section'   => 'single_posts_sec',
            'type'      => 'checkbox',
            'notice'    => __('Since Nov. 2015, Twitter disabled the share counts from its API. If you want to get the display count anyway, you can create an account for free (as of Feb. 2016) on [https://opensharecount.com/]. The Customizr Addons plugin is configured to use opensharecount.', 'customizr-addons'),
            'priority'  => 60
      ),
      'tc_twitter-username' => array(
            'default'   => '',
            'control'   => 'CZR_controls',
            'label'     => __('Twitter Username (without "@")', 'customizr-addons'),
            'notice'    => __('Simply enter your username without the "@" prefix. Your username will be added to share-tweets of your posts (optional).', 'customizr-addons'),
            'section'   => 'single_posts_sec',
            'type'      => 'text',
            'transport' => 'postMessage',
            'priority'  => 70
      ),
      'tc_sharrre-facebook-on' => array(
            'default'   => 1,
            'control'   => 'CZR_controls',
            'label'     => __('Enable Facebook Button', 'customizr-addons'),
            'section'   => 'single_posts_sec',
            'type'      => 'checkbox',
            'priority'  => 80
      ),
      'tc_sharrre-google-on' => array(
            'default'   => 1,
            'control'   => 'CZR_controls',
            'label'     => __('Enable Google Plus Button', 'customizr-addons'),
            'section'   => 'single_posts_sec',
            'type'      => 'checkbox',
            'priority'  => 90
      ),
      'tc_sharrre-pinterest-on' => array(
            'default'   => 0,
            'control'   => 'CZR_controls',
            'label'     => __('Enable Pinterest Button', 'customizr-addons'),
            'section'   => 'single_posts_sec',
            'type'      => 'checkbox',
            'priority'  => 100
      ),
      'tc_sharrre-linkedin-on' => array(
            'default'   => 0,
            'control'   => 'CZR_controls',
            'label'     => __('Enable LinkedIn Button', 'customizr-addons'),
            'section'   => 'single_posts_sec',
            'type'      => 'checkbox',
            'priority'  => 100
      )
    );

    return array_merge( $sharrre_settings, $settings );
  }




  //hook : wp_head
  function czra_write_sharre_style() {
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

}//end of class