<?php
 
/**
 * @author hofrob
 */
class SoapBehavior extends CBehavior {
 
    private $__soapClient;
 
    public function getSoapClient() {
 
        if(empty($this->__soapClient)) {
            if(function_exists('xdebug_disable'))
                @xdebug_disable();
 
            try {
                $this->__soapClient = @new SoapClient($this->owner->wsdl, $this->owner->config);
            } catch(SoapFault $e) {
                Yii::log(__METHOD__.' No connection to SoapService: '.$e->getMessage()."\n\n".
                    CVarDumper::dumpAsString($this->owner), 'warning', 'soap.behavior');
            }
        }
 
        return $this->__soapClient;
    }
 
    public function soapRequest($method, $request) {
 
        if(empty($this->soapClient))
            return array('success' => false);
 
        try {
            $ret = $this->soapClient->$method($request);
            $success = true;
        } catch(SoapFault $e) {
            Yii::log(__METHOD__.' soapRequest failed: '.$e->getMessage()."\n\n".
                '$method: '.CVarDumper::dumpAsString($method)."\n\n".
                '$request: '.CVarDumper::dumpAsString($request), 'warning', 'soap.behavior');
            $success = false;
        }
 
        return array(
            'success' => $success,
            'wsReturn' => $ret,
        );
    }
}
