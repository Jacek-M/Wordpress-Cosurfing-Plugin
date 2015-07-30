<?php

/*
Plugin Name: CoSurfing Add JavaScript
Plugin URI: https://cosurfing.net/
Description: Add JavaScript widget code to WordPress!
Author: Karol Miksztal & Jacek Moczydło
Author: URI: https://cosurfing.net/
Version: 1.0.1
*/

add_action('wp_head', 'addJsCode');
add_action('admin_menu', 'cosurfing_init_menu'); 
add_action('init', 'cosurfing_init_plugin');
add_action( 'deactivated_plugin', 'detect_plugin_deactivation', 10, 2 );

$accept = false;

$currentLang;
function loadLanguage()
{
	global $currentLang;
	$dirloc = plugin_dir_path( __FILE__);
	$xml = simplexml_load_file($dirloc.'/languages/current.xml');
	$currentLang = $xml->{'lang'};
}


function getTranslation($element)
{
	global $currentLang;
	$dirloc = plugin_dir_path( __FILE__);
	if($currentLang == 'pl')
	{
		$xml = simplexml_load_file($dirloc.'/languages/pl.xml');
		return $xml->{$element};
	}
	else // eng
	{
		$xml = simplexml_load_file($dirloc.'/languages/en.xml');
		return $xml->{$element};
	}
}

function changeLang($param)
{
	
	$dirloc = plugin_dir_path( __FILE__).'/languages/current.xml';
	$file = fopen($dirloc, 'w');

	if($param == 'pl')
	{
		$temp = '<?xml version="1.0" encoding="UTF-8"?><current><lang><![CDATA[pl]]></lang></current>';
		fwrite($file, $temp);
	}
		
	if($param == 'en')
	{
		$temp = '<?xml version="1.0" encoding="UTF-8"?><current><lang><![CDATA[en]]></lang></current>';
		fwrite($file, $temp);
	}
	fclose($file);
	loadLanguage();
}

function addJsCode()
{
	loadLanguage();
	$accept_local = get_option('cosurfing_agree');
	if($accept_local == 'true')
	{
		$options = get_option('cosurfing_option');
		echo '<script src="https://cosurfing.s3.amazonaws.com/cosurfing.nocache.js" data-token="'.$options.'" id="cosurfing_net_id"></script>';
	}
}

function getEmailAndPageUrl()
{
	$email = get_option('admin_email');
	$site = get_option('siteurl');
	return 'email='.$email.'&ampsite='.$site.'&ampfrom=wordpress';
}


function cosurfing_confirm()
{
	loadLanguage();
?>
	<script type="text/javascript">

	<?php echo 'var r = confirm("'.getTranslation('confirml1') .'");'?>
	if (r == true) 
	{
		<?php global $accept; $accept = true; ?>
	}
	else 
		<?php echo 'alert("'.getTranslation('confirml2') .'");'?>
	</script>

<?php
}



function detect_plugin_deactivation(  $plugin, $network_activation ) 
{
	update_option('cosurfing_agree', 'false', 'yes');
	update_option('cosurfing_option', '', 'yes');
}
function cosurfing_init_plugin()
{
	global $accept;
	$iso_code = get_bloginfo('language'); 
	if(strpos($iso_code, 'pl') !== false)
		changeLang('pl');
	else if(strpos($iso_code, 'en') !== false)
		changeLang('en');
	$accept_local = get_option('cosurfing_agree');
	add_option('cosurfing_agree', 'false', '', 'yes');
	if($accept_local == 'false')cosurfing_confirm();
	if($accept) update_option('cosurfing_agree', 'true', 'yes');

	/*$ch = curl_init("http://www.cosurfing.net/cpanel/index.html?" . getEmailAndPageUrl());
	curl_exec($ch);*/
}

function cosurfing_init_menu()
{
	loadLanguage();
	add_menu_page('CoSurfing Settings', 'CoSurfing DataToken Input', 'manage_options', 'cosurfing_settings_panel', 'cosurfing_menu');
	add_option( 'cosurfing_option', '', '', 'yes');
}


function cosurfing_menu()
{
	if (isset($_GET['cslan']) && ($_GET['cslan'] == 'pl')) 
		changeLang('pl');
	if (isset($_GET['cslan']) && ($_GET['cslan'] == 'en')) 	
		changeLang('en'); 
?>

<link rel="stylesheet" type="text/css" href="/wp-content/plugins/cosurfing_wordpress/style.css">
<div id="container">
	<div id="logo">
		<a href="http://cosurfing.net/"> <img src="https://cosurfing.net/assets/img/logoCosurfingBlackBg.png" width="250" height="70" /> </a>
	</div>
	<div id="h">
		<?php echo getTranslation('title'); ?>
	</div>
	<div id="plugin">
		<form method="post" action="">
		<?php echo getTranslation('label1'); ?><br><br>
		<input type="text" name="cosurfing_option" value="<?php echo esc_attr( get_option('cosurfing_option') ); ?>" /><br>
		<div id="button1"><?php submit_button( getTranslation('button'),'secondary'); ?></div>
	    <?php if(isset($_POST['submit']))
	    {
	    	update_option('cosurfing_option', $_POST['cosurfing_option'], 'yes');  
	    	?>
	    	<div id="msg1"><?php echo getTranslation('alert'); ?></div>
	    	<?php
	    }
	    ?>
	    </form>
	    <div id="link">
	    	<?php echo '<a href="https://cosurfing.net/cpanel/index.html?'.getEmailAndPageUrl().'">'.getTranslation('link').'</a>' ?>
	    </div>
	</div>
	<div id="monitor">
		<div id="monitorH">
				<?php echo getTranslation('monitor'); ?>
		</div>
		<div id="monitorB">
			<div class="subDoodleContainer">
					<img src="http://cosurfing.net/assets/img/subpages/screens.png" alt=""  id="screens" >
					<div id="subVscreen1" class="subVscreen"><img src="http://cosurfing.net/assets/img/cursor_ico.png" id="cursor1" class="vcursor" /></div>
					<div id="subVscreen2"   class="subVscreen"><img src="http://cosurfing.net/assets/img/cursor_ico.png" id="cursor2"  class="vcursor"/></div>
					<div id="subVlinker" class="subVlinker"></div>
					</div>
			<script src="http://cosurfing.net/js/agency.js"></script>
			<script src="https://cosurfing.net/assets/js/jquery-1.10.2.min.js"></script>
			<script src="https://cosurfing.net/assets/js/bootstrap.js"></script>
			<link href="https://cosurfing.net/assets/css/magic-bootstrap-cosurfing.min.css" rel="stylesheet">
			<link rel="https://cosurfing.net/assets/css/magic-bootstrap-cosurfing.less" type="text/css" href="style.less">
			<script src="https://cosurfing.net/assets/js/script.js" type="text/javascript"></script>
		</div>
	</div>
	<div id="footer">
			<?php echo getTranslation('footer'); ?>
	</div>
	<div id="footerAll">
		<div id="footerLeft">
			<span id="span1">WebLive Systems</span><br><br>  

			Phone: +48 602 885 891<br> 
			Fax: +48 022 247-82-57<br> 
			info@weblive.com.pl<br> 	
			www: weblive.com.pl<br><br>   

			NIP : 664-201-48-34<br> 
			REGON : 260618454<br> 
		</div>
		<div id="footerRight">
			<?php echo '<span id="span1">'.getTranslation('language').'</span><br><br>'  ?>
			<table id="foot">
				<tr>
					<td>
						<img src="https://cosurfing.net/assets/img/flagaPolski.gif" width="22" height="13">
					</td>
					<td>
						<?php echo'<a href="'.$_SERVER['REQUEST_URI'].'&cslan=pl">Polski</a>';?>
					</td>
				</tr>
				<tr>
					<td>
						<img src="https://cosurfing.net/assets/img/flagaBrytania.gif" width="22" height="13">
					</td>
					<td>
						<?php echo'<a href="'.$_SERVER['REQUEST_URI'].'&cslan=en">English</a>';?>
					</td>
				</tr>
			</table>
		</div>
		<br class="clear" />
	</div>
</div>
<?php 
}
?>