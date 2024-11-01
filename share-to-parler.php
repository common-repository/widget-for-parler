<?php
/*
Plugin Name: Share on Parler
Plugin URI: http://kimberlyhumphreys.com/parler_plugin/
Description: This plugin creates a follow me and a share on Parler buttons.
Version: 0.5
Author: RadianceLux
Author URI: http://kimberlyhumphreys.com/
License: GPL2
*/

// The widget class
class Parler_Widget extends WP_Widget {

	// Main constructor
	public function __construct() {
		parent::__construct(
			'parler_widget',
			__( 'Parler Widget', 'text_domain' ),
			array(
				'customize_selective_refresh' => true,
			)
		);
	}
	
	// The widget form (for the backend )
	public function form( $instance ) {

		// Set widget defaults
		$defaults = array(
			'title'    => '',
			'follow'     => '',
			'share' => '',
		);
		
		// Parse current settings with defaults
		extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>

		<?php // Widget Title ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Widget Title', 'text_domain' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<?php // Follow ?>
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'follow' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'follow' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $follow ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'follow' ) ); ?>"><?php _e( 'Follow Button', 'text_domain' ); ?></label>
		</p>
		
		<?php // Share ?>
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'share' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'share' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $share ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'share' ) ); ?>"><?php _e( 'Share Button', 'text_domain' ); ?></label>
		</p>	

		<?php // Dropdown ?>

	<?php }

	// Update widget settings
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']    = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['follow'] = isset( $new_instance['follow'] ) ? 1 : false;
		$instance['share'] = isset( $new_instance['share'] ) ? 1 : false;
		return $instance;
	}
	
	// Display the widget
	public function widget( $args, $instance ) {
		
		extract( $args );

		// Check the widget options
		$title    = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		$follow = ! empty( $instance['follow'] ) ? $instance['follow'] : false;
		$share = ! empty( $instance['share'] ) ? $instance['share'] : false;
		$parler_settings_options = get_option( 'parler_settings_option_name' ); // Array of All Options
		$parler_username_0 = $parler_settings_options['parler_username_0']; // Parler username
		$link	  = 'https://parler.com/profile/';

		// WordPress core before_widget hook (always include )
		echo $before_widget;

		// Display the widget
		echo '<div>';

			// Display widget title if defined
			if ( $title ) {
				echo $before_title . $title . $after_title;
			}

			// Display follow field
			if ( $follow ) {
				echo '
				<script>function parlerFOLLOW() {window.open("'. $link . $parler_username_0 .'");}</script>	
				<button class="parlerfollow" onclick="parlerFOLLOW()"><img src="https://image-cdn.parler.com/X/o/XouEnBddFl.png" alt="Follow me on Parler"></button>';
			}

			// Display share field
			if ( $share ) {
				echo '
				<script>function parlerSHARE() {window.open("https://parler.com/new-post?message="+ window.location.href);}</script>
				<button class="parlershare" onclick="parlerSHARE()"><img src="https://image-cdn.parler.com/I/l/IlJjuHlIuR.png" alt="Share on Parler"></button>';
			}
		echo '</div>';

		// WordPress core after_widget hook (always include )
		echo $after_widget;

	}

}

// Register the widget
function my_register_parler_widget() {
	register_widget( 'Parler_Widget' );
}
add_action( 'widgets_init', 'my_register_parler_widget' );

/**
 * Generated by the WordPress Option Page generator
 * at http://jeremyhixon.com/wp-tools/option-page/
 */

class ParlerSettings {
	private $parler_settings_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'parler_settings_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'parler_settings_page_init' ) );
	}

	public function parler_settings_add_plugin_page() {
		add_menu_page(
			'Parler Settings', // page_title
			'Parler Settings', // menu_title
			'manage_options', // capability
			'parler-settings', // menu_slug
			array( $this, 'parler_settings_create_admin_page' ), // function
			'dashicons-admin-generic', // icon_url
			81 // position
		);
	}

	public function parler_settings_create_admin_page() {
		$this->parler_settings_options = get_option( 'parler_settings_option_name' ); ?>

		<div class="wrap">
			<h2>Parler Settings</h2>
			<p>By RadianceLux 
Feel free to tip me a cup of coffee on Parler if you like the plugin!</p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'parler_settings_option_group' );
					do_settings_sections( 'parler-settings-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function parler_settings_page_init() {
		register_setting(
			'parler_settings_option_group', // option_group
			'parler_settings_option_name', // option_name
			array( $this, 'parler_settings_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'parler_settings_setting_section', // id
			'Settings', // title
			array( $this, 'parler_settings_section_info' ), // callback
			'parler-settings-admin' // page
		);

		add_settings_field(
			'parler_username_0', // id
			'Parler username', // title
			array( $this, 'parler_username_0_callback' ), // callback
			'parler-settings-admin', // page
			'parler_settings_setting_section' // section
		);
	}

	public function parler_settings_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['parler_username_0'] ) ) {
			$sanitary_values['parler_username_0'] = sanitize_text_field( $input['parler_username_0'] );
		}

		return $sanitary_values;
	}

	public function parler_settings_section_info() {
		
	}

	public function parler_username_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="parler_settings_option_name[parler_username_0]" id="parler_username_0" value="%s">',
			isset( $this->parler_settings_options['parler_username_0'] ) ? esc_attr( $this->parler_settings_options['parler_username_0']) : ''
		);
	}

}
if ( is_admin() )
	$parler_settings = new ParlerSettings();

/* 
 * Retrieve this value with:
 * $parler_settings_options = get_option( 'parler_settings_option_name' ); // Array of All Options
 * $parler_username_0 = $parler_settings_options['parler_username_0']; // Parler username
 */
 
function wpse_load_plugin_css() {
    $plugin_url = plugin_dir_url( __FILE__ );

    wp_enqueue_style( 'style1', $plugin_url . 'css/style1.css' );
}
add_action( 'wp_enqueue_scripts', 'wpse_load_plugin_css' );