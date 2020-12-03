/* BFA- Cange amount days cacel order pending payment */

// To change the amount of days just change '-7 days' to your liking
function get_unpaid_submitted() {        
	global $wpdb;

	$unpaid_submitted = $wpdb->get_col( $wpdb->prepare( "
			SELECT posts.ID
			FROM {$wpdb->posts} AS posts
			WHERE posts.post_status = 'wc-on-hold'
			AND posts.post_date < %s
	", date( 'Y-m-d H:i:s', strtotime('-15 days') ) ) );

	return $unpaid_submitted;
}

// This excludes check payment type.
function wc_cancel_unpaid_submitted() {        
	$unpaid_submit = get_unpaid_submitted();

	if ( $unpaid_submit ) {                
			foreach ( $unpaid_submit as $unpaid_order ) {                        
					$order = wc_get_order( $unpaid_order );
					$cancel_order = True;

					foreach  ( $order->get_items() as $item_key => $item_values) {                                
							$manage_stock = get_post_meta( $item_values['variation_id'], '_manage_stock', true );
							if ( $manage_stock == "no" ) {                                        
									$payment_method = $order->get_payment_method();                                        
									if ( $payment_method == "cheque" ) {
											$cancel_order = False;
									}
							}                                
					}
					if ( $cancel_order == True ) {
							$order -> update_status( 'cancelled', __( 'Pagamento não identificado e cancelado.', 'woocommerce') );
					}
			}
	}        
}
add_action( 'woocommerce_cancel_unpaid_submitted', 'wc_cancel_unpaid_submitted' );
