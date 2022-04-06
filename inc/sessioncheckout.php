<?php 

/**
 * @package WpItprix
 */


/**
 * 
 */
class SessionCheckout
{
	
	public function __construct()
	{
		add_action( 'woocommerce_load_cart_from_session',[$this,'itprix_get_session_id'] );
		add_action( 'woocommerce_checkout_after_customer_details', [$this,'itprix_handle_session_id'] );
		add_action( 'woocommerce_payment_complete',[$this,'handle_session_onpayment_compleate'] );
		add_filter( 'woocommerce_persistent_cart_enabled', '__return_false' );
		add_action( 'woocommerce_checkout_update_order_meta', [$this,'itprix_update_postmeta']);
		add_action( 'woocommerce_thankyou_paypal',[$this,'itprix_delete_post_meta']  );
		add_action( 'template_redirect', [$this,'woo_custom_redirect_after_purchase'] );
	}


	

	public function itprix_get_session_id() {

		// Bail if there isn't any data
		if ( ! isset( $_GET['session_id'] ) ) {
			return;
		}

		$session_id = sanitize_text_field( $_GET['session_id'] );

		try {

			$handler      = new \WC_Session_Handler();
			$session_data = $handler->get_session( $session_id );

	    // We were passed a session ID, yet no session was found. Let's log this and bail.
			if ( empty( $session_data ) ) {
				throw new \Exception( 'Could not locate WooCommerce session on checkout' );
			}

	    // Go get the session instance (WC_Session) from the Main WC Class
			$session = WC()->session;

	    // Set the session variable
			foreach ( $session_data as $key => $value ) {
				$session->set( $key, unserialize( $value ) );
			}

		} catch ( \Exception $exception ) {
			ErrorHandling::capture( $exception );
		}

	} 



	public	function itprix_handle_session_id() {
			// Bail if there isn't any data
			if ( ! isset( $_GET['session_id'] ) ) {
				return;
			} ?>

			<input
				type="hidden"
				name="headless-session"
				value="<?= esc_attr( $_GET['session_id'] ) ?>"
			/>
			<?php
		}




	public function handle_session_onpayment_compleate () {
		// Bail if there isn't any data
		if ( ! isset( $_POST['headless-session'] ) ) {
			return;
		}

		// Delete the headless session we set on POST during the checkout
		WC()->session->delete_session( sanitize_text_field( $_POST['headless-session'] ) );
	} 





	public function itprix_update_postmeta ( $order_id ) {
		// Bail if there isn't any data
		if ( ! isset( $_POST['headless-session'] ) ) {
			return;
		}

		update_post_meta( $order_id, 'headless-session', sanitize_text_field( $_POST['headless-session'] ) );
	} 



	public function itprix_delete_post_meta ( $order_id ) {
		$headless_session = get_post_meta( $order_id, 'headless-session', true );

		if ( empty( $headless_session ) ) {
			return;
		}

		// Delete the headless session we set on POST during the checkout
		WC()->session->delete_session( sanitize_text_field( $headless_session ) );

	  // Tidy things up so our db doesn't get bloated
	  delete_post_meta( $order_id, 'headless-session' );
	}



	public function woo_custom_redirect_after_purchase() {
		global $wp;
		if ( is_checkout() && !empty( $wp->query_vars['order-received'] ) ) {
			wp_redirect( get_site_url().'/thankyou?order_id='.$wp->query_vars['order-received'] );
			exit;
		}
	}
}