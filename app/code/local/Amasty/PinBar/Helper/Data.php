<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_PinBar
 */


class Amasty_PinBar_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getParamsEncode() {
        $request = Mage::app()->getRequest();
        $params = array();
        $params['params'] = $request->getParams();
        if (array_key_exists('key', $params['params'])) {
            unset($params['params']['key']);
        }
        $params['route'] = $request->getRouteName();
        $params['controller'] = $request->getControllerName();
        $params['action'] = $request->getActionName();

        if ($block = Mage::app()->getLayout()->getBlock('head')) {
            $title = $block->getTitle();
            $titleArr = explode('/', $title);
            if($titleArr[0]) {
                $params['title'] = $titleArr[0];
            } else {
                $params['title'] = $title;
            }
        } else {
            $params['title'] = '';
        }


        return base64_encode(serialize($params));
    }

    public function pinAlreadyAttached($path = null) {
        $attached = true;
        if (is_null($path)) {
            $encode = $this->getParamsEncode();
            $decode = unserialize(base64_decode($encode));
            if (array_key_exists('route', $decode) && array_key_exists('controller', $decode) && array_key_exists('action', $decode)) {
                $path = $decode['route'] . '/' . $decode['controller'] . '/' . $decode['action'];
                foreach ($decode['params'] as $key => $value) {
                    if(!is_array($value)){
                        $path .= '/' . $key . '/' . $value;
                    }
                }
            }
        }

        $pinCollection = Mage::getModel('ampinbar/pinbar')->getCollection()
            ->addFieldToFilter('path', array('like' => $path));
        if (!$this->sharedPins()) {
            $pinCollection->addFieldToFilter('user_id', Mage::getSingleton('admin/session')->getUser()->getUserId());
        }
        $pinCollection->getSelect()
            ->joinLeft(
                array('session' => $pinCollection->getTable('ampinbar/session')),
                'session.pin_id = main_table.pin_id'
            );
        if ($pinCollection->getSize()) {
            $session = Mage::getSingleton('adminhtml/session')->getData();
            foreach ($pinCollection as $model) {
                if ($model->getSession()) {
                    $sessionData = unserialize($model->getSession());
                    if (!empty($sessionData)) {
                        $arrayPinSession = array();
                        $arrayCommonSession = array();
                        foreach ($sessionData as $gridName => $params) {
                            foreach ($params as $param => $value) {
                                $arrayPinSession[$gridName . $param] = $value;
                                if (array_key_exists($gridName . $param, $session)) {
                                    $arrayCommonSession[$gridName . $param] = $session[$gridName . $param];
                                }
                            }
                        }
                        $arrayDiff = array_diff_assoc($arrayPinSession, $arrayCommonSession);
                        if (empty($arrayDiff)) {
                            $attached = true;
                            break;
                        } else{
                            $attached = false;
                        }
                    } else {
                        $attached = true;
                    }
                } else {
                    $attached = true;
                }
            }
            return $attached;
        }
        return false;
    }

    public function moduleEnabled() {
        return Mage::getStoreConfig('ampinbar/general/enable');
    }

    public function sharedPins() {
        return Mage::getStoreConfig('ampinbar/general/shared');
    }

}