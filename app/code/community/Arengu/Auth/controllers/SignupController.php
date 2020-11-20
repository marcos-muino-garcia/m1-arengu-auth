<?php

class Arengu_Auth_SignupController extends Arengu_Auth_SecureRestController {
    protected function getAllowedMethods() {
        return ['POST'];
    }

    public function indexAction() {
        $helper = $this->helper;

        $params = $helper->getTrimmedStrings($this->body, [
            'firstname', 'lastname', 'email', 'password', 'skip_confirmation'
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

        unset($customer);

        $newCustomer = Mage::getModel('customer/customer');

        $newCustomer
            ->setFirstname($params['firstname'])
            ->setLastname($params['lastname'])
            ->setEmail($params['email'])
            ->setPassword($params['password'])
            ->setConfirmation($params['password'])
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

        $this->handleConfirmation($newCustomer, $params['skip_confirmation']);

        $newCustomer->save();

        $token = null;

        if($newCustomer->getConfirmation() === null) {
            $token = $this->helper->buildTokenFromBody($this->body, $newCustomer);
        }

        $helper->sendDebugHeaders($this->response);

        $helper->renderData(
            $this->response,
            $helper->buildOutput($newCustomer, $token)
        );
    }

    private function handleConfirmation(Mage_Customer_Model_Customer $customer, $skipConfirmation) {
        $shouldForceConfirm =
            $customer->isConfirmationRequired() &&
            $skipConfirmation;

        $customer
            ->setConfirmation(null)
            ->setForceConfirmed($shouldForceConfirm);
    }
}
