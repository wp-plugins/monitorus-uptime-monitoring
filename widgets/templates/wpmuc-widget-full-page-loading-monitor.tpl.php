
<?php
$settings_model = WPMUC_Loader::getModel('settings');
$settings = $settings_model->getWidgetSettings('full-page'); 
$location = $settings['location'];
$view = $settings['view'];
if (is_admin())
{
    $height = '400px';
    $heightf = 400;
	$width = '100%';
    ?>
<table width="100%" id="contanier" style="height: 32px" >
<tr>
<td>
<div id="wd">
    <div id="fullpage-selector" class="wpmuc monitors-icons backend">

        <?php }else
            {
			$heightf = WPMUC_MainClass::getWidgetHeight('full-page');
			$height = $heightf.'px';
			
			$width = WPMUC_MainClass::getWidgetWidth('full-page').'px';
            ?>
                <div id="con_width" style="width:100%;height: 0px"></div>
        <table  style="width:<?php echo $width ?>;" id="contanier"  >
<tr>
<td <?php if(!is_admin()) { echo 'style = "border-bottom: 1px #eee solid;"'; }?>>
<div id="wd">
            <div id="fullpage-selector" class="wpmuc monitors-icons backend" <?php if(!is_admin()) { echo 'style = "padding: 0 !important;"'; }?>>
        <?php
    } if($view == 'all' || is_admin()){
        ?>
   <span>
        <div id='fullpage-chart' title="Full page loading chart" onclick="swith('0');" style="width:16px;background-image: url('<?php echo WPMUC_IMAGES_URL."/icons16x16.gif" ?>');" class="wpmuc chart"></div>
    </span>
    <span>
        <div id='fullpage-table' title="Full page loading table" onclick="swith('1');" style="width:16px;background-image: url('<?php echo WPMUC_IMAGES_URL."/icons16x16.gif" ?>');" class="wpmuc table"></div>
    </span>
    <span>
        <div  id='fullpage-pie' title="Last check" onclick="swith('2');" style="width:16px;background-image: url('<?php echo WPMUC_IMAGES_URL."/icons16x16.gif" ?>');" class="wpmuc pie"></div>
    </span>
    <span>
        <div id='fullpage-time' title="Browser page load time" onclick="swith('3');" style="width:16px;background-image: url('<?php echo WPMUC_IMAGES_URL."/icons16x16.gif" ?>');" class="wpmuc time"></div>
    </span>
    <br>
<?php } ?>
</div>
</div>
</td>
<td <?php if(!is_admin()) { echo 'style = "border-bottom: 1px #eee solid; vertical-align:top;"'; }?> >
 <?php  if($location == 'all' || is_admin()){ ?>  
	<div align="right" id='highchart-location'  style="display: none;<?php if(!is_admin()) { echo ' margin-top:-3px;'; }?>"></div>
    <div align="right" id='highchart-location-pie'  style="display: none;<?php if(!is_admin()) { echo ' margin-top:-3px;'; }?>"></div>
 <?php } ?>  
</td>
</tr>
</table>
<div id="highchart-table-full-window" closed="false" class="wpmuc width">
</div>

<div id="highchart-container-full" style="margin-top: 2px; height: <?php echo $height ?>; width:<?php echo $width ?>" > </div>
<table id='tb-time' style="width:<?php echo $width ?>" >
    <tr><td><div align="center" id="highchart-time-full" style="display: none;height: <?php echo $height ?>"> </div></td></tr>
</table>


<table id='tb-pie'  style="width:<?php echo $width ?>" >
    <tr ><td><div id="highchart-pie-full"  style="display: none;height: <?php echo $height ?>"> </div></td></tr>
</table>

<div  id="highchart-table-full-cont"  style="width:<?php echo $width ?>; margin-top: 2px; display: none;height: <?php echo $height ?>">
    <div id="highchart-table-full"  style="width:100%; height: <?php echo $height ?>" > </div>
</div>


<script language="javascript">
netDataClick=false;
var jq = jQuery.noConflict();
jq(document).ready(function()
{
<?php if(is_admin())
{ echo "
  jq.post('". admin_url() . "admin.php?wpmuc_c=monitors&wpmuc_action=getMonitorData&type=full-page&view=chart&offset=0', function (result) {
            var data = jq.parseJSON(get_muc_full_response(result));
      
            
            if (data.msg == 'no')
                {
                    jq('#contanier').css('display', 'none');
                    addChart(data.exist,data.newchart);
                    
                } else
                    {"; } ?>
                           display();
                  <?php if(is_admin()) echo "}
});"; ?>
        
});

    jq(window).resize(function () {

	setTimeout(function(){ resizeFull()} , 1000);

    });

 function display()
 {
     <?php
	 if(!is_admin()){
	 switch($view){
	 case 'all': echo 'requestData_full();DrawFullTable();';break;
	 case '0': echo 'requestData_full();';break;
	 case '1': echo 'DrawFullTable();';break;
	 default: echo 'requestData_full();';break;
	 }} else echo 'requestData_full();DrawFullTable();';
	 ?>

                            $=jq.noConflict();
                            easyloader.theme='gray';
                            jq('#dashboard_widget_full .handlediv').bind('click', function () {
                                setTimeout(function () {
                                if (jq('#highchart-table-full-cont div').attr('class').trim()=='panel datagrid')
                                    {
                                        jq('#highchart-table-full').datagrid('resize')
                                    }
                                }, 100);
                            });
                            var d = new Date()
                            var n = d.getTimezoneOffset();

                        <?php if (is_admin())
                        {
                            echo "swith('0');";
                        }
                        else
                        {
                            if($view == 'all') echo "swith('2');";
							else echo "swith('".$view."');";
                        }
                        ?>
 }
 

function addChart(exist,newchart)
{
        var  options = {
        chart: {
            renderTo: 'highchart-container-full',
            width:jq('#contanier').width(),
            reflow: false,
            defaultSeriesType:'pie'
        },
        loading:{
             labelStyle: {
               left:0
                    },
          style: {
            opacity: 0.5,
            textAlign: 'center'
            }  
        },
        title: {
            text: null
        },
        credits: {
            enabled: false
        }
    };
    
       chartsFull =  new Highcharts.Chart(options);
       newHei = <?php echo $heightf.';' ?>
       newSize = jq('#dashboard_widget_full').width()-20;
       chartsFull.setSize(newSize,newHei,false);
       chartsFull.redraw();
       chartsFull.showLoading('<a onclick="addFulpageChart();"><div style="cursor: pointer;">Replace '+exist+' monitor to monitor for '+newchart+'</div></a>');
    
}

function addFulpageChart()
{
    chartsFull.showLoading('Please wait...');
    jq.post('<?php echo admin_url() . 'admin.php?wpmuc_c=monitors&wpmuc_action=replaceFullPageMonitor';?>', function (result) {
        swith('0');
        jq('#contanier').css('display', 'block');
        requestData_full();
        DrawFullTable();
        jq('.highcharts-loading').css('width',newSize+'px');
        jq('.highcharts-loading').css('left','0px');
        resizeFull();
    });
}

function resizeFull() {
            newHei = <?php echo $heightf.';' ?>

            <?php if (is_admin())
            {
                echo "if (jq('#dashboard_widget_full').attr('class')=='postbox')
                    {
                         newSize = jq('#dashboard_widget_full').width()-20;
                    }
                    else
                    {
                        newSize = jq('#dashboard_widget_full').width()-20;
                        
                    }";
                    }
                    else
            {
                echo "        newSize = jq('#con_width').width();";
            }
            ?>

 
 
if (typeof chartsFull !== "undefined"){
        if (chartsFull!==''){
            
            chartsFull.setSize(newSize,newHei,false);
            chartsFull.redraw();
        }
  }      
  
if ( typeof chartsPie !== "undefined") {
        if (chartsPie !== '' ){
            chartsPie.setSize(newSize,newHei,false);
            chartsPie.redraw();
        }
}        

if ( typeof chartsTime !== "undefined") {
        if (chartsTime !== '' ){
            chartsTime.setSize(newSize,newHei,false);
            chartsTime.redraw();
        }
}

		var cls = jq('#highchart-table-full-cont div').attr('class');
        if (cls && cls.trim()=='panel datagrid')
        {
            jq('#highchart-table-full').datagrid('resize')
        }
        jq('.highcharts-loading').css('width',newSize+'px');
        jq('.highcharts-loading').css('left','0px');
}


function swith(id)
{
    switch (id) {
        case '1':
        {
            jq('#fullpage-selector span').children().removeClass('selected');
            jq('#fullpage-table').addClass('selected');
            jq('#highchart-container-full').css('display','none');

            jq('#highchart-pie-full').css('display','none');
            jq('#highchart-time-full').css('display','none');
            jq('#highchart-location').css('display','none');
            jq('#highchart-location-pie').css('display','none');
            jq('#tb-pie').css('display','none');
            jq('#tb-time').css('display','none');
            jq('#highchart-table-full-cont').css('display','block');

                var cls = jq('#highchart-table-full-cont div').attr('class');
				if (cls && cls.trim() =='panel datagrid')
                {
					jq('#highchart-table-full').datagrid('resize')
                }


            break
        }
        case '0':
        {
            jq('#fullpage-selector span').children().removeClass('selected');
            jq('#fullpage-chart').addClass('selected');
            jq('#highchart-time-full').css('display','none');
            jq('#highchart-table-full-cont').css('display','none');
            jq('#highchart-pie-full').css('display','none');
            jq('#highchart-location').css('display','none');
            jq('#highchart-location-pie').css('display','none');
            jq('#tb-pie').css('display','none');
            jq('#tb-time').css('display','none');
            jq('#highchart-container-full').css('display','block');
            break
        }
        case '2':
        {

            jq('#fullpage-selector span').children().removeClass('selected');
            jq('#fullpage-pie').addClass('selected');
            jq('#highchart-table-full-cont').css('display','none');
            jq('#highchart-container-full').css('display','none');
            jq('#highchart-time-full').css('display','none');
            jq('#highchart-location').css('display','none');
            jq('#highchart-location-pie').css('display','block');
            jq('#tb-pie').css('display','block');
            jq('#tb-time').css('display','none');
            jq('#highchart-pie-full').css('display','block');

            break
        }
        case '3':
        {
            jq('#fullpage-selector span').children().removeClass('selected');
            jq('#fullpage-time').addClass('selected');
            jq('#highchart-table-full-cont').css('display','none');
            jq('#highchart-container-full').css('display','none');
            jq('#tb-pie').css('display','none');
            jq('#tb-time').css('display','block');
            jq('#highchart-pie-full').css('display','none');
            jq('#highchart-location').css('display','block');
            jq('#highchart-location-pie').css('display','none');
            jq('#highchart-time-full').css('display','block');
            break
        }
    }
}
function get_muc_full_response(response)
{
    var expr = new RegExp(/<!--muc_json (.*) muc_json-->/m);
        return response.match(expr)[1];

}

    function requestData_full() {
        var d = new Date();
        var offset = d.getTimezoneOffset();
        jq.post('<?php $location == 'all' || is_admin() ? $loc = '' : $loc = $location; echo admin_url() . 'admin.php?wpmuc_c=monitors&wpmuc_action=getMonitorData&type=full-page&view=chart&location='.$loc.'&offset=';?>'+offset, function (result) {
            var data = jq.parseJSON(get_muc_full_response(result));
            var options = {
                chart:{
                    renderTo:'highchart-container-full',
                    width:jq('#contanier').width(),
                    type: 'line',
                    reflow: false,
                    style:{
                        margin:'0 auto'
                    }
                },
                credits:{
                    enabled:false
                },
				legend:{
				  enabled:<?php echo $location == 'all' || is_admin() ? 'true' : 'false';?>
				},
                xAxis:{
                    type:'datetime',

                    startOnTick:false,
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
                title:{
                    text:null
                },
                plotOptions:{
                    series:{
                        lineWidth:1,
                        marker:{
                            radius:2,
                            states:{
                                hover:{
                                    enabled:true
                                }
                            }
                        },
                        cursor:'pointer',
                        point:{
                            events:{
                                click:function () {
                                    DrawStepNetWindow(this.options.id, this.options.year, this.options.month, this.options.day);
                                }

                            }
                        }
                    }
                },
                yAxis:{

                    minPadding: 0.01,
                    min: 0,
                    title:{
                        text:'ms'
                    },
                    
                    startOnTick: false
                    <?php if (!is_admin()){ echo ",
                    tickPixelInterval: 50,
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
                    }";} ?> 
                },
                tooltip:{
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
                series:[]
            };


            if (typeof data[0] !== "undefined") {
                    if (typeof data[0].name !== "undefined") {
                        <?php
						if(!is_admin()){
						switch($view){
						case 'all': echo 'requestDataTime(data[0].name);requestDataPie(data[0].name);'; break;
						case '2': echo 'requestDataPie(data[0].name);'; break;
						case '3': echo 'requestDataTime(data[0].name);'; break;
						}} else echo 'requestDataTime(data[0].name);requestDataPie(data[0].name);';
						?>
						
                        jq('#highchart-location').html('<span id="location-' + data[0].name.replace('.', '') + '" class="wpmuc location selected" onclick="requestDataTime(\'' + data[0].name + '\')" >' + data[0].name + '</span>&nbsp;');
                        jq('#highchart-location-pie').html('<span id="locationpie-' + data[0].name.replace('.', '') + '" class="wpmuc location selected" onclick="requestDataPie(\'' + data[0].name + '\')" >' + data[0].name + '</span>&nbsp;');
                }

                if (typeof data[1] !== "undefined") {
                    jq('#highchart-location').append('<span id="location-' + data[1].name.replace('.', '') + '" class="wpmuc location" onclick="requestDataTime(\'' + data[1].name + '\')" >' + data[1].name + '</span>&nbsp; ');
                    jq('#highchart-location-pie').append('<span id="locationpie-' + data[1].name.replace('.', '') + '" class="wpmuc location" onclick="requestDataPie(\'' + data[1].name + '\')" >' + data[1].name + '</span>&nbsp; ');

                }
                if (typeof data[0] == "undefined") {

                        options.series = '';
                        chartsFull = new Highcharts.Chart(options);
                        chartsFull.showLoading('No data to display');

                }
                else {
                    options.series = data;
                    chartsFull=new Highcharts.Chart(options);
                }

            } else {

            <?php if (!is_admin())
            {
                echo "swith('0');";
            }
            ?>

                jq('#contanier').css('display', 'none');
                options.series = '';
                chartsFull = new Highcharts.Chart(options);
                chartsFull.showLoading('No data to display');
                <?php 
                if(is_admin()){
                echo "newSize = jq('#dashboard_widget_full').width()-20;
                    newHei = ". $heightf.";
                    chartsFull.setSize(newSize,newHei,false);";
                        
                    } ?>

            }
        });


    }

function DrawFullTable()
{
    easyloader.load('datagrid',function(){
    jq('#highchart-table-full').datagrid({  
                fitColumns:true,
                fit: true,
                sortName:'time',
                sortOrder:'desc',
                onOpen: function(){
                    jq('#highchart-table-full').datagrid('loading');
                    jq.ajax({
                        url: '<?php $location == 'all' || is_admin() ? $loc = '' : $loc = $location; echo admin_url().'admin.php?wpmuc_c=monitors&wpmuc_action=getMonitorData&type=full-page&view=table&location='.$loc;?>',
                        type: 'POST',
                        success: function(result) {
                            datafull = jq.parseJSON(get_muc_full_response(result));
                            if(jq('#highchart-table-full') !== null){
                                if(typeof datafull !== "undefined"){
                                    if (datafull!== null && datafull !==''){
                                        jq('#highchart-table-full').datagrid('loadData',datafull);
                                    }
                                }
                            }
                        }
                    });
                },
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
            });
    }

    function DrawStepNetWindow(id,year,month,day)
    {

        if (netDataClick == false){
            netDataClick=true;
        jq.post('<?php echo admin_url().'admin.php?wpmuc_c=monitors&wpmuc_action=getStepNetData&type=stepnet&view=table';?>'+'&resultId='+id+'&year='+year+'&month='+month+'&day='+day, function(result){

            var data = jq.parseJSON(get_muc_full_response(result));
            if (data == null)
            {
                easyloader.load('messager',function(){
                    jq.messager.alert('Warning','No data to display');
                    netDataClick=false;
            }
                    );
            }else {


                
                easyloader.load('window',function(){
                    jq('#highchart-table-full-window').window({
                        width:950,
                        left:  jq(window).width()/2 -475,
                        top: jq(window).height()/2 - 202 + jq(window).scrollTop(),
                        height: 405,
                        minimizable : false,
                        zIndex:10000,
                        maximizable: false,
                        closable : true,
                        collapsible : false,
                        doSize: true,
                        position: ["top"],
                        title : 'Full Page Load monitor details',
                        modal:true,
                        content:  '<div id="highchart-table-full-window-table"></div>',
                        onClose: function (){ netDataClick=false;},
                        onOpen: function ()
                        {
                            jq('div .panel .window').css('z-index','10000');
                                easyloader.load('datagrid',function(){
                                jq('#highchart-table-full-window-table').datagrid({
                                    fitColumns:true,
                                    maximized: true,
				    remoteSort:false,
                                    onBeforeLoad:function(){
                                        jq('#highchart-table-full-window-table').datagrid('loading');
                                    },
                                columns:[[
                                        {field:'objectName',title:'<div align="center">Object Name</div>',align:'left',fitColumns : true,width:200,sortable:true},
                                        {field:'returnCode',title:'Return <br>Code',align: 'center',fitColumns : true,width:50,sortable:true},
                                        {field:'totalEnd',title:'Total<br> (end-to-end ms)',align:'center',fitColumns : true,width:50,sortable:true},
                                        {field:'dnsLookup',title:'DNS lookup <br>(ms)',align:'center',fitColumns : true,width:50,sortable:true},
                                        {field:'connectionTime',title:'Connection <br>time (ms)',align:'center',fitColumns : true,width:50,sortable:true},
                                        {field:'firstByte',title:'1st <br>byte (ms)',align:'center',fitColumns : true,width:50,sortable:true},
                                        {field:'contentDownload',title:'Content <br>download (ms)',align:'center',fitColumns : true,width:50,sortable:true},
                                        {field:'numberOfBytes',title:'Number of<br> bytes (b)',align:'center',fitColumns : true,width:50,sortable:true}
                                    ]]
                                });
                                });
                            if (jq('#highchart-table-full-window div').attr('class').trim()=='panel datagrid')
                            {
                                jq('#highchart-table-full-window-table').datagrid('loadData',data);
                            }
                        }
                    });

                });
            }

        });

    }
}
function requestDataPie(location)
{
      var  options = {
        chart: {
            renderTo: 'highchart-pie-full',
            width:jq('#contanier').width(),
            reflow: false,
            defaultSeriesType:'pie'
        },
        title: {
            text: null
        },
        credits: {
            enabled: false
        }
    };
    chartsPie =  new Highcharts.Chart(options);
    chartsPie.showLoading('Please wait...');
    
        jq('#highchart-location-pie').children().removeClass('selected');
        jq('#locationpie-'+location.replace('.','')).addClass('selected');
    
    jq.post('<?php echo admin_url().'admin.php?wpmuc_c=monitors&wpmuc_action=getMonitorData&view=pie&type=stepnet&location=';?>'+location, function(result){
        var data = jq.parseJSON(get_muc_full_response(result));


        var options = {
        chart: {
            renderTo: 'highchart-pie-full',
            width:jq('#contanier').width(),
            plotShadow: false,
            reflow: false,
            style: {
                margin: '0 auto'
            }
        },

            loading:{
                style: {
                    textAlign: 'center'
                }
            },
        credits: {
            enabled: false
        },
        title: {
            text: null
        },
        tooltip: {
            formatter: function() {
                <?php if (is_admin()){ echo "return '<b>'+ this.point.name +'</b>: '+ this.point.y +' ms';";}
                else
                {
                    echo "return '<b>'+ this.point.name +'</b><br>'+ this.point.y +' ms';";
                }?>
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        series: []
    }

        if (data.data=='')
        {
            options.series='';
            chartsPie =  new Highcharts.Chart(options);
            chartsPie.showLoading('No data to display');
        }
        else
        {
            options.series[0] = data;
            chartsPie =  new Highcharts.Chart(options);
            chartsPie.hideLoading();
        }

});

}

function requestDataTime(location)
{
    var request_url ='<?php echo admin_url().'admin.php?wpmuc_c=monitors&wpmuc_action=getMonitorData&view=time&type=time&location=';?>'+location;
    jq('#highchart-location').children().removeClass('selected');

    jq('#location-'+location.replace('.','')).addClass('selected');
    
     var  options = {
        chart: {
                renderTo: 'highchart-time-full',
            reflow: false,
                width:jq('#contanier').width()
               },


        title: {
                text:null
        },
        credits: {
            enabled: false
        }
        };
    chartsTime =  new Highcharts.Chart(options);
    chartsTime.showLoading('Please wait...');
    
    jq.post(request_url, function(result){
        var data = jq.parseJSON(get_muc_full_response(result));

    var options = {
        chart: {
            renderTo: 'highchart-time-full',
            type: 'area',
            reflow: false,
            defaultSeriesType:'area',
            width:jq('#contanier').width()
        },
        credits: {
            enabled: false
        },
        title: {
            text:null
        },

        xAxis: {
            type: 'datetime',
            tickmarkPlacement: 'on',
            title: {
                enabled: false
            },
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
        yAxis: {
            title: {
                text: 'ms'
            }
        },
        tooltip: {
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
        plotOptions: {
            area: {
                stacking: 'normal',
                lineColor: '#666666',
                lineWidth: 1,
                marker: {
                    lineWidth: 1,
                    lineColor: '#666666'
                }
            }
        },
        series: []
    }
        if (jq.trim(data[0].data.toString())=='')
        {
            options.series='';
            chartsTime =  new Highcharts.Chart(options);
            chartsTime.showLoading('No data to display');
        }
        else
        {
            options.series = data;
            chartsTime =  new Highcharts.Chart(options);
            chartsTime.hideLoading();
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