<?php
/**
 * 重写UrlManager
 * @package cloud.core.components
 * @author oShine <oyjqdlp@126.com>
 * @link https://github.com/cloudbar/cloud.yii
 * @see https://github.com/cloudbar/cloud.yii/wiki
 */

namespace cloud\core\components;


class UrlManager extends \CUrlManager
{
    public function createUrl($route,$params=array(),$ampersand='&')
    {
        if(!isset($params["_t"])){
            $params["_t"] = time();
        }
        return parent::createUrl($route,$params,$ampersand);
    }

}