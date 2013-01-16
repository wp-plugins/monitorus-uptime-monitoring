<?php
class WPMUC_DataCacher
{
    private $model = null;
    private $option_last_cleanup_date = 'wpmuc_last_cleanup_date';

    /**
     * @var Cleanup period in seconds
     */
    private $default_cleanup_period = 86400;

    public function __construct($loader_class_name)
    {
        $this->model = $loader_class_name::getModel('cached-data');
    }

    /**
     * Cache api request
     *
     * @param $server_url
     * @param $action
     * @param $args
     * @param $results
     * @return bool
     */
    public function cacheData($params, $data_to_cache)
    {
        return $this->model->cacheData($params, $data_to_cache);
    }

    /**
     * Get cached request results
     *
     * @param $server_url
     * @param $action
     * @param $args
     * @return bool
     */
    public function getCachedData($params)
    {
        return $this->model->getCachedData($params);
    }

    /**
     * Clean cache and optimize the cache table after records deleting
     */
    public function cleanCachePeriodically(int $period = 0)
    {
        if($period == 0)
        {
            $cleanup_period = $this->default_cleanup_period;
        }
        else
        {
            $cleanup_period = $period;
        }

        $last_cleanup_date = get_transient($this->option_last_cleanup_date);

        if(!$last_cleanup_date)
        {
            // clean up the cache
            $this->model->cleanCache();

            set_transient($this->option_last_cleanup_date, time(), $cleanup_period);
            $this->setLastCleanupDate();
        }
    }
	
	/**
	* Immediately clean cache
	*/
	public function cleanCache()
	{
		return $this->model->cleanCache();
	}

    /**
     * Return last cleanup date
     *
     * @return mixed
     */
    public function getLastCleanupDate()
    {
        return get_option($this->option_last_cleanup_date);
    }

    /**
     * Set last cleanup date
     *
     * @return bool
     */
    private function setLastCleanupDate()
    {
        return update_option($this->option_last_cleanup_date, time());
    }
}
?>