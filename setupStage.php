<?php
/**
 * Created by PhpStorm.
 * User: vmart_000
 * Date: 7/3/2015
 * Time: 1:09 PM web/browser_capabilities/cookies
 */
require_once 'abstract.php';

class Gorilla_SetupStage extends Mage_Shell_Abstract {
    /**
     * Value of <meta name="robots"/> in Head of the page
     */
    const META_TAG_ROBOTS_VALUE = 'NOINDEX,NOFOLLOW';

    /**
     * Xml path for config in core_config_data that activates GA
     */
    const XML_PATH_GOOGLE_ACTIVE = 'google/analytics/active';

    /**
     * Xml path to GA account in core_config_data
     */
    const XML_PATH_GOOGLE_ANALYTICS = 'google/analytics/account';

    /**
     * Xml path to cookie domain in core_config_data
     */
    const XML_PATH_COOKIE_DOMAIN = 'web/cookie/cookie_domain';

    /**
     * Xml path to value used meta-tag name="robots"
     */
    const XML_PATH_DEFAULT_ROBOTS= 'design/head/default_robots';

    /**
     * Websites cache
     *
     * @var array
     */
    protected $_websites = null;

    /**
     * Stores cache
     *
     * @var array
     */
    protected $_stores = null;

    /**
     * Config Model Cache
     *
     * @var Mage_Core_Model_Config
     */
    protected $_configModel = null;

    /**
     * Run script
     *
     */
    public function run()
    {
        $this->changeBaseUrls();
        $this->removeCookieDomain();
        $this->disableIndexing();
        $this->changeGAConfig();
        $this->changePaymentMethodsConfig();
        $this->changeShippingMethodsConfig();
        $this->addProjectSpecificConfig();
        $this->removeCustomersData();
        $this->applyChanges();
    }

    protected function changeBaseUrls(){

    }

    /**
     * Update Google Analytics account in order to prevent tracking test data into real account
     */
    protected function changeGAConfig(){
        $this->setConfigGlobally(self::XML_PATH_GOOGLE_ACTIVE, 0);
        $this->setConfigGlobally(self::XML_PATH_GOOGLE_ANALYTICS, '');
    }

    /**
     * Remove cookie domain in core_config_data
     */
    protected function removeCookieDomain() {
        $this->setConfigGlobally(self::XML_PATH_COOKIE_DOMAIN, '');
    }

    /**
     * Disable indexing by search engines
     */
    protected function disableIndexing(){
        $this->setConfigGlobally(self::XML_PATH_DEFAULT_ROBOTS, self::META_TAG_ROBOTS_VALUE);
    }

    /**
     * Configure test accounts for payment-methods
     */
    protected function changePaymentMethodsConfig(){
        $stores = $this->getStores();
        foreach($stores as $store) {
            $storeId = $store->getId();
            $paymentMethods = Mage::getModel('payment/config')->getActiveMethods($storeId);

            foreach($paymentMethods as $paymentMethod){
                switch ($paymentMethod->getCode()){
                    case 'authorizenet':
                        $this->setupAuthorizenet($storeId);
                        break;
                    case 'authorizenetcim':
                        $this->setupAuthorizenetCIM($storeId);
                        break;
                    case 'paypal_express':
                        $this->setupPayPalExpress($storeId);
                        break;
                    case 'paypal_global_verisign':
                        $this->setupPayPalPaymentsPro($storeId);
                        break;
                }
            }
        }
    }

    protected function changeShippingMethodsConfig(){

    }

    protected function addProjectSpecificConfig(){

    }

    protected function removeCustomersData() {

    }

    /**
     * Configure test account for Authorize.net payment
     *
     * @param string $storeId
     */
    protected function setupAuthorizenet($storeId){

    }

    /**
     * Configure test account for Authorize.net CIM payment
     *
     * @param string $storeId
     */
    protected function setupAuthorizenetCIM($storeId){
        $configModel = $this->getConfigModel();
        $configModel->saveConfig('payment/authorizenetcim/test', 1, 'stores', $storeId);
        $configModel->saveConfig('payment/authorizenetcim/test_gateway_wsdl', 'https://apitest.authorize.net/soap/v1/Service.asmx?WSDL', 'stores', $storeId);
        $configModel->saveConfig('payment/authorizenetcim/test_gateway_url', 'https://apitest.authorize.net/soap/v1/Service.asmx', 'stores', $storeId);
        $configModel->saveConfig('payment/authorizenetcim/test_login', Mage::helper('core')->encrypt('9k7Se58M3'), 'stores', $storeId);
        $configModel->saveConfig('payment/authorizenetcim/test_trans_key', Mage::helper('core')->encrypt('772XZe96cpRa8jX2'), 'stores', $storeId);

    }

    /**
     * Configure test account for PayPal Express payment method
     *
     * @param string $storeId
     */
    protected function setupPayPalExpress($storeId){

    }

    /**
     * Configure test account for PayPal Payflow Pro payment method
     *
     * @param string $storeId
     */
    protected function setupPayPalPaymentsPro($storeId){

    }

    /**
     * Remove config for all websites/stores and set one global value
     *
     * @param string $config
     * @param string $value
     */
    protected function setConfigGlobally($config, $value){
        $websites = $this->getWebsites();
        $stores = $this->getStores();

        $configModel = $this->getConfigModel();

        foreach($websites as $website){
            $configModel->deleteConfig($config, 'websites', $website->getId());
        }

        foreach($stores as $store) {
            $configModel->deleteConfig($config, 'stores', $store->getId());
        }

        $configModel->saveConfig($config, $value);
    }

    /**
     * Clear cache and reload configuration
     */
    protected function applyChanges() {
        Mage::app()->getCacheInstance()->flush();
        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();
    }

    /**
     * Get all websites
     */
    protected function getWebsites(){
        if($this->_websites === null){
            $this->_websites = Mage::app()->getWebsites();
        }

        return $this->_websites;
    }

    /**
     * Get all stores
     */
    protected function getStores() {
       if($this->_stores === null) {
           $this->_stores = Mage::app()->getStores();
       }

       return $this->_stores;
    }

    /**
     * Get object of Mage_Core_Model_Config
     */
    protected function getConfigModel(){
        if($this->_configModel ===  null) {
            $this->_configModel = Mage::getModel('core/config');
        }
        return $this->_configModel;
    }
}

$shell = new Gorilla_SetupStage();
$shell->run();