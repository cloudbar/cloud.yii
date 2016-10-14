<?php

/**
 * Cloud本地环境引擎
 *
 * @package application.core.engines
 * @author oshine <oyjqdlp@126.com>
 * @version $Id:
 */

namespace cloud\core\engines;

use cloud\Cloud;
use cloud\core\components\Engine;
use cloud\core\engines\local\LocalIo;
use CMap;

class Local extends Engine {

    /**
     * 获取 IO 接口
     * @staticvar null $io
     * @return \cloud\core\engines\local\LocalIo
     */
    public function io() {
        static $io = null;
        if ( $io == null ) {
            $io = new LocalIo();
        }
        return $io;
    }

    public function setConfig($config)
    {
        $mainConfig = $config;

        $databases = isset($mainConfig["databases"]) && is_array($mainConfig["databases"])?$mainConfig["databases"]:array();

        $components = array();
        foreach($databases as $key => $value){
            $connectionString = "mysql:host={$value['host']};port={$value['port']};dbname={$value['dbname']}";
            $components[$key] = array(
                'class' => '\CDbConnection',
                'connectionString' => $connectionString,
                'username' => $value['username'],
                'password' => $value['password'],
                'tablePrefix' => $value['tableprefix'],
                'charset' => $value['charset']
            );
        }
        $config = array(
            'runtimePath' => PATH_ROOT . DIRECTORY_SEPARATOR . 'data/runtime',
            'language' => $mainConfig['env']['language'],
            'theme' => $mainConfig['env']['theme'],
            'components' => $components
        );
        unset($mainConfig['databases']);
        unset($mainConfig['env']);

        $config =  CMap::mergeArray($config ,$mainConfig);

        parent::setConfig($config); // TODO: Change the autogenerated stub
    }

    /**
     * 设置别名，加载驱动路径
     * @return void
     */
    protected function init() {
        // 设置data别名
        Cloud::setPathOfAlias( 'data', PATH_ROOT . DIRECTORY_SEPARATOR . 'data' );
        // 设置引擎驱动别名
        Cloud::setPathOfAlias( 'engineDriver', Cloud::getPathOfAlias( 'cloud.core.engines.local' ) );
    }

}
