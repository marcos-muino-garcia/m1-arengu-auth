<?php

abstract class Arengu_Auth_SecureRestController extends Arengu_Auth_RestController {
    public function preDispatch() {
        parent::preDispatch();

        if(!$this->helper->isRequestAllowed($this->request)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);

            $this->response->setHeader('WWW-Authenticate', 'Bearer');

            $this->helper->renderError(
                $this->response,
                'invalid_authorization',
                $this->helper->trans('Authorization is missing or invalid.'),
                401
            );

            return;
        }
    }
}
