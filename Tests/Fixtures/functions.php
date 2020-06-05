<?php

if ( ! function_exists( 'rocket_mkdir_p' ) ) {
	/**
	 * Recursive directory creation based on full path.
	 *
	 * @since 1.3.4
	 *
	 * @source wp_mkdir_p() in /wp-includes/functions.php
	 *
	 * @param string $target path to the directory we want to create.
	 * @return bool True if directory is created/exists, false otherwise
	 */
	function rocket_mkdir_p( $target ) {
		$wrapper = null;

		if ( rocket_is_stream( $target ) ) {
			list( $wrapper, $target ) = explode( '://', $target, 2 );
		}

		// from php.net/mkdir user contributed notes.
		$target = str_replace( '//', '/', $target );

		// Put the wrapper back on the target.
		if ( null !== $wrapper ) {
			$target = $wrapper . '://' . $target;
		}

		// safe mode fails with a trailing slash under certain PHP versions.
		$target = rtrim( $target, '/\\' );
		if ( empty( $target ) ) {
			$target = '/';
		}

		if ( rocket_direct_filesystem()->exists( $target ) ) {
			return rocket_direct_filesystem()->is_dir( $target );
		}

		// Attempting to create the directory may clutter up our display.
		if ( rocket_mkdir( $target ) ) {
			return true;
		} elseif ( rocket_direct_filesystem()->is_dir( dirname( $target ) ) ) {
			return false;
		}

		// If the above failed, attempt to create the parent node, then try again.
		if ( ( '/' !== $target ) && ( rocket_mkdir_p( dirname( $target ) ) ) ) {
			return rocket_mkdir_p( $target );
		}

		return false;
	}
}


if ( ! function_exists( 'rocket_is_stream' ) ) {
	/**
	 * Test if a given path is a stream URL.
	 *
	 * @since 3.5.3
	 *
	 * @source wp_is_stream() in /wp-includes/functions.php
	 *
	 * @param string $path The resource path or URL.
	 *
	 * @return bool true if the path is a stream URL; else false.
	 */
	function rocket_is_stream( $path ) {
		$scheme_separator = strpos( $path, '://' );

		if ( false === $scheme_separator ) {
			// $path isn't a stream.
			return false;
		}

		$stream = substr( $path, 0, $scheme_separator );

		return in_array( $stream, stream_get_wrappers(), true );
	}
}

if ( ! function_exists( 'rocket_mkdir' ) ) {
	/**
	 * Directory creation based on WordPress Filesystem
	 *
	 * @since 1.3.4
	 *
	 * @param string $dir The path of directory will be created.
	 * @return bool
	 */
	function rocket_mkdir( $dir ) {
		$chmod = rocket_get_filesystem_perms( 'dir' );
		return rocket_direct_filesystem()->mkdir( $dir, $chmod );
	}
}

if ( ! function_exists( 'rocket_has_constant' ) ) {
	/**
	 * Checks if the constant is defined.
	 *
	 * NOTE: This function allows mocking constants when testing.
	 *
	 * @since 3.5
	 *
	 * @param string $constant_name Name of the constant to check.
	 *
	 * @return bool true when constant is defined; else, false.
	 */
	function rocket_has_constant( $constant_name ) {
		return defined( $constant_name );
	}
}

if ( ! function_exists( 'rocket_get_filesystem_perms' ) ) {
	/**
	 * Get the permissions to apply to files and folders.
	 *
	 * Reminder:
	 * `$perm = fileperms( $file );`
	 *
	 *  WHAT                                         | TYPE   | FILE   | FOLDER |
	 * ----------------------------------------------+--------+--------+--------|
	 * `$perm`                                       | int    | 33188  | 16877  |
	 * `substr( decoct( $perm ), -4 )`               | string | '0644' | '0755' |
	 * `substr( sprintf( '%o', $perm ), -4 )`        | string | '0644' | '0755' |
	 * `$perm & 0777`                                | int    | 420    | 493    |
	 * `decoct( $perm & 0777 )`                      | string | '644'  | '755'  |
	 * `substr( sprintf( '%o', $perm & 0777 ), -4 )` | string | '644'  | '755'  |
	 *
	 * @since  3.2.4
	 *
	 * @param  string $type The type: 'dir' or 'file'.
	 *
	 * @return int          Octal integer.
	 */
	function rocket_get_filesystem_perms( $type ) {
		static $perms = [];

		if ( rocket_get_constant( 'WP_ROCKET_IS_TESTING', false ) ) {
			$perms = [];
		}

		// Allow variants.
		switch ( $type ) {
			case 'dir':
			case 'dirs':
			case 'folder':
			case 'folders':
				$type = 'dir';
				break;

			case 'file':
			case 'files':
				$type = 'file';
				break;

			default:
				return 0755;
		}

		if ( isset( $perms[ $type ] ) ) {
			return $perms[ $type ];
		}

		// If the constants are not defined, use fileperms() like WordPress does.
		if ( 'dir' === $type ) {
			$fs_chmod_dir   = (int) rocket_get_constant( 'FS_CHMOD_DIR', 0 );
			$perms[ $type ] = $fs_chmod_dir > 0
				? $fs_chmod_dir
				: fileperms( rocket_get_constant( 'ABSPATH' ) ) & 0777 | 0755;
		} else {
			$fs_chmod_file  = (int) rocket_get_constant( 'FS_CHMOD_FILE', 0 );
			$perms[ $type ] = $fs_chmod_file > 0
				? $fs_chmod_file
				: fileperms( rocket_get_constant( 'ABSPATH' ) . 'index.php' ) & 0777 | 0644;
		}

		return $perms[ $type ];
	}
}

if ( ! function_exists( 'rocket_get_constant' ) ) {
	/**
	 * Gets the constant is defined.
	 *
	 * NOTE: This function allows mocking constants when testing.
	 *
	 * @since 3.5
	 *
	 * @param string     $constant_name Name of the constant to check.
	 * @param mixed|null $default       Optional. Default value to return if constant is not defined.
	 *
	 * @return bool true when constant is defined; else, false.
	 */
	function rocket_get_constant( $constant_name, $default = null ) {
		if ( ! rocket_has_constant( $constant_name ) ) {
			return $default;
		}

		return constant( $constant_name );
	}
}

if ( ! function_exists( 'get_rocket_parse_url' ) ) {
	/**
	 * Extract and return host, path, query and scheme of an URL
	 *
	 * @since 2.11.5 Supports UTF-8 URLs
	 * @since 2.1 Add $query variable
	 * @since 2.0
	 *
	 * @param string $url The URL to parse.
	 *
	 * @return array Components of an URL
	 */
	function get_rocket_parse_url( $url ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
		if ( ! is_string( $url ) ) {
			return;
		}

		$encoded_url = preg_replace_callback(
			'%[^:/@?&=#]+%usD',
			function( $matches ) {
				return rawurlencode( $matches[0] );
			},
			$url
		);

		$url      = wp_parse_url( $encoded_url );
		$host     = isset( $url['host'] ) ? strtolower( urldecode( $url['host'] ) ) : '';
		$path     = isset( $url['path'] ) ? urldecode( $url['path'] ) : '';
		$scheme   = isset( $url['scheme'] ) ? urldecode( $url['scheme'] ) : '';
		$query    = isset( $url['query'] ) ? urldecode( $url['query'] ) : '';
		$fragment = isset( $url['fragment'] ) ? urldecode( $url['fragment'] ) : '';

		/**
		 * Filter components of an URL
		 *
		 * @since 2.2
		 *
		 * @param array Components of an URL
		 */
		return (array) apply_filters(
			'rocket_parse_url',
			[
				'host'     => $host,
				'path'     => $path,
				'scheme'   => $scheme,
				'query'    => $query,
				'fragment' => $fragment,
			]
		);
	}
}

if ( ! function_exists( 'rocket_is_live_site' ) ) {
	/**
	 * Check if the current URL is for a live site (not local, not staging).
	 *
	 * @since  3.5
	 * @author Remy Perona
	 *
	 * @return bool True if live, false otherwise.
	 */
	function rocket_is_live_site() {
		if ( rocket_get_constant( 'WP_ROCKET_DEBUG' ) ) {
			return true;
		}

		$host = wp_parse_url( home_url(), PHP_URL_HOST );
		if ( ! $host ) {
			return false;
		}

		// Check for local development sites.
		$local_tlds = [
			'127.0.0.1',
			'localhost',
			'.local',
			'.test',
			'.docksal',
			'.docksal.site',
			'.dev.cc',
			'.lndo.site',
		];
		foreach ( $local_tlds as $local_tld ) {
			if ( $host === $local_tld ) {
				return false;
			}

			// Check the TLD.
			if ( substr( $host, - strlen( $local_tld ) ) === $local_tld ) {
				return false;
			}
		}

		// Check for staging sites.
		$staging = [
			'.wpengine.com',
			'.pantheonsite.io',
			'.flywheelsites.com',
			'.flywheelstaging.com',
			'.kinsta.com',
			'.kinsta.cloud',
			'.cloudwaysapps.com',
			'.azurewebsites.net',
			'.wpserveur.net',
			'-liquidwebsites.com',
			'.myftpupload.com',
		];
		foreach ( $staging as $partial_host ) {
			if ( strpos( $host, $partial_host ) ) {
				return false;
			}
		}

		return true;
	}
}

if ( ! function_exists( 'rocket_notice_html' ) ) {
	/**
	 * Outputs notice HTML
	 *
	 * @since  2.11
	 * @author Remy Perona
	 *
	 * @param array $args An array of arguments used to determine the notice output.
	 *
	 * @return void
	 */
	function rocket_notice_html( $args ) {
		$defaults = [
			'status'           => 'success',
			'dismissible'      => 'is-dismissible',
			'message'          => '',
			'action'           => '',
			'dismiss_button'   => false,
			'readonly_content' => '',
		];

		$args = wp_parse_args( $args, $defaults );

		switch ( $args['action'] ) {
			case 'clear_cache':
				$args['action'] = '<a class="wp-core-ui button" href="' . wp_nonce_url( admin_url( 'admin-post.php?action=purge_cache&type=all' ), 'purge_cache_all' ) . '">' . __( 'Clear cache', 'rocket' ) . '</a>';
				break;
			case 'stop_preload':
				$args['action'] = '<a class="wp-core-ui button" href="' . wp_nonce_url( admin_url( 'admin-post.php?action=rocket_stop_preload&type=all' ), 'rocket_stop_preload' ) . '">' . __( 'Stop Preload', 'rocket' ) . '</a>';
				break;
			case 'force_deactivation':
				/**
				 * Allow a "force deactivation" link to be printed, use at your own risks
				 *
				 * @since 2.0.0
				 *
				 * @param bool $permit_force_deactivation true will print the link.
				 */
				$permit_force_deactivation = apply_filters( 'rocket_permit_force_deactivation', true );

				// We add a link to permit "force deactivation", use at your own risks.
				if ( $permit_force_deactivation ) {
					global $status, $page, $s;
					$plugin_file  = 'wp-rocket/wp-rocket.php';
					$rocket_nonce = wp_create_nonce( 'force_deactivation' );

					$args['action'] = '<a href="' . wp_nonce_url( 'plugins.php?action=deactivate&amp;rocket_nonce=' . $rocket_nonce . '&amp;plugin=' . $plugin_file . '&amp;plugin_status=' . $status . '&amp;paged=' . $page . '&amp;s=' . $s, 'deactivate-plugin_' . $plugin_file ) . '">' . __( 'Force deactivation ', 'rocket' ) . '</a>';
				}
				break;
		}

		?>
		<div class="notice notice-<?php echo esc_attr( $args['status'] ); ?> <?php echo esc_attr( $args['dismissible'] ); ?>">
			<?php
			$tag = 0 !== strpos( $args['message'], '<p' ) && 0 !== strpos( $args['message'], '<ul' );

			echo ( $tag ? '<p>' : '' ) . $args['message'] . ( $tag ? '</p>' : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Dynamic content is properly escaped in the view.
			?>
			<?php if ( ! empty( $args['readonly_content'] ) ) : ?>
				<p><?php esc_html_e( 'The following code should have been written to this file:', 'rocket' ); ?>
					<br><textarea readonly="readonly" id="rules" name="rules" class="large-text readonly" rows="6"><?php echo esc_textarea( $args['readonly_content'] ); ?></textarea>
				</p>
			<?php
			endif;
			if ( $args['action'] || $args['dismiss_button'] ) :
				?>
				<p>
					<?php echo $args['action']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
					<?php if ( $args['dismiss_button'] ) : ?>
						<a class="rocket-dismiss" href="<?php echo wp_nonce_url( admin_url( 'admin-post.php?action=rocket_ignore&box=' . $args['dismiss_button'] ), 'rocket_ignore_' . $args['dismiss_button'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Dismiss this notice.', 'rocket' ); ?></a>
					<?php endif; ?>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}
}
