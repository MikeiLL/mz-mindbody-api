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
				'title' => __( 'Basic Settings', 'mz-mindbody-api' ),
			)
		);

		// Section: Other Settings.
		self::$wposa_obj->add_section(
			array(
				'id'    => 'wposa_other',
				'title' => __( 'Other Settings', 'mz-mindbody-api' ),
			)
		);
		
		// Field: HTML.
		self::$wposa_obj->add_field(
			'wposa_basic',
			array(
				'id'      => 'server_check',
				'type'    => 'html',
				'name'    => __( 'Server Check', 'mz-mindbody-api' ),
				'desc'    => $this->server_check()
			)
		);
		
		// Field: Text.
		self::$wposa_obj->add_field(
			'wposa_basic',
			array(
				'id'      => 'credentials_intro',
				'type'    => 'html',
				'name'    => __( 'Enter your mindbody credentials below.', 'mz-mindbody-api' ),
				'desc'    => $this->credentials_intro()
			)
		);

		// Field: Number.
		self::$wposa_obj->add_field(
			'wposa_basic',
			array(
				'id'                => 'text_no',
				'type'              => 'number',
				'name'              => __( 'Number Input', 'mz-mindbody-api' ),
				'desc'              => __( 'Number field with validation callback `intval`', 'mz-mindbody-api' ),
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
				'name' => __( 'Password Input', 'mz-mindbody-api' ),
				'desc' => __( 'Password field description', 'mz-mindbody-api' ),
			)
		);

		// Field: Textarea.
		self::$wposa_obj->add_field(
			'wposa_basic',
			array(
				'id'   => 'textarea',
				'type' => 'textarea',
				'name' => __( 'Textarea Input', 'mz-mindbody-api' ),
				'desc' => __( 'Textarea description', 'mz-mindbody-api' ),
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
				'name' => __( 'Checkbox', 'mz-mindbody-api' ),
				'desc' => __( 'Checkbox Label', 'mz-mindbody-api' ),
			)
		);

		// Field: Radio.
		self::$wposa_obj->add_field(
			'wposa_basic',
			array(
				'id'      => 'radio',
				'type'    => 'radio',
				'name'    => __( 'Radio', 'mz-mindbody-api' ),
				'desc'    => __( 'Radio Button', 'mz-mindbody-api' ),
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
				'name'    => __( 'Multile checkbox', 'mz-mindbody-api' ),
				'desc'    => __( 'Multile checkbox description', 'mz-mindbody-api' ),
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
				'name'    => __( 'A Dropdown', 'mz-mindbody-api' ),
				'desc'    => __( 'A Dropdown description', 'mz-mindbody-api' ),
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
				'name'    => __( 'Image', 'mz-mindbody-api' ),
				'desc'    => __( 'Image description', 'mz-mindbody-api' ),
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
				'name'    => __( 'File', 'mz-mindbody-api' ),
				'desc'    => __( 'File description', 'mz-mindbody-api' ),
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
				'name'        => __( 'Color', 'mz-mindbody-api' ),
				'desc'        => __( 'Color description', 'mz-mindbody-api' ),
				'placeholder' => __( '#5F4B8B', 'mz-mindbody-api' ),
			)
		);

		// Field: WYSIWYG.
		self::$wposa_obj->add_field(
			'wposa_other',
			array(
				'id'   => 'wysiwyg',
				'type' => 'wysiwyg',
				'name' => __( 'WP_Editor', 'mz-mindbody-api' ),
				'desc' => __( 'WP_Editor description', 'mz-mindbody-api' ),
			)
		);
	
	}
	
	private function server_check() {
	
		$return = '';
		$mz_requirements = 0;
		
		include 'PEAR/Registry.php';

		$reg = new \PEAR_Registry;

		if (extension_loaded('soap'))
		{
			$return .= __('SOAP installed! ', 'mz-mindbody-api');
		}
		else
		{
			$return .= __('SOAP is not installed. ', 'mz-mindbody-api');
			$mz_requirements = 1;
		}
		$return .=  '&nbsp;';
		
		if (class_exists('System')===true)
		{
		   $return .= __('PEAR installed! ', 'mz-mindbody-api');
		}
		else
		{
		   $return .= __('PEAR is not installed. ', 'mz-mindbody-api');
		   $mz_requirements = 1;
		}

		if ($mz_requirements == 1)
		{

			$return .=  '<div class="settings-error"><p>';
			$return .= __('MZ Mindbody API requires SOAP and PEAR. Please contact your hosting provider or enable via your CPANEL of php.ini file.', 'mz-mindbody-api');
			$return .=  '</p></div>';
		}
		else
		{
			
			$return .=  '<div class="" ><p>';
			$return .= __('Congratulations. Your server appears to be configured to integrate with mindbodyonline.', 'mz-mindbody-api');
			$return .=  '</p></div>';
		}
		return $return;
	}
	
	private function credentials_intro(){
		$return = '';
		$return .= '</p>'.sprintf(__('If you do not have them yet, visit the %1$s MindBodyOnline developers website %2$s and register for developer credentials.', 'mz-mindbody-api'),
							'<a href="https://api.mindbodyonline.com/Home/LogIn">', 
							'</a>').'</p>';
		$return .= '(<a href="http://www.mzoo.org/creating-your-mindbody-credentials/">'. __('Detailed instructions here', 'mz-mindbody-api').'</a>.)';
		return $return;
	}

}