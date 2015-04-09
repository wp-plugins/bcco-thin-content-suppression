<?php
	function thin_content_find_parent($_page,$pages){
		$item = '';
		foreach($pages as $struct) {			
				if ($_page->post_parent == $struct->ID) {
				$item .= $struct->post_name;
				if($struct->post_parent){
					$item = thin_content_find_parent($struct,$pages).'/'.$item;
				}
				break;
			}
		}
		return $item;
	}
	global $wpdb;
	$table_name1 = $wpdb->prefix . 'thin_general';
	function thin_content_details($table_name1){
		global $wpdb;
		$res_general = $wpdb->get_results( "SELECT *,count(*) as cnt FROM $table_name1");
		return $res_general = $res_general[0];
	}		
	$res_general = thin_content_details($table_name1);
	if(isset($_POST['_find_page'])){
		if($_POST['_no_pages']==""){
			echo '<script>alert("Please enter word count.");window.location.href=window.location.href;</script>';exit;
		}elseif (!ctype_digit($_POST['_no_pages'])){
		  echo '<script>alert("Please enter integer word count only.");window.location.href=window.location.href;</script>';exit;
		}
	}
	if(isset($_POST['apply_suppression'])){	
		global $wpdb;
		$table_name1 = $wpdb->prefix . 'thin_general';
		$meta_tag = mysql_real_escape_string($_POST['post_meta_type']);
		$_no_pages = mysql_real_escape_string($_POST['post_page_no']);
		if($res_general->cnt>0){
			$wpdb->query("UPDATE $table_name1 SET meta_type = ".$meta_tag.",word_count = ".$_no_pages." WHERE id = 1");
		}else{
			$wpdb->insert( 
				$table_name1, 
				array( 
					'id' => 1,
					'meta_type' => $meta_tag, 
					'word_count' => $_no_pages 
				), 
				array( 
					'%d',
					'%d', 
					'%d' 
				) 
			);
		}		
		$pages_id_mets = explode(',',mysql_real_escape_string($_POST['pages_id_mets']));	
		$table_name2 = $wpdb->prefix . 'thin_pages';
		$wpdb->query('delete from '.$table_name2);
		foreach($pages_id_mets as $res){
			$wpdb->insert( 
				$table_name2, 
				array(
					'page_id'=>$res
				), 
				array( 
					'%d' 
				) 
			);
		}
		echo "<script>alert('Suppression applied successfully.');window.location.href=window.location.href;</script>";
	}
?>
<div class="wpcontent">
	<h3><label for="title">Settings Page</label></h3>
	<div class="inside">
		<p><b>IMPORTANT:</b> For this plugin to function, you must copy and paste the following code snippet into your <?php echo htmlspecialchars("<head></head>");?> tag.</p>
		<p class="php-color"><span class="color">&lt;?php</span> echo do_shortcode( '[META_TAGS page="'.get_the_ID().'"]' ); <span class="color">?&gt;</span></p>
		<p>It is extremely important that you do not alter this code snippet in any way.</p>
		<p>If you add text to one of the pages that has been processed by this plugin, you must re-process everything.</p>
	</div>
	<div class="inside sttings_tab" id="find_page_div">
		<form id="frm_find_pages" name="frm_find_pages" method="post">
			<div class="textfield">
				<input type="radio" name="meta_tag" id="follow" <?php if(@$res_general->meta_type==1||@$res_general->meta_type==""){echo "checked";} ?> value="1"><?php echo htmlspecialchars('<META NAME="ROBOTS" CONTENT="NOINDEX, FOLLOW">');?></input>
			</div>
			<div class="textfield">
				<input type="radio" name="meta_tag" id="nofollow" <?php if(@$res_general->meta_type==2){echo "checked";} ?> value="2"><?php echo htmlspecialchars('<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">');?></input>
			</div>
			<div class="textfield">
				<label>All pages that have less than</label>
				<input type="textbox" name="_no_pages" maxlength="4" id="_no_pages" value="<?php echo @$res_general->word_count; ?>" />
				<label>words on a page</label>
			</div>
			<input type="submit" id="_find_page" name="_find_page" value="Find pages" />
		</form>
	</div>
	<div class="inside" id="apply_suppression_div" style="display:none">
		<form id="frm_apply_suppression" name="frm_apply_suppression" method="post">
			<h3><label for="title">Exclude these pages:</label></h3>
			<?php
				if(isset($_POST['_no_pages'])){
					$copy_urls="";
					$pages = get_pages(); 
					$i=0;				
					$act_plugins = get_option( 'active_plugins' );
					foreach($pages as $_page){
						$permalink_structure = get_option( 'permalink_structure' );
						if(trim($permalink_structure)!=""){
							if($_page->post_parent=='0'){
								$_url = get_site_url().'/'.$_page->post_name;
							}else{								
								$item = thin_content_find_parent($_page,$pages);
								$_url = get_site_url().'/'.$item.'/'.$_page->post_name;		
							}
							if(in_array('html-on-pages/html-on-pages.php',$act_plugins)){
								$url = rtrim($_url,'/').".html";
								$output = @file_get_contents($url);
							}else{
								$url = $_url.'/';
								$output = @file_get_contents($url);		
							}
						}else{
							$url = get_site_url().'/?page_id='.$_page->ID;
							$output = @file_get_contents($url);
						}	
						$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
								   '@<head>.*?</head>@siU',            // Lose the head section
								   '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
								   '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
						);						
						$_content = preg_replace($search, '', $output); 
						$word_count = str_word_count(preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ',strip_tags($_content)));
						if($word_count<=$_POST['_no_pages'] && $word_count != "0"){
							echo '<div class="checkbox"><input type="checkbox" name="exclude[]" value="'.$_page->ID.'">'.$url.'</input></div>';
							$copy_urls .= $url."\r\n";
							$i++;
						}		
					}					
					if($i<=0){
						echo "<script>alert('We did not find any pages meeting that criteria.');window.location.href=window.location.href;</script>";					
						}else{?>						
						<script>							
							var overlay = jQuery('<div id="overlay"></div><div class="popup"><div class="cnt223"><div style="text-align:center;"><img src="<?php echo plugins_url( 'thin-content-suppression/images/loader.gif' );?>" class="_loader_img" /></div><p>Please wait for a moment.</p></div></div>');							
							overlay.show();							
							jQuery('body').prepend(overlay);
							jQuery('.popup').show();	
							jQuery( document ).ready(function() {
								jQuery("#find_page_div").hide();
								jQuery("#apply_suppression_div").show();
								overlay.hide();		
							});						
						</script>					
						<?php }
				}
			?>
			<textarea id="_copy_clip" style="display:none;"><?php echo $copy_urls; ?></textarea>
			<input type="submit" id="apply_suppression" name="apply_suppression" value="Apply Suppression" />
			<input type="submit" id="copy-button" data-clipboard-target="_copy_clip"  name="btn_copy_urls" value="Copy URLs" />
			<input type="hidden" id="pages_id_mets" name="pages_id_mets" />
			<input type="hidden" id="post_meta_type" name="post_meta_type" value="<?php echo @$_POST['meta_tag']; ?>" />
			<input type="hidden" id="post_page_no" name="post_page_no" value="<?php echo @$_POST['_no_pages']; ?>" />
		</form>	</div>
	<div class="inside disclaimer_tab">
         <p><b>NOTE :- </b> This plugin counts all of the words on a page, not just the words in the WordPress text editor.<p> 
		<p><b>Disclaimer:</b> This plugin can be very dangerous if not used correctly. 
		By using this plugin, you agree that BCCO, and it’s owners, will not be held liable for any loses or damages that may occur. 
		<b>USE AT YOUR OWN RISK.</b> Please read our <a target="_balnk" href="https://thebc.co/terms-and-conditions/">Terms & Conditions</a>.</p>
        <p><a class="button" href="http://eepurl.com/M6rIv" target="_blank">Subscribe To Email Updates</a></p>
	</div>
</div>
<script>
	jQuery("#frm_apply_suppression").submit(function() {
		var meta_pages_ids = "";
		jQuery("input:checkbox:not(:checked)").each(function () {
			meta_pages_ids += jQuery(this).val()+',';		
        });
		jQuery("#pages_id_mets").val(meta_pages_ids.substring(0, meta_pages_ids.length - 1));
	});
	jQuery("input:checkbox").click(function(){
		var _copy_urls="";
		jQuery("input:checkbox:not(:checked)").each(function () {
			_copy_urls += jQuery(this).parent().text()+"\r\n";
			jQuery("#_copy_clip").html(_copy_urls);
		});
	});
	jQuery("#frm_find_pages").submit(function() {
		var regex_number = /^\+?(0|[1-9]\d*)$/;
		var _no_pages = jQuery("#_no_pages").val();
		if(_no_pages == ""){
			alert("Please enter word count.");
			return false;
		}else if(!regex_number.test( _no_pages )){
			alert("Please enter integer word count only.");
			return false;
		}
	});
	jQuery( document ).ready(function() {
		jQuery("#copy-button").click(function(){
			return false;
		});
		var client = new ZeroClipboard( document.getElementById("copy-button") );
		client.on( "ready", function( readyEvent ) {
		  client.on( "aftercopy", function( event ) {
			alert("URLs copied in clipboard successfully.");
		  });
		});
	});
</script>