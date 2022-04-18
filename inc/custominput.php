function itprix_create_custom_field() {
    $args = array(
    'id' => 'isCustomizeProduct',
    'label' => __( 'Active Custom Text Field', 'itprix' ),
    'class' => 'itprix-custom-field',
    'desc_tip' => true,
    'options' =>["false" => "No","true"=>"Yes"],
    'description' => __( 'Do you need Custom Text Field?.', 'itprix' ),
    );
    
    woocommerce_wp_select( $args );
   }
   add_action( 'woocommerce_product_options_advanced', 'itprix_create_custom_field' );



   function itprix_save_custom_field( $post_id ) {
    $product = wc_get_product( $post_id );
    $isCustomizeProduct = isset( $_POST['isCustomizeProduct'] ) ? $_POST['isCustomizeProduct'] : '';
    $product->update_meta_data( 'isCustomizeProduct', sanitize_text_field( $isCustomizeProduct ) );
    $product->save();
   }
   add_action( 'woocommerce_process_product_meta', 'itprix_save_custom_field' );

   



// 1. Show input field 

add_action( 'woocommerce_before_add_to_cart_button', 'itprix_product_add_on', 9 );

function itprix_product_add_on() {
    global $post;
    $product = wc_get_product( $post->ID );
    $isCustomizeProduct = $product->get_meta( 'isCustomizeProduct' );
    if( $isCustomizeProduct=="true" ) {
        $value = isset( $_POST['custom_text_add_on'] ) ? sanitize_text_field( $_POST['custom_text_add_on'] ) : '';
        echo '<div class="itprix-custom-field-wrapper"><input type="text" id="itprix-title-field" name="custom_text_add_on" value="' . $value . '"></div>';
        }

}


// -----------------------------------------

// 2. Throw error if custom input field empty

add_filter( 'woocommerce_add_to_cart_validation', 'itprix_product_add_on_validation', 10, 3 );

function itprix_product_add_on_validation( $passed, $product_id, $qty ){

   if( isset( $_POST['custom_text_add_on'] ) && sanitize_text_field( $_POST['custom_text_add_on'] ) == '' ) {

      wc_add_notice( 'Custom Text is a required field', 'error' );

      $passed = false;

   }

   return $passed;

}

// -----------------------------------------

// 3. Save custom input field value into cart item data

add_filter( 'woocommerce_add_cart_item_data', 'itprix_product_add_on_cart_item_data', 10, 2 );

function itprix_product_add_on_cart_item_data( $cart_item, $product_id ){

    if( isset( $_POST['custom_text_add_on'] ) ) {

        $cart_item['custom_text_add_on'] = sanitize_text_field( $_POST['custom_text_add_on'] );

    }

    return $cart_item;

}

// -----------------------------------------

// 4. Display custom input field value @ Cart

add_filter( 'woocommerce_get_item_data', 'itprix_product_add_on_display_cart', 10, 2 );

function itprix_product_add_on_display_cart( $data, $cart_item ) {

    if ( isset( $cart_item['custom_text_add_on'] ) ){

        $data[] = array(

            'name' => 'Custom Text',

            'value' => sanitize_text_field( $cart_item['custom_text_add_on'] )

        );

    }

    return $data;

}

// -----------------------------------------

// 5. Save custom input field value into order item meta

add_action( 'woocommerce_add_order_item_meta', 'itprix_product_add_on_order_item_meta', 10, 2 );

function itprix_product_add_on_order_item_meta( $item_id, $values ) {

    if ( ! empty( $values['custom_text_add_on'] ) ) {

        wc_add_order_item_meta( $item_id, 'Custom Text Add-On', $values['custom_text_add_on'], true );

    }

}

// -----------------------------------------

// 6. Display custom input field value into order table

add_filter( 'woocommerce_order_item_product', 'itprix_product_add_on_display_order', 10, 2 );

function itprix_product_add_on_display_order( $cart_item, $order_item ){

    if( isset( $order_item['custom_text_add_on'] ) ){

        $cart_item['custom_text_add_on'] = $order_item['custom_text_add_on'];

    }

    return $cart_item;

}

// -----------------------------------------

// 7. Display custom input field value into order emails

add_filter( 'woocommerce_email_order_meta_fields', 'itprix_product_add_on_display_emails' );

function itprix_product_add_on_display_emails( $fields ) {

    $fields['custom_text_add_on'] = 'Custom Text Add-On';

    return $fields;

}
