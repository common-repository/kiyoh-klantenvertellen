<?php
use KiyOh_Klantenvertellen\KiyOh_Klantenvertellen_Plugin;
?>

<form action="options.php" method="post">

    <?php settings_fields($this->settings_ids['emails']); ?>

    <div id="kk-plugin-emails-settings" class="kk-plugin-settings-box">

        <div class="kk-plugin-emails-settings-form">

            <h2><?php _e('E-mails', 'kk_plugin'); ?></h2>
            <table class="form-table">
                <?php do_settings_fields( $this->settings_ids['emails'], 'kk_plugin_emails_section' ); ?>
            </table>

        </div>

        <div class="kk-clearfix"></div>

    </div>

    <?php submit_button(); ?>

</form>