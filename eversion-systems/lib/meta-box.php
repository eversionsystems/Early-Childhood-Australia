<?php
/**
 * Registering meta boxes
 *
 * All the definitions of meta boxes are listed below with comments.
 * Please read them CAREFULLY.
 *
 * You also should read the changelog to know what has been changed before updating.
 *
 * For more information, please visit:
 * @link http://www.deluxeblogtips.com/meta-box/
 */


add_filter( 'rwmb_meta_boxes', 'eca_xero_tracking_meta_box' );

/**
 * Register meta boxes
 *
 * @return void
 */
function eca_xero_tracking_meta_box( $meta_boxes )
{
	/**
	 * Prefix of meta keys (optional)
	 * Use underscore (_) at the beginning to make keys hidden
	 * Alt.: You also can make prefix empty to disable it
	 */
	// Better has an underscore as last sign
	
	$prefix = 'eca_';

	// 1st meta box
	$meta_boxes[] = array(
		// Meta box id, UNIQUE per meta box. Optional since 4.1.5
		'id' => 'standard',

		// Meta box title - Will appear at the drag and drop handle bar. Required.
		'title' => __( 'Xero Cost Centre', 'rwmb' ),

		// Post types, accept custom post types as well - DEFAULT is array('post'). Optional.
		'pages' => array( 'product' ),

		// Where the meta box appear: normal (default), advanced, side. Optional.
		'context' => 'normal',

		// Order of meta box: high (default), low. Optional.
		'priority' => 'high',

		// Auto save: true, false (default). Optional.
		'autosave' => true,

		// List of meta fields
		'fields' => array(
			// Company Number
			array(
				// Field name - Will be used as label
				'name'  => __( 'Cost Centre', 'rwmb' ),
				// Field ID, i.e. the meta key
				'id'    => "{$prefix}cost_centre",
				// Field description (optional)
				'desc'  => __( 'Xero Cost Centre', 'rwmb' ),
				'type'  => 'text',
				// Default value (optional)
				//'std'   => __( '', 'rwmb' ),
				// CLONES: Add to make the field cloneable (i.e. have multiple value)
				//'clone' => false,
			),
			//Company Name
			array(
				// Field name - Will be used as label
				'name'  => __( 'Sub Cost Centre', 'rwmb' ),
				// Field ID, i.e. the meta key
				'id'    => "{$prefix}sub_cost_centre",
				// Field description (optional)
				'desc'  => __( 'Xero Sub Cost Centre', 'rwmb' ),
				'type'  => 'text',
				// Default value (optional)
				//'std'   => __( '', 'rwmb' ),
				// CLONES: Add to make the field cloneable (i.e. have multiple value)
				//'clone' => false,
			)
		),
		'validation' => array(
			'rules' => array(
				"{$prefix}cost_centre" => array(
					'required'  => false,
					'minlength' => 1), 
				"{$prefix}sub_cost_centre" => array(
					'required'  => false,
					'minlength' => 1)
			),
			// optional override of default jquery.validate messages
			'messages' => array(
				"{$prefix}cost_centre" => array(
					'required'  => __( 'Cost Centre is required', 'rwmb' ),
					'minlength' => __( 'Cost Centre must be at least 1 character long', 'rwmb' ),
				),
				"{$prefix}sub_cost_centre" => array(
					'required'  => __( 'Sub Cost Centre is required', 'rwmb' ),
					'minlength' => __( 'Sub Cost Centre must be at least 1 character long', 'rwmb' )
				)
			)
		)
	);

	return $meta_boxes;
}