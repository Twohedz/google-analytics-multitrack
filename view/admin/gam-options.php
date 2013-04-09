<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>

<div class="wrap">

    <!-- Display Plugin Icon, Header, and Description -->
    <div class="icon32" id="icon-options-general"><br></div>
    <h2>Google Analytics Multitrack configuration</h2>
    <p>Add Google Analytic account details for current blog only</p>
    <!-- Beginning of the Plugin Options Form -->
    <form method="post" action="options.php">
        <?php settings_fields('gam_plugin_options'); ?>

        <!-- Table Structure Containing Form Controls -->
        <!-- Each Plugin Option Defined on a New Table Row -->
        <table class="form-table">

            <!-- Text Area Control -->
            <tr>
                <th scope="row">Google Analytics account code</th>
                <td>
                    <textarea name="gam_options[gam_account]" rows="1" cols="50" type='textarea'><?php echo $options['gam_account']; ?></textarea><br /><span style="color:#666666;margin-left:2px;">Format: UA-xxxxxxxx-x</span>
                </td>
            </tr>

            <tr>
                <th scope="row">Domain name for current blog</th>
                <td>
                    <textarea name="gam_options[gam_domain]" rows="1" cols="50" type='textarea'><?php echo isset($options['gam_domain'])? $options['gam_domain'] : preg_replace('#^https?://#', '', home_url()); ?></textarea><br /><span style="color:#666666;margin-left:2px;">Don't include 'http://' or trailing slash</span>
                </td>
            </tr>

        </table>
        <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>

</div>