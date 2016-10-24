<?php
/**
 * 用户浏览器信息检测类
 *
 * @package cloud.core.components
 * @author oShine <oyjqdlp@126.com>
 * @link https://github.com/cloudbar/cloud.yii
 * @see https://github.com/cloudbar/cloud.yii/wiki
 */

namespace cloud\core\components;

use CDbLogRoute;

class Log extends CDbLogRoute {

    public $logTableName = '{{log}}';
    public $connectionID = 'db';

    public function init() {
        $tableId = \cloud\core\model\Log::getLogTableId();
        $this->logTableName = \cloud\core\model\Log::getTableName( $tableId );
        parent::init();
    }

    protected function createLogTable( $db, $tableName ) {
        $db->createCommand()->createTable( $tableName, array(
            'id' => 'pk',
            'level' => 'varchar(128)',
            'category' => 'varchar(128)',
            'logtime' => 'integer',
            'message' => 'text',
                ), 'ENGINE=MyISAM' );
    }

}
