<?php 

class RegisterFields
{
	function __construct()
	{
		add_action('graphql_register_types',[$this,'custom_post_field']);
    add_action('graphql_register_types',[$this,'register_Swag']);
	}

 

function custom_post_field()
{
  register_graphql_object_type( 'CustomPost', [
      'fields' => [
        'isCustomizeProduct'  => [ 'type' => 'String' ],
        'instractionText' => [ 'type' => 'String' ],
      ]
    ] );

  // register_graphql_object_type( 'CustomPosts', [
  //     'description' => __( 'Custompost instraction', 'WPitprix' ),
  //     'fields'      => [
  //       'customize'   => [
  //         'type' => [
  //           'list_of' => 'CustomPost'
  //         ]
  //       ],
  //     ],
  //   ] );


register_graphql_field(
      'product',
      'CustomProduct',
      [
        'description' => __( 'CustomPost', 'headless-cms' ),
        'type'        => 'CustomPost',
        'resolve'     => function ($product) {
          $isCustomizeProduct = get_post_meta( $product->ID, '_isCustomizeProduct', true );
          $instractionText = get_post_meta( $product->ID, '_instraction_text', true );
          return ['isCustomizeProduct'  => $isCustomizeProduct,'instractionText' => $instractionText];

        },
      ]
    );


}


function register_Swag()
{


  register_graphql_object_type( 'swatch_image', [
      'fields' => [
        'sourceUrl'  => [ 'type' => 'String' ],   
      ]
    ] );

  register_graphql_object_type( 'swatch_field', [
      'fields' => [
        'type'  => [ 'type' => 'String' ],
        'color' => [ 'type' => 'String' ],
        'image'=>['type' => 'swatch_image']
          
      ]
    ] );


  register_graphql_field(
      'paColor',
      'Swatch',
      [
        'description' => __( 'swatch', 'wpitprix' ),
        'type'        => 'String',
        'resolve'     => function ($result, $args) {
          // $swatch = get_post_meta( $product->ID, '_swatch_type_options', true );
          // return ["color"=>"red","type"=>"default","image"=>["sourceUrl"=>""]];
          $swatch = wc_get_attribute( $result->termTaxonomyId );
          return json_encode($swatch);

        },
      ]
    );





}



}