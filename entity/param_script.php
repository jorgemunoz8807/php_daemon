<?php

/**
 * Description of param_script
 *
 * @author jmunoz
 */
class param_script {

    var $attr_name;
    var $value;

    function __construct() {
        
    }

    function getAttr_name() {
        return $this->attr_name;
    }

    function getValue() {
        return $this->value;
    }

    function setAttr_name($attr_name) {
        $this->attr_name = $attr_name;
    }

    function setValue($value) {
        $this->value = $value;
    }

}
