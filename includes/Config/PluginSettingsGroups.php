<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'nf_paypal_subscriptions_plugin_settings_groups', array(

    'paypal_subscriptions' => array(
        'id' => 'paypal_subscriptions',
        'label' => __( 'PayPal Subscriptions', 'ninja-forms' ),
    ),
));