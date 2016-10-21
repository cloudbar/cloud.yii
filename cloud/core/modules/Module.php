<?php
/**
 * @author oshine <oyjqdlp@126.com>
 * @link https://github.com/cloudbar/cloud.yii
 * @see https://github.com/cloudbar/cloud.yii/wiki
 */
namespace cloud\core\modules;

use CEvent;
use CWebModule;

class Module extends CWebModule {

    /**
     * 加入命名空间
     */
    final protected function init() {
        $this->controllerNamespace = 'application\modules\\' . $this->getId() . '\controllers';
        parent::init();
    }
	
}
