<?php
/*
Plugin Name: Recent Posts Photos
Plugin URI: http://www.ProgrammersCountry.com
Description: Displays Recent Posts Photos from the media library in the sidebar. If the post does not contain a photo it is not displayed.
Version: 0.0.1
Author: Saadi Iqbal
Author URI: http://www.ProgrammersCountry.com
*/
/*  Copyright 2010  Asad Iqbal (email : engr.software@yahoo.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Main Widget Function */


function rp_widget($args)
{
  extract($args);
  global $rw_settings;
  //Get the options
  $rw_settings = get_option("rw_widget_option");
  
  //If number of attachments to be displayed is less than 1 make it default to 8
  if ($rw_settings['rw_number'] < 1) $rw_settings['rw_number'] = 8;
  
  
	echo $before_widget;
	echo $before_title . $rw_settings['rw_widget_title'] . $after_title; 
	 global $wpdb;

	   $querystr = "
		SELECT $wpdb->posts.*
		FROM $wpdb->posts
		WHERE $wpdb->posts.post_status = 'publish'
		AND ($wpdb->posts.post_type = 'post'
		 OR $wpdb->posts.post_type = 'revision')    
		ORDER BY $wpdb->posts.post_date_gmt DESC
		LIMIT 0," . $rw_settings["rw_number"];
		$pageposts = $wpdb->get_results($querystr, OBJECT);
?>

<div align="center">

<?php	          
if ($pageposts):
   foreach ($pageposts as $post):
        
		$result = array();
		preg_match_all('/<img[^>]+>/i',$post->post_content, $result); 					
		$img = array();
		preg_match_all('/(alt|title|src)=("[^"]*")/i',$result[0][0], $img);
		$img_thumb = explode("/", $img[2][1]);
		$img_path="http://";
		for($n=2; $n <= (count($img_thumb)-2); $n++){

			$img_path = $img_path . $img_thumb[$n] . "/" ;
		
		}
		
					if($result[0][0]==""){
	 					$wud = WP_PLUGIN_URL . "/recent-post-photos";
						$extension = $wud . "/default-150x150.jpg";
						continue;
					}else{
					
						$thumb_nail_m = explode('.',str_replace('"', '',$img_thumb[count($img_thumb)-1]));
						$thumb_nail_n = explode("-", $thumb_nail_m[0]); 
						$extension = $img_path . $thumb_nail_n[0] . "-" . get_option( 'thumbnail_size_w' ) . 'x' . get_option( 'thumbnail_size_h' ) . "." . $thumb_nail_m[1];
					}
		
		 ?>

         <div style="width:138px; float:left; margin-top:5px; margin-bottom:5px; margin-left:5px; display:inline;" >
              <a  href="<?php echo $post->guid ?>" title="<?php echo $post->post_title; ?>">
                <img src="<?php echo $extension; ?>" alt="<?php echo $post->post_title; ?>" title="<?php echo $post->post_title; ?>" height="100" width="100" />                
              </a>   
             <div>
              <a href="<?php echo $post->guid ?>" title="Permanent Link to <?php echo $post->post_title; ?>">
              <strong><?php echo substr($post->post_title, 0,12); ?>...</strong></a>       
             </div>
        </div>                               
       

  <?php endforeach; 
		 endif; 
	?>
    </div>
 <?php   
	  echo $after_widget.'<div style="margin-top:5px; width:100%; height:20px; float:left;"></div>';
}


/* Function for administration of the widget */
function rp_widget_Admin() {
  $rw_settings = get_option("rw_widget_option");
	// check if options have been updated
	if (isset($_POST['update_rp_widget'])) {
		$rw_settings['rw_widget_title']= strip_tags(stripslashes($_POST['rw_widget_title']));
    	$rw_settings['rw_number'] = strip_tags(stripslashes($_POST['rw_number']));   
		update_option("rw_widget_option",$rw_settings);
	}
	echo '<p>
	      <label for="rp_widget_title"><strong>Title:</strong>
          <input  id="rw_widget_title" tabindex="1" name="rw_widget_title" type="text" size="15" value="'.$rw_settings['rw_widget_title'].'" />
        </label><br />
        <label for="rp_number"><strong>Number of Posts:</strong>
          <input  id="rw_number" name="rw_number" type="text" tabindex="2" size="3" value="'.$rw_settings['rw_number'].'" />
        </label><br /> 
		  
          See <a  target="_blank" href="';
		 	echo WP_PLUGIN_URL;
 			echo '/recent-photos/readme.txt">Readme.txt</a> for Details.
          <br />
        </label><br />
      </p>';
	echo '<input type="hidden" id="update_rp_widget" name="update_rp_widget" value="1" />';
}


register_sidebar_widget('Recent Post Photos', 'rp_widget');
register_widget_control('Recent Post Photos', 'rp_widget_Admin');
?>