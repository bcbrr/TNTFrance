<?php
/**
 * This class has been generated by TheliaStudio
 * For more information, see https://github.com/thelia-modules/TheliaStudio
 */

namespace TNTFrance\Controller;

use Thelia\Form\Exception\FormValidationException;
use TNTFrance\Controller\Base\TNTFranceConfigController as BaseTNTFranceConfigController;
use TNTFrance\Form\TNTPriceWeightForm;
use TNTFrance\Model\Config\TNTFranceConfigValue;
use TNTFrance\Model\TntPriceWeightQuery;
use TNTFrance\TNTFrance;

/**
 * Class TNTFranceConfigController
 * @package TNTFrance\Controller
 */
class TNTFranceConfigController extends BaseTNTFranceConfigController
{
    public function defaultAction()
    {
        $productsEnabled = TNTFrance::getConfigValue(TNTFranceConfigValue::PRODUCTS_ENABLED);

        return $this->render(
            "tntfrance-configuration",
            [
                'products_enabled' => $productsEnabled
            ]
        );
    }

    public function saveAction()
    {
        $errorMessage = null;

        $current_tab = $this->getRequest()->get('current_tab');
        if ($current_tab != "weight") {
            return parent::saveAction();
        }

        $form = new TNTPriceWeightForm($this->getRequest());

        try {

            $formValidate = $this->validateForm($form);

            TNTFrance::setConfigValue(TNTFranceConfigValue::FREE_SHIPPING, $formValidate->get(TNTFranceConfigValue::FREE_SHIPPING)->getData() ? $formValidate->get(TNTFranceConfigValue::FREE_SHIPPING)->getData() : 0);
            TNTFrance::setConfigValue(TNTFranceConfigValue::SURCHARGE_FUEL, $formValidate->get(TNTFranceConfigValue::SURCHARGE_FUEL)->getData() ? $formValidate->get(TNTFranceConfigValue::SURCHARGE_FUEL)->getData() : 0);
            TNTFrance::setConfigValue(TNTFranceConfigValue::SURCHARGE_SECURITY_FEE, $formValidate->get(TNTFranceConfigValue::SURCHARGE_SECURITY_FEE)->getData() ? $formValidate->get(TNTFranceConfigValue::SURCHARGE_SECURITY_FEE)->getData() : 0);
            TNTFrance::setConfigValue(TNTFranceConfigValue::SURCHARGE_MULTI_PACKAGE, $formValidate->get(TNTFranceConfigValue::SURCHARGE_MULTI_PACKAGE)->getData() ? $formValidate->get(TNTFranceConfigValue::SURCHARGE_MULTI_PACKAGE)->getData() : 0);
            TNTFrance::setConfigValue(TNTFranceConfigValue::SEPARATE_PRODUCT_IN_PACKAGE, $formValidate->get(TNTFranceConfigValue::SEPARATE_PRODUCT_IN_PACKAGE)->getData() ? $formValidate->get(TNTFranceConfigValue::SEPARATE_PRODUCT_IN_PACKAGE)->getData() : 0);
            TNTFrance::setConfigValue(TNTFranceConfigValue::OPTION_P_PAYMENT_BACK, $formValidate->get(TNTFranceConfigValue::OPTION_P_PAYMENT_BACK)->getData() ? $formValidate->get(TNTFranceConfigValue::OPTION_P_PAYMENT_BACK)->getData() : 0);
            TNTFrance::setConfigValue(TNTFranceConfigValue::OPTION_W_EXPEDITION_UNDER_PROTECTION, $formValidate->get(TNTFranceConfigValue::OPTION_W_EXPEDITION_UNDER_PROTECTION)->getData() ? $formValidate->get(TNTFranceConfigValue::OPTION_W_EXPEDITION_UNDER_PROTECTION)->getData() : 0);
            TNTFrance::setConfigValue(TNTFranceConfigValue::OPTION_D_RELAY_PACKAGE, $formValidate->get(TNTFranceConfigValue::OPTION_D_RELAY_PACKAGE)->getData() ? $formValidate->get(TNTFranceConfigValue::OPTION_D_RELAY_PACKAGE)->getData() : 0);
            TNTFrance::setConfigValue(TNTFranceConfigValue::OPTION_Z_HOME_DELIVERY, $formValidate->get(TNTFranceConfigValue::OPTION_Z_HOME_DELIVERY)->getData() ? $formValidate->get(TNTFranceConfigValue::OPTION_Z_HOME_DELIVERY)->getData() : 0);
            TNTFrance::setConfigValue(TNTFranceConfigValue::OPTION_E_WITHOUT_ANNOTATING, $formValidate->get(TNTFranceConfigValue::OPTION_E_WITHOUT_ANNOTATING)->getData() ? $formValidate->get(TNTFranceConfigValue::OPTION_E_WITHOUT_ANNOTATING)->getData() : 0);

            //Save the contracted rates
            $prices = $formValidate->get(TNTFranceConfigValue::PRICE_ONE_KG)->getData();
            $priceKgSups = $formValidate->get(TNTFranceConfigValue::PRICE_KG_SUP)->getData();

            foreach ($prices as $id => $price) {

                if (null != $tntPriceWeight = TntPriceWeightQuery::create()->findPk($id)) {
                    $tntPriceWeight->setPrice($price);

                    if (is_array($priceKgSups) && array_key_exists($id, $priceKgSups)) {
                        $tntPriceWeight->setPriceKgSup($priceKgSups[$id]);
                    }

                    $tntPriceWeight->save();
                }

            }

        } catch (FormValidationException $ex) {
            // Invalid data entered
            $errorMessage = $this->createStandardFormValidationErrorMessage($ex);
        } catch (\Exception $ex) {
            // Any other error
            $errorMessage = $this->getTranslator()->trans(
                'Sorry, an error occurred: %err',
                [
                    '%err' => $ex->getMessage()
                ],
                [],
                TNTFrance::MESSAGE_DOMAIN
            );
        }

        if (null !== $errorMessage) {
            // Mark the form as with error
            $form->setErrorMessage($errorMessage);

            // Send the form and the error to the parser
            $this->getParserContext()
                ->addForm($form)
                ->setGeneralError($errorMessage)
            ;
        } else {
            $this->getParserContext()
                ->set("success", true)
            ;
        }

        return $this->defaultAction();
    }
}