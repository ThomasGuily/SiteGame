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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
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

define( 'WP_SITEURL', 'http://localhost/' );
define( 'WP_HOME', 'http://localhost/' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Y=Uu=6}Z&2zFPOP!t)8..<_+FpuFlsFRaqbyJ~C]wQYdYCG?oWf1$)Iu(ovPd^|#' );
define( 'SECURE_AUTH_KEY',  '4=j3V/7vbg3Tt*/D=i:l#>aBFayEj5PLP[8,Nu.]q/k`x;Wz_w*dUx2)G*,K.)DL' );
define( 'LOGGED_IN_KEY',    '?6{UAt?itl~heweDw&p$(SCoF)qSH9 n<n<1]>F2AgB,6F@J=Xi-Vwhn*2V{@Jln' );
define( 'NONCE_KEY',        'WGcl;j;)c`{`Bw j*F35L&Wgpn5#*V~ZhZ}_E(LFc:MYu^8[_hSw1pSCZvI7@Jnr' );
define( 'AUTH_SALT',        '`x^79gP.f($~UdO!X(@l>uR*eS{vcxbez9Nd#tUv$Z|;_tI`Mfx3`:e.M(5K6cfo' );
define( 'SECURE_AUTH_SALT', '(G?6T6.hNkx1uG.N/9Ag8JA$To)cEd.Dq&(Mz1A93OWsk+ds[[[ T=ypU1snS6:F' );
define( 'LOGGED_IN_SALT',   'J]Ff7Bow5Wy_$wb.O@i/3&{t<%c0$Y{5J)v4Fv{mWTmGtxT[lm9_!uYJiwU+fXYC' );
define( 'NONCE_SALT',       'XN#-G;P=AWdzHmFGQV<%r31G<|1vU-&&y.g6i.k%Pj%($c2C.9:I!Z:!%O(hYt?k' );

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
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
