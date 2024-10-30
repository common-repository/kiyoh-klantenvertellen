<h2><?php _e('Need support?','kk_plugin');?></h2>
<p><?php printf(__('Need support with this plugin? Use the %s and we will reply within 7 days.','kk_plugin'),'<a href="https://wordpress.org/plugins/kiyoh-klantenvertellen/" target="_blank">'.__('WordPress.org forums','kk_plugin').'</a>');?></p>
<h3><?php _e('Get priority support!','kk_plugin');?></h3>
<p><?php printf(__('Or checkout our %s and get support by priority support by mail within 2 workdays.','kk_plugin'),'<a href="https://wpfortune.com/shop/plugins/kiyoh-klantenvertellen/" target="_blank">'.__('Premium plugin','kk_plugin').'</a>');?></p>
<h3><?php _e('No support by phone','kk_plugin');?></h3>
<p><?php printf(__("You may always call us for new projects, but unfortunately we %s","kk_plugin"),'<strong>'.__('cannot provide support by phone.','kk_plugin').'</strong>');?> <?php _e("Please don't call us for plugin related questions.","kk_plugin");?></p>
<hr>
<h2><?php _e('How to use', 'kk_plugin'); ?></h2>
<p><?php _e('You can use this plugin in 2 ways:', 'kk_plugin'); ?>:</p>
<ol>
	<li><?php _e('By a shortcode, so you can show your reviews inside a text.', 'kk_plugin'); ?></li>
	<li><?php _e('By using a PHP function, so you can show your reviews inside your theme.','kk_plugin'); ?></li>
</ol>
<hr>
<h3>1. <?php _e('Shortcode', 'kk_plugin'); ?></h3>
<p><?php printf(__('With a shortcode you can show the customer reviews in a normal text, on a page or a post for example. The shortcode you can use is %s. This shortcode uses the default settings by default. The default settings are adjustable per shortcode. You can use the following options for this.', 'kk_plugin'), '<code>[kiyoh-klantenvertellen]</code>'); ?></p>
<h4><?php _e('Shortcode options', 'kk_plugin'); ?>:</h4>

<ul class="kk-plugin-shortcode-options">
    <li><span class="kk-plugin-option-name"><code>layout</code></span><span class="kk-plugin-option-value">default | slider | list</span></li>
    <li><span class="kk-plugin-option-name"><code>auto_slide</code></span><span class="kk-plugin-option-value">yes | no</span></li>
    <li><span class="kk-plugin-option-name"><code>stars_theme</code></span><span class="kk-plugin-option-value">none | default | yellow | black-grey | black | white</span></li>
    <li><span class="kk-plugin-option-name"><code>stars_size</code></span><span class="kk-plugin-option-value">small | medium | large</span></li>
    <li><span class="kk-plugin-option-name"><code>show_logo</code></span><span class="kk-plugin-option-value">yes | no</span></li>
    <li><span class="kk-plugin-option-name"><code>logo_type</code></span><span class="kk-plugin-option-value">image | full</span></li>
    <li><span class="kk-plugin-option-name"><code>show_summary</code></span><span class="kk-plugin-option-value">yes | no</span></li>
    <li><span class="kk-plugin-option-name"><code>do_show_reviews</code></span><span class="kk-plugin-option-value">yes | no</span></li>
    <li><span class="kk-plugin-option-name"><code>show_reviews_amount</code></span><span class="kk-plugin-option-value">1 - 100</span></li>
    <li><span class="kk-plugin-option-name"><code>start_with_review</code></span><span class="kk-plugin-option-value">1 - 100</span></li>
    <li><span class="kk-plugin-option-name"><code>show_review_rating</code></span><span class="kk-plugin-option-value">yes | no</span></li>
    <li><span class="kk-plugin-option-name"><code>limit_review_length</code></span><span class="kk-plugin-option-value">0 - 500</span></li>
    <li><span class="kk-plugin-option-name"><code>hide_without_rating</code></span><span class="kk-plugin-option-value">yes | no</span></li>
    <li><span class="kk-plugin-option-name"><code>hide_without_rating_text</code></span><span class="kk-plugin-option-value">yes | no</span></li>
    <li><span class="kk-plugin-option-name"><code>hide_without_author</code></span><span class="kk-plugin-option-value">yes | no</span></li>
	<li><span class="kk-plugin-option-name"><code>show_average_rating</code></span><span class="kk-plugin-option-value">yes | no</span></li>
	<li><span class="kk-plugin-option-name"><code>show_stars</code></span><span class="kk-plugin-option-value">yes | no</span></li>
	<li><span class="kk-plugin-option-name"><code>color</code></span><span class="kk-plugin-option-value"><?php _e('Define color of single review rating. HTML color, example: #000000','kk_plugin');?></span></li>
	<li><span class="kk-plugin-option-name"><code>id</code></span><span class="kk-plugin-option-value"><?php _e('id of review to show a single review', 'kk_plugin'); ?></span></li>
</ul>
<h4><?php _e('Shortcode example', 'kk_plugin'); ?>:</h4>
<p><code>[kiyoh-klantenvertellen layout="default" auto_slide="no" stars_theme="yellow" stars_size="small" show_logo="yes" logo_type="full" show_summary="yes" hide_without_rating="yes" hide_without_rating_text="yes" hide_without_author="no" do_show_reviews="yes" show_reviews_amount="3" start_with_review="2" show_review_rating="yes" limit_review_length="20"]</code></p>
<h4><?php _e('Show a single review', 'kk_plugin'); ?>:</h4>
<p><?php _e('To show a single review, you can use the id option in the shortcode above or alternatively you can use the shortcode below. The shortcode below shows a more simple version of a single review. You need to have the id of a single review to show this.', 'kk_plugin'); ?>
<p><code>[kiyoh-klantenvertellen-single-review id="<?php _e('ID here', 'kk_plugin'); ?>" show_review_rating="yes|no" show_stars="yes|no"]</code></p>
<hr>
<h4><?php _e('Show global rating summary','kk_plugin');?>:</h4>
<p><code>[kiyoh-klantenvertellen-summary show_stars=yes|no]</code></p>
<hr>
<h4><?php _e('Show rating statistics','kk_plugin');?>:</h4>
<p><?php _e('Use shortcode below to show rating statistics on a page.','kk_plugin');?> <?php _e('Tip: Create a new page in WordPress and add shortcodes to create a review page with all reviews.','kk_plugin');?>
<p><code>[kiyoh-klantenvertellen-stats style="light|dark" bgcolor="#ffffff" txtcolor="#000000"]</code></p>
<hr>
<h3>2. <?php _e('Widget', 'kk_plugin'); ?> <?php _e('(Deprecated)','pep');?></h3>
<p><?php printf(__('Show customer reviews in a widget. Go to Appearance &#8594; Widgets or use the %s.', 'kk_plugin'), '<a href="'.admin_url('customize.php').'">'.__('WordPress Customizer', 'kk_plugin').'</a>'); ?></p>
<p><?php _e('The widget uses the default settings by default, but you can override this per widget.', 'kk_plugin'); ?></p>
<p><em><?php _e('We do not advise to use the widget anymore. Please use a shortcode inside a default text widget.','kk_plugin');?></em></p>
<hr>
<h3>3. <?php _e('Where to find the review id?', 'kk_plugin'); ?></h3>
<p><?php _e('You can find the id of a single review in the example on the \'Default settings\' tabs in the plugin settings or you can use the link below to lookup the id in the actual data', 'kk_plugin'); ?>:
    <?php
    $provider = $this->main_plugin->plugin_settings['general']['kk_provider'];
    $link = false;
    switch ($provider) {
        case 'klantenvertellen':
            $slug = $this->main_plugin->plugin_settings['general']['klantenvertellen_slug'];
            if (!empty($slug)) {
                $link = $this->main_plugin->provider_urls['klantenvertellen']['xml'].'/'.$slug;
            }
            break;
        case 'klantenvertellen_v2':
            $tenant_id = $this->main_plugin->plugin_settings['general']['klantenvertellen_v2_tenant_id'];
            $location_id = $this->main_plugin->plugin_settings['general']['klantenvertellen_v2_location_id'];
            if (!empty($tenant_id) && !empty($location_id)) {
                $link = $this->main_plugin->provider_urls['klantenvertellen_v2']['xml'].'?locationId='.$location_id.'&tenantId='.$tenant_id;
            }
            break;
        case 'klantenvertellen_mobiliteit':
            $slug = $this->main_plugin->plugin_settings['general']['klantenvertellen_mobiliteit_slug'];
            if (!empty($slug)) {
                $link = $this->main_plugin->provider_urls['klantenvertellen_mobiliteit']['xml'].'/'.$slug;
            }
            break;
        case 'kiyoh':
            $connectorcode = $this->main_plugin->plugin_settings['general']['kiyoh_connectorcode'];
            $company_id = $this->main_plugin->plugin_settings['general']['kiyoh_company_id'];
            if (!empty($connectorcode) && !empty($company_id)) {
                $link = $this->main_plugin->provider_urls['kiyoh']['xml'].'?connectorcode=' . $connectorcode . '&company_id=' . $company_id;
            }
            break;
        case 'kiyoh_com':
            $connectorcode = $this->main_plugin->plugin_settings['general']['kiyoh_com_connectorcode'];
            $company_id = $this->main_plugin->plugin_settings['general']['kiyoh_com_company_id'];
            if (!empty($connectorcode) && !empty($company_id)) {
                $link = $this->main_plugin->provider_urls['kiyoh_com']['xml'].'?connectorcode=' . $connectorcode . '&company_id=' . $company_id;
            }
            break;

    }

    if (!empty($link)) {
        echo '<a href="'.$link.'" target="_blank">'.$link.'</a>';
    } else {
        _e('Link not available, please fill in your account settings on the general settings tab.', 'kk_plugin');
    }


    ?>
<hr>
<h3>4. <?php _e('PHP code for developers', 'kk_plugin'); ?></h3>
<p><?php _e('With the following PHP code you can show your customer reviews inside your theme. The shortcode accepts all options as described above.', 'kk_plugin'); ?></p>
<code>&#x3C;?php do_shortcode(&#x27;[kiyoh-klantenvertellen]&#x27;); ?&#x3E;</code>
<h4><?php _e('Disabling styling (CSS)', 'kk_plugin'); ?></h4>
<p><?php _e('To disable the default styling (CSS) of the plugin, you can add the following code to the functions.php file of your theme or inside your own plugin.', 'kk_plugin'); ?></p>
<p><code>add_filter('kk_plugin_enable_css', '__return_false');</code></p>
<h4><?php _e('Adjusting auto slide delay', 'kk_plugin'); ?></h4>
<p><?php _e('To adjust the pause of each slide when auto slide is enabled for the slider layout, you can use the filter below. Please note that this is the time in milliseconds. The default pause is set to 6000 ms.', 'kk_plugin'); ?></p>
<p><code>add_filter('kk_plugin_slider_auto_delay', function() { return 6000; }); </code></p>
<hr>
<h3>5. <?php _e('Overriding templates via a theme','kk_plugin');?></h3>
<p><?php _e('Template files contain the markup and template structure for frontend of your website.','kk_plugin');?><br>
<?php _e('Template files can be found within the <code>/kiyoh-klantenvertellen/templates/</code> directory.','kk_plugin');?></p>
<h4><?php _e('How to edit files','kk_plugin');?></h4>
<p><?php _e('Edit files in an upgrade-safe way using overrides. Copy the template into a directory within your theme named <code>/kiyoh-klantenvertellen</code> keeping the same file structure but removing the <code>/templates/</code> subdirectory.','kk_plugin');?></p>
<p><?php printf(__('Example: To override the slider layout, copy: <code>wp-content/plugins/kiyoh-klantenvertellen/templates/slider.php</code> to %s','kk_plugin'),'<code>'.get_template_directory().'/kiyoh-klantenvertellen/slider.php</code>');?></p>
<p><?php _e('The copied file will now override the default template file.','kk_plugin');?>
<p><strong><?php _e('Warning:','kk_plugin');?></strong> <?php _e('Do not edit these files within the core plugin itself as they are overwritten during the upgrade process and any customizations will be lost.','kk_plugin');?></p>