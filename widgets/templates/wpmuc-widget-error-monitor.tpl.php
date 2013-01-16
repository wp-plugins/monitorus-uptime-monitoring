<?php
if(is_admin())
{
   echo  __('<p> There is no data to display.<br> You can check monitor.us credentials  <a href="'.admin_url().'admin.php?page=monitorus-uptime-monitoring/pages/wpmuc-index.php">here. </a> </p>');
}
else
{
   echo __('<p style="text-align:center;"> No data to display </p><br>');
}