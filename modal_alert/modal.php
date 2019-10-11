<?php
/**
 * Plugin Name: Modal Plugin
 * Plugin URI: http://www.localhost.test
 * Description: Modal Alert plugin.
 * Version: 1.0
 * Author: Nebojsa Dabic
 * Author URI: http://www.localhost.test
 */
 
 
 // Get all post types
function post_types() {
	$post_types = Array("post", "page");
	$args = array(
		'public'   => true,
		'_builtin' => false
	);

	$output = 'names';
	$operator = 'and';

	return $post_types += get_post_types( $args, $output, $operator ); 
} 


// Get posts by type
function get_posts_by_type($post_type) {
	$collection = Array();
	$loop = new WP_Query( array( 'post_type' => $post_type ) ); // , 'posts_per_page' => 10
	while ( $loop->have_posts() ) : $loop->the_post();
		$collection[get_the_ID()] = get_the_title();
	endwhile;
	
	return $collection;
}


 // Display plugin content
function modal_plugin_content ( $content ) {
    return $content .= '
		<script src="/wp-content/plugins/modal_alert/inc/jquery.modal.min.js"></script>
		<link rel="stylesheet" href="/wp-content/plugins/modal_alert/inc/jquery.modal.min.css" />

		<link rel="stylesheet" href="/wp-content/plugins/modal_alert/inc/modal.css" type="text/css" />
		<script src="/wp-content/plugins/modal_alert/inc/modal.js"></script>

		<div id="openModal" class="modal">
		  <h2>'. get_option('modal_option_title') .'</h2>
		  <p>'. get_option('modal_option_description') .'</p>
		</div>
	';
}


// Add plugin only on pages that user selected
function enable_plugins_selectively( $plugins ) {
	$array = Array();
	$post_types = post_types();
	foreach ( $post_types  as $post_type ) {
		if(!empty(get_option('modal_select_option_'.$post_type))){
			$array = array_merge($array, get_option('modal_select_option_'.$post_type));
		}
	}
	
	// Activate if current page is selected by user in admin settings page
	if(in_array(get_the_ID(), $array)) {
		add_action( 'the_content', 'modal_plugin_content' );
	}
}
add_action( 'wp_head', 'enable_plugins_selectively' );

 
// Options from settings page 
function modal_register_settings() {
	add_option( 'modal_option_title', 'Enter title here.');
	register_setting( 'modal_options_group', 'modal_option_title', 'myplugin_callback' );

	add_option( 'modal_option_description', 'Enter description here.');
	register_setting( 'modal_options_group', 'modal_option_description', 'myplugin_callback' );

	$post_types = post_types();
	foreach ( $post_types  as $post_type ) {
		register_setting( 'modal_options_group', 'modal_option_'. $post_type, 'myplugin_callback' );
		register_setting( 'modal_options_group', 'modal_select_option_'. $post_type, 'myplugin_callback' );
	}
 }
add_action( 'admin_init', 'modal_register_settings' );


// Right Settings Menu
function modal_register_options_page() {
  add_options_page('Modal Plugin', 'Modal box', 'manage_options', 'myplugin', 'modal_options_page');
}
add_action('admin_menu', 'modal_register_options_page');


// Custom post type function
function create_posttype() {
	
    register_post_type( 'companies',
        array(
            'labels' => array(
                'name' => __( 'Companies' ),
                'singular_name' => __( 'Company' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'companies'),
        )
    ); 
	
    register_post_type( 'reviews',
        array(
            'labels' => array(
                'name' => __( 'Reviews' ),
                'singular_name' => __( 'Review' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'reviews'),
        )
    ); 
	
    register_post_type( 'team',
        array(
            'labels' => array(
                'name' => __( 'Teams' ),
                'singular_name' => __( 'Team' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'team'),
        )
    );
}
add_action( 'init', 'create_posttype' );


// Will be dispayed on settings page
function modal_options_page()
{
?>
<div>
<link rel="stylesheet" href="/wp-content/plugins/modal_alert/inc/modal.css" type="text/css" />
<script src="/wp-content/plugins/modal_alert/inc/jquery.modal.min.js"></script>
<script src="/wp-content/plugins/modal_alert/inc/modal.js"></script>

<?php screen_icon(); ?>
<h2>Modal box options</h2>
<form method="post" action="options.php">
	<?php settings_fields( 'modal_options_group' ); ?>
	<h3>This is my option</h3>
	<p>Some text here.</p>
	<table>
		<tr valign="top">
			<th scope="row"><label for="modal_option_title">Modal title</label></th>
			<td><input size="98" type="text" id="modal_option_title" name="modal_option_title" value="<?php echo get_option('modal_option_title'); ?>" /></td>
		</tr>
			<tr valign="top">
			<th scope="row"><label for="modal_option_description">Description</label></th>
		<td><textarea cols="100" rows="10" id="modal_option_description" name="modal_option_description"><?php echo get_option('modal_option_description'); ?></textarea></td>
		</tr>
			<tr valign="top">
			<th scope="row"><label for="modal_option_description">Post types</label></th>
			<td>
			<?php
			$post_types = post_types();
			foreach ( $post_types  as $post_type ) {
				$checked = (get_option('modal_option_'.$post_type) == 1) ? 'checked' : '';
				echo "<div>";
				echo "<input value=\"1\" type=\"checkbox\" class=\"openSelect\" id=\"modal_option_{$post_type}\" name=\"modal_option_{$post_type}\" {$checked} />";
				echo "<label for=\"modal_option_{$post_type}\">". ucFirst($post_type) ."</label><br />";

				$posts = get_posts_by_type($post_type);
				if(!empty($posts)) {
					echo "<select style=\"width: 300px;\" class=\"selectBox modal_option_{$post_type}\" id=\"modal_select_option_{$post_type}\" name=\"modal_select_option_{$post_type}[]\" size=\"3\" multiple>";
					foreach ( $posts as $id => $title ) {
						$selected = in_array( $id, get_option('modal_select_option_'.$post_type) ) ? 'selected' : '';
						echo "<option {$selected} value=\"{$id}\">". $title ."</option>";
					}
					echo "</select>";
					echo "</div>";
				}
				else {
					echo "<div class=\"selectBox modal_option_{$post_type}\">No posts</div>";
				}
			}
			?>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
</form>
</div>
<?php
} ?>
