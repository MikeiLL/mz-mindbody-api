<?php
/**
 * HTML Element
 *
 * This file contains the class used to generate HTML DOM elements.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Libraries;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * HTML Element
 *
 * Create an html element, like in js.
 * Source: https://davidwalsh.name/create-html-elements-php-htmlelement-class
 */
class HtmlElement {

    /**
     * Type of HTML element generated.
     *
     * @access public
     * @var string $type Type of dom element (a, tr, td, p, etc...).
     */
    public $type;

    /**
     * Element Attributes
     *
     * @access public
     * @var array $attributes Attributes assigned to html element.
     */
    public $attributes;

    /**
     * Self Closers.
     *
     * @access public
     * @var array $self_closers Elements not requiring a closing html tag.
     */
    public $self_closers;


    /**
     * Constructor.
     *
     * @param string $type Type of html element.
     * @param array  $self_closers Elements not requiring a closing tag.
     */
    function __construct( $type, $self_closers = array( 'input', 'img', 'hr', 'br', 'meta', 'link' ) ) {
        $this->type         = strtolower( $type );
        $this->self_closers = $self_closers;
    }


    /**
     * Get an attribute from instance of this class.
     *
     * @param string $attribute an html element attribute.
     *
     * @return string value of attribute.
     */
    function get( $attribute ) {
        return $this->attributes[ $attribute ];
    }

    /**
     * Set an array of key, value pairs.
     *
     * @param string $attribute an html element attribute.
     * @param string $value an html element attribute value.
     * @return void.
     */
    function set( $attribute, $value = '' ) {
        if ( ! is_array( $attribute ) ) {
            $this->attributes[ $attribute ] = $value;
        } else {
            $this->attributes = array_merge( $this->attributes, $attribute );
        }
    }

    /**
     * Remove an attribute.
     *
     * @param string $att an html element attribute.
     * @return void.
     */
    function remove( $att ) {
        if ( isset( $this->attributes[ $att ] ) ) {
            unset( $this->attributes[ $att ] );
        }
    }

    /**
     * Clear attributes.
     */
    function clear() {
        $this->attributes = array();
    }

    /**
     * Inject
     *
     * If object is instance of this class, inject
     * another instance of the html object class into
     * it.
     *
     * @param HtmlElement $object An instance of this class.
     */
    function inject( $object ) {
        if ( __class__ === get_class( $object ) ) {
            $this->attributes['text'] .= $object->build();
        }
    }

    /**
     * Build
     *
     * Generate an HTML string
     *
     * @return string $build html string.
     */
    function build() {
        // Start.
        $build = '<' . $this->type;

        // Add attributes.
        if ( count( $this->attributes ) ) {
            foreach ( $this->attributes as $key => $value ) {
                if ( 'text' !== $key ) {
                    $build .= ' ' . $key . '="' . $value . '"';
                }
            }
        }

        // Closing.
        if ( ! in_array( $this->type, $this->self_closers, true ) ) {
            $build .= '>' . $this->attributes['text'] . '</' . $this->type . '>';
        } else {
            $build .= ' />';
        }

        return $build;
    }

    /**
     * Output
     *
     * Echo the HTML string onto php page.
     *
     * @return void.
     */
    function output() {
        echo wp_kses($this->build(), [
          "div" => [
            "div" => [
                "class" => [],
                "id" => [],
                "title" => [],
              ],
              "a" => [
                "class" => [],
                "id" => [],
                "title" => [],
                "href" => [],
                "target" => [],
                "data-classid" => [],
                "data-classname" => [],
                "data-staffname" => [],
                "data-time" => [],
              ],
              "span" => [
                "class" => [],
                "id" => [],
                "title" => [],
              ],
              "p" => [
                "class" => [],
                "id" => [],
                "title" => [],
              ],
              "h1" => [
                "class" => [],
                "id" => [],
                "title" => [],
              ],
              "h2" => [
                "class" => [],
                "id" => [],
                "title" => [],
              ],
              "h3" => [
                "class" => [],
                "id" => [],
                "title" => [],
              ],
              "h4" => [
                "class" => [],
                "id" => [],
                "title" => [],
              ]
          ]
        ]);
    }
}
