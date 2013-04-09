<?php
/*
Plugin Name: Google Analytics Multitrack
Description: Embed multiple GA tracker scripts in a blog page - one for the individual blog, one for the whole network
Version: 0.1
Author: Mike Kelly
Licence: GPL3
*/

/*
Copyright (C) 2013 Mike Kelly

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once(dirname(__FILE__) . '/plugin.php');

class GoogleAnalyticsMultitrack extends GoogleAnalyticsMultitrack_Plugin {

    public function __construct(){
        $this->register_plugin('google-analytics-multitrack', __FILE__);
        $this->add_hooks();
    }
    
    public function add_hooks(){
    	if (is_admin()){
    		$this->add_action('admin_menu');
    		$this->add_action('network_admin_menu');
    	}
    	$this->add_action('admin_init', 'register_settings');
    	$this->add_action('wp_print_scripts');
    }
    
    public function register_settings(){
    	// whitelist options
    	register_setting('gam_plugin_options', 'gam_options', array (&$this,'validate_options'));
    	register_setting('gam_plugin_network_options', 'gam_network_options', array (&$this,'validate_network_options')); 
    }

    public function uninstall(){
        $this->delete_plugin_options();
    }

    public function admin_menu(){
        add_options_page(__('Google Analytics Multitrack', $this->plugin_name), __('Google Analytics Multitrack', $this->plugin_name), 'manage_network', 'gam_options', array(&$this, 'view_options'));
    }

    public function network_admin_menu(){
        add_submenu_page('settings.php', __('Google Analytics Multitrack network-wide configuration', $this->plugin_name), __('Google Analytics Multitrack', $this->plugin_name), 'manage_network', 'gam_network_options', array(&$this, 'view_network_options'));
    }

    public function view_options(){
        $vars = array();
        $vars['options'] = get_option('gam_options');
        $this->render_admin('gam-options', $vars );
    }

    public function render_network_options($updated){
        $vars = array();
        $vars['updated'] = $updated;
        $vars['options'] = get_site_option('gam_network_options');
        $this->render_admin('gam-network-options', $vars);
    }

    // Sanitize and validate input. Accepts an array, return a sanitized array.
    function validate_options($input){
        // TODO: check for invalid input, e.g. empty strings. Preferably without using js.
        // strip html from textboxes
        $input['gam_account'] =  wp_filter_nohtml_kses($input['gam_account']); // Sanitize textarea input (strip html tags, and escape characters)
        $input['gam_domain'] =  wp_filter_nohtml_kses($input['gam_domain']);
        return $input;
    }

    function validate_network_options($input){
        // strip html from textboxes
        $input['gam_network_account'] =  wp_filter_nohtml_kses(trim($input['gam_network_account'])); // Sanitize textarea input (strip html tags, and escape characters)
        $input['gam_network_domain'] =  wp_filter_nohtml_kses(trim($input['gam_network_domain']));
        return $input;
    }

    // Renders the sitewide configuration page
    function view_network_options(){
        if (isset($_POST['action']) && $_POST['action'] == 'update'){
            $settings = $_POST['gam_network_options'];
            if (!is_array($settings)){
                $updated = array('Problem getting options. Settings not saved.');
            } else {
                // Simple validation check for empty fields
                $updated = array();
                $errors = false;
                foreach ($settings as $setting){
                    if (empty($setting)){
                        $updated[] = 'A settings field was empty. Settings not saved.';
                        $errors = true;
                    }
                }
                
                if (!$errors){
                    $updated = $this->save_network_settings();
                }
            }
        }
        else {
            $updated = false;
        }
        $this->render_network_options($updated);
    }

    function save_network_settings(){
        check_admin_referer('google_analytics_multitrack_network_options');
        $settings = $_POST['gam_network_options'];
        // save to database
        update_site_option('gam_network_options', $settings);
        return true;
    }

    // Delete options table entries ONLY when plugin deactivated AND deleted
    public static function delete_plugin_options(){
    	if (is_multisite()){
    		global $wpdb;
    		$blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
    		if ($blogs) {
    			foreach($blogs as $blog) {
    				switch_to_blog($blog['blog_id']);
    				delete_option('gam_options');
    			}
    			restore_current_blog();
    		}
    	} else {
    		delete_option('gam_options');
    	}
    	delete_site_option('gam_network_options');
    }

    public function wp_print_scripts(){
        $networkoptions = get_site_option('gam_network_options');
        if ($networkoptions){
            $gam_network_account = $networkoptions['gam_network_account'];
            $gam_network_domain = $networkoptions['gam_network_domain'];
        }

        $options = get_option('gam_options');
        if ($options){
            $gam_account = $options['gam_account'];
            $gam_domain = $options['gam_domain'];
        }

        $isset_network_ga = false;
        $isset_current_blog_ga = false;

        if (isset($gam_network_account) && isset($gam_network_domain)){
            $isset_network_ga = true;
        }
        if (isset($gam_account) && isset($gam_domain)){
            $isset_current_blog_ga = true;
        }

        if ($isset_network_ga || $isset_current_blog_ga){
    ?>
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '<?php echo ($isset_network_ga? $gam_network_account : $gam_account) ?>']);
_gaq.push(['_setDomainName', '<?php echo ($isset_network_ga? $gam_network_domain : $gam_domain) ?>']);
_gaq.push(['_trackPageview']);

<?php 
    if ($isset_network_ga && $isset_current_blog_ga){
?>
_gaq.push(['b._setAccount', '<?php echo $gam_account ?>']);
_gaq.push(['b._setDomainName', '<?php echo $gam_domain ?>']);
_gaq.push(['b._trackPageview']);

<?php
    }
?>
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
<?php
    }
} // close wp_print_scripts
} // close GAM class

function google_analytics_multitrack_uninstall(){
	GoogleAnalyticsMultitrack::delete_plugin_options();
}

function google_analytics_multitrack_activate() {
	register_uninstall_hook( __FILE__, 'google_analytics_multitrack_uninstall' );
}
register_activation_hook( __FILE__, 'google_analytics_multitrack_activate' );

$GLOBALS['google-analytics-multitrack'] = new GoogleAnalyticsMultitrack;
