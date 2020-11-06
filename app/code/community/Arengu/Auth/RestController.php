<?php

abstract class Arengu_Auth_RestController extends Mage_Core_Controller_Front_Action {
    protected $helper;

    protected $request;
    protected $response;

    abstract protected function getAllowedMethods();

    public function preDispatch() {
        parent::preDispatch();

        $this->helper = Mage::helper('arengu_auth');

        $this->request = $this->getRequest();
        $this->response = $this->getResponse();

        $method = $this->request->getMethod();

        if(!in_array($method, $this->getAllowedMethods())) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);

            $this->response->setHeader(
                'Allow',
                implode($this->getAllowedMethods(), ', ')
            );

            $this->helper->renderError(
                $this->response,
                'method_not_allowed',
                $this->helper->trans('This method is not allowed'),
                405
            );

            return;
        }

        $this->body = [];

        if(in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $this->body = $this->helper->parseBody();
        }
    }
}
