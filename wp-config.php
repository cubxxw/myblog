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
define('WP_CACHE', true);
define( 'WPCACHEHOME', '/www/wwwroot/w.nsddd.top/wp-content/plugins/wp-super-cache/' );
define( 'DB_NAME', 'w_nsddd_top' );

/** MySQL database username */
define( 'DB_USER', 'w_nsddd_top' );

/** MySQL database password */
define( 'DB_PASSWORD', '1234' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '*xh<V3r<P].2u7*6+cW/&A3`!vbTes(THAfW>X81.;`{4mu.>88@gKZsCPodwY$o' );
define( 'SECURE_AUTH_KEY',  'Ud 8g{CER]k0]~#$f=+aNk>A}YWfK>_B,[$c1C:!Et(7aPCnw{c(.[%^qj=p;uf^' );
define( 'LOGGED_IN_KEY',    'wo)On9#UF2IOWkIygJiuY.*Gvlhzz36>P$=# ,g%jLRoL{g}$a<.lP-y_bGFA-$J' );
define( 'NONCE_KEY',        '3x,/l}ipNR?lfN-w^1B}GDme<xy>eje=Dsa!TtZ:,`ihlo?RA,FfY=B280mCL2KP' );
define( 'AUTH_SALT',        'e,u2B`5~,Y$J2=yInq9m2f0;q7,$aC6GIE<`^]W+|n{$+KF_GhpE} 2Caf uz Da' );
define( 'SECURE_AUTH_SALT', 'JsK[Yw*0{EN(bZPAs6kx}%4Kq#{7|_ZqOmM6TiG5LK_] X[#OE 133GW@:d<+DIG' );
define( 'LOGGED_IN_SALT',   'vrm:OjT}d0ofkB[Y{{N:41^A<CBIN`9MxYzBZo/>}3X+J~@Wp{kf#/W/YBfel:Qv' );
define( 'NONCE_SALT',       '.wufmbv.7_S~WUqmmn*,.W*1v5e$5xz:kPsUN^hil!{$pk|}U,z=v1)J2;re!$x+' );

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
