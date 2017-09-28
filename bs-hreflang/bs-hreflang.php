<?php
/*
Plugin Name: bsHreflang
Plugin URI: https://github.com/Brain8torm/WP-bsHreflang
Description: Плагин добавляет в <code>&lt;head&gt;</code> тег link с атрибутом hreflang
Version: 1.0
Author: Andrey Kudryashov (BrainStorm)
Author URI: http://brain8torm.ru
*/

	add_action('init', 'bs_hreflang_init');
	function bs_hreflang_init() {
		$hreflang_data = get_option( 'bs_hreflang_settings' );
		$hreflang_line = explode("\n", str_replace("\r", "", $hreflang_data['bs_hreflang_textarea']));
		
		$data = array();
		
		foreach ( $hreflang_line as $line ) {
			array_push($data, str_getcsv($line, ';', '"') );
		}
		
		foreach ( $data as $d ) {
			$url_path = parse_url($d[0], PHP_URL_PATH);
			$url_param = parse_url($d[0], PHP_URL_QUERY);
			$url = $url_path;
			if ( $url_param ) $url .= '?' . $url_param;

			if ( $url == $_SERVER['REQUEST_URI'] ) {
				add_action('wp_head', 'bs_put_hreflang', 1);
				do_action('wp_head', $d[1]);
			}
		}

	}
	
	add_action( 'admin_init', 'bs_hreflang_settings_init' );
	
	function bs_hreflang_settings_init(  ) { 

		register_setting( 'pluginPage', 'bs_hreflang_settings' );

		add_settings_section(
			'bs_hreflang_pluginPage_section',
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
