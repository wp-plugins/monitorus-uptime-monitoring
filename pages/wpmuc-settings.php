<?php
// get settings
$settings_model = WPMUC_Loader::getModel('settings');
$settings = $settings_model->getSettings();
?>
<div class="wrap" style="height:auto;">
    <h1><?php echo __('Monitor.us uptime monitoring','monitor-us-client'); ?></h1><br />
    <h2><?php echo __('Settings','monitor-us-client'); ?></h2>
    <?php
    if($result_msg<>'')
    {
        echo '<div id="message" class="updated"><p>'.$result_msg.'</p></div>';
    }
    ?>

    <form action="<?php echo admin_url().'admin.php?wpmuc_c=settings&wpmuc_action=save';?>" method="post">
        <table>
            <tr>
                <td><?php echo __( 'User Email', 'monitor-us-client' );?></td>
                <td><input style="margin-left: 1px; margin-right: 1px; width: 250px; margin-top: 1px; margin-bottom: 1px;" type="text" name="settings[wpmuc-email]" value=""></td>
            </tr>
            <tr>
                <td><?php echo __( 'User Password', 'monitor-us-client' );?></td>
                <td><input style="margin-left: 1px; margin-right: 1px; width: 250px; margin-top: 1px; margin-bottom: 1px;" type="password" name="settings[wpmuc-pass]" value=""></td>
            </tr>
        </table>
        <input type="submit" value="<?php echo __( 'Save', 'monitor-us-client' );?>" class="button-primary" />
    </form>
</div>