window.jsonlint = require( 'jsonlint-mod' );

require( 'codemirror/mode/javascript/javascript' );
require( 'codemirror/addon/edit/matchbrackets' );
require( 'codemirror/addon/comment/continuecomment' );
require( 'codemirror/addon/comment/comment' );
require( 'codemirror/addon/lint/lint' );
require( 'codemirror/addon/lint/javascript-lint' );
require( 'codemirror/addon/lint/json-lint' );
const CodeMirror = require( 'codemirror' );

( function( $ ) {
	'use strict';

	// ACF V5
	if ( typeof acf.add_action !== 'undefined' ) {

		/*
		 *  ready append (ACF5)
		 *
		 *  These are 2 events which are fired during the page load
		 *  ready = on page load similar to $(document).ready()
		 *  append = on new DOM elements appended via repeater field
		 *
		 *  @type	event
		 *  @date	20/07/13
		 *
		 *  @param	$el (jQuery selection) the jQuery element which contains the ACF fields
		 *  @return	n/a
		 */
		acf.add_action( 'ready append', function( $el ) {

			// search $el for fields of type 'table_field'
			acf.get_fields( {
				type: 'acf_json_field'
			}, $el ).each( function() {

				const $input = $( this );
				const $field = acf.get_field_wrap( $input );
				const $textarea = $field.find( 'textarea.acf-json-field-data' );

				const key = acf.get_field_key( $field );
				const config = $field.find( `script[data-config="${key}"]` ).text();

				/**
				 * Get a field option from the parsed json.
				 * @param {string} name
				 * @param {mixed} fallback
				 * @param {boolean} forceFallback If true and opition is false|null|0, return fallback.
				 */
				const option = ( name, fallback = null, forceFallback = false ) => {
					try {
						const data = JSON.parse( config );

						if ( name in data ) {
							const value = data[ name ];

							if ( !forceFallback ) {
								return value;
							} else {
								if ( value ) {
									return value;
								}
							}
						}
					} catch ( e ) {}

					return fallback;
				};

				const codeEditor = CodeMirror.fromTextArea( $textarea.get( 0 ), {
					lineNumbers: true,
					matchBrackets: true,
					autoCloseBrackets: true,
					mode: 'application/' + option( 'json_type', 'json', true ),
					lineWrapping: true,
					gutters: [ 'CodeMirror-lint-markers' ],
					line: true,
					lint: true
				} );

				codeEditor.on( 'change', () => {
					codeEditor.save();
					acf.set( key, codeEditor.getValue() );
				} );

			} );
		} );
	}


} )( jQuery );

