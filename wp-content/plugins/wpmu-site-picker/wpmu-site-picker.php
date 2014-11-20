<?php
/*
Plugin Name: WPMU site picker
Plugin URI: http://none
Description: Wordpress multisite go to widget
Version: 0.1
Author: me
Author Email: me@home.com
License:

  Copyright 2011 me (me@home.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

class WPMUsitepicker extends WP_Widget {

	/*--------------------------------------------*
	 * Constants
	 *--------------------------------------------*/
	const name = 'WPMU site picker';
	const slug = 'wpmu_site_picker';
	/**
	 * Constructor
	 */
	function __construct() {
		$widget_ops = array( 'classname' => 'example', 'description' => __('Form for going to other sites from network', 'example') );
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'example-widget' );
		$this->WP_Widget( 'example-widget', __('WPMS goTo', 'example'), $widget_ops, $control_ops );

		//Hook up to the init action
		add_action( 'init', array( &$this, 'init_wpmu_site_picker' ) );
	}
	/**
	 * Runs when the plugin is activated
	 */
	function install_wpmu_site_picker() {
		// do not generate any output here
	}

	function init_wpmu_site_picker() {
		$this->register_scripts_and_styles();
	}

	function form($instance) {
		//Устанавливаем параметры по умолчанию.
		$defaults = array( 'title' => __('Change destination', 'example'), 'label' => __('Go', 'example'));
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'example'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'label' ); ?>"><?php _e('Button label:', 'example'); ?></label>
			<input id="<?php echo $this->get_field_id( 'label' ); ?>" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo $instance['label']; ?>" style="width:100%;" />
		</p>

	<?php
	}

	// widget update

	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		//Strip tags from title and name to remove HTML
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['label'] = strip_tags( $new_instance['label'] );


		return $instance;
	}

	// widget display

	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title'] );
		$label = $instance['label'];
		if ( ! $label ){$label = "go to";}

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

		echo "<select id='st_select'>";
		$blog_list = get_blog_list( 0, 'all' );
		$blog_id = get_current_blog_id();
		foreach ($blog_list AS $blog) {
			if($blog['blog_id'] != 1){
				$bTitle =  get_blog_option( $blog['blog_id'], 'blogname');
				if($blog_id == $blog['blog_id']){
					$bTitle=$bTitle."<b>*</b>";
				}

				$bUrl = get_blog_option( $blog['blog_id'], 'siteurl');

				echo '<option value="'.$bUrl.'">'.$bTitle.'</option>';
			}
//			echo '<pre>'.var_dump($blog).'</pre>';
//			echo 'Blog '.$blog['blog_id'].': '.$blog['domain'].$blog['path'].'<br>';
		}
		echo "</select>";
		echo '<input id="st_go" type="submit" value="'.$label.'" onclick="goto();">';

		echo $after_widget;
	}


	/**
	 * Registers and enqueues stylesheets for the administration panel and the
	 * public facing site.
	 */
	private function register_scripts_and_styles() {
		if ( is_admin() ) {

		} else {
			$this->load_file( self::slug . '-script', '/sitepicker.js', true );
			$this->load_file( self::slug . '-style', '/sitepicker.css' );
		} // end if/else
	} // end register_scripts_and_styles

	/**
	 * Helper function for registering and enqueueing scripts and styles.
	 *
	 * @name	The 	ID to register with WordPress
	 * @file_path		The path to the actual file
	 * @is_script		Optional argument for if the incoming file_path is a JavaScript source file.
	 */
	private function load_file( $name, $file_path, $is_script = false ) {

		$url = plugins_url($file_path, __FILE__);
		$file = plugin_dir_path(__FILE__) . $file_path;

		if( file_exists( $file ) ) {
			if( $is_script ) {
				wp_register_script( $name, $url, array('jquery') ); //depends on jquery
				wp_enqueue_script( $name );
			} else {
				wp_register_style( $name, $url );
				wp_enqueue_style( $name );
			} // end if
		} // end if

	} // end load_file


} // end class
//new WPMUsitepicker();
add_action('widgets_init', create_function('', 'return register_widget("WPMUsitepicker");'));
?>