<?php
/**
 * Plugin Name:       BC Testimonials
 * Plugin URI:        https://github.com/nikhil-twinspark/bc-testimonials
 * Description:       A simple plugin for creating custom post types for displaying testimonials.
 * Version:           1.0.0
 * Author:            Blue Corona
 * Author URI:        #
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bc-testimonials
 * Domain Path:       /languages
 */

 if ( ! defined( 'WPINC' ) ) {
     die;
 }

define( 'BC_TESTIMONIAL_VERSION', '1.0.0' );
define( 'BCTESTIMONIALDOMAIN', 'bc-testimonials' );
define( 'BCTESTIMONIALPATH', plugin_dir_path( __FILE__ ) );

require_once( BCTESTIMONIALPATH . '/post-types/register.php' );
add_action( 'init', 'bc_testimonial_register_testimonial_type' );

require_once( BCTESTIMONIALPATH . '/custom-fields/register.php' );

function bc_testimonial_rewrite_flush() {
    bc_testimonial_register_testimonial_type();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'bc_testimonial_rewrite_flush' );

// plugin uninstallation
register_uninstall_hook( BCTESTIMONIALPATH, 'bc_testimonial_uninstall' );
function bc_testimonial_uninstall() {
    // Removes the directory not the data
}

// Add Conditionally css & js for specific pages
add_action('admin_enqueue_scripts', 'bc_testimonil_include_css_js');
function bc_testimonil_include_css_js($hook){
    $current_screen = get_current_screen();
    if ( $current_screen->post_type == 'bc_testimonials') {
        // Include CSS Libs
        wp_register_style('bc-plugin-css', plugins_url('assests/css/bootstrap.min.css', __FILE__), array(), '1.0.0', 'all');
        wp_enqueue_style('bc-plugin-css');

        wp_enqueue_script('bc-testimonials-image-upload-js', plugin_dir_url(__FILE__).'assests/js/bc-image-upload.js', array( 'jquery'));
    } 
}


add_shortcode( 'bc-testimonial', 'bc_testimonial_shortcode' );
function bc_testimonial_shortcode ( $atts , $content = null) {
    static $count = 0;
    $count++;
    add_action( 'wp_footer' , function() use($count){
    ?>
        <script>
        var testimonialSwiper<?php echo $count ?> = new Swiper('#bc_testimonial_swiper_<?php echo $count ?>', {
            pagination: false,
            navigation: {
                nextEl: '.bc_testimonial_swiper_next',
                prevEl: '.bc_testimonial_swiper_prev',
            },
        });
        </script>
    <?php });
    $Ids = null;
    $args  = array( 'post_type' => 'bc_testimonials', 'posts_per_page' => -1, 'order'=> 'DESC','post_status'  => 'publish');
    if(isset($atts['id'])) {
        $Ids = explode(',', $atts['id']);
        $postIds = $Ids;
        $args['post__in'] = $postIds;
    } 
    ob_start();
    ?>
<style type="text/css">
.circular--landscape {
  display: inline-block !important;
  position: relative;
  width: 100px;
  height: 100px;
  overflow: hidden;
  border-radius: 50%;
}

.circular--landscape img {
  width: auto;
  height: 100%;
  margin-left: 0px;
}
@media only screen and (max-width: 600px) {
    .bc_moblie_p{
        font-size:16px;
        line-height:30px;
        font-weight:400;
    }
} 
</style>
<div class="container-fluid bc_testimonials_container bc_home_section_bg py-5  text-center" style="background-image:url('<?php echo get_template_directory_uri();?>/img/testimonial_bg.png'); background-position:center;">
    <div class="text-center"><h2 class="bc_font_alt_1 pb-4 text-capitalize">Testimonials</h2></div>
    <div class="container">
<div id="bc_testimonial_swiper_<?php echo $count;?>" class="bc_testimonial_swiper swiper-container">
    <div class="swiper-wrapper text-center">
        <?php
        $query = new WP_Query( $args );
        if ( $query->have_posts() ) :
            while($query->have_posts()) : $query->the_post();
        $title = get_post_meta( get_the_ID(), 'testimonial_title', true );
        $message = get_post_meta( get_the_ID(), 'testimonial_message', true );
        $image = get_post_meta( get_the_ID(), 'testimonial_custom_image', true );
        ?>
        <div class="swiper-slide">
            <div class="swiper-slide-container">
                <div class="swiper-slide-content">

                    <div class="circular--landscape d-none d-md-block">
                      <img src="<?php echo $image;?>" /> 
                    </div> 
                    <!-- <div class="d-none d-md-block">
                        <img src="<?php echo $image;?>" class="w-25 rounded-circle img-responsive" />
                    </div>  -->
                    <div>
                        <p class="bc_moblie_p">
                        <?php 
                        if (strlen($message) > 165){
                            $message = substr($message, 0, 165) . '...';
                            echo $message; 
                        }else{
                            echo $message;
                        }
                        ?>
                        </p>
                    </div>
                    <div class="mt-2 d-none d-md-block">
                        <span class="bc_alternate_font_blue m-0 bc_text_18">- <?php the_title(); ?></span>
                        <p class="m-0"><?php echo $title;?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
            endwhile; 
            wp_reset_query();
        endif;?>
    </div>
    <div class="bc_testimonial_swiper_next swiper-button-next d-none d-lg-block"><em class="fa fa-chevron-circle-right"></em></div>
    <div class="bc_testimonial_swiper_prev swiper-button-prev d-none d-lg-block"><em class="fa fa-chevron-circle-left"></em></div>
</div>
 </div>
    <br>
    <button class="btn bc_color_primary_bg mt-2 mb-2 px-4 text-white " type="button">Read Testimonials</button>
    <br>
</div>
<?php 
$output = ob_get_clean();
return $output;
}

/** ADMIN COLUMN - HEADERS*/
add_filter('manage_edit-bc_testimonials_columns', 'add_new_testimonials_columns');
function add_new_testimonials_columns($columns) {
    return array(
                'cb' => $columns['cb'],
                'title' => $columns['title'],
                'name' => __('From'),
                'updated' => __('Updated'),
                'date' => 'Status',
            ); 
}

/** ADMIN COLUMN - CONTENT*/
add_action('manage_bc_testimonials_posts_custom_column', 'manage_testimonials_columns', 10, 2);
function manage_testimonials_columns($column_name, $id) {
    global $post;
    switch ($column_name) {
        case 'name':
            echo get_post_meta( $post->ID , 'testimonial_title' , true );
            break;
        case 'updated':
            $updated_day = get_the_modified_time('m/d/Y');
            echo $updated_day;
            break;
        default:
            break;
    } // end switch
}


// Admin notice for displaying shortcode on index page
add_action('admin_notices', 'bc_testimonials_general_admin_notice');
function bc_testimonials_general_admin_notice(){
    global $pagenow;
    global $post;
    if ($pagenow == 'edit.php' &&  (isset($post->post_type) ? $post->post_type : null) == 'bc_testimonials') { 
     echo '<div class="notice notice-success is-dismissible">
            <p><b>Shortcode Example</b> All : [bc-testimonial] Specific : [bc-testimonial id="1,2,3"]</p>
         </div>';
    }
}
