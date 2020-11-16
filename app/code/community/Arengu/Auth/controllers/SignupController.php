<?php

class Arengu_Auth_SignupController extends Arengu_Auth_SecureRestController {
    protected function getAllowedMethods() {
        return ['POST'];
    }

    public function indexAction() {
        $helper = $this->helper;

        $params = $helper->getTrimmedStrings($this->body, [
            'firstname', 'lastname', 'email', 'password'
        ]);

        $customer = Mage::getModel('customer/customer');

        $customer
            ->setWebsiteId(Mage::app()->getWebsite()->getId())
            ->loadByEmail($params['email']);

        if($customer->getId()) {
            $helper->renderError(
                $this->response,
                'validation_error',
                $helper->trans('Customer with the same email already exists.'),
                409
            );

            return;
        }

        $params['password'] =
            empty($params['password']) ?
            $customer->generatePassword(32) :
            $params['password'];

        $newCustomer = Mage::getModel('customer/customer');

        $newCustomer
            ->setFirstname($params['firstname'])
            ->setLastname($params['lastname'])
            ->setEmail($params['email'])
            ->setPassword($params['password'])
            ->setConfirmation($params['password']) // magento 1.7
            ->setPasswordConfirmation($params['password']) // magento 1.9
        ;

        $validationResult = $newCustomer->validate();

        if(is_array($validationResult)) {
            $helper->renderError(
                $this->response,
                'validation_error',
                implode(' ', $validationResult),
                400
            );

            return;
        }

        $newCustomer->save();
        
        $token = $helper->buildTokenFromBody($this->body, $newCustomer);

        $helper->renderData(
            $this->response,
            $helper->buildOutput($newCustomer, $token)
        );
    }
}