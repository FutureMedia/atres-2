<?php
/**
 * Author: Future Media 
 * URL: http://futuremedia.gr
 *
 * @package atres
 * @since 	atres 1.0
 *
 * The main template file
 * 
 */

get_header(); 
?>

	<div id="content">

		<div id="inner-content" class="grid cf">

			<main id="main" class="m-all t-2-3 d-5-7 cf" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

				<?php 
				if (have_posts()) : 

					while (have_posts()) : the_post();

						get_template_part( 'content-blog', get_post_format() );

					endwhile;

					fmedia_page_navi();

				endif;
				?>

			</main>

			<?php get_sidebar(); ?>

		</div>

	</div>

<?php get_footer(); ?>
