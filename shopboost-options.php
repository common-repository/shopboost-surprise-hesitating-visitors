<?php
/**
 * Generate the option page.
 *
 * @return string $html
 */
function shopboost_options() : string {
    $html = '<h2 class="title">' . __("Options:", "shopboost") . '</h2>';

    // Show confirmation.
    $processStatus = shopboost_processform();
    if (true === $processStatus) {
        $html .= '<div class="updated"><p><strong>' . __("Changes are saved.", "shopboost") . '</strong></p></div>';
	}
    if (true !== $processStatus && false !== $processStatus) {
        $html .= '<div class="error"><p><strong>' . $processStatus . '</strong></p></div>';
	}

	// Form part.
    $html .=  ' <form method="post" action="' . esc_attr(esc_url(admin_url())) . 'options-general.php?page=shopboost">' . PHP_EOL
          .   '     <input type="hidden" name="shopboost_nonce" size="10" value="' . esc_attr(wp_create_nonce('shopboost-options')) . '" />' . PHP_EOL
          .   '     <table class="form-table">' . PHP_EOL
		  .   '			<tr valign="top">' . PHP_EOL
          .   '                <th>' .  __("Shopboost merchant id", "shopboost") . ' (' . __("required", "shopboost") . '):</th>' . PHP_EOL
		  .   '                <td><input type="text" name="shopboost_merchant_id" size="20" value="' . esc_attr(get_option('shopboost_merchant_id')) . '" /></td>' . PHP_EOL
          .   '			</tr>' . PHP_EOL
          .   '			<tr valign="top">' . PHP_EOL
          .   '                <th> - ' .  __("Load Shopboost script", "shopboost")  . ':</th>' . PHP_EOL
          .   '                <td>' . shopboost_generate_select('shopboost_enable', get_option('shopboost_enable')) . '</td>' . PHP_EOL
          .   '			</tr>' . PHP_EOL
          .   '			<tr valign="top">' . PHP_EOL
          .   '                <th> - ' .  __("Load Shopboost when a WP Administrator is logged in", "shopboost")  . ':</th>' . PHP_EOL
          .   '                <td>' . shopboost_generate_select('shopboost_enable_for_admin', get_option('shopboost_enable_for_admin')) . '</td>' . PHP_EOL
          .   '			</tr>' . PHP_EOL
          .   '		</table>' . PHP_EOL
          .   '     <p class="submit"><input type="submit" class="button-primary" value="' .  __("Save changes", "shopboost") . '" /></p>' . PHP_EOL
          .   '	</form>' . PHP_EOL;

    return $html;
}

/**
 * Generate a select option element with yes or no options.
 *
 * @param string $name
 * @param string $selectedOption
 * @return string $html
 */
function shopboost_generate_select(string $name, string $selectedOption) : string {
    $isNoneSelected = ('' == $selectedOption) ? 'selected' : '';
    $isYesSelected  = ('yes' == $selectedOption) ? 'selected' : '';
    $isNoSelected   = ('no' == $selectedOption) ? 'selected' : '';

    $html = '<select name="' . esc_attr($name) . '" id="' . esc_attr($name) . '">' . PHP_EOL
          . '   <option value="" ' . esc_attr($isNoneSelected) . '></option>' . PHP_EOL
          . '   <option value="yes" ' . esc_attr($isYesSelected) . '>' . __("Yes", "shopboost") . '</option>' . PHP_EOL
          . '   <option value="no" ' . esc_attr($isNoSelected) . '>' . __("No", "shopboost") . '</option>' . PHP_EOL
          . '</select>' . PHP_EOL;

    return $html;
}

/**
 * Process the form. Generate errors and save data to the database.
 *
 * @return mixed
 */
function shopboost_processform() {
	$values = [];
	$errors = [];
		
	// Check first if the form is correctly submitted.
	if (0 == count($_POST)) {
		return false;
	}
    if (!wp_verify_nonce($_POST['shopboost_nonce'], 'shopboost-options')) {
		return __("Illegal attempt to update the settings stopped.", "shopboost") ;
	}

    // Process the field values.
	if (isset($_POST['shopboost_merchant_id']) && 0 !== preg_match('/^\d+$/', $_POST['shopboost_merchant_id'], $result)) {
        $values['shopboost_merchant_id'] = sanitize_text_field($_POST['shopboost_merchant_id']);
	} else {
        $errors[] = __('"Shopboost merchant id" is a required field.', 'shopboost') ;
	}

    if (isset($_POST['shopboost_enable']) && in_array($_POST['shopboost_enable'], ['yes','no'])) {
        $values['shopboost_enable'] = sanitize_text_field($_POST['shopboost_enable']);
    } else {
        $errors[] = __('"Load Shopboost script" is a required field.', 'shopboost') ;
    }

    if (isset($_POST['shopboost_enable_for_admin']) && in_array($_POST['shopboost_enable_for_admin'], ['yes','no'])) {
        $values['shopboost_enable_for_admin'] = sanitize_text_field($_POST['shopboost_enable_for_admin']);
    } else {
        $errors[] = __('"Load Shopboost when a WP Administrator is logged in" is a required field.', 'shopboost') ;
    }

	// Check if required values are available, status will be true.
	if (0 == count($errors)) {
        foreach ($values as $key => $value) {
            update_option($key, $value);
        }

        return true;
    } else {
	    return implode('<br />', $errors);
    }
}