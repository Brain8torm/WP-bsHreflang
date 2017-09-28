<?php
/*
Plugin Name: bsHreflang
Plugin URI: http://brain8torm.ru
Description: Плагин добавляет в <code>&lt;head&gt;</code> тег link с атрибутом hreflang
Version: 1.0
Author: Andrey Kudryashov (BrainStorm)
Author URI: http://brain8torm.ru
*/

	add_action('init', 'bs_hreflang_init');
	function bs_hreflang_init() {
		$hreflang_data = get_option( 'bs_hreflang_settings' );
		$hreflang_line = explode("\n", str_replace("\r", "", $hreflang_data['bs_hreflang_textarea']));
		//print_r( str_getcsv($csv['bs_hreflang_textarea']));
		//print_r($ids);
		$data = array();
		
		foreach ( $hreflang_line as $line ) {
			array_push($data, str_getcsv($line, ';', '"') );
		}
		
		foreach ( $data as $d ) {
			$url_path = parse_url($d[0], PHP_URL_PATH);
			$url_param = parse_url($d[0], PHP_URL_QUERY);
			$url = $url_path;
			if ( $url_param ) $url .= '?' . $url_param;
			//echo $url . '\n';
			if ( $url == $_SERVER['REQUEST_URI'] ) {
				//echo $d[1];
				add_action('wp_head', 'bs_put_hreflang', 1);
				do_action('wp_head', $d[1]);
			}
		}
		//print_r( $_SERVER );
		/*
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		if($wpdb->get_var("SHOW TABLES LIKE 'wp_options';") == $wpdb->prefix.'options') {
			$sql = "show table status like '". $wpdb->prefix . "options'";
			$db_coll = $wpdb->get_row($sql, ARRAY_A);
			
		}
		
		$table_name = $wpdb->prefix."bs_hreflang";
		$wpdb->query($sql);
		if($wpdb->get_var("SHOW TABLES LIKE '" . $table_name. "'") != $table_name){
			$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `url` VARCHAR(255) NOT NULL,
					  `hreflang` VARCHAR(255) NOT NULL,
					  PRIMARY KEY (`id`)
					) DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . $db_coll['Collation'] . " AUTO_INCREMENT=1 ;";
		
			dbDelta($sql);
		} else {
			
		}
		*/

	}
	
	add_action( 'admin_init', 'bs_hreflang_settings_init' );
	
	function bs_hreflang_settings_init(  ) { 

		register_setting( 'pluginPage', 'bs_hreflang_settings' );

		add_settings_section(
			'bs_hreflang_pluginPage_section', 
			'',//__( 'Your section description', '222' ), 
			'',//'bs_hreflang_settings_section_callback', 
			'pluginPage'
		);

		add_settings_field( 
			'bs_hreflang_textarea', 
			__( 'CSV код', '222' ), 
			'bs_hreflang_textarea_render', 
			'pluginPage', 
			'bs_hreflang_pluginPage_section' 
		);
	}
	
	
	function bs_hreflang_textarea_render(  ) { 

		$options = get_option( 'bs_hreflang_settings' );
		?>
		<textarea style="font-family: 'Consolas', monospace;font-size: 12px;width:100%;overflow: auto;resize: both;" wrap="off" cols='40' rows='5' name='bs_hreflang_settings[bs_hreflang_textarea]'><?php echo $options['bs_hreflang_textarea']; ?></textarea>
		<?php

	}


	function bs_hreflang_settings_section_callback(  ) { 

		echo __( 'This section description', '222' );

	}


	
	
	function bs_put_hreflang( $s ) {
		echo $s . "\n";
	}
	
	//add_action('wp_head', 'bs_put_hreflang', 1);
	
	add_action('admin_menu', 'bs_hreflang_admin_menu');

	function bs_hreflang_admin_menu(){
	    add_options_page('bsHrefLang0', 'bsHrefLang', 'manage_options', 'bs_hreflang', 'bs_hreflang_page');
	}
	
	function bs_hreflang_page(){
		echo '<div class="wrap">';
		echo '<h1>bsHrefLang - устанавливает hreflang для страниц</h1>';
		echo '<p>Вставьте в поле с новой строки, текст в CSV-формате(<code>url;hreflang</code>). URL страниц - абсолютный (<code>http://site.com/page/</code>).</p>';
		echo '<div><code>http://site.com/;"&lt;link rel="alternate" hreflang="de" href="http://de.site.com/" /&gt;"</code></div>';
		echo '<div><code>http://site.com/page/;"&lt;link rel="alternate" hreflang="de" href="http://de.site.com/page/" /&gt;"</code></div>';
	?>
	<form action='options.php' method='post'>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>

	</form>
	<?php
		echo '</div>';
	}