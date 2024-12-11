<?php
define('DISALLOW_FILE_EDIT', true);
define('WP_AUTO_UPDATE_CORE', true);
define('DISALLOW_FILE_MODS', true);
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'pqyqclvx_wordpress' );

/** Database username */
define( 'DB_USER', 'pqyqclvx_wordpress' );

/** Database password */
define( 'DB_PASSWORD', '@1340@nhatbao?' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'XUnHwYHxyqutw8;xHfJ<WA`Q<pqPF&^oY{h<<TNMPusk m&o(FN~=V$28Ld%MG(%' );
define( 'SECURE_AUTH_KEY',  'uH9<ZLs(Od(^nKWyGzf9T4-}6:Amzd0x%P<&y;n=@,jy?83:+OSt,]6(ePq)nY%z' );
define( 'LOGGED_IN_KEY',    't^;>uqF2[tg./uGMD7$*y7a[eQe-<vc5*U~}]t#:e4.A+f|Q(@?3[{2J=.^+4V+n' );
define( 'NONCE_KEY',        'k|v$f*T%$UU}hRhy@1=UhJ& ftc^BU2OW7!1cU+%e&8Z`S<%#}8csl/.Zby#OvV{' );
define( 'AUTH_SALT',        '1C0~%2l9<5JE]K`:x.:zk*FM;kqEgb[|&ML#~o=`i?&0&Cqu|Os[T/NQ,tqsX&S ' );
define( 'SECURE_AUTH_SALT', '@L!P;0=uVH5^iCcbe4_eOv)Tj(opHN&uJ+X,5LLHT,I(N=>J<!] V&7!.cD,,TGD' );
define( 'LOGGED_IN_SALT',   'CBZQitm 6&hJ& <hJ)x>{6Rx*7;kWUCW?~LqpK,*ziLT9GRatqtU>paQ3d7A)NUW' );
define( 'NONCE_SALT',       'O~/y>`a1%!d)*auPH&yNotkq:ka_Zgfp=-I`NU;`=ln.#>}u7vhj`<8+DaSWm-bB' );

/**#@-*/

/**
 * WordPress database table prefix.
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
