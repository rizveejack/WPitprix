<?php 


class SearchProductByAttribute 
{

	function __construct(){

		add_action('graphql_register_types',[$this,'searchVariation']);
	}

	function find_matching_product_variation_id($product_id, $variation_data)
	{
	$product         = wc_get_product( $product_id );
	$attribute_names = array_keys( $product->get_attributes() );
 	$attributes = array();
		foreach ( $variation_data as $attribute ) {
			$attribute_name = $attribute['attributeName'];
			if ( in_array( "pa_{$attribute_name}", $attribute_names, true ) ) {
				$attribute_name = "pa_{$attribute_name}";
			} elseif ( ! in_array( $attribute_name, $attribute_names, true ) ) {
				throw new UserError(
					sprintf(
						/* translators: %1$s: attribute name, %2$s: product name */
						__( '%1$s is not a valid attribute of the product: %2$s.', 'wp-graphql-woocommerce' ),
						$attribute_name,
						$product->get_name()
					)
				);
			}

			$attribute_value = ! empty( $attribute['attributeValue'] ) ? $attribute['attributeValue'] : '';
			$attribute_key   = "attribute_{$attribute_name}";

			$attributes[ $attribute_key ] = $attribute_value;
		}   
    
        return (new \WC_Product_Data_Store_CPT())->find_matching_product_variation(
            new \WC_Product($product_id),
            $attributes
        );
	}



	function searchVariation(){
		register_graphql_mutation( 'searchVariation', [

			# inputFields expects an array of Fields to be used for inputting values to the mutation
			'inputFields'         => [
				'productId' => [
					'type' => array( 'non_null' => 'Int' ),
					'description' => __( 'Description of the input field', 'wpitprix' ),
				],
				'variation'   => [
						'type'        => array( 'list_of' => 'ProductAttributeInput' ),
						'description' => __( 'Cart item product variation attributes', 'wp-graphql-woocommerce' ),
					]
			],

			# outputFields expects an array of fields that can be asked for in response to the mutation
			
			'outputFields'        => [
				'varientId' => [
					'type' => 'Int',
					'description' => __( 'Description of the output field', 'wpitprix' ),
					'resolve' => function( $payload, $args, $context, $info ) {
		                   		return isset( $payload['varientId'] ) ? $payload['varientId'] : null;
					}
				],
				'price' => [
					'type' => 'String',
					'description' => __( 'Description of the output field', 'wpitprix' ),
					'resolve' => function( $payload, $args, $context, $info ) {
		                   		return isset( $payload['price'] ) ? $payload['price'] : null;
					}
				]
			],

			# mutateAndGetPayload expects a function, and the function gets passed the $input, $context, and $info
			
			'mutateAndGetPayload' => function( $input, $context, $info ) {
				// Do any logic here to sanitize the input, check user capabilities, etc
				$exampleOutput = null;
				if ( ! empty( $input['productId'] ) ) {
					$exampleOutput = $input['productId'];
					$product         = wc_get_product( $exampleOutput );
					$attributes = $input[variation];
					$variationid = $this->find_matching_product_variation_id($exampleOutput, $attributes);
				}
				return [
					'varientId' => $variationid,
					'price'=>strip_tags(html_entity_decode($product->get_price_html())),
				];
			}
		] );
	}
}