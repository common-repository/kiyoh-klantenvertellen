<div class="kk-rating-summary kk-stars-theme-<?php echo $stars_theme;?>" itemscope="itemscope" itemtype="https://schema.org/Organization">

    <meta itemprop="name" content="<?php echo $data['company_name']; ?>">

    <div itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
		<?php if (!empty($data['website'])): ?><meta itemprop="sameAs" content="<?php echo esc_html($data['website']); ?>"><?php endif; ?>
		<meta itemprop="worstRating" content="1">
        <?php if ($show_stars): ?>
            <?php \KiyOh_Klantenvertellen\KiyOh_Klantenvertellen_Plugin::render_star_rating($data['total_score']); ?>
        <?php endif; ?>

        <div class="kk-rating-summary-text">
            <a target="_blank" href="<?php echo $data['company_url']; ?>" itemprop="url">
				<?php 
				$current=sprintf( _n( '%s customer rates us with a %s', '%s customers rate us with a %s', $data['total_reviews'], 'kk_plugin' ), '<span class="kk-rating-count" itemprop="reviewCount">'.esc_html($data['total_reviews']).'</span>', '<span class="kk-rating-value" itemprop="ratingValue">'.esc_html($data['total_score']).'</span>/<span class="kk-rating-max" itemprop="bestRating">10</span>' );
				echo apply_filters('kk_plugin_shortcode_summary',$current,esc_html($data['total_reviews']),esc_html($data['total_score']),10);
				?>
			</a>
        </div>

    </div>
</div>