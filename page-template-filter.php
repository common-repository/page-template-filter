<?php
/*
	Author: t31os
	Description: 
	Domain Path: /lang
	Plugin Name: Page Template Filter
	Plugin URI: 
	Text Domain: ptf-lang
	Version: 1.0.0
*/

if( !function_exists('add_action') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}
if( version_compare( PHP_VERSION, '5.0.0', '<' ) ) {
	add_action( 'admin_notices', 'ptf_version_require' );
	function t31os_ptf_version_require() {
		if( current_user_can( 'manage_options' ) )
			echo '<div class="error"><p>This plugin requires at least PHP 5.</p></div>';
	}
	return;
}

class PageTemplate_Filter {
	
	private $templates = array();
	private $current   = '';
	private $conflict  = false;
	private $add_col   = true;
	
	public function __construct() {
		add_action( 'admin_init', array( $this, 'ptf_admin_init' ), 2000 );
	}
	public function ptf_admin_init() {
		
		add_action( 'parse_query',           array( $this, 'ptf_parse_query' ) );
		add_action( 'restrict_manage_posts', array( $this, 'ptf_restrict_manage_posts' ) );
		
		$add_template_column = apply_filters( 'ptf_add_template_column', (bool) $this->add_col );
		
		if( !$add_template_column )
			return;
		
		global $wp_post_types;
		
		foreach( $wp_post_types as $type => $type_object ) {
			
			if( !post_type_supports( $type, 'page-attributes' ) )
				continue;
			
			add_filter( 'manage_'.$type.'_posts_columns',         array( $this, 'ptf_col_head' ) );
			add_action( 'manage_'.$type.'_posts_custom_column',   array( $this, 'ptf_col' ) );
		}
	}
	public function ptf_col_head( $columns ) {
		if( isset( $columns['template'] ) ) {
			$this->conflict = true;
			return $columns;
		}
		$columns['template'] = 'Template';
		return $columns;
	}
	public function ptf_col( $col ) {
		if( $col != 'template' || $this->conflict )
			return;
		global $post;
		$t = get_post_meta( $post->ID, '_wp_page_template', true );
		if( empty( $t ) || 'default' == $t )
			return;
		echo $t;
	}
	public function ptf_parse_query( $query ) {
		
		global $pagenow, $post_type;
		
		if( 'edit.php' != $pagenow )
			return;
		
		if( !post_type_supports( $post_type, 'page-attributes' ) )
			return;
			
		$this->templates = get_page_templates();

		if( empty( $this->templates ) )
			return;
			
		if( !$this->is_set_template() )
			return;
		
		$template = $this->get_template();
		
		if( empty( $template ) )
			return;
		
		$meta_group = array( 'key' => '_wp_page_template', 'value' => $template );
		set_query_var( 'meta_query', array( $meta_group ) );

	}
	public function ptf_restrict_manage_posts() {
		if( empty( $this->templates ) )
			return;
		$this->ptf_template_dropdown();
	}
	private function get_template() {
		if( !$this->is_set_template() )
			return '';
		
		foreach( $this->templates as $template ) {
			if( $template != $_GET['page_template'] )
				continue;
			
			if( empty( $this->current ) )
				$this->current = $template;
				
			return $this->current;
		}
		return '';
	}
	private function is_set_template() {
		return (bool) ( isset( $_GET['page_template'] ) );
	}
	private function ptf_template_dropdown() {
		?>
		<select name="page_template" id="page_template">
			<option value=""> <?php _e( 'Show all templates' ); ?> </option>
			<?php foreach( $this->templates as $name => $file ): ?>
			<option value="<?php echo $file; ?>"<?php selected( $this->get_template() == $file ); ?>> - <?php _e( $name ); ?></option>
			<?php endforeach;?>
		</select>
		<?php 
	}
}

$ptf = new PageTemplate_Filter;