<?php

class Arengu_Auth_CheckemailController extends Arengu_Auth_SecureRestController {
    protected function getAllowedMethods() {
        return ['POST'];
    }

    public function indexAction() {
        $helper = $this->helper;

        $email = $helper->getTrimmedString($this->body, 'email');

        if(!$email) {
            $helper->renderError(
                $this->response,
                'missing_email',
                $helper->trans('Property email is missing.'),
                400
            );

            return;
        }

        $customer = Mage::getModel('customer/customer');

        $customer
            ->setWebsiteId(Mage::app()->getWebsite()->getId())
            ->loadByEmail($email);

        $helper->renderData(
            $this->response,
            ['email_exists' => (bool) $customer->getId()]
        );
    }
}
