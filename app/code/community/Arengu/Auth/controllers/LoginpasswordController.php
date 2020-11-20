<?php

class Arengu_Auth_LoginpasswordController extends Arengu_Auth_SecureRestController {
    protected function getAllowedMethods() {
        return ['POST'];
    }

    public function indexAction() {
        $helper = $this->helper;

        $params = $helper->getTrimmedStrings($this->body, [
            'email', 'password',
        ]);

        if(!$params['email']) {
            $helper->renderError(
                $this->response,
                'missing_email',
                $helper->trans('Property email is missing.'),
                400
            );

            return;
        }

        if(!$params['password']) {
            $helper->renderError(
                $this->response,
                'missing_password',
                $helper->trans('Property password is missing.'),
                400
            );

            return;
        }

        $customer = Mage::getModel('customer/customer');

        $customer
            ->setWebsiteId(Mage::app()->getWebsite()->getId())
            ->loadByEmail($params['email']);

        if(!$customer->getId()) {
            $helper->renderError(
                $this->response,
                'unknown_email',
                $helper->trans('No customer found with this email.'),
                404
            );

            return;
        }

        if(!$customer->validatePassword($params['password'])) {
            $helper->renderError(
                $this->response,
                'wrong_password',
                $helper->trans('The password is not correct for this user.'),
                400
            );

            return;
        }

        $token = $helper->buildTokenFromBody($this->body, $customer);

        $helper->sendDebugHeaders($this->response);

        $this->helper->renderData(
            $this->response,
            $this->helper->buildOutput($customer, $token)
        );
    }
}
