<?php

/**
 * Class Mutex 用于任务锁
 * @category cloud.redis
 * @package cloud.redis
 * @author oShine <oyjqdlp@126.com>
 * @version 2.0.0.0
 * @copyright oShine 2015/08/07
 * @example
 * Mutex::create("download")->wait()
 * Mutex::create("download")->doing()
 * Mutex::create("download")->done()
 * Mutex::create("download")->error()
 *
 * @link https://github.com/cloudbar/cloud.yii
 * @see https://github.com/cloudbar/cloud.yii/wiki
 */
namespace cloud\redis;

use Exception;
use Redis;

class Mutex {

    /**
     * @param $key
     * @return Mutex
     */
    public static function create($key){
        return new MutexStorage(MutexGenerate::key($key), CacheFactory::create());
    }

    /**
     * @param $key
     */
    public static function clear($key){
        $cache = CacheFactory::create();
        $cache->del($cache->keys(MutexGenerate::key($key)));
    }

}

/**
 * Class MutexStorage
 * @category mutex
 * @package cache
 * @author oShine <oyjqdlp@126.com>
 * @version 2.0.0.0
 * @copyright oShine 2015/08/07
 */
class MutexStorage {

    private $key;

    /**
     * @var Redis $redis
     */
    private $redis;

    public function __construct($key,$redis){
        $this->key = $key;
        $this->redis = $redis;
    }


    /**
     * @return $this
     * @throws Exception
     */
    public function wait(){
        $this->save("WAIT");
        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function doing(){
        $this->save("DOING");
        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function done(){
        $this->save("DONE");
        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function error(){
        $this->save("ERROR");
        return $this;
    }

    /**
     * @param $data
     * @return $this
     * @throws Exception
     */
    protected function save($data){

        $data = json_encode(array("status"=>$data,"date"=>date("Y-m-d"),"timestamp"=>time()));

        $flag = $this->redis->setex($this->key,3*24*2400,$data);
        if(!$flag)
            throw new Exception("mutex-save-error:$data");
        return $this;
    }

    /**
     * @return string|null
     */
    protected function get(){

        $data = json_decode($this->redis->get($this->key),true);
        if(strtotime(date("Y-m-d")) == strtotime($data["date"])){
            return $data["status"];
        }
        return null;
    }


    /**
     * @return bool
     */
    public function isDoing(){
        $data = json_decode($this->redis->get($this->key),true);
        if(isset($data) && isset($data["status"]) && $data["status"] == "DOING" && isset($data["timestamp"]) && (time()-$data["timestamp"])<60)
            return true;
        return false;
    }

    /**
     * @return bool
     */
    public function isDone(){
        $status = $this->get();
        if(isset($status) && $status == "DONE")
            return true;
        return false;
    }

    /**
     * @return bool
     */
    public function isError(){
        $status = $this->get();
        if(isset($status) && $status == "ERROR")
            return true;
        return false;
    }
}


/**
 * Class Generate key生成
 * @category cache
 * @package cache
 * @author oShine <oyjqdlp@126.com>
 * @version 2.0.0.0
 * @copyright oShine 2015/08/07
 */
class MutexGenerate
{
    /**
     * @static
     * @param $key
     * @return string
     */
    public static function key($key)
    {
        return "sys.mutex." . md5($key);
    }
}