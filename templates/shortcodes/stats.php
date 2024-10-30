<div itemprop="aggregateRating" itemscope="" itemtype="https://schema.org/AggregateRating" class="kk-results-averages kk-results-<?php echo $style;?> kk-results-box-<?php echo $box;?>" style="<?php if($bgcolor!="") echo 'background-color:'.$bgcolor.';';?><?php if($txtcolor!="") echo 'color:'.$txtcolor.';';?>" >
	<div itemprop="itemReviewed" itemscope="" itemtype="https://schema.org/Organization">
		<meta itemprop="name" content="<?php echo esc_html($data['company_name']);?>">
		<?php if (!empty($data['company_url'])): ?>
			<meta itemprop="sameAs" content="<?php echo esc_url($data['company_url']); ?>">
		<?php endif; ?>
		<?php if (!empty($data['website'])): ?>
			<meta itemprop="url" content="<?php echo esc_url($data['website']);?>">
		<?php endif;?>
		
		<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
			<meta itemprop="streetAddress" content="<?php echo esc_html($data['street']);?> <?php echo esc_html($data['houseNumber']);?><?php echo esc_html($data['houseNumberExtension']);?>">
			<meta itemprop="postalCode" content="<?php echo esc_html($data['postCode']);?>">
			<span itemprop="addressLocality" content="<?php echo esc_html($data['city']);?>">
			<span itemprop="addressCountry" content="<?php echo esc_html($data['country']);?>">
		</div>
		
	</div>
	<div class="kk-results-average">
		<div class="kk-results-score">
			<span class="kk-results-total-score"><?php echo str_replace('.',',',$data['total_score']);?></span>
			<span class="kk-results-review-count"><span class="kk-results-review-count-number"><?php echo $data['total_reviews'];?></span> <?php _e('reviews','kk_plugin');?></span>
			<?php if(isset($data['last_review_date'])): ?>
				<span class="kk-results-last-review-date"><?php _e('Last review:','kk_plugin');?> <span><?php printf( __( '%s ago', 'kk_plugin' ), human_time_diff( strtotime($data['last_review_date']), current_time( 'timestamp' ) ) ); ?></span> </span>
			<?php endif; ?>
		</div>
		
		<?php if(!empty($data['averages'])) { ?>
		
			<div class="kk-results-averages-list"><ul>
			<?php 
				foreach($data['averages'] as $name => $result) { ?>
					<li class="kk-results-average-single"><span class="kk-results-label"><?php echo $name;?></span> <span class="kk-results-rating"><?php echo $result;?></span>
			<?php } ?>
			</ul></div>
		
		<?php } ?>
		
		<?php if(isset($data['recommendation_perc'])): ?>
        <div class="kk-results-recommendation">
            
			<?php printf( __( '%d%% recommends %s', 'kk_plugin' ), $data['recommendation_perc'], $data['company_name'] ); ?>
			
		</div>
		<?php endif;?>
		
		<?php if (!empty($data['company_url'])): ?>
			<meta itemprop="url" content="<?php echo esc_url($data['company_url']); ?>">
		<?php endif; ?>
		<meta itemprop="ratingValue" content="<?php echo $data['total_score'];?>">
		<meta itemprop="bestRating" content="10">
		<meta itemprop="reviewCount" content="<?php echo esc_html($data['total_reviews']); ?>">
		<meta itemprop="worstRating" content="1">
	</div>
</div>