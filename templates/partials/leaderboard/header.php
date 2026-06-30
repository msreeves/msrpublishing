	   <div id="Carousel" class="carousel slide advert" data-bs-ride="carousel" data-bs-interval="3000">
                  <div class="carousel-inner">
                     <?php $slider = get_posts([
                          'post_type' => 'advert',
                          'posts_per_page' => -1,
                           'tax_query' => array(
            array(
              'taxonomy' => 'location',
              'field' => 'slug',
              'terms' => 'header'
            )
          )
                     ]); ?>
                            <?php $count = 0; ?>
                            <?php foreach ($slider as $slide): ?>
                                <?php setup_postdata($slide->ID); ?>
                                <div class="carousel-item <?php echo $count == 0 ? "active" : ""; ?>">
                                   <div class="row g-0">
                                
				   <div class="col-md-4">
               		<div class="panel">
				<div class="my-auto">
				<h2 class="text-center">Advertisement</h2>
		  </div>
		  </div>
                </div>
                      <div class="col-md-8"> 
                <div class="panel">
				<div class="my-auto mx-auto">
                      <?php
            $link = get_field('link', $slide->ID);
            $link_url = $link['url'];
            $link_title = $link['title'];
            $link_target = $link['target'] ? $link['target'] : '_self';
    ?>
   <a href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"> 
                                               <?php $image = wp_get_attachment_image_src(
                                                   get_post_thumbnail_id(
                                                       $slide->ID
                                                   ),
                                                   "single-post-thumbnail"
                                               ); ?>
                                                <img src="<?php echo esc_url( $image[0] ?? '' ); ?>" alt="<?php the_post_thumbnail_caption(); ?>"> 
                                            </a>
                                            </div>
                            </div>
                            </div>
                     </div>
                        </div>
                               <?php $count++; ?>
                            <?php endforeach; ?>
                                </div>
                              
                        </div>
    <?php // Reset the global post object so that the rest of the page works correctly.
    wp_reset_postdata(); ?>
