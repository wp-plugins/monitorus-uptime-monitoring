<?php
if (is_admin())
{
    $heigth='400px';
    $heigthe = 400;
	
	$width='100%';
}
    else
    {
        $heigthe = WPMUC_MainClass::getWidgetHeight('external');
		$heigth = $heigthe.'px';
		
		$width = WPMUC_MainClass::getWidgetWidth('external').'px';
    }
?>
<div id="con_widthex" style="padding-bottom: 7px; width:<?php echo $width ?>; ">
<?php
if (is_admin())
{ ?>
    <div id="external-selector"  class="wpmuc monitors-icons backend">

        <?php }else
{	$settings_model = WPMUC_Loader::getModel('settings');
    $settings = $settings_model->getWidgetSettings('external'); 
	$location = $settings['location'];
	$view = $settings['view'];
?>
        <div id="external-selector"  class="wpmuc monitors-icons frontend">
    <?php
}	if($view == 'all' || is_admin()){
?>
    <span>
        <div id="external-chart" title="External statistics chart" onclick="swith_ext('0');" style="width:16px;background-image: url('<?php echo WPMUC_IMAGES_URL."/icons16x16.gif" ?>');" class="wpmuc chart selected"></div>
    </span>
    <span>
        <div id="external-table" title="External statistics table" onclick="swith_ext('1');" style="width:16px;background-image: url('<?php echo WPMUC_IMAGES_URL."/icons16x16.gif" ?>');" class="wpmuc table"></div>
    </span>
    <br>
<?php
}
?>
</div>
</div>

<div id="highchart-container" style="align:center;height: <?php echo $heigth ?>; width:<?php echo $width ?>"> </div>
<div id="highchart-table-external-cont" style="display:none; height:<?php echo $heigth ?>; width:<?php echo $width ?>">
    <div id="highchart-table-external" class="wpmuc width" style="height:<?php echo $heigth ?>;"> </div>
</div>

<script language="javascript">
    var jq = jQuery.noConflict();
	charts = '';
    jq(document).ready(function()
    {
        jq('#dashboard_widget_ext .handlediv').bind('click', function() {
            setTimeout(function(){
                if (jq('#highchart-table-external-cont div').attr('class').trim()=='panel datagrid')
                {
                    jq('#highchart-table-external').datagrid('resize')
                }
            },100);
        });
        
        <?php
		if(!is_admin()){
		switch($view){
		case 'all' : echo 'requestData(); DrawExTable();'; break;
		case '0' : echo 'requestData();'; break;
		case '1' : echo 'DrawExTable();'; break;
		}} else echo 'requestData(); DrawExTable();';
		?>
		
        swith_ext('<?php echo $view == 'all' || is_admin() ? 0 : $view;?>');
      
    });

 
     jq(window).resize(function () {

	setTimeout(function(){ resizeExt()} , 1000);

    });


    function resizeExt(){
            newHei = <?php echo $heigthe.';' ?>
            <?php if (is_admin())
            {

            echo "if (jq('#dashboard_widget_ext').attr('class').trim()=='postbox')
                        {
                            newSize = jq('#dashboard_widget_ext').width()-20;
                        }else
                        {
                            newSize = jq('#dashboard_widget_ext').width()-20;
                            
                        }";
            }else
            {
                echo "newSize = jq('#con_widthex').width();";
            }
            ?>
                    
        if (typeof charts !== "undefined"){
            if (charts!==''){
                charts.setSize(newSize,newHei,false);
                charts.redraw();
            }
        }
            jq('#highchart-container div.highcharts-loading').css('width',newSize+'px');


            if (jq('#highchart-table-external-cont div').attr('class').trim()=='panel datagrid')
            {
                jq('#highchart-table-external').datagrid('resize')
            }


    }

    function swith_ext(id)
    {
        switch (id)
        {
            case '0' :
            {
                jq('#highchart-table-external-cont').css('display','none');
                jq('#highchart-container').css('display','block');
                jq('#external-selector span').children().removeClass('selected');
                jq('#external-chart').addClass('selected');
				break
            }
            case '1' :
            {
                jq('#highchart-container').css('display','none');
                jq('#highchart-table-external-cont').css('display','block');
                jq('#external-selector span').children().removeClass('selected');
                jq('#external-table').addClass('selected');

                    if (jq('#highchart-table-external-cont div').attr('class').trim()=='panel datagrid')
                    {
                        jq('#highchart-table-external').datagrid('resize')
                    }

				break;
            }
        }
    }

    function get_muc_ext_response(response)
    {
        var expr = new RegExp(/<!--muc_json (.*) muc_json-->/m);
		return response.match(expr)[1];
    }

    function requestData() {

        jq.post('<?php $location == 'all' ? $loc = '' : $loc = $location; echo admin_url().'admin.php?wpmuc_c=monitors&wpmuc_action=getMonitorData&type=external&view=chart&location='.$loc; ?>', function(result){
            var data = jq.parseJSON(get_muc_ext_response(result));
            var options = {
                chart: {
                    renderTo: 'highchart-container',
                    width:jq('#external-selector').width(),
                    reflow:false,
                    type: 'line',
                    style: {
                        margin: '0 auto'
                    }
                },
                credits: {
                    enabled: false
                },
				legend:{
				  enabled:<?php echo $location == 'all' || is_admin() ? 'true' : 'false';?>
				},
                xAxis: {
                    type: 'datetime',
                    dateTimeLabelFormats: {
                        second: '%H:%M',
                        minute: '%H:%M',
                        hour: '%H:%M',
                        day: '%e. %b'
                    },
                    startOnTick: false,
                    
                    <?php if(!is_admin()){
                        echo "tickInterval: 12 * 3600 * 1000,";
                        } ?>

                    labels: {
                        formatter: function() {
                            if (Highcharts.dateFormat('%H:%M',this.value)!=='00:00')
                            {
                                return Highcharts.dateFormat('%H:%M',this.value);
                            }
                            else
                            {
                                return Highcharts.dateFormat('%e. %b',this.value);
                            }
                        }
                    }

                },
                title: {
                    text: null
                },
                plotOptions: {
                    series: {
                        lineWidth: 1,
                        marker:{
                            radius:2,
                            states:{
                                hover:{
                                    enabled:true
                                }
                            }
                        },
                        cursor:'pointer'
                    }
                },
                yAxis: {
                    minPadding: 0.01,
                    min: 0,

                    tickPixelInterval: 50,
                    title: {
                        text: 'ms'
                    },
                    labels: {
                        formatter: function() {
                            if (this.value >='1000')
                            {
                                return this.value/1000 +' k';
                            }
                            else
                            {
                                return this.value;
                            }
                        }
                    }
                    
                },
                tooltip: {
                    xDateFormat: '%Y-%m-%d %H:%M',
                    formatter: function() {
                    <?php if (is_admin()){
                      echo "  return '<b>'+ this.series.name +', '+Highcharts.dateFormat('%Y-%m-%d %H:%M',this.point.x) +', '+ this.point.y +' ms</b>';";
                    }else
                    {
                        echo "  return '<b>'+ this.series.name +'</b><br>'+Highcharts.dateFormat('%Y-%m-%d %H:%M',this.point.x) +'<br>'+ this.point.y +' ms';";
                    }
                    ?>
                    }

                },
                series: [{
                    name: '',
                    data: []
                }]

            };

            if (typeof data[0] !== "undefined")
            {
                        if (jq.trim(data[0].data.toString())!=='')
                        {
                            options.series = data;
                            charts =  new Highcharts.Chart(options);
                        }
                        else
                            {
                                jq('#external-selector').css('display','none');
                                options.series='';
                                charts =  new Highcharts.Chart(options);
                                charts.showLoading('No data to display');
                            }
             }
                else
                {
                    jq('#external-selector').css('display','none');
                    options.series='';
                    charts =  new Highcharts.Chart(options);
                    charts.showLoading('No data to display');
                }
        });


    }


function DrawExTable(){
				jq.ajax({
                        url: '<?php $location == 'all' ? $loc = '' : $loc = $location; echo admin_url().'admin.php?wpmuc_c=monitors&wpmuc_action=getMonitorData&type=external&view=table&location='.$loc;?>',
                        type: 'POST',
                        success: function(result) {
                            dataex = jq.parseJSON(get_muc_ext_response(result));
                            if(jq('#highchart-table-external') !== null){
                                if(typeof dataex !== "undefined"){
                                    if (dataex!== null && dataex !==''){
                                            easyloader.base = "<?php echo WPMUC_ASSETS_URL?>/scripts/easyui/";    // set the easyui base directory
                        easyloader.load('datagrid',function(){
                        jq('#highchart-table-external').datagrid({  
                                    fitColumns:true,
                                    fit: true,
                                    sortName:'time',
                                    sortOrder:'desc',
                                    remoteSort:false,
                                    columns:[[
                                        {field:'time',title:'Time',align:'center',sortable:true,width:100,
                                            sorter:function(a,b){
                                                a = a.split('/');
                                                b = b.split('/');
                                                if (a[2] == b[2]){
                                                    if (a[0] == b[0]){
                                                        return (a[1]>b[1]?1:-1);
                                                    } else {
                                                        return (a[0]>b[0]?1:-1);
                                                    }
                                                } else {
                                                    return (a[2]>b[2]?1:-1);
                                                }
                                            }
                                        },
                                        {field:'resp',title:'Response (ms)',align: 'center',sortable:true,width:60},
                                        {field:'status',title:'Status',align:'center',sortable:true,width:40},
                                        {field:'location',title:'Location',align:'center',sortable:true,width:80}
                                    ]]
                                });
                                jq('#highchart-table-external').datagrid('loadData',dataex);
                                });         
                                        
                                    }
                                }
                            }
                        }
                    });
    }
</script>
<?php
if (!is_admin())
{
    ?>
<br><br>
<?php } ?>