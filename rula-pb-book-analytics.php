<?php
/*
 * Plugin Name: Pressbooks - Book Level Analytics
 * Plugin URI: https://github.com/ryersonlibrary/rula_pb_book_analytics
 * Author: Ryerson University Library & Archives
 * Author URI: https://github.com/ryersonlibrary
 * Description: Enables book level analytics for Pressbooks on a subdirectory installation (Pressbooks only supports this on subdomain installations).
 * GitHub Plugin URI: https://github.com/ryersonlibrary/rula_pb_book_analytics
 * Version: 0.0.1
 */

function rula_pb_add_book_analytics_menu() {
  add_options_page(
    __('Google Analytics', 'rula_pb'),
    __('Google Analytics', 'rula_pb'),
    'manage_options',
    'rula_pb_book_analytics',
    'rula_pb_display_book_analytics_settings'
  );
}

function rula_pb_display_book_analytics_settings() {
  ?>
  <div class="wrap">
    <h2><?php _e( 'Google Analytics', 'rula_pb' ); ?></h2>
    <form method="POST" action="options.php">
      <?php
      settings_fields( 'rula_pb_book_analytics' );
      do_settings_sections( 'rula_pb_book_analytics' );
      ?>
      <?php submit_button(); ?>
    </form>
  </div>
  <?php
}

function rula_pb_book_analytics_settings_init() {
  $_section = 'rula_pb_book_analytics';
  $_page = 'rula_pb_book_analytics';
  add_settings_section(
    $_section,
    '',
    '\rula_pb_analytics_settings_section_callback',
    $_page
  );
  add_settings_field(
    'rula_pb_ga_id',
    __( 'Google Analytics ID', 'rula_pb' ),
    '\rula_pb_book_analytics_callback',
    $_page,
    $_section,
    [
      __( 'The Google Analytics ID for your book, e.g. &lsquo;UA-01234567-8&rsquo;.', 'rula_pb' ),
    ]
  );
  register_setting(
    $_page,
    'rula_pb_ga_id',
    [
      'type' => 'string',
      'default' => '',
    ]
  );
}

function rula_pb_analytics_settings_section_callback() {
  echo '<p>' . __( 'Google Analytics settings.', 'rula_pb' ) . '</p>';
}

function rula_pb_book_analytics_callback( $args ) {
  $rula_pb_ga_id = get_option( 'rula_pb_ga_id' );
  $html = '<input type="text" id="rula_pb_ga_id" name="rula_pb_ga_id" value="' . $rula_pb_ga_id . '" />';
  $html .= '<p class="description">' . $args[0] . '</p>';
  echo $html;
}

function rula_pb_print_analytics() {
  $analytics_code = get_option('rula_pb_ga_id');
  $tracking_html = "";
  $tracking_html .= "ga('create', '{$analytics_code}', 'auto', 'bookTracker');\n";
  $tracking_html .= "ga('bookTracker.send', 'pageview');\n";

  $html = '';
  $html .= "<script>\n{$tracking_html}</script>\n";

  if( !empty( $analytics_code ) ) {
    echo $html;
  }
}

add_action( 'admin_menu', 'rula_pb_add_book_analytics_menu' );
add_action( 'admin_init', 'rula_pb_book_analytics_settings_init' );

add_action( 'wp_footer', 'rula_pb_print_analytics');