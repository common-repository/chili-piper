<?php
/**
* Plugin Name: Chili Piper
* Plugin URI: https://github.com/Chili-Piper/cp-wp-plugin
* Description: Chili Piper Plugin to deploy Concierge on your app
* Requires at least: 4.3
* Version: 1.0.16
* Author: chilipiper
* Author URI: https://www.chilipiper.com
* License: GPLv2 or later
* License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
**/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function chilipiper_get_snippet_url($tenant, $router) {
  return 'https://' . esc_html($tenant) .'.chilipiper.com/concierge-js/cjs/concierge.js';
}

function chilipiper_generate_concierge_snippet($tenant, $router, $form_type) {
  wp_enqueue_script('chilipiper_concierge_script', 'https://' . esc_html($tenant) .'.chilipiper.com/concierge-js/cjs/concierge.js', array(), '1.0', array('strategy'  => 'defer'));
  $script = 'ChiliPiper.deploy("'. esc_html($tenant) . '", "'. esc_html($router) . '", { "formType": "'. $form_type . '" }); ';
	
	wp_add_inline_script('chilipiper_concierge_script', $script, 'after');
}

function chilipiper_concierge_box_html( $post ) {
  $tenant = get_post_meta( $post->ID, '_cp_concierge_tenant', true );
  $router = get_post_meta( $post->ID, '_cp_concierge_router', true );
  $form_type = get_post_meta( $post->ID, '_cp_concierge_form', true );
  if (!$form_type) {
    $form_type = 'HTML';
  }
  wp_nonce_field( plugin_basename(__FILE__), 'chilipiper_nonce' );
	?>
    <div class="cp_concierge__container">
      <div class="cp_concierge_field__container">
        <label for="cp_tenant_id" class="cp_concierge_field__label">Tenant</label>
        <div class="cp_concierge_field__input-container" tabindex="-1">
            <div data-wp-c16t="true" data-wp-component="Flex" class="components-flex">
              <input type="text" name="cp_tenant_id" id="cp_tenant_id" value="<?php echo esc_html($tenant); ?>" utocomplete="off" class="components-form-token-field__input" />
        </div>
      </div>
      <div class="cp_concierge_field__container">
        <label for="cp_router_slug"  class="cp_concierge_field__label">Router</label>
        <div class="cp_concierge_field__input-container" tabindex="-1">
            <div data-wp-c16t="true" data-wp-component="Flex" class="components-flex">
              <input type="text" name="cp_router_slug"  value="<?php echo esc_html($router); ?>" id="cp_router_slug" autocomplete="off" class="components-form-token-field__input" />
        </div>
      </div>
      <div class="cp_concierge_field__container">
        <label for="cp_form_type"  class="cp_concierge_field__label">Form Type</label>
        <div class="cp_concierge_field__input-container" tabindex="-1">
            <div data-wp-c16t="true" data-wp-component="Flex" class="components-flex">
              <select name="cp_form_type"  value="<?php echo esc_html($form_type); ?>" id="cp_form_type" class="components-form-token-field__input">
                <option value='HTML' <?php if ($form_type === 'HTML') { echo 'selected'; } ?>>Html Form</option>
                <option value='Hubspot' <?php if ($form_type === 'Hubspot') { echo 'selected'; } ?>>Hubspot</option>
                <option value='HubspotPopup' <?php if ($form_type === 'HubspotPopup') { echo 'selected'; } ?>>Hubspot on Popup</option>
                <option value='PardotFormHandler' <?php if ($form_type === 'PardotFormHandler') { echo 'selected'; } ?>>Pardot Form Handler</option>
                <option value='PardotIframeThankYouCode' <?php if ($form_type === 'PardotIframeThankYouCode') { echo 'selected'; } ?>>Pardot Thank you Page</option>
                <option value='PardotFormHandlerThankYouCode' <?php if ($form_type === 'PardotFormHandlerThankYouCode') { echo 'selected'; } ?>>Pardot Form Handler Thank you Page</option>
                <option value='PardotIframeParentPage' <?php if ($form_type === 'PardotIframeParentPage') { echo 'selected'; } ?>>Pardot Iframe Parent Page</option>
                <option value='Marketo' <?php if ($form_type === 'Marketo') { echo 'selected'; } ?>>Marketo</option>
                <option value='GravityForms' <?php if ($form_type === 'GravityForms') { echo 'selected'; } ?>>Gravity Forms</option>
              </select>
        </div>
      </div>
    </div>
	<?php
}

function chilipiper_add_concierge_box() {
	$screens = [ 'post', 'page' ];
	foreach ( $screens as $screen ) {
		add_meta_box(
			'cp_box_id',
			'Concierge Info',      // Box title
			'chilipiper_concierge_box_html',  // Content callback, must be of type callable
			$screen,                            // Post type
      'side',
      'high'
		);
	}
}
add_action( 'add_meta_boxes', 'chilipiper_add_concierge_box' );

function chilipiper_save_postdata( $post_id ) {
  if (!isset($_POST['chilipiper_nonce'])) {
    return $post_id;
  }
  if ( !wp_verify_nonce( $_POST['chilipiper_nonce'], plugin_basename(__FILE__) ) ) {
    return $post_id;
  }

  // verify if this is an auto save routine. 
  // If it is our form has not been submitted, so we dont want to do anything
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
    return $post_id;
  }
  if ( array_key_exists( 'cp_tenant_id', $_POST ) ) {
    $tenant = sanitize_text_field( $_POST['cp_tenant_id'] );
    $router = sanitize_text_field( $_POST['cp_router_slug'] );
    $form_type = sanitize_text_field( $_POST['cp_form_type'] );

    if ($tenant && !$router) {
      update_user_option( get_current_user_id(), 'cp_router_empty', true );
      return $post_id;
    }

    if ($tenant && $router) {
      $url = chilipiper_get_snippet_url($tenant, $router);
      $response = wp_remote_get( $url );
    
      if ( is_wp_error( $response ) ) {
        update_user_option( get_current_user_id(), 'cp_tenant_not_found_error', true );
        return $post_id;
      }
    }

    update_post_meta(
      $post_id,
      '_cp_concierge_tenant',
      $tenant
    );

    update_post_meta(
      $post_id,
      '_cp_concierge_router',
      $router
    );

    update_post_meta(
      $post_id,
      '_cp_concierge_form',
      $form_type
    );
  }
}
add_action( 'save_post', 'chilipiper_save_postdata' );

add_action( 'admin_notices', function() {
  if ( get_user_option( 'cp_tenant_not_found_error' ) ) {
    delete_user_option( get_current_user_id(), 'cp_tenant_not_found_error' );
    echo '<div class="notice notice-error"><p>The tenant was not found</p></div>';
  }
  if ( get_user_option( 'cp_router_empty' ) ) {
    delete_user_option( get_current_user_id(), 'cp_router_empty' );
    echo '<div class="notice notice-error"><p>The router cannot be empty</p></div>';
  }
});

function chilipiper_concierge_add_snippet( $content ) {
  if ( is_singular() && in_the_loop() && is_main_query() ) {
    global $post;
    $tenant = get_post_meta( $post->ID, '_cp_concierge_tenant', true );
    $router = get_post_meta( $post->ID, '_cp_concierge_router', true );
    $form_type = get_post_meta( $post->ID, '_cp_concierge_form', true );
    if (!$form_type) {
      $form_type = 'HTML';
    }
    if ($tenant && $router) {
      return chilipiper_generate_concierge_snippet($tenant, $router, $form_type) . $content;
    }
  }
  return $content;
}

add_filter('the_content', 'chilipiper_concierge_add_snippet');

wp_enqueue_style( 'cp-concierge-admin-css', plugin_dir_url( __FILE__ ) . 'admin.css', null, '1.0' );
