<?php
/**
 * Plugin Name: Shopboost - Surprise Hesitating Visitors
 * Plugin URI: https://www.shopboost.nl
 * Description: Surprise Hesitating Visitors, offer hesitating visitors automatically a surprise and sell more!
 * Version: 1.0.1
 * Author: Shopboost
 * Text Domain: shopboost
 * Domain Path: /languages/
 * License: This Shopboost plugin for WordPress is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

define('SHOPBOOST_VERSION', shopboost_get_version());
define('SHOPBOOST_PLUGIN_PATH', plugin_dir_path( __FILE__ ));

/**
 * Check if the current user may manage the plugin.
 */
function shopboost_init() {
    if (current_user_can('administrator')) {
        add_action('admin_menu', 'shopboost_admin_actions');
        add_action('init', 'shopboost_languages');

        register_uninstall_hook(__FILE__, 'shopboost_uninstall');

        include(SHOPBOOST_PLUGIN_PATH . '/shopboost-options.php');
    }
}
add_action('plugins_loaded', 'shopboost_init');

/**
 * Uninstall the plugin, remove saved options.
 */
function shopboost_uninstall() {
    delete_option('shopboost_enable');
    delete_option('shopboost_enable_for_admin');
    delete_option('shopboost_merchant_id');
    delete_option('shopboost_first_time');
}

/**
 * Decide if a notice must be showed when the plugin is activated for the first time.
 */
function shopboost_first_time() {
    if (get_option('shopboost_first_time')) {
        delete_option( 'shopboost_first_time');
        add_action('admin_notices', 'shopboost_first_time_notice');
    }
}
add_action( 'admin_init', 'shopboost_first_time');

/**
 * Show notice after the first time the plugin is activated.
 */
function shopboost_first_time_notice() {
    $url = esc_url(add_query_arg('page', 'shopboost', get_admin_url() . 'options-general.php'));

    echo '<div class="updated notice is-dismissible">' . PHP_EOL;
    echo '    <p>' . __("Thank you for activating the Shopboost plugin. You must add your Shopboost merchant id to use this plugin.", "shopboost") . '</p>' . PHP_EOL;
    echo '    <p><a href="' . esc_attr($url) . '" class="button button-primary">' . __("Add Shopboost merchant id", "shopboost") . '</a> <a href="javascript:window.location.reload();" class="button">' . __("Hide message", "shopboost") . '</a></p>' . PHP_EOL;
    echo '</div>' . PHP_EOL;
}
register_activation_hook( __FILE__, function() {
    if (false === get_option('shopboost_enable')) {
        update_option('shopboost_enable', 'yes');
    }
    if (false === get_option('shopboost_enable_for_admin')) {
        update_option('shopboost_enable_for_admin', 'yes');
    }

    update_option( "shopboost_first_time", 1);
});

/**
 * Add settings link to the plugin overview page.
 *
 * @param array $links
 * @return array $links
 */
function shopboost_settings_link(array $links) {
    $url = esc_url(add_query_arg('page', 'shopboost', get_admin_url() . 'options-general.php'));
    $links[] = '<a href="' . esc_attr($url) . '">' . __('Settings', 'shopboost') . '</a>';

    return $links;
}
add_filter('plugin_action_links_shopboost/shopboost.php', 'shopboost_settings_link');

/**
 * Register a custom menu page.
 */
function shopboost_custom_menu_page() {
    $menuIcon = esc_attr('dashicons-cart');
    if (false !== $menuIcon = file_get_contents(plugins_url('/img/shopboost_menu_icon.svg?v=' . SHOPBOOST_VERSION, __FILE__))) {
        $menuIcon = esc_attr('data:image/svg+xml;base64,' . base64_encode($menuIcon));
    }

    add_menu_page('Shopboost', 'Shopboost', 'manage_options', 'shopboost', 'shopboost_page', $menuIcon, 999 );

    wp_enqueue_style('shopboost_admin_css', plugins_url('/css/shopboost_admin.css?v=' . SHOPBOOST_VERSION, __FILE__));
}
add_action( 'admin_menu', 'shopboost_custom_menu_page' );

/**
 * Get the plugin version.
 *
 * @return string
 */
function shopboost_get_version() : string {
    $pluginData = get_file_data(__FILE__, array('Version' => 'Version'), false);

    return $pluginData['Version'];
}

/**
 * Add support for different languages, at the moment NL and EN.
 */
function shopboost_languages() {
    load_plugin_textdomain('shopboost', false, '/shopboost-surprise-hesitating-visitors/languages/' );

    // List translations that are only used in the plugin header section but needs translations.
    $value = __('Surprise Hesitating Visitors, offer hesitating visitors automatically a surprise and sell more!', 'shopboost');
    $value = __('The Shopboost plugin will implement the Shopboost tag into your website, so you can make use of the Shopboost service. Go to \'Shopboost\' menu option, fill in your Shopboost merchant id and click the save button.', 'shopboost');

}

/**
 * Add a menu option for the plugin.
 */
function shopboost_admin_actions() {
	add_options_page("Shopboost", "Shopboost", 'manage_options', "shopboost", "shopboost_page");
}

/**
 * Load the plugin page when clicked on the menu option.
 */
function shopboost_page() {
    include(SHOPBOOST_PLUGIN_PATH . 'shopboost-page.php');
}

/**
 * Load the Shopboost plugin and add the merchant id that is saved in the database.
 */
function shopboost_front_footer() {
    $loadJavascript = true;

    // Don't load the script in the backend.
	if(is_admin()) {
        $loadJavascript = false;
    }

	// Load the script only when allowed.
	if ('yes' !== get_option('shopboost_enable')) {
	    $loadJavascript = false;
    }

	// Load the script not when a administrator user is logged in.
    if ('yes' !== get_option('shopboost_enable_for_admin') && current_user_can( 'administrator' )) {
        $loadJavascript = false;
    }

	if ($loadJavascript) {
	    $shopboostMerchantId = esc_js(get_option('shopboost_merchant_id'));

        echo '
<!--Start Shopboost script V1.5-->
<script type="text/javascript">
function addListenershopboost(c,a,b){a.addEventListener?a.addEventListener(c,b,!1):a.attachEvent("on"+c,b)}addListenershopboost("load",window,function(){"object"===typeof Cookiebot?(console.log("Cookiebot is actief"),Cookiebot.consent.marketing&&(console.log("Cookiebot consent"),loadshopboost())):loadshopboost()});
function loadshopboost(){function c(){"undefined"==typeof shopboost?setTimeout(function(){c()},10):(refshopboost=document.referrer,shopboost(' . $shopboostMerchantId . ',refshopboost))}var a=document.createElement("script");a.type="text/javascript";a.src="https://www.shopboostapp.com/v3/notification/shopboostv3.js";var b=document.createElement("script");b.type="text/javascript";b.src="https://www.shopboostapp.com/v3/notification/detshopboostnew.js";var d=document.getElementsByTagName("HEAD")[0];d.appendChild(b);d.appendChild(a);
c()};
</script>
<!--Einde Shopboost script-->';
	}
}
add_action('wp_footer', 'shopboost_front_footer', 100);