<?php
/**
 * The Template for displaying star rating
 *
 * This template can be overridden by copying it to yourtheme/kiyoh-klantenvertellen/star-rating.php.
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
<div class="kk-rating">

    <div class="kk-rating-stars">

        <?php echo $star_elements; ?>

    </div>
</div>