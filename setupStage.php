<?php
/**
 * Created by PhpStorm.
 * User: vmart_000
 * Date: 7/3/2015
 * Time: 1:09 PM
 */
require_once 'abstract.php';

class Gorilla_SetupStage extends Mage_Shell_Abstract {
    const GOOGLE_ANALYTHICS_ACCOUN_ID = 'test';

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
    }

    protected function setBaseUrls(){

    }

    protected function setGA(){

    }

    protected function removeCookieDomain() {

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
}

$shell = new Gorilla_SetupStage();
$shell->run();