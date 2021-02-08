<?php

namespace AcfJsonField\Wrappers;
use Exception;
use AcfJsonField\Utilities\Strings;
use WP_Post, WP_Query;

use ReflectionClass;
use ReflectionMethod;

class WpModel {

	/**
	 * @var array|WP_Post|null The core wordpress post item.
	 */
	public $wpObject;

	/**
	 * @var null|self The cached parent object.
	 */
	public $parentObject;

	/**
	 * @var string[] Property aliases for WP_Post.
	 */
	private static $aliases = [
		'id' => 'ID',
		'title' => 'post_title',
		'content' => 'post_content',
		'date' => 'post_date',
		'date_gmt' => 'post_date_gmt',
		'author' => 'post_author',
		'excerpt' => 'post_excerpt',
		'status' => 'post_status',
		'password' => 'post_password',
		'name' => 'post_name',
		'slug' => 'post_name',
		'modified' => 'post_modified',
		'modified_gmt' => 'post_modified_gmt',
		'content_filtered' => 'post_content_filtered',
		'parent' => 'post_parent',
		'type' => 'post_type',
		'mime_type' => 'post_mime_type'
	];

	/**
	 * @var string[] Properties to be excluded from toArray() method
	 */
	public static $protected = [
		'wpObject'
	];

	/**
	 * WpModel constructor.
	 *
	 * @param $wp_post
	 */
	public function __construct( $wp_post ) {
		$this->wpObject = get_post( $wp_post );
	}

	/**
	 * @returns array
	 */
	public function __debugInfo() {
		return $this->toArray( true );
	}

	/**
	 * Get property directly from WP_Post object.
	 *
	 * @param $name
	 * @returns mixed
	 * @throws Exception
	 */
	public function __get( $name ) {
		/**
		 * Generate a magic attribute method name from getter key. Inspired by Laravel.
		 *
		 * @example color -> getColorAttribute()
		 * @example fullName -> getFullNameAttribute()
		 */
		$propert_method = ( new Strings( "get $name attribute" ) )->camel()->get();

		/**
		 * See if we have a magic attribute method.
		 */
		if ( method_exists( $this, $propert_method ) ) {
			return call_user_func( [ $this, $propert_method ] );
		}

		/**
		 * Check if property exists on WP_Post object
		 */
		else if ( $this->wpObject && property_exists( $this->wpObject, $name ) ) {
			return $this->wpObject->{$name};
		}

		/**
		 * Finally, check if we have an alias for this property
		 */
		else if ( $this->wpObject && isset( static::$aliases[ $name ] ) ) {
			return $this->wpObject->{ static::$aliases[ $name ] };
		}

		throw new Exception( "Property $name is not defined" );
	}

	/**
	 * Call method directly from WP_Post object.
	 *
	 * @param $name
	 * @returns mixed
	 */
	public function __call( $name, $arguments ) {
		if ( $this->wpObject && method_exists( $this->wpObject, $name ) ) {
			return call_user_func_array( [ $this->wpObject, $name ], $arguments );
		}
	}

	/**
	 * Convert object data to associative array.
	 *
	 * @returns array
	 */
	public function toArray( $excludeProtected = true ) {
		$info = [
			'id' => $this->id,
			'object' => $this->wpObject
		];

		// First add WP_Post properties
		foreach ( static::$aliases as $alias => $property ) {
			$info[ $alias ] = $this->{$alias};
		}

		$class = new ReflectionClass( static::class );
		$methods = $class->getMethods(ReflectionMethod::IS_PUBLIC );

		foreach ( $methods as $method ) {
			if ( preg_match( '/^get(\w+)Attribute$/', $method->name, $matches ) ) {
				$info[ lcfirst( $matches[ 1 ] ) ] = call_user_func( [ $this, $matches[ 0 ] ] );
			}
		}

		if ( $excludeProtected ) {
			foreach ( static::$protected as $key ) {
				unset( $info[ $key ] );
			}
		}

		return $info;
	}

	/**
	 * Quickly check if the post exists
	 *
	 * @returns bool
	 */
	public function getExistsAttribute() {
		return !empty( $this->wpObject );
	}

	/**
	 * Get the post parent object
	 *
	 * @returns null|self
	 */
	public function getParentAttribute() {

		if ( !$this->parentObject && $this->post_parent ) {

			// Query database for fields
			$this->parentObject = get_post( $this->post_parent );

			if ( $this->parentObject ) {
				$this->parentObject = new self( $this->parentObject );
			}

		}

		return $this->parentObject;
	}


	/**
	 * Get post meta from cache or DB.
	 *
	 * @param string $key
	 * @param boolean $single
	 * @returns mixed
	 */
	public function getMeta( string $key, $single = false ) {
		return get_post_meta( $this->id, $key, $single );
	}

	/**
	 * Add new post meta.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @returns false|int
	 */
	public function addMeta( string $key, $value ) {
		return add_post_meta( $this->id, $key, $value );
	}

	/**
	 * Update database post meta.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param mixed $prev_value
	 * @returns bool|int
	 */
	public function updateMeta( string $key, $value, $prev_value = '' ) {
		return update_post_meta( $this->id, $key, $value, $prev_value );
	}

}
