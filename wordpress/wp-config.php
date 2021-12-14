<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'game' );

/** MySQL database username */
define( 'DB_USER', 'magellan' );

/** MySQL database password */
define( 'DB_PASSWORD', 'Magellanthenavigateur' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost:3306' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );
define ('FS_METHOD','direct');

define( 'WP_SITEURL', 'https://the-games.magellan.fpms.ac.be/wordpress' );
define( 'WP_HOME',    'https://the-games.magellan.fpms.ac.be/wordpress' );


/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '~0{JV(+}/63VPBLL_gJ9tmtU90Q?g}+_cv{e^e>4c:&Fz5r}h0&6plg=HQb+A3/4' );
define( 'SECURE_AUTH_KEY',  'u.ktmN99{u$:GXvcV|HI4!>>kuF^m+{KL6sy:!(fSB8okQig[]OZDaBZ?tpm-Z+|' );
define( 'LOGGED_IN_KEY',    'N+ Ht0mldZik^7~a1hXs*Lt>7Neas9Svno%_2#)s$_54ekcxCiADYz]10vyl_myX' );
define( 'NONCE_KEY',        'T7:*;>9eR`WOB*,LLM!;L+Acm(8^,XCm$e@09p*-:jxVM2g!C;t071z^I?lgXMmM' );
define( 'AUTH_SALT',        '7X%6z<NLZzuOX@in3K?b4.g2>4JbPgtrDj@L{LaY//,t_oLAnK`@DrE,om>0>E!V' );
define( 'SECURE_AUTH_SALT', 'q^K`,.xgWm7 1n1,L;WP7d({IgrN.7*_Cmu7{p^c/.AHk=!HVPAg*6rlbN?(w]Of' );
define( 'LOGGED_IN_SALT',   'q#X6l7OmQjG2&H@]7$xzV-fAs*8>B#l})mfs`.)+R`aM^8l^;i{$8)5qu}l=m#H#' );
define( 'NONCE_SALT',       ':Jc:PvB45Y)BuUGBbG1P{a/D+*eFJO~?P5M|)8QwFE]A:*g@}]U0^tH|~H-z?lX5' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
