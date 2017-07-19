<?php
/*
Plugin Name: IP Address Widget II
Plugin URI: http://www.find-ip.net/ip-script
Description: Show the visitor's IP address, country, city, region, operating system and browser in a widget. You can specify the information to be shown.
Author: Find-IP.net
Author URI: http://www.find-ip.net/
Version: 1.0.4
*/

function ip_address_widget_check_options($args) {
	$def = array(
		'title' => __('Your Information', 'ip-address-widget'),		
		'country' => '1',
		'flag' => '1',
		'city' => '1',
		'region' => '1',
		'language' => '1',
		'system' => '1',
		'browser' => '1',
		'shadow' => '1',
		);
	if (!is_array($args)) { $args = array(); }
	return array_merge($def, $args);
}

function ip_address_widget_info($attr) {
	$options = ip_address_widget_check_options($attr);
	$country = $options["country"];
	$flag = $options["flag"];
	$city = $options["city"];
	$region = $options["region"];
	$language = $options["language"];
	$browser = $options["browser"];
	$system = $options["system"];
	$shadow = $options["shadow"];	
	$out = "<style>.findipinfowp img{border:0;display:inline} .findipinfowp li{cursor: pointer}</style>";
	if($shadow)
	{
		$out = "<style>.findipinfowp img{box-shadow: 1px 1px 3px #ccc;border:0;display:inline;} .findipinfowp li{cursor: pointer}</style>";
	}
	$out .="<ul class=\"findipinfowp\">";
	$out .= "<div id=\"findipwidgetwp\"></div>";
	$out .= "<li id=\"findipurl\">Powered by <a href=\"http://www.find-ip.net/\" target=\"_blank\">Find-IP.net</a></li>";
	$out .= "</ul>";
	
	$wpversion = get_bloginfo('version');
	$script_url = "https://";
	if(0 <= version_compare($wpversion, "3.5", ">=")) {
		$script_url = "//";
	}
	$script_url .= "api.find-ip.net/wp-widget.js?country=$country&flag=$flag&city=$city&region=$region&language=$language&browser=$browser&system=$system";
	wp_register_script('ip-address-widget-js', $script_url, array(), null, true);
	if(0 <= version_compare($wpversion, "4.1.0", ">=")) {
		add_filter('script_loader_tag', 'ip_address_widget_defer', 10, 2);
	}
	wp_enqueue_script('ip-address-widget-js');	
	return $out;
}

function ip_address_widget_defer($tag, $handle) {
    if('ip-address-widget-js' !== $handle) {
        return $tag;
	}
    return str_replace(' src', ' defer src', $tag);
}

function widget_ip_address($args) {
	extract($args);
        $options = ip_address_widget_check_options(get_option("ip_address_widget"));
	echo $before_widget;
	echo $before_title;
	echo $options['title'];
	echo $after_title;
	echo ip_address_widget_info($options);
	echo $after_widget;
}

function widget_ip_address_control() {
	$options = ip_address_widget_check_options(get_option("ip_address_widget"));
	if($_POST['ip_address_widget-submit']) {
		$options['title'] = htmlspecialchars($_POST['ip_address_widget-title']);		
		$options['system'] = (isset($_POST['ip_address_widget-system'])) ? "1" : "0";
		$options['browser'] = (isset($_POST['ip_address_widget-browser'])) ? "1" : "0";
		$options['country'] = (isset($_POST['ip_address_widget-country'])) ? "1" : "0";
		$options['flag'] = (isset($_POST['ip_address_widget-flag'])) ? "1" : "0";
		$options['city'] = (isset($_POST['ip_address_widget-city'])) ? "1" : "0";
		$options['region'] = (isset($_POST['ip_address_widget-region'])) ? "1" : "0";
		$options['language'] = (isset($_POST['ip_address_widget-language'])) ? "1" : "0";
		$options['shadow'] = (isset($_POST['ip_address_widget-shadow'])) ? "1" : "0";
		update_option("ip_address_widget", $options);
	}
?>
	<p style="text-align: left;"><?php echo __('Widget Title', 'ip-address-widget') ?>:
	<input type="text" id="widgettitle" name="ip_address_widget-title" value="<?php echo $options['title'];?>" /></p>         
	<input type="checkbox" <?php if($options['country'] == 1) echo "checked=\"checked\""; ?>" value="1" id="ip_address_widget-country" name="ip_address_widget-country"/> <?php echo __('Country', 'ip-address-widget') ?><br>
	<input type="checkbox" <?php if($options['flag'] == 1) echo "checked=\"checked\""; ?>" value="1" id="ip_address_widget-flag" name="ip_address_widget-flag"/> <?php echo __('Flag', 'ip-address-widget') ?><br>         
	<input type="checkbox" <?php if($options['city'] == 1) echo "checked=\"checked\""; ?>" value="1" id="ip_address_widget-city" name="ip_address_widget-city"/> <?php echo __('City', 'ip-address-widget') ?><br>
	<input type="checkbox" <?php if($options['region'] == 1) echo "checked=\"checked\""; ?>" value="1" id="ip_address_widget-region" name="ip_address_widget-region"/> <?php echo __('Region', 'ip-address-widget') ?><br>
	<input type="checkbox" <?php if($options['system'] == 1) echo "checked=\"checked\""; ?>" value="1" id="ip_address_widget-system" name="ip_address_widget-system"/> <?php echo __('System', 'ip-address-widget') ?><br>
	<input type="checkbox" <?php if($options['browser'] == 1) echo "checked=\"checked\""; ?>" value="1" id="ip_address_widget-browser" name="ip_address_widget-browser"/> <?php echo __('Browser', 'ip-address-widget') ?><br>
	<input type="checkbox" <?php if($options['language'] == 1) echo "checked=\"checked\""; ?>" value="1" id="ip_address_widget-language" name="ip_address_widget-language"/> <?php echo __('Language', 'ip-address-widget') ?><br>	
	<input type="checkbox" <?php if($options['shadow'] == 1) echo "checked=\"checked\""; ?>" value="1" id="ip_address_widget-shadow" name="ip_address_widget-shadow"/> <?php echo __('Flag Shadow', 'ip-address-widget') ?><br>	
	<input type="hidden" id="ip_address_widget-submit" name="ip_address_widget-submit" value="1" />
<?php
}

function ip_address_widget_init() {
	load_plugin_textdomain('ip-address-widget', str_replace(ABSPATH, '', dirname(__FILE__)), dirname(plugin_basename(__FILE__)));
	register_sidebar_widget('IP Address Widget', 'widget_ip_address');
	register_widget_control('IP Address Widget', 'widget_ip_address_control', 250, 100 );
}

function ip_address_widget_display($args = array()) {
	echo ip_address_widget_info($args);
}

add_action('init', 'ip_address_widget_init');
?>