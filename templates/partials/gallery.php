<section>
<div class="container d-flex space-between flex-wrap max-w-5xl mx-auto px-6">
<?php
    $images = acf_photo_gallery('image_gallery', $post->ID);
    if( count($images) ):
        foreach($images as $image):
            $id = $image['id'];
            $title = $image['title'];
            $caption= $image['caption'];
            $full_image_url= $image['full_image_url'];
            $full_image_url_thumbnail = acf_photo_gallery_resize_image($full_image_url, 300, 300);
            $thumbnail_image_url= $image['thumbnail_image_url'];
            $url= $image['url'];
            $target= $image['target'];
            $alt = get_field('photo_gallery_alt', $id);
            $class = get_field('photo_gallery_class', $id);
?>
<div class="col-xs-6 col-md-3">
    <div class="thumbnail py-3">
		  <a data-fancybox="gallery" src="<?php echo $full_image_url; ?>" alt="<?php echo $title; ?>" title="<?php echo $title; ?>" href="<?php echo $full_image_url; ?>">
    <img class="rounded" src="<?php echo $full_image_url_thumbnail; ?>"  alt="<?php echo $title; ?>" title="<?php echo $title; ?>" />
  </a>
    </div>
</div>
<?php endforeach; endif; ?>
        </div>
        </section>