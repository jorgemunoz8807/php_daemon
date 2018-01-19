<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of date_validate
 *
 * @author jmunoz
 */
class process_task {

    var $process;
    var $task_id;

    function __construct() {
        
    }

    function getProcess() {
        return $this->process;
    }

    function getTask_id() {
        return $this->task_id;
    }

    function setProcess($process) {
        $this->process = $process;
    }

    function setTask_id($task_id) {
        $this->task_id = $task_id;
    }

}
