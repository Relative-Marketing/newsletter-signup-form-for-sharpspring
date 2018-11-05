<?php
/**
 * Class Form_Generator
 *
 * Class form creating the different components of a form
 */

namespace RelativeMarketing\Options\Form;

class Generate {
	/**
	 * An array of self closing tags
	 * 
	 * Example: Self closing
	 * 
	 * <input />
	 * 
	 * Example: non self closing
	 * 
	 * <textarea>value</textarea>
	 */
	const SELF_CLOSING_TAGS = ['input'];

	/**
	 * Will return a form field based on the user input
	 */
	public static function field( string $id, string $type, string $value, array $args = [] ) {
		// @TODO Add support for specific field types such as color, select etc
		$value = sanitize_text_field( $value );

		if ( self::element_is_self_closing( $type ) ) {
			return sprintf( '<%s name="%s" id="%2$s" value="%3$s" />', $type, $id, $value );
		}

		return sprintf( '<%s name="%s" id="%2$s">%3$s</%1$s>', $type, $id, $value );
	}

	public static function element_is_self_closing( $element ) {
		return in_array( $element, self::SELF_CLOSING_TAGS );
	}
}