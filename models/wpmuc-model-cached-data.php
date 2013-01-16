<?php
/**
 * WPMUC_Model_CachedData
 *
 * Class responsible for interaction with database, it implements basic methods for caching;
 */
class WPMUC_Model_CachedData extends WPMUC_Model
{
  /**
     * Field responsible for table records caching
     * @var array
     */
    private $table_records = array();

  /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->table_name = $this->wpdb->prefix.WPMUC_DB_TABLE_CACHED_DATA;
    }

    /**
     * Create cache table
     *
     * @return mixed
     */
    private function createTable()
	{
		$q = "CREATE TABLE IF NOT EXISTS `{$this->table_name}` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `request_hash` varchar(32) NOT NULL,
          `request_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `request_data` longtext NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `request_hash` (`request_hash`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

        return $this->wpdb->query($q);
	}

    /**
     * Prepare DB
     */
    public function prepareDB()
    {
        $this->createTable();
    }

    /**
     * Cache request
     *
     * @param $server_url
     * @param $request_name
     * @param $request_params
     * @param $request_data
     * @return mixed
     */
    public function cacheData($params, $data_to_cache)
    {
        $hash = $this->formHash($params);

        $serialized_data = $this->serializeRequestData($data_to_cache);
        return $this->insertRecord($hash, $serialized_data);
    }

    /**
     * Serialize request data
     *
     * @param $request_data
     * @return string
     */
    private function serializeRequestData($request_data)
    {
        return serialize($request_data);
    }

    /**
     * Unserialize request data
     *
     * @param $request_data
     * @return mixed
     */
    private function unserializeRequestData($request_data)
    {
        return unserialize($request_data);
    }

     /**
     * Form request hash
     *
     * @param $server_url
     * @param $request_name
     * @param $request_params
     * @return string
     */
    private function formHash($params)
    {
        $params_str = serialize($params);
        return md5($params_str);
    }

    /**
     * Get request results by hash
     *
     * @param $server_url
     * @param $request_name
     * @param $request_params
     * @return bool
     */
    public function getCachedData($params)
    {
        $hash = $this->formHash($params);

        $record = $this->isRecordExists($hash);

        if(!$record)
        {
            return false;
        }

        $cached_request = $this->getRecord($hash);

        if(!array_key_exists('request_data', $cached_request))
        {
            return false;
        }

        return $this->unserializeRequestData($cached_request['request_data']);
    }

    /**
     * Insert cache record into the table
     *
     * @param $request_hash
     * @param $request_data
     * @return mixed
     */
    private function insertRecord($request_hash, $request_data)
    {
        // check record existence
        $row = $this->isRecordExists($request_hash);

        if ($row) {
            $this->wpdb->query(
                $this->wpdb->prepare("DELETE FROM $this->table_name
		                              WHERE request_hash = %s",
                                        $request_hash
                )
            );
            //return false;
        }

        $data = array(
            'request_hash' => $request_hash,
            'request_data' => $request_data
        );

        $format = array(
            '%s',
            '%s'
        );

        return $this->wpdb->insert($this->table_name, $data, $format);
    }

    /**
     * Return request data by hash
     *
     * @param $hash
     */
    private function getRecord($hash)
    {
        $q = "SELECT *
        FROM {$this->table_name}
        WHERE request_hash = %s";

        $pq = $this->wpdb->prepare($q, $hash);

        return $this->wpdb->get_row($pq, ARRAY_A);
    }

    /**
     * Return all records
     *
     * @return mixed
     */
    public function getHashedRecords()
    {
        $q = "SELECT *
        FROM {$this->table_name}";

        $rows = $this->wpdb->get_results($q, ARRAY_A);

        $results = array();
        for($i=0; $i<count($rows); $i++)
        {
            $results[$rows[$i]['request_hash']] = $rows[$i];
        }

        return $results;
    }

    /**
     * Check record existence by request_hash
     * If such records exists then record will returned, otherwise return false
     *
     * @param $hash
     * @return mixed
     */
    public function isRecordExists($hash)
    {
        if(count($this->table_records) == 0)
        {
            $this->table_records = $this->getHashedRecords();
        }

        if(array_key_exists($hash, $this->table_records))
        {
            return $this->table_records[$hash];
        }
        else
        {
            return false;
        }
    }

    /**
     * Clean cache and optimize the cache table after records deleting
     */
    public function cleanCache()
    {
        $q = "DELETE
        FROM {$this->table_name}";

        $this->wpdb->query($q);


        $q2 = "OPTIMIZE TABLE {$this->table_name}";
        $this->wpdb->query($q2);
    }

    private function  dropDb()
    {
        $q = "DROP TABLE {$this->table_name}";

        $this->wpdb->query($q);
    }
    public function cleanDb()
    {
        $this->dropDb();
    }
}
?>