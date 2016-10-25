<?php
namespace cloud\redis;

/**
 * Class Cache redis 用于报表的缓存基本存储和读写 2.0
 * <pre>
 *  Cache::read("diamond.account");
 *  Cache::readSync("diamond.account");
 *  $finder = Cache::createFinder("diamond.account");
 *  $finder->read();
 *  $finder->readSync();
 *
 *  Cache::save("diamond.account",$data);
 *  $storage = Cache::createStorage("diamond.account");
 *  $storage->save($data);
 *  $storage->save($data,7200);
 * </pre>
 * @category cloud.redis
 * @package cloud.redis
 * @author oShine <oyjqdlp@126.com>
 * @version 2.0.0.0
 * @copyright oShine 2015/08/07
 * @link https://github.com/cloudbar/cloud.yii
 * @see https://github.com/cloudbar/cloud.yii/wiki
 */
class Cache {

    /**
     * 非安全读取的数据
     * @param $key
     * @return array|null
     */
    public static function read($key){
        $finder = self::createFinder($key);
        return $finder->read();
    }

    /**
     * 同步读取数据
     * @param $key
     * @return mixed
     */
    public static function readSync($key){
        $finder = self::createFinder($key);
        return $finder->readSync();
    }

    /**
     * 创建Finder
     * @param $key
     * @return Finder
     */
    public static function createFinder($key){
        $key = CacheGenerate::key($key);
        return new Finder($key);
    }

    /**
     * 创建Storage
     * @param $key
     * @return Storage
     */
    public static function createStorage($key){
        $key = CacheGenerate::key($key);
        return new Storage($key);
    }

    /**
     * 保存数据
     * @param $key
     * @param array $data
     * @param int $expired
     * @return bool
     */
    public static function save($key,$data = array(),$expired=7200){
        $storage = self::createStorage($key);
        return $storage->save($data,$expired);
    }

    /**
     * @param string $key
     */
    public static function clear($key){
        $redis = CacheFactory::create();
        $redis->del($redis->keys(CacheGenerate::key($key)));
    }

}



/**
 * Class Finder  数据读取
 * @author oShine <oyjqdlp@126.com>
 * @version 2.0.0.1
 * @copyright oShine 2015/08/07
 */
class Finder {

    /**
     * @var string $key
     */
    public $key;

    /**
     * @param string $key
     */
    public function __construct($key){
        $this->key = $key;
    }

    /**
     * 非安全读取数据
     * @return mixed
     */
    public function read(){
        $data = $this->readData();
        if($data->isRead === true && !$data->isExpired()) {
            return json_decode(json_encode($data->data), true);
        }
        return null;
	}

    /**
     * @return Data
     */
    protected function readData(){
        $redis =  CacheFactory::create();
        $rptData = new Data();
        $data = json_decode($redis->get($this->key));
        if(false == $data){
            $rptData->isRead = false;
            $rptData->expiredTime = time();
            $rptData->expired = 24*3600;
        }else{
            $rptData->expired = $data->expired;
            $rptData->isRead = isset($data->isRead) && $data->isRead === true?true:false;
            $rptData->expiredTime = $data->expiredTime;
            $rptData->data = $data->data;
        }
        return $rptData;
    }

    /**
     * 同步读取数据
     * @return mixed
     */
    public function readSync(){
        while(true){
            $rptData = $this->readData();
            if($rptData->isRead && !$rptData->isExpired())
                return $this->read();
            sleep(1);
        }
	}
}

/**
 * Class Storage  数据存储
 * @author oShine <oyjqdlp@126.com>
 * @version 2.0.0.0
 * @copyright oShine 2015/08/07
 */
class Storage {

    /**
     * @var string key
     */
    public $key;

    /**
     * @param string $key
     */
    public function __construct($key){
      $this->key = $key;
    }

    /**
     * @return bool
     */
    public function flush(){
        $rptData = new Data();
        $rptData->data = null;
        $rptData->expiredTime = time();
        $rptData->isRead = false;
        $rptData->expired = 1;
        $redis =  CacheFactory::create();

        return $redis->setex($this->key, $rptData->expired,json_encode($rptData));
    }

    /**
     * 写入数据
     * @param $data
     * @param int $expired
     * @return bool
     */
    public function save($data,$expired=7200){

        $rptData = new Data();
        $rptData->data = $data;
        $rptData->expiredTime = time();
        $rptData->isRead = true;
        $rptData->expired = $expired;
        $redis = CacheFactory::create();

        return $redis->setex($this->key, $rptData->expired,json_encode($rptData));
    }
}

/**
 * Class Data redis存储数据实体
 * @author oShine <oyjqdlp@126.com>
 * @version 2.0.0.0
 * @copyright oShine 2015/08/07
 */
class Data {
    /**
     * @var int $expired 失效间隔时长
     */
    public $expired;
    /**
     * @var int
     */
    public $expiredTime;
    /**
     * @var mixed 存储的具体数据
     */
    public $data;
    /**
     * @var bool 是否可以读取
     */
    public $isRead;

    /**
     * 是否失效
     * @return bool
     */
    public function isExpired(){
        if(time()-$this->expiredTime > $this->expired)
            return true;
        return false;
    }
}

/**
 * Class Generate key生成
 * @author oShine <oyjqdlp@126.com>
 * @version 2.0.0.0
 * @copyright oShine 2015/08/07
 */
class CacheGenerate {
    /**
     * @static
     * @param $key
     * @return string
     */
    public static function key($key){
        return "sys.data.".md5($key);
    }
}