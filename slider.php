<?php ob_start();
/*
Plugin Name: Slider 
Plugin URI: 
Description: This is a unique plugin build for slider
Author: www.phpfreelance.net
Version: 1.0
Author URI: http://www.phpfreelance.net/
*/

//Begins : slide database Activation function
function activate_slider_plugin() 
{
	global $wpdb;
	
	$queryslide = " CREATE TABLE IF NOT EXISTS `wp_slide` (
	`id` BIGINT( 40 ) NOT NULL AUTO_INCREMENT ,
	`imgname` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	PRIMARY KEY ( `id` )
	) ENGINE = MYISAM";
	$wpdb->query($queryslide);
}
//End : slide database Activation function

// Call the database activation function
register_activation_hook(__FILE__,'activate_slider_plugin');

//Begins : Display menu for Buyer
add_action ( 'admin_menu', 'addAdminMenu' );


//Begins : Display menu for user
add_action ( 'admin_menu', 'addAdminMenu' );


function addAdminMenu() 
{
	//Begins : Menu Heading		
	if (function_exists ( 'add_menu_page' )) 
	{

		add_menu_page(		__('slide','slide'),
							__('slide','slide'),
							10,
							'slide',
							("slide_display") 
					);
	}
	//Ends : Menu Heading
}		
//Ends : Adding the Menu

//define a maxim size for the uploaded images in Kb
 define ("MAX_SIZE","100"); 

//This function reads the extension of the file. It is used to determine if the file  is an image by checking the extension.
 function getExtension($str) {
         $i = strrpos($str,".");
         if (!$i) { return ""; }
         $l = strlen($str) - $i;
         $ext = substr($str,$i+1,$l);
         return $ext;
 }

// function for enter values from admin slide tab
function slide_display()
{

	//insert data of slide
	if($_POST['submit'] == 'submit')
	{
// echo $_POST['submit']; exit;
		//reads the name of the file the user submitted for uploading
 		$image=$_FILES['image']['name'];
		//if it is not empty
		if ($image) 
		{
		//get the original name of the file from the clients machine
			$filename = stripslashes($_FILES['image']['name']);
		//get the extension of the file in a lower case format
			$extension = getExtension($filename);
			$extension = strtolower($extension);
		//if it is not a known extension, we will suppose it is an error and will not  upload the file,  
		//otherwise we will do more tests
		if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) 
				{
				//print error message
					echo '<h1>Unknown extension!</h1>';
					$errors=1;
				}
				else
				{
		//get the size of the image in bytes
		//$_FILES['image']['tmp_name'] is the temporary filename of the file
		//in which the uploaded file was stored on the server
		$size=filesize($_FILES['image']['tmp_name']);
		
		//compare the size with the maxim size we defined and print error if bigger
		if ($size > MAX_SIZE*1024)
		{
			echo '<h1>You have exceeded the size limit!</h1>';
			$errors=1;
		}
		
		//we will give an unique name, for example the time in unix time format
		$image_name=time().'.'.$extension;
		//the new name will be containing the full path where will be stored (images folder)
		$newname= dirname(realpath(__FILE__)).'/images/'.$image_name;
		//echo $newname;
		//we verify if the image has been uploaded, and print error instead
		$copied = copy($_FILES['image']['tmp_name'], $newname);
		if (!$copied) 
		{
			echo '<h1>Copy unsuccessfull!</h1>';
			$errors=1;
		}}}


		mysql_query('insert into wp_slide (`imgname`) values ("'.$image_name.'")');
	}
	//End:insert data of slide

	//delete data of slide
	if($_POST['submit'] == 'delete')
	{
		mysql_query('delete from wp_slide where id = "'.$_POST['id'].'"');
	}
	//End:delete data of slide

	//select data of slide
	$select = mysql_query('select * from wp_slide') or die();

 while($select_data = mysql_fetch_array($select))
	{ ?>
<div style="float:left;width:33%;">
	<form name="gsdelete" action="" method="POST" action="">
		<table>
			<tr><TD>
			<img width="100" height="100" src="<?php echo get_option('home'); ?>/wp-content/plugins/Slide_hkh/images/<?php echo $select_data['imgname']; ?>"/></TD>

			<td>
				<input type="hidden" name="id" value="<?php echo $select_data['id']; ?>"><br>
				<input type="submit" name="submit" value="delete" align="right">
			</td>
			</tr>
		</table>
	</form>
</div>
<?php }  
?>

<div style="clear:both;"></div>
<div class="wrap">
<h2>Save a Slide</h2>
	<form name="imgslide" action="" method="POST" enctype="multipart/form-data" >
		<table class="form-table">
			
		<TR><th><label>Upload your slide here :</label></th>
		<br>
		<TD><input class="widefat" type="file" name="image"></TD></TR>

		<TR><TD colspan="2"><input type="submit" name="submit" value="submit" align="right"></TD></TR>
		

		</table>
	</form>
</div>

<?php } ?>

<?php function slide_hkh()
{ 
	//select data of slide
	$select_query = mysql_query('select * from wp_slide order by id desc limit 3');
	
?>
<link rel="stylesheet" href="<?php echo get_option('home'); ?>/wp-content/plugins/Slide_hkh/sample.css" type="text/css" /> 
<script src="<?php echo get_option('home'); ?>/wp-content/plugins/Slide_hkh/jquery-1.4.4.min.js" type="text/javascript"></script> 
<script src="<?php echo get_option('home'); ?>/wp-content/plugins/Slide_hkh/jquery.orbit.min.js" type="text/javascript"></script> 

<div id="featured"> 
<?php while($slider_image = mysql_fetch_array($select_query)) { ?>
<img width="300" height="400" src="<?php echo get_option('home'); ?>/wp-content/plugins/Slide_hkh/images/<?php echo $slider_image['imgname']; ?>"/>
<?php } ?>
</div>
<script type="text/javascript"> 
$.noConflict();
jQuery(document).ready(function($) {
$('#featured').orbit({
"bullets" : true,
"animation" : "horizontal-push"
});	
});
</script> 

<?php }

 ?> 