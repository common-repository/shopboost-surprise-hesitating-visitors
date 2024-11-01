<?php
/**
 * Introduction page of the plugin.
 *	
 * @package Shopboost
 * @author Shopboost
 * @since 1.0.0
 */
?>
<div class="wrap">
    <img class="shopboost_header" src="<?php echo esc_attr(plugins_url('/img/shopboost_logo.svg?v=' . esc_attr(SHOPBOOST_VERSION), __FILE__)); ?>" />
    <h1><?php echo __('Shopboost - Surprise Hesitating Visitors', 'shopboost'); ?></h1>
    <br />
    <strong><?php echo __('Version', 'shopboost'); ?>:</strong> <?php echo esc_html(SHOPBOOST_VERSION); ?>
    <br />
    <br />
    <?php echo __('Fill your merchant id in the field below to use the Shopboost service. To use this plugin you must have a Shopboost account. Visit <a href="https://www.shopboostapp.com/dashboard/login.php" target="_blank">www.shopboostapp.com</a> for more information.', 'shopboost'); ?>
    <br />
    <h2><?php echo __('Goto', 'shopboost'); ?></h2>
    - <a href="https://www.shopboostapp.com/dashboard/login.php" target="_blank"><?php echo __('My Shopboost dashboard', 'shopboost'); ?></a>
    <br />
    <br />
    <?php echo shopboost_options(); ?>
</div>