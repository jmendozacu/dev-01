<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_PinBar
 */


class Amasty_Pinbar_Adminhtml_Pinbar_IndexController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $request = $this->getRequest();
        $params = $request->getParams();

        $common = unserialize(base64_decode($params['common']));
        $session = base64_decode($params['session']);
        if (array_key_exists('route', $common) && array_key_exists('controller', $common) && array_key_exists('action', $common) && array_key_exists('title', $common)) {
            $url = $common['route'] . '/' . $common['controller'] . '/' . $common['action'];
            foreach ($common['params'] as $key => $value) {
                $url .= '/' . $key . '/' . $value;
            }
        } else {
            return 'no data';
        }

        $pinModel = Mage::getModel('ampinbar/pinbar');
        $userId = Mage::getSingleton('admin/session')->getUser()->getUserId();
        $pinModel->setUserId($userId);
        $pinModel->setPath($url);
        $pinModel->setTitle($common['title']);
        $pinModel->save();

        $pinId = $pinModel->getId();

        $sessionData = unserialize($session);

        if (!empty($sessionData)) {
            $sessionModel = Mage::getModel('ampinbar/session');
            $sessionModel->setPinId($pinId);
            $sessionModel->setSession($session);
            $sessionModel->save();
        }

        $result = array('pin_id' => $pinId, 'title' => $common['title']);

        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        $pinId = (int)$request->getParam('pinId');
        $pinModel = Mage::getModel('ampinbar/pinbar')->load($pinId);
        $sessionModel = Mage::getModel('ampinbar/session')->load($pinId);
        $thisPageOpened = true;

        if ($sessionModel->getSession()) {
            $pinSession = unserialize($sessionModel->getSession());
            $session = Mage::getSingleton('adminhtml/session')->getData();

            foreach ($pinSession as $gridName => $params) {
                foreach ($params as $param => $value) {
                    if (array_key_exists($gridName . $param, $session) && $session[$gridName . $param] == $value) {
                        $thisPageOpened = true;
                    } else {
                        $thisPageOpened = false;
                    }
                }
            }
        }

        $result = array('url' => $pinModel->getPath(), 'delete' => $thisPageOpened);

        $pinModel->delete();

        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    public function renameAction()
    {
        $request = $this->getRequest();
        $pinId = (int)$request->getParam('pinId');
        $newTitle = $request->getParam('title');
        $pinModel = Mage::getModel('ampinbar/pinbar')->load($pinId);
        $pinModel->setTitle($newTitle);
        $pinModel->save();
    }

    public function loadpinAction() {
        $request = $this->getRequest();
        $pinId = (int)$request->getParam('pinId');
        $pinModel = Mage::getModel('ampinbar/pinbar')->load($pinId);
        $sessionModel = Mage::getModel('ampinbar/session')->load($pinModel->getId());
        $sessionData = unserialize($sessionModel->getSession());
        $session = Mage::getSingleton('adminhtml/session');
        if ($sessionData) {
            foreach ($sessionData as $gridName => $params) {
                foreach ($params as $param => $value) {
                    $session->setData($gridName . $param, $value);
                }
            }
        }
        $this->_redirect($pinModel->getPath());
    }

    protected function _isAllowed()
    {
        return true;
    }
}