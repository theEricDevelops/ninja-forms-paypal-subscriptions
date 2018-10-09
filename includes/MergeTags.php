<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_PayPalSubscriptions_MergeTags
 */
final class NF_PayPalSubscriptions_MergeTags extends NF_Abstracts_MergeTags
{
    protected $id = 'paypal_subscriptions';

    private $transaction_id = '';

    public function __construct()
    {
        parent::__construct();
        $this->title = __( 'PayPal Subscriptions', 'ninja-forms' );

        $this->merge_tags = array(
            'transaction_id' => array(
                'id' => 'transaction_id',
                'tag' => '{paypal_subscriptions:transaction_id}',
                'label' => __( 'Transaction ID', 'ninja-forms-paypal-subscriptions' ),
                'callback' => 'get_transaction_id'
            ),
        );
    }

    public function set_transaction_id( $transaction_id = '' )
    {
        $this->transaction_id = $transaction_id;
    }

    public function get_transaction_id()
    {
        return $this->transaction_id;
    }

} // END CLASS NF_PayPalSubscriptions_MergeTags
