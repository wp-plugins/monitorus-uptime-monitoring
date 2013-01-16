<?php
class WPMUC_Model_Monitors extends WPMUC_Model
{
    function getChartData($type, $location = '')
    {
        $monitors_obj = WPMUC_Loader::getMonitors();
        $res =  $monitors_obj->getMonitors($type);
        $dataArr = array();
        if (isset($res['error']))
        {
            $datas = new stdClass();
            return array();
        }

        if (count($res)==0)
        {
            $res = array();
        }

        foreach ($res as $graph)
        {
            $data=array();
                foreach ($graph['data'] as $graphData)
                {
                    $d = strtotime($graphData[0])*1000;
                    $datas = new stdClass();
                    $datas->id = $graphData[3];
                    $datas->x = $d;
                    $datas->y = round($graphData[1],1);
                    $datas->year = date('Y',strtotime($graphData[0]));
                    $datas->month = date('m',strtotime($graphData[0]));
                    $datas->day = date('d',strtotime($graphData[0]));
                    $data[]=$datas;
                }
            $dataArr[]=array('name'=>$graph['locationName'],'data'=>$data);
        }
		
		if($location != '')
		{
			foreach($dataArr as $key => $data)
			{
				if($data['name'] != $location)
				{
					unset($dataArr[$key]);
					$dataArr = array_values($dataArr);
					var_dump($dataArr);
				}
			}
		}
        return $dataArr;
    }

    function getStepNetData($type,$view,$resultId,$year,$month,$day)
    {
        $monitors_obj = WPMUC_Loader::getMonitors();
        $params['resultId'] = $resultId;
        $params['year'] = $year;
        $params['month'] = $month;
        $params['day'] = $day;
        $dataArr=array();
        $res = $monitors_obj->getMonitors($type, $params);
        if (isset($res['data'])) {

            $dataArr = array();
            $i=1;
            foreach ($res['data']['netContent'] as $graph) {
                $data = new stdClass();
                $key = $graph['Started']['start'] + 10;
                $data->start = $graph['Started']['start'];
                $data->objectName = $graph['URL'];
                $data->returnCode = $graph['Status'];
                $data->totalEnd = $graph['Duration'];
                $data->dnsLookup = $graph['Resolving']['elapsed'];
                $data->connectionTime = $graph['Connecting']['elapsed'];
                $data->firstByte = $graph['Receiving']['start'];
                $data->numberOfBytes = $graph['Size'];
                $data->contentDownload = $graph['Receiving']['elapsed'];
                if (!array_key_exists($key, $dataArr)) {
                    $dataArr[$key] = $data;
                } else {
                    $i++;
                    if ($dataArr[$key]->connectionTime == 0 || $dataArr[$key]->numberOfBytes !== -1) {
                        $dataArr[$key + $i] = $data;
                    } else {
                        $dataArr[0] = $data;
                    }
                }

            }
        }

        ksort($dataArr);
        foreach ($dataArr as $graph) {
            $dataOut[] = $graph;
        }
        return $dataOut;
    }


    function getTableData($type, $location='')
    {
        $monitors_obj = WPMUC_Loader::getMonitors();
        $res =  $monitors_obj->getMonitors($type);
        $data=array();
        if (count($res)==0)
        {
            $res = array();
        }
        if (!isset($res['error'])){
            foreach ($res as $series)
            {
                if (isset($series['data'])){
                        foreach ($series['data'] as $value)
                        {
                            $dataObj = new stdClass();
                            $date=date("m-d H:i", strtotime($value[0]));
                            $dataObj->time = $date;
                            $dataObj->resp = $value[1];
                            $dataObj->status = $value[2];
                            $dataObj->location = $series['locationName'];
                            $data[]=$dataObj;
                        }
                    }
                }
            }
			
		if($location != '')
		{
			for($i = 0; $i < count($data); $i++)
			{
				if($data[$i]->location != $location)
				{
					array_splice($data, $i, 1);
					$i--;
				}
			}
		}
        return $data;
    }

    function validateView($view)
    {
        $viewArray = $this->getValidViews();
        if (isset($view) && !in_array($view,$viewArray))
        {
            return false;
        }

        return $view;
    }

    function validateType($type)
    {
        $typeArray = $this->getValidTypes();
        if (isset($type) && !in_array($type,$typeArray))
        {
            return false;
        }

        return $type;
    }

    function getValidTypes()
    {
        return array('external', 'full-page','stepnet');
    }

    function getValidViews()
    {
        return array('chart', 'table','pie','time');
    }

    function getMonitorData($type,$view,$location='',$offset)
    {
        $type = $this->validateType($type);
        $view = $this->validateView($view);
        $ofsett_DB=get_option('wpmuc_offset');
        if ($offset!==''){
        if ($ofsett_DB!==$offset )
            {
                update_option('wpmuc_offset',$offset);
            }
        }
        if ($type==false && $view ==false)
        {

            return false;
        }

        switch ($view)
        {
            case 'chart':
				$data = $this->getChartData($type, $location);
                break;

            case 'table':

                $data = $this->getTableData($type, $location);
                break;

            case 'pie':

                $data = $this->getPieData($location);
                break;
            case 'time':

                $data = $this->getTimeData($location);
                break;

        }

        return json_encode($data);
    }

    function getPieData($location='EU')
    {
        $monitors_obj = WPMUC_Loader::getMonitors();

        $model = WPMUC_Loader::getModel('cached-data');
        $cachedData = $model->getCachedData(array('full-page'));
        if (!$cachedData){
            $res=$monitors_obj->getMonitors('full-page');
            $model->cacheData(array('full-page'),$res);
        }
        else
        {
            $res = $cachedData;
        }
        $params = array();
        if (!is_array($res))
        {
            $res = array();
        }
        foreach ($res as $result) {
            if ($location==$result['locationName']){
                $sizeOfFullPage = sizeof($result['data'])-1;
                $params['resultId'] = $result['data'][$sizeOfFullPage][3];
                $timeStamp=strtotime($result['data'][$sizeOfFullPage][0]);
            }
        }
        $params['year']= date('Y',$timeStamp);
        $params['month']= date('m',$timeStamp);
        $params['day']= date('d',$timeStamp);
        $stepNetResult = $model->getCachedData(array($location,$timeStamp,$params['resultId']));
        if (!$stepNetResult)
        {
            $stepNetResult = $monitors_obj->getTransactionStepNet($params);
            $model->cacheData(array($location,$timeStamp,$params['resultId']),$stepNetResult);
        }

        $dataObj = new stdClass();
        if (!is_array($stepNetResult))
        {
            $stepNetResult=array();
        }
        if(!isset($stepNetResult['error']))
		{
			foreach ($stepNetResult['data']['netContent'] as $data)
			{
				$dataObj->totalEnd += round($data['Duration']);
				$dataObj->dnsLookup += round($data['Resolving']['elapsed']);
				$dataObj->connectionTime += round($data['Connecting']['elapsed']);
				$dataObj->firstByte += round($data['Receiving']['start']);
				$dataObj->numberOfBytes += round($data['Size']);
				$dataObj->contentDownload += round($data['Receiving']['elapsed']);
			}
			$count = sizeof($stepNetResult['data']['netContent']);

			$pie = array();
			$test = new stdClass();
			$test->name = 'Total';
			$test->x = $dataObj->totalEnd;
				if ($count!=='0')
					if($dataObj->dnsLookup == 0 && $dataObj->connectionTime == 0 && $dataObj->contentDownload == 0)
					{
						$pie='';
					} else {
						$pie[]=array('DNS lookup',$dataObj->dnsLookup);
						$pie[]=array('Connection time',$dataObj->connectionTime);
						$pie[]=array('Content download',$dataObj->contentDownload);
					}
		} else $pie = '';
        return array('type'=>'pie','data'=>$pie);
    }

    function getTimeData($location='EU')
    {
        $data= $this->getChartData('full-page');
        $model = WPMUC_Loader::getModel('cached-data');
        $i=0;
        foreach ($data as $locations) {
            if ($location==$locations['name']) {
            foreach ($locations['data'] as $values) {
                $cachedData = $model->getCachedData(array($locations['name'],$values->x,$values->id));
                if (!$cachedData){
                    $timeData=$this->getStepNetData('stepnet','',$values->id,$values->year,$values->month,$values->day);
                    $model->cacheData(array($locations['name'],$values->x,$values->id),$timeData);
                }
                else
                {
                   $timeData = $cachedData;
                }
                $dns = 0;
                $time = 0;
                $contDown = 0;
                if (is_array($timeData) || !empty($timeData)){
                    foreach ($timeData as $val) {
                        $dns  += $val->dnsLookup;
                        $time += $val->connectionTime;
                        $contDown +=$val->contentDownload;
                    }
                }
                else {

                    return array(array('data'=>''));
                }

                $dnsArray[$i]['y'] = round($dns);
                $dnsArray[$i]['x'] = $values->x;
                $timeArray[$i]['y'] = round($time);
                $timeArray[$i]['x'] = $values->x;
                $contDownArray[$i]['y'] = round($contDown);
                $contDownArray[$i]['x'] = $values->x;
                $i++;
            }
        }
        }
        $result = array(
                        array('name'=>'DNS lookup','data'=>$dnsArray),
                        array('name'=>'Connection time','data'=>$timeArray),
                        array('name'=>'Content download','data'=>$contDownArray)
                        );
        return $result;

    }

    function getCachedData($type)
    {
        $model = WPMUC_Loader::getModel('cached-data');
        $data = $model->getCachedData(array($type));
        return $data;
    }
}