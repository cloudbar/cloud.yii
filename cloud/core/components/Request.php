<?php

/**
 * 重写AJAX判断
 *
 * @package cloud.core.components
 * @author oShine <oyjqdlp@126.com>
 * @link https://github.com/cloudbar/cloud.yii
 * @see https://github.com/cloudbar/cloud.yii/wiki
 */

namespace cloud\core\components;

use CHttpRequest;

class Request extends CHttpRequest {
	public function getIsAjaxRequest() {
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest')||isset($_SERVER['HTTP_ISCORS']);
	}
}
