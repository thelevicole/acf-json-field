<?php


namespace AcfJsonField;

use acf_field;
use AcfJsonField\Utilities\Constants;
use AcfJsonField\Http\Requests\FieldPreview;

class FieldV5 extends acf_field {

	/**
	 * FieldV5 constructor.
	 */
	public function __construct() {

		/**
		 * @var $name string Single word, no spaces. Underscores allowed.
		 */
		$this->name = 'acf_json_field';

		/**
		 * @var $label string Multiple words, can include spaces, visible when selecting a field type.
		 */
		$this->label = __( 'JSON', 'skape' );

		/**
		 * @var $category string basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		 */
		$this->category = 'content';

		/**
		 * @var $defaults array Array of default settings which are merged into the field object. These are used later in settings.
		 */
		$this->defaults = [
			'json_type' => 'json', // json | ld+json
			'return_value' => 'json', // json | array | script
		];

		/**
		 * @var $settings array Store plugin settings (url, path, version) as a reference for later use with assets.
		 */
		$this->settings = [
			'url' => Constants::get( 'URL' ),
			'version' => Constants::get( 'VERSION' )
		];

		// do not delete!
		parent::__construct();

	}

	/**
	 * Build a validated array of javascript variables.
	 *
	 * @param array $field
	 * @return array
	 */
	public function _parse_js_vars( $field ) {

		$js_vars = wp_parse_args( [
			'json_type' => $field[ 'json_type' ],
			'return_value' => $field[ 'return_value' ],
		], $this->defaults );

		return $js_vars;
	}

	/**
	 *  input_admin_enqueue_scripts()
	 *
	 *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	 *  Use this action to add CSS + JavaScript to assist your create_field() action.
	 *
	 *  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	 *  @type	action
	 *  @since	3.6
	 *  @date	23/01/13
	 */
	public function input_admin_enqueue_scripts() {

		$version = $this->settings[ 'version' ];
		$url = $this->settings[ 'url' ];

		wp_enqueue_editor();
		wp_enqueue_script( 'acf-json-field', $url . 'build/js/input.js', [ 'editor', 'acf-input' ], $version );
		wp_enqueue_style( 'acf-json-field', $url . 'build/css/input.css', [ 'acf-input' ], $version );

	}

	/**
	 *  field_group_admin_enqueue_scripts()
	 *
	 *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	 *  Use this action to add CSS + JavaScript to assist your render_field_options() action.
	 *
	 *  @type	action (admin_enqueue_scripts)
	 *  @since	3.6
	 *  @date	23/01/13
	 *
	 *  @param	n/a
	 *  @return	n/a
	 */
	public function field_group_admin_enqueue_scripts() {

		$version = $this->settings[ 'version' ];
		$url = $this->settings[ 'url' ];

		wp_enqueue_script( 'acf-json-field-group', $url . 'build/js/input-group.js', [ 'acf-field-group' ], $version );

	}

	/**
	 *  render_field()
	 *
	 *  Create the HTML interface for your field
	 *
	 *  @param	$field - an array holding all the field's data
	 *
	 *  @type	action
	 *  @since	3.6
	 *  @date	23/01/13
	 */
	public function render_field( $field ) {

		/**
		 * @var $attributes array Empty array for HTML attributes with key value pairs.
		 */
		$attributes = [];

		// Populate valued attributes
		foreach ( [ 'id', 'name', 'key' ] as $key ) {
			if ( array_key_exists( $key, $field ) ) {
				$attributes[ $key ] = $field[ $key ];
			}
		}

		// Populate boolean attributes
		foreach ( [ 'readonly', 'disabled' ] as $key ) {
			if ( array_key_exists( $key, $field ) && $field[ $key ] ) {
				$attributes[ $key ] = $key;
			}
		}

		?>
			<script type="application/json" data-config="<?= esc_attr( $field[ 'key' ] ); ?>"><?= json_encode( $this->_parse_js_vars( $field ) ); ?></script>
			<textarea class="acf-json-field-data"<?= acf_esc_attr( $attributes ); ?>><?= esc_textarea( $field[ 'value' ] ); ?></textarea>
		<?php
	}

	/**
	 *  render_field_settings()
	 *
	 *  Create extra options for your field. This is rendered when editing a field.
	 *  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	 *
	 *  @param	$field	- an array holding all the field's data
	 *
	 *  @type	action
	 *  @since	3.6
	 *  @date	23/01/13
	 */
	public function render_field_settings( $field ) {

		acf_render_field_setting( $field, [
			'label' => __( 'JSON type', 'skape' ),
			'type' => 'select',
			'choices' => [
				'json' => __( 'JSON', 'skape' ),
				'ld+json' => __( 'JSON-LD', 'skape' ),
			],
			'default' => 'json',
			'name' => 'json_type'
		] );

		acf_render_field_setting( $field, [
			'label' => __( 'Return value', 'skape' ),
			'type' => 'select',
			'choices' => [
				'array' => __( 'PHP array', 'skape' ),
				'json' => __( 'JSON object', 'skape' ),
				'script' => __( 'HTML script tag', 'skape' ),
			],
			'default' => 'html',
			'name' => 'return_value'
		] );

	}

	/**
	 *  format_value()
	 *
	 *  This filter is applied to the $value after it is loaded from the db and before it is returned to the template
	 *
	 *  @type	filter
	 *  @since	3.6
	 *  @date	23/01/13
	 *
	 *  @param	$value (mixed) the value which was loaded from the database
	 *  @param	$post_id (mixed) the $post_id from which the value was loaded
	 *  @param	$field (array) the field array holding all the field options
	 *
	 *  @return	$value (mixed) the modified value
	 */
	public function format_value( $value, $post_id, $field ) {

		// Bail early if no value
		if ( empty( $value ) || !is_string( $value ) ) {
			return $value;
		}

		// Return different data types
		switch ( $field[ 'return_value' ] ) {
			case 'json':
				return $value;
			case 'array':
				return json_decode( $value, true );
			case 'script':
				return '<script type="application/' . $field[ 'json_type' ] . '">' . $value . '</script>';
		}

		return $value;
	}

	/**
	 *  load_field()
	 *
	 *  This filter is appied to the $field after it is loaded from the database
	 *
	 *  @type	filter
	 *  @since	3.6
	 *  @date	23/01/13
	 *
	 *  @param	$field - the field array holding all the field options
	 *
	 *  @return	$field - the field array holding all the field options
	 */
	public function load_field( $field ) {
		$field[ 'sub_fields' ] = acf_get_fields( $field );
		return $field;
	}

	/**
	 * validate_value()
	 *
	 * This filter is used to perform validation on the value prior to saving.
	 * All values are validated regardless of the field's required setting. This allows you to validate and return
	 * messages to the user if the value is not correct
	 *
	 * @type	filter
	 * @date	11/02/2014
	 * @since	5.0.0
	 *
	 * @param	$valid (boolean) validation status based on the value and the field's required setting
	 * @param	$value (mixed) the $_POST value
	 * @param	$field (array) the field array holding all the field options
	 * @param	$input (string) the corresponding input name for $_POST value
	 * @return	$valid
	 */
	function validate_value( $valid, $value, $field, $input ) {

		// Only validate if has value
		if ( !empty( $value ) ) {
			$decoded = json_decode( wp_unslash( $value ) );

			if ( $decoded === null && json_last_error() !== JSON_ERROR_NONE ) {
				switch ( json_last_error() ) {
					case JSON_ERROR_DEPTH:
						$error = __( 'Maximum stack depth exceeded.', 'skape' );
						break;
					case JSON_ERROR_STATE_MISMATCH:
						$error = __( 'Underflow or the modes mismatch.', 'skape' );
						break;
					case JSON_ERROR_CTRL_CHAR:
						$error = __( 'Unexpected control character found.', 'skape' );
						break;
					case JSON_ERROR_SYNTAX:
						$error = __( 'Syntax error, malformed JSON.', 'skape' );
						break;
					case JSON_ERROR_UTF8:
						$error = __( 'Malformed UTF-8 characters, possibly incorrectly encoded.', 'skape' );
						break;
					default:
						$error = __( 'Unknown error.', 'skape' );
						break;
				}

				$valid = sprintf( __( 'This value is not a valid JSON (%s). Error: %s', 'skape' ), strtoupper( $field[ 'json_type' ] ), $error );
			}
		}

		return $valid;
	}

}
