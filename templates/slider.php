<?php
/**
 * The Template for displaying reviews in slider view
 *
 * This template can be overridden by copying it to yourtheme/kiyoh-klantenvertellen/slider.php.
 *
 * HOWEVER, on occasion KiyOh / Klantenvertellen will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @version 1.2.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div id="slider-<?php echo $options['random_id']; ?>" class="kk-reviews kk-layout-slider kk-stars-theme-<?php echo ((isset($options['stars_theme']) && !empty($options['stars_theme']))?$options['stars_theme']:'none'); ?> kk-stars-size-<?php echo ((isset($options['stars_size']) && !empty($options['stars_size']))?$options['stars_size']:'medium'); ?> <?php echo esc_attr($options['class']);?>" itemscope="itemscope" itemtype="https://schema.org/Organization">

    <meta itemprop="name" content="<?php echo $data['company_name']; ?>">
	
	<?php if (isset($options['show_average_rating']) && $options['show_average_rating'] == 'yes'): ?>
		<span class="kk-average-score">
        <?php echo esc_html($data['total_score']);?>
		</span>
	<?php endif;?>

    <?php if (isset($options['do_show_reviews']) && $options['do_show_reviews'] == 'yes' && is_array($data['reviews']) && count($data['reviews']) > 0): ?>

        <div class="kk-reviews">
        <?php $review_count = 1; foreach ($data['reviews'] AS $review_data):

            // Display options
            if (isset($options['id']) && !empty($options['id']) && $options['id'] != $review_data['id'])
                continue;

            if (isset($options['review_display_options']['hide_without_rating']) && $options['review_display_options']['hide_without_rating'] == 1 && empty($review_data['total_score']))
                continue;

            if (isset($options['review_display_options']['hide_without_rating_text']) && $options['review_display_options']['hide_without_rating_text'] == 1 && empty($review_data['positive']))
                continue;

            if (isset($options['review_display_options']['hide_without_author']) && $options['review_display_options']['hide_without_author'] == 1 && empty($review_data['name']))
                continue;

            if (isset($options['show_reviews_rating_higher_than']) && !empty($options['show_reviews_rating_higher_than']) && !empty($review_data['total_score']) && $review_data['total_score'] < floatval($options['show_reviews_rating_higher_than']))
                continue;

            if (isset($options['start_with_review']) && !empty($options['start_with_review']) && $options['start_with_review'] > $review_count) {
               $review_count++;
               continue;
            }

            // Review amount
            if (isset($options['show_reviews_amount']) && is_numeric($options['show_reviews_amount']) && $options['show_reviews_amount'] > 0 && $review_count > $options['show_reviews_amount'])
                break;
            ?>

            <?php self::render_single_review($review_data, $options); ?>

        <?php $review_count++; endforeach; ?>
        </div>

    <?php endif; ?>


    <?php if (isset($options['show_logo']) && $options['show_logo'] == 'yes'): ?>

        <div class="kk-logo">
            <a href="<?php echo esc_html($data['company_url']); ?>" target="_blank"><img width="32" height="32" src="<?php echo self::get_logo($provider, $options); ?>" alt="<?php _e('View our results', 'kk_plugin'); ?>" /></a>
        </div>

    <?php endif; ?>
	
	

    <?php if (isset($options['show_average_stars']) && $options['show_average_stars'] == 'yes' && !empty($data['total_score'])): ?>

        <?php self::render_star_rating($data['total_score']); ?>

    <?php endif; ?>

    <?php if (isset($options['show_summary']) && $options['show_summary'] == 'yes'): ?>

        <div class="kk-summary" itemprop="aggregateRating" itemscope="itemscope" itemtype="https://schema.org/AggregateRating">
			<meta itemprop="worstRating" content="1">
            <div class="kk-summary-text">

                <?php printf( _n( '%s customer rates us with a %s', '%s customers rate us with a %s', $data['total_reviews'], 'kk_plugin' ), '<span class="kk-rating-count" itemprop="reviewCount">'.esc_html($data['total_reviews']).'</span>', '<span class="kk-rating-value" itemprop="ratingValue">'.esc_html($data['total_score']).'</span>/<span class="kk-rating-max" itemprop="bestRating">10</span>' ); ?>

            </div>

            <?php if (!empty($data['company_url'])): ?>
                <div class="kk-summary-company-url">
                    <a itemprop="url" href="<?php echo esc_html($data['company_url']); ?>" target="_blank"><?php _e('View on', 'kk_plugin'); ?> <?php echo self::get_name($provider); ?></a>
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>

        <div itemprop="aggregateRating" itemscope="itemscope" itemtype="https://schema.org/AggregateRating">
            <meta itemprop="reviewCount" content="<?php echo esc_html($data['total_reviews']); ?>">
			<meta itemprop="worstRating" content="1">
            <meta itemprop="ratingValue" content="<?php echo esc_html($data['total_score']); ?>">
            <meta itemprop="bestRating" content="10">
			<?php if (!empty($data['company_url'])): ?><meta itemprop="url" content="<?php echo esc_html($data['company_url']); ?>"><?php endif; ?>
        </div>

    <?php endif; ?>

</div>