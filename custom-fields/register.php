<?php
function bc_testimonial_create_metabox() {
    add_meta_box(
        'bc_testimonial_metabox',
        'Testimonial',
        'bc_testimonial_metabox',
        'bc_testimonials',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'bc_testimonial_create_metabox' );

function bc_testimonial_metabox() {
global $post; // Get the current post data
$name = get_post_meta( $post->ID, 'testimonial_name', true );
$title = get_post_meta( $post->ID, 'testimonial_title', true );
$message = get_post_meta( $post->ID, 'testimonial_message', true );
$image = get_post_meta( $post->ID, 'testimonial_custom_image', true );
?>

<div class="container">
  <div class="form-group row">
    <label class="col-sm-2 col-form-label">Name</label>
    <div class="col-sm-4">
      <input type="text" class="form-control" name="testimonial_name" id="testimonial_name" value="<?= $name?>" required>
    </div>
    <label class="col-sm-2 col-form-label">Title/Role</label>
    <div class="col-sm-4">
      <input type="text" class="form-control" name="testimonial_title" id="testimonial_title" value="<?= $title?>" required>
    </div>
  </div>

  <div class="form-group row">
    <label class="col-sm-2 col-form-label">Testimonial</label>
    <div class="col-sm-10">
      <textarea rows="6" name="testimonial_message" class="form-control" required><?= $message?></textarea>
    </div>
  </div>

  <div class="form-group row">
    <label class="col-sm-2 col-form-label">Photo<br/>(optional)<br/>150*150.jpg</label>
    <div class="col-sm-10">
        <input type="text" name="testimonial_custom_image" id="" class="meta-image col-sm-2" value="<?= $image;?>" required accept='image/*' style="margin-top: 40px;">
        <input type="button" class="button bc-testimonial-image-upload col-sm-3" value="Upload" style="margin-top: 40px;">

        <div class="image-preview col-sm-3" style="float: right;margin-right: 30%;">
            <?php if(isset($image) && !empty($image)){?>
            <img src="<?php echo $image;?>" class="rounded-circle" style="width:150px;">
            <?php }else{?>
            <img src="http://placehold.it/150x150" class="rounded-circle" style="width: 90px; height: 90px;"/>
            <?php }?>
        </div>
    </div>
  </div>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label"><b>Shortcode :</b></label>
    <div class="col-sm-10">
      [bc-testimonial id="<?= $post->ID?>"]
    </div>
  </div>
</div>

<?php
    wp_nonce_field( 'bc_testimonial_form_metabox_nonce', 'bc_testimonial_form_metabox_process' );
}

function bc_testimonial_save_metabox( $post_id, $post ) {
    if ( !isset( $_POST['bc_testimonial_form_metabox_process'] ) ) return;
    if ( !wp_verify_nonce( $_POST['bc_testimonial_form_metabox_process'], 'bc_testimonial_form_metabox_nonce' ) ) {
        return $post->ID;
    }
    if ( !current_user_can( 'edit_post', $post->ID )) {
        return $post->ID;
    }
    if ( !isset( $_POST['testimonial_name'] ) ) {
        return $post->ID;
    }
    if ( !isset( $_POST['testimonial_title'] ) ) {
        return $post->ID;
    }
    if ( !isset( $_POST['testimonial_message'] ) ) {
        return $post->ID;
    }
    if ( !isset( $_POST['testimonial_custom_image'] ) ) {
        return $post->ID;
    }
    $sanitizedname = wp_filter_post_kses( $_POST['testimonial_name'] );
    $sanitizedtitle = wp_filter_post_kses( $_POST['testimonial_title'] );
    $sanitizedmessage = wp_filter_post_kses( $_POST['testimonial_message'] );
    $sanitizedcustomimage = wp_filter_post_kses( $_POST['testimonial_custom_image'] );

    update_post_meta( $post->ID, 'testimonial_name', $sanitizedname );
    update_post_meta( $post->ID, 'testimonial_title', $sanitizedtitle );
    update_post_meta( $post->ID, 'testimonial_message', $sanitizedmessage );
    update_post_meta( $post->ID, 'testimonial_custom_image', $sanitizedcustomimage );
}
add_action( 'save_post', 'bc_testimonial_save_metabox', 1, 2 );

// Change Title on insert and update of location title
add_filter('wp_insert_post_data', 'bc_testimonial_change_title');
function bc_testimonial_change_title($data){
    if($data['post_type'] != 'bc_testimonials'){
        return $data;
    }
    if ( !isset( $_POST['testimonial_name'] ) ) {
        return $data;
    }
    $data['post_title'] = $_POST['testimonial_name'];
    return $data;
}
