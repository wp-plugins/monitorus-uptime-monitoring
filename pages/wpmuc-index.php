<?php
if (!extension_loaded('curl'))
{
    echo '<div class="wpmuc message"><p>';
    echo __('PHP Curl extension need to be installed for correct work of the plugin.<br>', 'monitor-us-client');
    echo __('Please ask your server administrator to install it.', 'monitor-us-client');
    echo '</p></div>';
	return false;
}
$result = '';
$result_msg = ''; 
$isLogedIn = false;
$signedUp = true;
if(array_key_exists('result',$_GET))
{
    $result = $_GET['result'];
    $id = $_GET['id'];
    $name = $_GET['name'];
   switch($result)
    {
	    case 'success':
            $result_msg = __('Settings are saved', 'monitor-us-client');
			$isLogedIn = true;
            break;
        case 'apikey_failure':
            $result_msg = __('Invalid email or password.', 'monitor-us-client');
            break;
        case 'fullexist':
            $result_msg = __('Full Page Load monitor already exists for url '.$name.'.<br>', 'monitor-us-client');
            $result_msg .= __('Do you want to replace it with a monitor for your blog.&nbsp;&nbsp;<a style="cursor: pointer" onclick="wpmuc.settings.act(\''.admin_url().'admin.php?wpmuc_c=settings&wpmuc_action=replaceExistingMonitor&id='.$id.'\');">Yes</a>&nbsp;&nbsp;&nbsp;', 'monitor-us-client');
            $result_msg .= __('<a style="cursor: pointer" onclick="wpmuc.settings.act(\''.admin_url().'admin.php?wpmuc_c=settings&wpmuc_action=useExistingMonitor&id='.$id.'\');">No</a>', 'monitor-us-client');
            //$result_msg .= __('<br>If user selects no, than the fullpageload module should show a link to add the monitor in case user changes his mind later.', 'monitor-us-client');
            break;
		 case 'Email address is already in use.':
            $result_msg = __($result, 'monitor-us-client');
			$signedUp = false;
			break;
        default:
            $result_msg = __($result, 'monitor-us-client');
    }
}

// get settings
$settings_model = WPMUC_Loader::getModel('settings');
$settings = $settings_model->getSettings();
$widgetSettings = $settings_model->getWidgetSettings(); 
?>
<div class="wrap" style="height:auto;">
<h1><?php echo __('Settings','monitor-us-client'); ?></h1>
    <?php
    if($result_msg<>'')
    {
        echo '<div id = "messageBox" class="wpmuc message"><p>'.$result_msg.'</p></div>';
    }
	else
	{
	    echo '<div id = "messageBox" class="wpmuc message hidden"></div>';
	}
    ?>
    <h2 class="wpmuc h2"><?php echo __('Monitor.US account information','monitor-us-client'); ?></h2><br>
   <form onkeypress = "return wpmuc.settings.preventSubmit(event)" action="<?php echo admin_url().'admin.php?wpmuc_c=settings&wpmuc_action=save';?>" method="post" name="signin_form" onsubmit="return wpmuc.settings.validate_form()">
        <table id="login_table">
            <tr>
			<?php if($isLogedIn):?>
                <td>
					<?php echo __( 'Email:', 'monitor-us-client' );?></td>
                <td id = 'emailTd'>
					<span style="margin-left: 5px; margin-right: 1px; width: 250px; margin-top: 1px; margin-bottom: 1px;"><?php echo $settings['email']?></span>
                </td>
			<?php else: ?> 
				<td>
					<?php echo __( 'Email', 'monitor-us-client' );?></td>
                <td id = 'emailTd'>
					<input style="margin-left: 5px; margin-right: 1px; width: 250px; margin-top: 1px; margin-bottom: 1px;" type="text" name="settings[email]" value="<?php echo $settings['email']?>">
                </td>
			<?php endif;?>
			</tr>
            <?php if(!$isLogedIn):?>
			<tr>
                <td><?php echo __( 'Password', 'monitor-us-client' );?></td>
                <td id = 'passTd'>
				<?php if($isLogedIn):?>
                    <div id="pass_field"><a style="margin-left: 5px" href="#" id="pass_link" onclick="wpmuc.settings.show_pass_field()"><?php echo __('Enter new password','monitor-us-client');?></a></div>
                <?php else: ?>
                    <input style="margin-left: 5px; margin-right: 1px; width: 250px; margin-top: 1px; margin-bottom: 1px;" type="password" name="settings[pass]" value="">
                <?php endif;?>
				</td>
		   </tr> 
		    <?php else: ?>
			<tr id = 'passTr' class = 'hidden'>
                <td><?php echo __( 'Password', 'monitor-us-client' );?></td>
                <td>
				<?php if($isLogedIn):?>
                    <div id="pass_field"><a style="margin-left: 5px" href="#" id="pass_link" onclick="wpmuc.settings.show_pass_field()"><?php echo __('Enter new password','monitor-us-client');?></a></div>
                <?php else: ?>
                    <input style="margin-left: 5px; margin-right: 1px; width: 250px; margin-top: 1px; margin-bottom: 1px;" type="password" name="settings[pass]" value="">
                <?php endif;?>
				</td>
		   </tr> 
			<?php endif;?>

            <tr class="hidden">
                <td><?php echo __( 'First name', 'monitor-us-client' );?></td>
                <td><input style="margin-left: 5px; margin-right: 1px; width: 250px; margin-top: 1px; margin-bottom: 1px;" type="text" name="settings[firstName]" ></td>
            </tr>
            <tr class="hidden">
               <td><?php echo __( 'Last name', 'monitor-us-client' );?></td>
               <td><input style="margin-left: 5px; margin-right: 1px; width: 250px; margin-top: 1px; margin-bottom: 1px;" type="text" name="settings[lastName]" ></td>
            </tr>
        </table>
        <br>
        <input type="hidden" name="settings[below]" value="1" />
        <input type="hidden" name="settings[signup]" id = 'isSignUp' value=false />
       	<span id = 'formButtons'>	
		<?php if($isLogedIn):?>
             <input type="button" onclick = "wpmuc.settings.showSigninForm()" value="<?php echo __('Change account', 'monitor-us-client') ?>" class="button-primary" />
        <?php else: ?>
             <input type="submit" value="<?php echo __('Sign in', 'monitor-us-client') ?>" class="button-primary" />
        <?php endif;?>
		</span>
		<?php if(!$isLogedIn):?>
			<p id="signup_link"><?php echo __( "Don't have an account? ", 'monitor-us-client' );?><a onclick="wpmuc.settings.showSignupForm()"><?php echo __( "Get Started now! It's totally Free!", 'monitor-us-client' );?></a></p>
		<?php else: ?>       
			<p class = 'hidden' id="signup_link"><?php echo __( "Don't have an account? ", 'monitor-us-client' );?><a onclick="wpmuc.settings.showSignupForm()"><?php echo __( "Get Started now! It's totally Free!", 'monitor-us-client' );?></a></p>
		<?php endif;?>
		<span id="signin_link" style="padding-left: 15px;" class="hidden"><a onclick="wpmuc.settings.showSigninForm()"><?php echo __( "Sign in", 'monitor-us-client' );?></a></span>
		<input type="submit" id='signup_button' onclick="wpmuc.settings.signup()" class="button-primary hidden" value="<?php echo __( "Submit", 'monitor-us-client' );?>"/>
     </form><br>
	 <?php if(!$signedUp) echo "<script>wpmuc.settings.showSignupForm()</script>";?>
<form id="chartSettingsForm" action="<?php echo admin_url().'admin.php?wpmuc_c=frontendChart&wpmuc_action=save';?>" method="post">
        <br>
        <h2 class="wpmuc h2"><?php echo __('Frontend widgets settings', 'monitor-us-client' );?></h2>
        <div style="margin-top: 20px;">
			<table id='chart_settings_table'>
				<tbody>
					<tr id="row_0">
						<td><input type="checkbox" name="fixExternalLocation" value="1" onchange="wpmuc.settings.changeChartSettings(this, 0)" <?php if($widgetSettings['external']['location'] != 'all') echo 'checked' ?> />
						</td>
						<td><label><?php echo __('Fix location for external monitor', 'monitor-us-client' );?></label>
						</td>
						<td>
							<select name="wpmuc_widgets[external][location]" <?php if($widgetSettings['external']['location'] == 'all') echo 'class="hidden"' ?> id="select_0">
								<option value="US" <?php if($widgetSettings['external']['location'] == 'US') echo 'selected' ?> >US</option>
								<option value="EU" <?php if($widgetSettings['external']['location'] == 'EU') echo 'selected' ?> >EU</option>
							</select>
						</td>
					</tr>
					<tr id="row_1">
						<td><input type="checkbox" name="fixExternalView" value="1" onchange="wpmuc.settings.changeChartSettings(this, 1)" <?php if($widgetSettings['external']['view'] != 'all') echo 'checked' ?>/>
						</td>
						<td><label><?php echo __('Fix view for external monitor', 'monitor-us-client' );?></label>
						</td>
						<td>
							<select name="wpmuc_widgets[external][view]" <?php if($widgetSettings['external']['view'] == 'all') echo 'class="hidden"' ?> id="select_1" >
								<option value="0" <?php if($widgetSettings['external']['view'] == '0') echo 'selected' ?> >Chart</option>
								<option value="1" <?php if($widgetSettings['external']['view'] == '1') echo 'selected' ?> >Table</option>
							</select>
						</td>
					</tr>
					<tr id="row_2">
						<td><input type="checkbox" name="fixFullLocation" value="1" onchange="wpmuc.settings.changeChartSettings(this, 2)" <?php if($widgetSettings['full-page']['location'] != 'all') echo 'checked' ?>/>
						</td>
						<td><label><?php echo __('Fix location for full page load monitor', 'monitor-us-client' );?></label>
						</td>
						<td>
							<select name="wpmuc_widgets[full-page][location]" <?php if($widgetSettings['full-page']['location'] == 'all') echo 'class="hidden"' ?> id="select_2">
								<option value="US" <?php if($widgetSettings['full-page']['location'] == 'US') echo 'selected' ?> >US</option>
								<option value="EU" <?php if($widgetSettings['full-page']['location'] == 'EU') echo 'selected' ?> >EU</option>
							</select>
						</td>
					</tr>
					<tr id="row_3">
						<td><input type="checkbox" name="fixFullView" value="1" onchange="wpmuc.settings.changeChartSettings(this, 3)" <?php if($widgetSettings['full-page']['view'] != 'all') echo 'checked' ?> />
						</td>
						<td><label><?php echo __('Fix view for full page load monitor', 'monitor-us-client' );?></label>
						</td>
						<td>
							<select name="wpmuc_widgets[full-page][view]" <?php if($widgetSettings['full-page']['view'] == 'all') echo 'class="hidden"' ?> id="select_3">
								<option value="0" <?php if($widgetSettings['full-page']['view'] == '0') echo 'selected' ?> >Chart</option>
								<option value="1" <?php if($widgetSettings['full-page']['view'] == '1') echo 'selected' ?> >Table</option>
								<option value="2" <?php if($widgetSettings['full-page']['view'] == '2') echo 'selected' ?> >Pie</option>
								<option value="3" <?php if($widgetSettings['full-page']['view'] == '3') echo 'selected' ?> >Time</option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	<input type="submit" style="margin-top: 25px;" value="<?php echo __('Save changes', 'monitor-us-client'); ?>" class="button-primary" />
    </form>
    <br><br>
    <form action="<?php echo admin_url().'admin.php?wpmuc_c=settings&wpmuc_action=saveBadges';?>" method="post">
        <br>
        <h2 class="wpmuc h2"><?php echo __('Choose a badge to show in your blog', 'monitor-us-client' );?></h2>
            <ul >
            <?php
            $arrayBadges = WPMUC_Loader::badgesBelow();
           
            $i=1;
            foreach ($arrayBadges as $badges) {
                $badges = preg_replace("/\<a(.*)\>(.*)\<\/a\>/iU", "$2", $badges);
                if ($settings['below']==$i)
                {
                    $isChecked = 'checked="checked"';
                }else
                {
                    $isChecked = '';
                }
                if ($settings['below'] == 0 && $i == 1)
                {
                    $isChecked = 'checked="checked"';
                }
                
                echo "<li style='display:inline; width: 100%;padding-right:25px;'><input id='below".$i."' type='radio' name='settings[below]' value='".$i."' ".$isChecked.">";
                echo "<label for='below".$i."''>$badges</label></li>";
                $i++;
            }

            ?>
               <li style='display:inline; width: 100%;padding-right:25px;'><input type="radio" id ="belowNo" name="settings[below]" value='0'>
                    <label for="belowNo"><?php echo __('None', 'monitor-us-client' );?></label><li>   
                </ul>
        <br>
        <input type="submit" value="<?php echo __('Save changes', 'monitor-us-client'); ?>" class="button-primary" />
    </form>
    <br><br>

</div>