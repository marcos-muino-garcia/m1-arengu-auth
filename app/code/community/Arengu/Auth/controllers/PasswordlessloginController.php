<?php

class Arengu_Auth_PasswordlessloginController extends Arengu_Auth_SecureRestController {
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

        if(!$customer->getId()) {
            $helper->renderError(
                $this->response,
                'unknown_email',
                $helper->trans('No customer found with this email.'),
                404
            );

            return;
        }

        $token = $helper->buildTokenFromBody($this->body, $customer);

        $helper->renderData(
            $this->response,
            $helper->buildOutput($customer, $token)
        );
    }
}
