<?php
/**
* dkpdf-index.php
* This template is used to display the content in the PDF
*
* Do not edit this template directly,
* copy this template and paste in your theme inside a directory named dkpdf
*/
?>

<html>
    <head>
      	<?php wp_head(); ?>
      	<style type="text/css">
      		body {
      			background:#FFF;
      			font-size: 100%;
      		}
          /* fontawesome compatibility */
          .fa {
              font-family: fontawesome;
              display: inline-block;
              font: normal normal normal 14px/1 FontAwesome;
              font-size: inherit;
              text-rendering: auto;
              -webkit-font-smoothing: antialiased;
              -moz-osx-font-smoothing: grayscale;
              transform: translate(0, 0);
          }

		  .mw-page-title {
			font-size: 30px;
		  }

			<?php
				echo( file_get_contents( get_stylesheet_directory() . '/print.css' ) );

				// get pdf custom css option
				$css = get_option( 'dkpdf_pdf_custom_css', '' );
				echo $css;
			?>

		</style>

   	</head>

    <body class="dkpdf">
		<div style="border-bottom: 1px solid #222; padding-bottom: 12px;" >
			<div style="float:left; width: 25%">
				<img src="<?php echo esc_attr( home_url() ); ?>/wp-content/uploads/2023/01/MediaWell-logo.png" rel="logo" alt="<?php bloginfo( 'name' ); ?>" style="margin-top: -10px">
			</div>

			<div style="float:right; width: 70%;text-align: right; color: #159498;">
			</div>
		</div>
	    <?php

	    global $post;
	    $pdf  = get_query_var( 'pdf' );
	    $post_type = get_post_type( $pdf );


		$args = array(
			'p' => $pdf,
			'post_type' => $post_type,
			'post_status' => 'publish'
		);

		$the_query = new WP_Query( apply_filters( 'dkpdf_query_args', $args ) );

		if ( $the_query->have_posts() ) {

			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				global $post;

				$post_type = get_post_type_object( get_post_type() );

				?>

				<?php if ( 'ssrc_lr_version' === $post_type ) : ?>
					<?php get_template_part( 'parts/single-lr-version-main' ); ?>
				<?php elseif ( 'ssrc_expref_pt' === $post_type ) : ?>
					<?php get_template_part( 'parts/single-expert-reflection-main' ); ?>
				<?php else : ?>
					<div class="item-upper">
						<div class="pageHeading">
							<div class="mw-page-type-label"><?php echo esc_html( $post_type->labels->singular_name ); ?></div>
							<h1 class="mw-page-title"><?php the_title() ?></h1>
						</div>
						<div class="item-meta">

							<?php $custom_author = pressforward( 'controller.metas' )->retrieve_meta( get_the_ID(), 'item_author' ); ?>
							<?php if ( $custom_author ) : ?>
								<div class="item-authors"><?php printf( 'By %s', esc_html( $custom_author ) ); ?></div>
							<?php endif; ?>

							<div class="item-date">
								<?php the_date( 'F j, Y' ); ?>
							</div>
						</div>
					</div>

					<div class="item-main-content">
						<?php the_content(); ?>
					</div>
				<?php endif; ?>

			<?php }

		} else {

			echo 'no results';
		}

		wp_reset_postdata();


		?>

    </body>

</html>
