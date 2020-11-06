<?php

class Arengu_Auth_LoginjwtController extends Arengu_Auth_RestController {
    protected function getAllowedMethods() {
        return ['GET'];
    }

    public function indexAction() {
        $helper = $this->helper;

        try {
            $decodedToken = (array) \Firebase\JWT\JWT::decode(
                $this->request->getParam('token'),
                $helper->getJwtSecret(),
                [$helper::JWT_ALG]
            );
        } catch (\Firebase\JWT\ExpiredException $ex) {
            $this->renderError(
                $helper->trans('Sorry, the provided token is expired.')
            );
            return;
        } catch (\Exception $ex) {
            $this->renderError(
                $helper->trans('Sorry, the provided token is not valid.')
            );
            return;
        }

        $issuer = $helper->getTrimmedString($decodedToken, 'iss');
        $email = $helper->getTrimmedString($decodedToken, 'email');
        $customerId = $helper->getTrimmedString($decodedToken, 'sub');
        $redirectUri = $helper->getTrimmedString($decodedToken, 'redirect_uri');

        if($issuer !== $_SERVER['SERVER_NAME'] || !$email || !$customerId) {
            $this->renderError('Sorry, the provided token is not valid.');
            return;
        }

        $customer = Mage::getModel('customer/customer');

        $customer
            ->setWebsiteId(Mage::app()->getWebsite()->getId())
            ->loadByEmail($email);

        // check for freaky coincidence where 2 users somehow managed to
        // exchange email addresses between them after the token was generated
        if ((string) $customer->getId() !== $customerId) {
            $this->renderError(
                $helper->trans('There was a problem validating your session, please try again.')
            );

            return;
        }

        Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);

        if($redirectUri) {
            // $this->_redirect() doesn't allow absolute/external URLs
            $this->response->setRedirect($redirectUri);
        } else {
            $this->_redirect('customer/account');
        }
    }

    protected function renderError($message, $status = 400) {
        Mage::getSingleton('core/session')->addError($message);

        $this->loadLayout();
        $this->renderLayout();
    }
}
