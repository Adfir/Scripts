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
     * ID of test account used for GA on stage servers
     */
    const GOOGLE_ANALYTHICS_ACCOUN_ID = 'test';

    /**
     * Path for GA account in core_config_data
     */
    const XML_PATH_GOOGLE_ANALYTICS = 'google/analytics/account';

    /**
     * Path to cookie domain in core_config_data
     */
    const XML_PATH_COOKIE_DOMAIN = 'web/cookie/cookie_domain';

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
        $this->setBaseUrls();
        $this->removeCookieDomain();
        $this->disableIndexing();
        $this->setGA();
        $this->setPaymentMethods();
        $this->setShippingMethods();
        $this->setCustom();
        $this->removeCustomersData();
        $this->applyChanges();
    }

    protected function setBaseUrls(){

    }

    /**
     * Update Google Analytics account in order to prevent tracking test data into real account
     */
    protected function setGA(){
        $this->setConfigGlobally(self::XML_PATH_GOOGLE_ANALYTICS, self::GOOGLE_ANALYTHICS_ACCOUN_ID);
    }

    /**
     * Remove cookie domain in core_config_data
     */
    protected function removeCookieDomain() {
        $this->setConfigGlobally(self::XML_PATH_COOKIE_DOMAIN, '');
    }

    protected function disableIndexing(){

    }

    protected function setPaymentMethods(){

    }

    protected function setShippingMethods(){

    }

    protected function setCustom(){

    }

    protected function removeCustomersData() {

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