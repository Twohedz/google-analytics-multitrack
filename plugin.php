<?php

// ======================================================================================
// This library is free software; you can redistribute it and/or
// modify it under the terms of the GNU Lesser General Public
// License as published by the Free Software Foundation; either
// version 2.1 of the License, or (at your option) any later version.
//
// This library is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
// Lesser General Public License for more details.
// ======================================================================================
// @author     John Godley (http://urbangiraffe.com)
// @version    0.1.25
// @copyright  Copyright &copy; 2007 John Godley, All Rights Reserved
// ======================================================================================
// 0.1.6  - Corrected WP locale functions
// 0.1.7  - Add phpdoc comments
// 0.1.8  - Support for Admin SSL
// 0.1.9  - URL encoding, defer localization until init
// 0.1.10 - Better URL encoding
// 0.1.11 - Make work in WP 2.0, fix HTTPS issue on IIS
// 0.1.12 - Activation/deactivation actions that take into account the directory
// 0.1.13 - Add realpath function
// 0.1.14 - Add select/checked functions, fix locale loader
// 0.1.15 - Remove dependency on prototype
// 0.1.16 - Add support for homedir in realpath
// 0.1.17 - Added widget class
// 0.1.18 - Expand checked function
// 0.1.19 - Make url() cope with sites with no trailing slash
// 0.1.20 - Change init function to prevent overloading
// 0.1.21 - Make widget work for WP 2.1
// 0.1.22 - Make select work with option groups, RSS compatability fix
// 0.1.23 - Make widget count work better, fix widgets in K2
// 0.1.24 - Make realpath better
// 0.1.25 - Support for new WP2.6 config location
// ======================================================================================

class GoogleAnalyticsMultitrack_Plugin {

    /**
     * Plugin name
     * @var string
     **/
    var $plugin_name;

    /**
     * Plugin 'view' directory
     * @var string Directory
     **/
    var $plugin_base;

    /**
     * The main plugin file
     * @var string Filename
     **/
    var $plugin_file;

    /**
     * The plugin data
     * @var string Filename
     **/

    var $plugin_data;

    /**
     * Register plugin
     *
     * @param string $name Name of your plugin.  Is used to determine the plugin locale domain
     * @param string $base Directory containing the plugin's 'view' files.
     * @return void
     **/

    function register_plugin($name, $base){
        $this->requires_wordpress_version('3.3');
        $this->plugin_base = rtrim(dirname ($base), '/');
        $this->plugin_file = basename($base);
        $this->plugin_name = $name;
        $this->add_action ('init', 'load_locale');
    }

    /**
     * Register a WordPress action and map it back to the calling object
     *
     * @param string $action Name of the action
     * @param string $function Function name (optional)
     * @param int $priority WordPress priority (optional)
     * @param int $accepted_args Number of arguments the function accepts (optional)
     * @return void
     **/

    function add_action($action, $function = '', $priority = 10, $accepted_args = 1){
        add_action ($action, array (&$this, $function == '' ? $action : $function), $priority, $accepted_args);
    }

    /**
     * Register a WordPress filter and map it back to the calling object
     *
     * @param string $action Name of the action
     * @param string $function Function name (optional)
     * @param int $priority WordPress priority (optional)
     * @param int $accepted_args Number of arguments the function accepts (optional)
     * @return void
     **/

    function add_filter($filter, $function = '', $priority = 10, $accepted_args = 1){
        add_filter ($filter, array (&$this, $function == '' ? $filter : $function), $priority, $accepted_args);
    }

    function load_locale (){
        $locale = get_locale();
        if (empty($locale)){
            $locale = 'en_US';
        }
        $mofile = dirname (__FILE__)."/locale/$locale.mo";
        load_textdomain($this->plugin_name, $mofile);
    }

    function requires_wordpress_version($version){
        global $wp_version;
        if (version_compare($wp_version, $version, '<')) {
            if(is_plugin_active($this->plugin_name)) {
                deactivate_plugins($this->plugin_file);
                wp_die( "'" . $this->plugin_name . "' requires WordPress " . $version . " or higher, and has been deactivated! Please upgrade WordPress and try again.<br /><br />Back to <a href='".admin_url()."'>WordPress admin</a>." );
            }
        }
    }

    /**
     * Special activation function that takes into account the plugin directory
     *
     * @param string $pluginfile The plugin file location (i.e. __FILE__)
     * @param string $function Optional function name, or default to 'activate'
     * @return void
     **/
    
    function register_activation($pluginfile, $function = ''){
        add_action ('activate_'.basename (dirname ($pluginfile)).'/'.basename ($pluginfile), array (&$this, $function == '' ? 'activate' : $function));
    }
    
    
    /**
     * Special deactivation function that takes into account the plugin directory
     *
     * @param string $pluginfile The plugin file location (i.e. __FILE__)
     * @param string $function Optional function name, or default to 'deactivate'
     * @return void
     **/

    function register_deactivation($pluginfile, $function = ''){
        add_action ('deactivate_'.basename (dirname ($pluginfile)).'/'.basename ($pluginfile), array (&$this, $function == '' ? 'deactivate' : $function));
    }

    /**
     * Display a standard error message (using CSS ID 'message' and classes 'fade' and 'error)
     *
     * @param string $message Message to display
     * @return void
     **/

    function render_error($message){
        ?>
        <div class="fade error" id="message">
        <p><?php echo $message ?></p>
        </div>
        <?php
    }

    /**
     * Display a standard notice (using CSS ID 'message' and class 'updated').
     * Note that the notice can be made to automatically disappear, and can be removed
     * by clicking on it.
     *
     * @param string $message Message to display
     * @param int $timeout Number of seconds to automatically remove the message (optional)
     * @return void
     **/

    function render_message($message, $timeout = 0){
        ?>
        <div class="updated" id="message" onclick="this.parentNode.removeChild (this)">
        <p><?php echo $message ?></p>
        </div>
        <?php    
    }

    function render_admin ($ug_name, $ug_vars = array ()){
        foreach ($ug_vars AS $key => $val){
            $$key = $val;
        }

        if (file_exists ("{$this->plugin_base}/view/admin/$ug_name.php"))
            include ("{$this->plugin_base}/view/admin/$ug_name.php");
        else
            echo "<p>Rendering of admin template {$this->plugin_base}/view/admin/$ug_name.php failed</p>";
    }

} // close class