<?php
/**
 * @category cloud.redis
 * @package cloud.redis
 * @author oShine <oyjqdlp@126.com>
 * @version 2.0.0.0
 * @copyright oShine 2015/08/07
 * @link https://github.com/cloudbar/cloud.yii
 * @see https://github.com/cloudbar/cloud.yii/wiki
 */
namespace cloud\redis;

use Exception;
use Redis;
use Yii;

class CacheFactory {

    /**
     * @var Redis $instance
     */
    private static $instance = null;

    /**
     * @return Redis
     */
    public static function create(){

        $ip = Yii::app()->params["RedisServerIP"];

        if(self::$instance == null){
            self::$instance = new Redis();
            self::$instance->connect($ip);
        }else{
            try{
                if(preg_match("/PONG/",self::$instance->ping())){
                    return self::$instance;
                }
            }catch (Exception $e){
                self::$instance = new Redis();
                self::$instance->connect($ip);
            }
        }
        return self::$instance;

    }

}