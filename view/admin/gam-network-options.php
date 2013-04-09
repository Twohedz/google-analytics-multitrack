<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>

<div class="wrap">
<?php
if ($updated === true) {
    $this->render_message('Options saved.');
}
else if (is_array($updated)) {
    $this->render_error(implode('<br />', $updated));
}
?>
        <h2>Google Analytics Multitrack network-wide configuration</h2>
            <form method="post" action="">
            <?php wp_nonce_field('google_analytics_multitrack_network_options') ?>
                <h3>Add settings for network-wide Google Analytics account</h3>
                <p>For each individual site, add GA account details from that site's dashboard.</p>
                    <table class="form-table">
                        <!-- Text Area Control -->
                        <tr>
                            <th scope="row">Google Analytics account code</th>
                            <td>
                                <textarea name="gam_network_options[gam_network_account]" rows="1" cols="50" type='textarea'><?php echo $options['gam_network_account']; ?></textarea><br /><span style="color:#666666;margin-left:2px;">Format: UA-xxxxxxxx-x</span>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">Domain name for network (blog #1)</th>
                            <td>
                                <textarea name="gam_network_options[gam_network_domain]" rows="1" cols="50" type='textarea'><?php echo isset($options['gam_network_domain'])? $options['gam_network_domain'] : preg_replace('#^https?://#', '', home_url()); ?></textarea><br /><span style="color:#666666;margin-left:2px;">Don't include 'http://' or trailing slash</span>
                            </td>
                        </tr>
                    </table>
                <input type='hidden' name='action' value='update' />
                <p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
            </form>
    </div>