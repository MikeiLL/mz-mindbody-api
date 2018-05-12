<?php

namespace MZ_Mindbody\Inc\Backend;

use MZ_Mindbody\Inc\Libraries as Libraries;

/**
 * This file contains the class which holds all the actions and methods to create the admin dashboard sections
 *
 * This file contains all the actions and functions to create the admin dashboard sections.
 * It should probably be refactored to use oop approach at least for the sake of consistency.
 *
 * @since 2.1.0
 *
 * @package MZ_Mindbody
 * 
 */
 /**
 * Actions/Filters
 *
 * Related to all settings API.
 *
 * @since  1.0.0
 */
 
 class Settings_Page {
 	
 	static protected $wposa_obj;
 
 	public function __construct() {
		self::$wposa_obj = new Libraries\WP_OSA();
	}
	
	public function addSections() {
	
		// Section: Basic Settings.
		self::$wposa_obj->add_section(
			array(
				'id'    => 'wposa_basic',
				'title' => __( 'Basic Settings', 'WPOSA' ),
			)
		);

		// Section: Other Settings.
		self::$wposa_obj->add_section(
			array(
				'id'    => 'wposa_other',
				'title' => __( 'Other Settings', 'WPOSA' ),
			)
		);

		// Field: Text.
		self::$wposa_obj->add_field(
			'wposa_basic',
			array(
				'id'      => 'text',
				'type'    => 'text',
				'name'    => __( 'Text Input', 'WPOSA' ),
				'desc'    => __( 'Text input description', 'WPOSA' ),
				'default' => 'Default Text',
			)
		);

		// Field: Number.
		self::$wposa_obj->add_field(
			'wposa_basic',
			array(
				'id'                => 'text_no',
				'type'              => 'number',
				'name'              => __( 'Number Input', 'WPOSA' ),
				'desc'              => __( 'Number field with validation callback `intval`', 'WPOSA' ),
				'default'           => 1,
				'sanitize_callback' => 'intval',
			)
		);

		// Field: Password.
		self::$wposa_obj->add_field(
			'wposa_basic',
			array(
				'id'   => 'password',
				'type' => 'password',
				'name' => __( 'Password Input', 'WPOSA' ),
				'desc' => __( 'Password field description', 'WPOSA' ),
			)
		);

		// Field: Textarea.
		self::$wposa_obj->add_field(
			'wposa_basic',
			array(
				'id'   => 'textarea',
				'type' => 'textarea',
				'name' => __( 'Textarea Input', 'WPOSA' ),
				'desc' => __( 'Textarea description', 'WPOSA' ),
			)
		);

		// Field: Separator.
		self::$wposa_obj->add_field(
			'wposa_basic',
			array(
				'id'   => 'separator',
				'type' => 'separator',
			)
		);

		// Field: Title.
		self::$wposa_obj->add_field(
			'wposa_basic',
			array(
				'id'   => 'title',
				'type' => 'title',
				'name' => '<h1>Title</h1>',
			)
		);

		// Field: Checkbox.
		self::$wposa_obj->add_field(
			'wposa_basic',
			array(
				'id'   => 'checkbox',
				'type' => 'checkbox',
				'name' => __( 'Checkbox', 'WPOSA' ),
				'desc' => __( 'Checkbox Label', 'WPOSA' ),
			)
		);

		// Field: Radio.
		self::$wposa_obj->add_field(
			'wposa_basic',
			array(
				'id'      => 'radio',
				'type'    => 'radio',
				'name'    => __( 'Radio', 'WPOSA' ),
				'desc'    => __( 'Radio Button', 'WPOSA' ),
				'options' => array(
					'yes' => 'Yes',
					'no'  => 'No',
				),
			)
		);

		// Field: Multicheck.
		self::$wposa_obj->add_field(
			'wposa_basic',
			array(
				'id'      => 'multicheck',
				'type'    => 'multicheck',
				'name'    => __( 'Multile checkbox', 'WPOSA' ),
				'desc'    => __( 'Multile checkbox description', 'WPOSA' ),
				'options' => array(
					'yes' => 'Yes',
					'no'  => 'No',
				),
			)
		);

		// Field: Select.
		self::$wposa_obj->add_field(
			'wposa_basic',
			array(
				'id'      => 'select',
				'type'    => 'select',
				'name'    => __( 'A Dropdown', 'WPOSA' ),
				'desc'    => __( 'A Dropdown description', 'WPOSA' ),
				'options' => array(
					'yes' => 'Yes',
					'no'  => 'No',
				),
			)
		);

		// Field: Image.
		self::$wposa_obj->add_field(
			'wposa_other',
			array(
				'id'      => 'image',
				'type'    => 'image',
				'name'    => __( 'Image', 'WPOSA' ),
				'desc'    => __( 'Image description', 'WPOSA' ),
				'options' => array(
					'button_label' => 'Choose Image',
				),
			)
		);

		// Field: File.
		self::$wposa_obj->add_field(
			'wposa_other',
			array(
				'id'      => 'file',
				'type'    => 'file',
				'name'    => __( 'File', 'WPOSA' ),
				'desc'    => __( 'File description', 'WPOSA' ),
				'options' => array(
					'button_label' => 'Choose file',
				),
			)
		);

		// Field: Color.
		self::$wposa_obj->add_field(
			'wposa_other',
			array(
				'id'          => 'color',
				'type'        => 'color',
				'name'        => __( 'Color', 'WPOSA' ),
				'desc'        => __( 'Color description', 'WPOSA' ),
				'placeholder' => __( '#5F4B8B', 'WPOSA' ),
			)
		);

		// Field: WYSIWYG.
		self::$wposa_obj->add_field(
			'wposa_other',
			array(
				'id'   => 'wysiwyg',
				'type' => 'wysiwyg',
				'name' => __( 'WP_Editor', 'WPOSA' ),
				'desc' => __( 'WP_Editor description', 'WPOSA' ),
			)
		);
	
	}


}