<?php
/**
 * Shortcode Loader
 *
 * This file contains the absctract class for registering and displaying shortcodes.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Common\Interfaces;

    /**
     * "WordPress Plugin Template" Copyright (C) 2018 Michael Simpson  (email : michael.d.simpson@gmail.com)
     *
     * This file is part of WordPress Plugin Template for WordPress.
     *
     * WordPress Plugin Template is free software: you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation, either version 3 of the License, or
     * (at your option) any later version.
     *
     * WordPress Plugin Template is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     * GNU General Public License for more details.
     *
     * You should have received a copy of the GNU General Public License
     * along with Contact Form to Database Extension.
     * If not, see http://www.gnu.org/licenses/gpl-3.0.html
     */
abstract class ShortcodeLoader {

    /**
     * Register
     *
     * Register text string that generates call to shortcode in wp page.
     *
     * @param mixed $shortcode_name Either string name or array of names.
     *
     * Shortcode name is as it would appear in a post, e.g. [shortcode_name],
     * or an array of such names in case you want to have more than one name
     * for the same shortcode.
     *
     * @return void
     */
    public function register( $shortcode_name ) {
        $this->register_shortcode_to_function( $shortcode_name, 'handle_shortcode' );
    }

    /**
     * Register Shortcode to Function
     *
     * @param  mixed  $shortcode_name either string name of the shortcode
     *                         (as it would appear in a post, e.g. [shortcode_name])
     *                         or an array of such names in case you want to have more than one name
     *                         for the same shortcode.
     * @param  string $function_name   Name of public function in this class to call as the shortcode handler.
     * @return void
     */
    protected function register_shortcode_to_function( $shortcode_name, $function_name ) {
        if ( is_array( $shortcode_name ) ) {
            foreach ( $shortcode_name as $sc_name ) {
                add_shortcode( $sc_name, array( $this, $function_name ) );
            }
        } else {
            add_shortcode( $shortcode_name, array( $this, $function_name ) );
        }
    }

    /**
     * Handle Shortcode
     *
     * @abstract Override this function and add actual shortcode handling here.
     * @param    array  $atts shortcode inputs.
     * @param    string $content What is between matching pair of shortcode tags.
     * @return   string shortcode content.
     */
    abstract public function handle_shortcode( $atts, $content = 'hello');
}
