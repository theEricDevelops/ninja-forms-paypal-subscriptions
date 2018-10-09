<?php

/*
 *
 * This class sets up all of our PayPal settings in the wp-admin.
 *
 * @since 1.0
 */

class NF_Paypal_Settings 
{
    /**
   * Initialize the plugin
   */
  public function __construct() { 
    // load scripts
    //add_action( 'ninja_forms_display_js', array( &$this, "load_scripts" ) );

    // load settings
    add_action( 'admin_menu', array( $this, 'load_paypal_settings' ) );
    add_action( 'admin_init', array( $this, 'load_paypal_form_settings' ) );
    add_action( 'ninja_forms_edit_field_after_registered', array( $this, 'load_paypal_field_settings' ), 12, 2 );
  }

  public function load_paypal_settings() {
    // Add a submenu to Ninja Forms for PayPal settings.
    $paypal = add_submenu_page( 'ninja-forms', __( 'PayPal Settings', 'ninja-forms-paypal-subscriptions' ), __( 'PayPal', 'ninja-forms-paypal-subscriptions' ), 'administrator', 'ninja-forms-paypal', 'ninja_forms_admin' );

    // Enqueue default Ninja Forms admin styles and JS.
    add_action('admin_print_styles-' . $paypal, 'ninja_forms_admin_css');
    add_action('admin_print_styles-' . $paypal, 'ninja_forms_admin_js');

    // Register a tab to our new page for PayPal settings.
    $args = array(
      'name' => __( 'PayPal Settings', 'ninja-forms-paypal-subscriptions' ),
      'page' => 'ninja-forms-paypal',
      'display_function' => '',
      'save_function' => array( $this, 'save_paypal_settings' ),
      'tab_reload' => true,
    );
    if ( function_exists( 'ninja_Forms_register_tab' ) ) {
      ninja_forms_register_tab( 'general_settings', $args);
    }

    // Grab our current settings.
    $plugin_settings = get_option( 'ninja_forms_paypal' );
    
    if ( isset ( $plugin_settings['debug'] ) ) {
      $debug = $plugin_settings['debug'];
    } else {
      $debug = '';
    }

    if ( isset ( $plugin_settings['currency'] ) ) {
      $selected_currency = $plugin_settings['currency'];
    } else { 
      $selected_currency = 'USD';
    }

    if ( isset ( $plugin_settings['test_api_user'] ) ) {
      $test_api_user = $plugin_settings['test_api_user'];
    } else { 
      $test_api_user = '';
    }

    if ( isset ( $plugin_settings['test_api_pwd'] ) ) {
      $test_api_pwd = $plugin_settings['test_api_pwd'];
    } else {
      $test_api_pwd = '';
    }

    if ( isset ( $plugin_settings['test_api_signature'] ) ) {
      $test_api_signature = $plugin_settings['test_api_signature'];
    } else {
      $test_api_signature = '';
    }    

    if ( isset ( $plugin_settings['live_api_user'] ) ) {
      $live_api_user = $plugin_settings['live_api_user'];
    } else { 
      $live_api_user = '';
    }

    if ( isset ( $plugin_settings['live_api_pwd'] ) ) {
      $live_api_pwd = $plugin_settings['live_api_pwd'];
    } else {
      $live_api_pwd = '';
    }

    if ( isset ( $plugin_settings['live_api_signature'] ) ) {
      $live_api_signature = $plugin_settings['live_api_signature'];
    } else {
      $live_api_signature = '';
    }

    // Register our Genearl Settings metabox.
    $paypal_currencies = apply_filters( 'nf_paypal_subscriptions_currencies', array(
      array( 'name' => __( 'Australian Dollars', 'ninja-forms-paypal-subscriptions' ),   'value' => 'AUD' ),
      array( 'name' => __( 'Canadian Dollars', 'ninja-forms-paypal-subscriptions' ),     'value' => 'CAD' ),
      array( 'name' => __( 'Czech Koruna', 'ninja-forms-paypal-subscriptions' ),         'value' => 'CZK' ),
      array( 'name' => __( 'Danish Krone', 'ninja-forms-paypal-subscriptions' ),         'value' => 'DKK' ),
      array( 'name' => __( 'Euros', 'ninja-forms-paypal-subscriptions' ),                'value' => 'EUR' ),
      array( 'name' => __( 'Hong Kong Dollars', 'ninja-forms-paypal-subscriptions' ),    'value' => 'HKD' ),
      array( 'name' => __( 'Hungarian Forints', 'ninja-forms-paypal-subscriptions' ),    'value' => 'HUF' ),
      array( 'name' => __( 'Israeli New Sheqels', 'ninja-forms-paypal-subscriptions' ),  'value' => 'ILS' ),
      array( 'name' => __( 'Japanese Yen', 'ninja-forms-paypal-subscriptions' ),         'value' => 'JPY' ),
      array( 'name' => __( 'Mexican Pesos', 'ninja-forms-paypal-subscriptions' ),        'value' => 'MXN' ),
      array( 'name' => __( 'Norwegian Krone', 'ninja-forms-paypal-subscriptions' ),      'value' => 'NOK' ),
      array( 'name' => __( 'New Zealand Dollars', 'ninja-forms-paypal-subscriptions' ),  'value' => 'NZD' ),
      array( 'name' => __( 'Philippine Pesos', 'ninja-forms-paypal-subscriptions' ),     'value' => 'PHP' ),
      array( 'name' => __( 'Polish Zloty', 'ninja-forms-paypal-subscriptions' ),         'value' => 'PLN' ),
      array( 'name' => __( 'Pound Sterling', 'ninja-forms-paypal-subscriptions' ),       'value' => 'GBP' ),
      array( 'name' => __( 'Singapore Dollars', 'ninja-forms-paypal-subscriptions' ),    'value' => 'SGD' ),
      array( 'name' => __( 'Swedish Krona', 'ninja-forms-paypal-subscriptions' ),        'value' => 'SEK' ),
      array( 'name' => __( 'Swiss Franc', 'ninja-forms-paypal-subscriptions' ),          'value' => 'CHF' ),
      array( 'name' => __( 'Taiwan New Dollars', 'ninja-forms-paypal-subscriptions' ),   'value' => 'TWD' ),
      array( 'name' => __( 'Thai Baht', 'ninja-forms-paypal-subscriptions' ),            'value' => 'THB' ),
      array( 'name' => __( 'U.S. Dollars', 'ninja-forms-paypal-subscriptions' ),         'value' => 'USD' ),
    ) );

    $args = array(
      'page' => 'ninja-forms-paypal',
      'tab' => 'general_settings',
      'slug' => 'general',
      'title' => __( 'Basic Settings', 'ninja-forms-paypal-subscriptions' ),
      'display_function' => '',
      'state' => 'closed',
      'settings' => array(    
        array(
          'name' => 'currency',
          'type' => 'select',
          'options' => $paypal_currencies,
          'label' => __( 'Transaction Currency', 'ninja-forms-paypal-subscriptions'),
          'default_value' => $selected_currency,
        ),
      ),
    );
    if ( function_exists( 'ninja_forms_register_tab_metabox' ) ) {
      ninja_forms_register_tab_metabox($args);
    }

    // Register our API Settings metabox.
    $args = array(
      'page' => 'ninja-forms-paypal',
      'tab' => 'general_settings',
      'slug' => 'live_credentials',
      'title' => __( 'Live API Credentials', 'ninja-forms-paypal-subscriptions' ),
      'display_function' => '',
      'state' => 'open',
      'settings' => array(
        array(
          'name' => 'live_api_user',
          'type' => 'text',
          'label' => __( 'Live API Username', 'ninja-forms-paypal-subscriptions' ),
          'default_value' => $live_api_user,
        ),
        array(
          'name' => 'live_api_pwd',
          'type' => 'text',
          'label' => __( 'Live API Password', 'ninja-forms-paypal-subscriptions' ),
          'default_value' => $live_api_pwd,
        ),        
        array(
          'name' => 'live_api_signature',
          'type' => 'text',
          'label' => __( 'Live API Signature', 'ninja-forms-paypal-subscriptions' ),
          'default_value' => $live_api_signature,
        ),
      ),
    );
    if ( function_exists( 'ninja_forms_register_tab_metabox' ) ) {
      ninja_forms_register_tab_metabox($args);
    }    

    $args = array(
      'page' => 'ninja-forms-paypal',
      'tab' => 'general_settings',
      'slug' => 'test_credentials',
      'title' => __( 'Sandbox (Test Mode) API Credentials', 'ninja-forms-paypal-subscriptions' ),
      'display_function' => '',
      'state' => 'open',
      'settings' => array(
        array(
          'name' => 'debug',
          'type' => 'checkbox',
          'label' => __( 'Run in debug mode', 'ninja-forms-paypal-subscriptions' ),
          'default_value' => $debug,
        ),
        array(
          'name' => 'test_api_user',
          'type' => 'text',
          'label' => __( 'Sandbox API Username', 'ninja-forms-paypal-subscriptions' ),
          'default_value' => $test_api_user,
        ),
        array(
          'name' => 'test_api_pwd',
          'type' => 'text',
          'label' => __( 'Sandbox API Password', 'ninja-forms-paypal-subscriptions' ),
          'default_value' => $test_api_pwd,
        ),        
        array(
          'name' => 'test_api_signature',
          'type' => 'text',
          'label' => __( 'Sandbox API Signature', 'ninja-forms-paypal-subscriptions' ),
          'default_value' => $test_api_signature,
        ),
      ),
    );
    if ( function_exists( 'ninja_forms_register_tab_metabox' ) ) {
      ninja_forms_register_tab_metabox($args);
    }

  }

  public function save_paypal_settings( $data ) {
    $plugin_settings = get_option( 'ninja_forms_paypal' );
    if ( is_array( $data ) ) {
      foreach ( $data as $key => $val ) {
        $plugin_settings[$key] = trim( $val );
      }
    }
    update_option( 'ninja_forms_paypal', $plugin_settings );

    return __( 'Settings Updated', 'ninja-forms-paypal-subscriptions' );
  }

  public function load_paypal_form_settings() {
    // Register our PayPal Settings metabox.
    $args = array(
      'page' => 'ninja-forms',
      'tab' => 'form_settings',
      'slug' => 'paypal',
      'title' => __( 'PayPal Settings', 'ninja-forms-paypal-subscriptions' ),
      'display_function' => '',
      'state' => 'closed',
      'settings' => array(
        array(
          'name' => 'paypal_subscriptions',
          'type' => 'checkbox',
          'label' => __( 'Use PayPal Subscriptions', 'ninja-forms-paypal-subscriptions' ),
        ),
        array(
          'name' => 'paypal_test_mode',
          'type' => 'checkbox',
          'label' => __( 'Run in sandbox (test) mode', 'ninja-forms-paypal-subscriptions'),
        ),
        array(
          'name' => 'paypal_product_name',
          'type' => 'text',
          'label' => __( 'Default Product Name', 'ninja-forms-paypal-subscriptions' ),
          'desc' => __( 'If you do not plan on adding any calculation fields to your form, enter a product name here.', 'ninja-forms-paypal-subscriptions' ),
        ),        
        array(
          'name' => 'paypal_product_desc',
          'type' => 'text',
          'label' => __( 'Default Product Description', 'ninja-forms-paypal-subscriptions' ),
          'desc' => __( 'If you do not plan on adding any calculation fields to your form, enter a product description here.', 'ninja-forms-paypal-subscriptions' ),
        ),
        array(
          'name' => 'paypal_default_total',
          'type' => 'text',
          'label' => __( 'Default Total', 'ninja-forms-paypal-subscriptions' ),
          'desc' => __( 'If you do not want to use a Total Field in your form, you can use this setting. Please leave out any currency markers.', 'ninja-forms-paypal-subscriptions' ),
        ),
      ),
    );
    if ( function_exists( 'ninja_forms_register_tab_metabox' ) ) {
      ninja_forms_register_tab_metabox($args);
    }
  }

  public function load_paypal_field_settings( $field_id, $field_data ) {
    global $ninja_forms_fields;

    // Output our edit field settings
    $field = ninja_forms_get_field_by_id( $field_id );

    $field_type = $field['type'];
    if ( isset ( $ninja_forms_fields[ $field_type ]['process_field'] ) && ! $ninja_forms_fields[ $field_type ]['process_field'] )
      return false;

    if ( isset ( $field_data['paypal_item'] ) ) {
      $paypal_item = $field_data['paypal_item'];
    } else {
      $paypal_item = 0;
    }

   ?>
      <div id="paypal_settings">
        <h4>PayPal Settings</h4>
        <?php

        ninja_forms_edit_field_el_output( $field_id, 'checkbox', __( 'Consider this an "item" for PayPal purposes. (If you are setting up a quantity field, uncheck this box.)', 'ninja-forms-paypal-subscriptions' ), 'paypal_item', $paypal_item, 'wide', '', '' );
        // If we're working with a list, add the checkbox option to use the List Item Label for the PayPal Product Name.
        if ( $field['type'] == '_list' ) {
      
          if ( isset ( $field_data['list_label_product_name'] ) ) {
            $list_label_product_name = $field_data['list_label_product_name'];
          } else {
            $list_label_product_name = 0;
          }
          
          ninja_forms_edit_field_el_output( $field_id, 'checkbox', __( 'Use List Label For PayPal Product Name', 'ninja-forms-paypal-subscriptions' ), 'list_label_product_name', $list_label_product_name, 'wide', '', '' );
        }

          ?>

      </div>
      <?php
  }

} // Class

function ninja_forms_paypal_initiate(){
  if ( is_admin() ) {
    $NF_Paypal_Settings = new NF_Paypal_Settings();     
  }
}

add_action( 'init', 'ninja_forms_paypal_initiate' );