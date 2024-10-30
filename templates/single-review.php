<?php
/**  * The Template for displaying a single review including in slider.php, list.php and default.php
 *
 * This template can be overridden by copying it to yourtheme/kiyoh-klantenvertellen/single-review.php.
 *
 * HOWEVER, on occasion KiyOh / Klantenvertellen will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @version 2.0.1
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="kk-single-review" itemprop="review" itemscope="itemscope" itemtype="https://schema.org/Review" lang="<?php echo $review_data['lang'];?>">
    <?php if (isset($options['show_review_rating']) && $options['show_review_rating'] == 'yes'): ?>

        <?php if (!empty($review_data['total_score'])): ?>

            <?php if ($options['layout'] != 'list' && ($options['layout'] == 'slider' || (isset($options['show_stars']) && $options['show_stars'] == 'yes'))): ?>
                <div class="kk-single-review-rating-stars" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
                    <?php self::render_star_rating($review_data['total_score']); ?>
					<meta itemprop="worstRating" content="1">
                    <meta itemprop="ratingValue" content="<?php echo esc_html($review_data['total_score']); ?>">
                    <meta itemprop="bestRating" content="10">
                </div>
            <?php else: ?>
                <div class="kk-single-review-rating" style="<?php echo (isset($options['color'])) ? 'background-color:'.$options['color'].';' : '';?><?php echo (isset($options['txtcolor'])) ? 'color:'.$options['txtcolor'].';':'';?>" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
					<meta itemprop="worstRating" content="1">
                    <span class="kk-single-review-rating-value" itemprop="ratingValue"><?php echo esc_html($review_data['total_score']); ?></span>
                    <meta itemprop="bestRating" content="10">
                </div>
            <?php endif; ?>

        <?php endif; ?>

    <?php endif; ?>

    <div class="kk-single-review-text-meta">
		<?php if ($options['layout'] == 'list' && isset($options['show_stars']) && $options['show_stars'] == 'yes'): ?>
                <div class="kk-single-review-rating-stars">
                    <?php self::render_star_rating($review_data['total_score']); ?>
                </div>
        <?php endif; ?>

        <div class="kk-single-review-text" itemprop="reviewBody">
			<?php if(isset($review_data['one_liner'])) echo apply_filters('kk_plugin_single_review_title', '<h3>'.wp_kses($review_data['one_liner'], array('br' => array())).'</h3>', $review_data, $options); ?>
            <?php 
			
			if(isset($review_data['positive'])) echo apply_filters('kk_plugin_single_review_text', wp_kses($review_data['positive'], array('br' => array())), $review_data, $options); ?>
            <meta itemprop="inLanguage" content="<?php echo get_bloginfo('language'); ?>">
        </div>
		
		<?php if(!empty($review_data['comment'])) { ?>
		<div class="kk-single-review-comment">
			<?php echo apply_filters('kk_plugin_review_comment_heading','<h4>'.__('Response from the company','kk_plugin').'</h4>');?>
			<p><?php echo $review_data['comment'];?></p>
		</div>
		<?php } ?>
		
		
		<?php if(isset($review_data['recommendation']) && $options['layout']=='list'): ?>
		<div class="kk-single-review-recommendation">
			<?php
			$recommendation_text=sprintf('<span>'.__('Would you recommend us? %s','kk_plugin').'</span>','<span class="kk-rating">'.$review_data['recommendation'].'</span>');
			echo apply_filters('kk_plugin_single_review_recommendation', $recommendation_text,$review_data['recommendation'], $review_data, $options);
			?>
		</div>
		<?php endif;
		?>
		<?php if(isset($review_data['category'])) { ?>
		<div class="kk-single-review-branche-data kk-single-extra">
			<ul class="kk-single-extra-list">
			<?php foreach($review_data['category'] as $custom_data) {
				if(isset($options['reviewField_'.$key]) && $options['reviewField_'.$key]=='yes') {
					echo self::output_custom_data($custom_data);
				}
				
				if(isset($options['reviewField_'.$key.'_rating']) && $options['reviewField_'.$key.'_rating']=='yes') {
					$custom_data['questionType']='STARS';
					echo self::output_custom_data($custom_data);
				}
			} ?>
			</ul>
		</div>
		<?php } ?>
		<?php if(isset($review_data['custom'])) { 
		?>
		<div class="kk-single-review-custom-data kk-single-extra">
			<ul class="kk-single-extra-list">
			<?php 
			foreach($review_data['custom'] as $key=>$custom_data) {
				
				if(isset($options['reviewField_'.$key]) && $options['reviewField_'.$key]=='yes') {
					echo self::output_custom_data($custom_data);
				}
				
				if(isset($options['reviewField_'.$key.'_rating']) && $options['reviewField_'.$key.'_rating']=='yes') {
					$custom_data['questionType']='STARS';
					echo self::output_custom_data($custom_data);
				}
				
			} ?>
			</ul>
		</div>
		<?php } ?>
		
		<?php /* To be added in future release 
		if(isset($options['custom_review_data']) && $options['custom_review_data']!="" && isset($review_data['custom'])) { ?>
			<div class="kk-custom-review-data">
				<?php echo apply_filters('kk_plugin_single_review_custom_data',self::format_custom_review_data($options['custom_review_data'],$review_data['custom']),$options['custom_review_data'],$review_data['custom']);?>
			</div>
			<?php 
		} */
		$divider=false;
		?>
        <div class="kk-single-review-meta">
			<?php if(esc_html(apply_filters('kk_plugin_single_review_name', $review_data['name'], $review_data))) { $divider=true;?>
            <span class="kk-single-review-name" itemprop="author"><?php echo esc_html(apply_filters('kk_plugin_single_review_name', $review_data['name'], $review_data)); ?></span>
			<?php } ?>
			
			<?php if(isset($review_data['company_name']) && $review_data['company_name']!="") { ?>
			<?php self::show_single_review_divider($divider,$review_data);?>
			<span class="kk-single-review-company"><?php echo esc_html(apply_filters('kk_plugin_single_review_company_name', $review_data['company_name'], $review_data)); ?></span>
			<?php 
			$divider=true;
			} ?>
			
			<?php if(isset($review_data['place']) && $review_data['place']!="") { ?>
			<?php self::show_single_review_divider($divider,$review_data);?>
            <span class="kk-single-review-place"><?php echo esc_html(apply_filters('kk_plugin_single_review_place', $review_data['place'], $review_data)); ?></span>
			<?php 
			$divider=true;
			}?>
			
			<?php if (isset($options['show_date']) && $options['show_date'] == 'yes' && strtotime($review_data['date'])>0): ?>
				<?php self::show_single_review_divider($divider,$review_data);?>
				<span class="kk-single-review-date" itemprop="dateCreated"><?php echo date_i18n( get_option( 'date_format' ), strtotime( esc_html($review_data['date']) ) ); ?></span>
			<?php else: ?>
				<meta itemprop="dateCreated" content="<?php echo esc_html($review_data['date']); ?>">
			<?php endif; ?>
		
        </div>
		<?php
	if (isset($options['show_id']) && $options['show_id'] == '1'): ?>
        <div class="kk-single-review-id">
            - <?php _e('ID', 'kk_plugin'); ?>: <?php echo $review_data['id']; ?> -
        </div>
    <?php endif; ?>
    </div>
</div>