<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DaemonController
 *
 * @author jmunoz
 */
class DaemonController {

    function __construct() {
        
    }

    function Get_Task_List($conn, $db_query) {
        $task_ls = array();
        $result = $conn->query($db_query->GET_TASK_LIST());
        while ($row = $result->fetch_assoc()) {
            array_push($task_ls, $row);
            $updt_task = $db_query->UPDATE_TASK_LIST($row['id']);
            $conn->query($updt_task);
        }
        return $task_ls;
    }

    /* function decrypt($encrypted_string) {
      $key = "123jms9.78@43&8.@";
      $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($encrypted_string), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
      return $decrypted;
      }

      function Create_Dynamic_Values_File($conn, $db_query, $id_task) {
      $result = $conn->query($db_query->GET_DYNAMIC_VALUES($id_task));
      while ($row = $result->fetch_assoc()) {
      if ($row['param_secure'] == 1) {
      $row['param_value'] = $this->decrypt($row['param_value']);
      }

      file_put_contents($id_task."_dynamic_values.json", json_encode($row), FILE_APPEND | LOCK_EX);
      }
      } */

    /* function Get_Script_Param_Attr_Values($conn, $db_query, $id_script_config) {
      $result = $conn->query($db_query->GET_SCRIPT_PARAM_ATTR_VALUES($id_script_config));
      $param_opt_script = array();

      while ($attr = $result->fetch_assoc()) {
      $param_scipt = new param_script();
      $param_scipt->setAttr_name($attr['param_name']);
      $param_scipt->setValue($attr['param_value']);
      array_push($param_opt_script, $param_scipt);
      }
      return $param_opt_script;
      }

      function Get_Resources_Values($conn, $db_query, $id_script_config, $inpt_opt) {
      $result = $conn->query($db_query->GET_RESOURCES_VALUES($id_script_config, $inpt_opt));
      $param_opt_script = array();

      $suf = $inpt_opt;
      while ($attr = $result->fetch_assoc()) {
      $param_scipt = new param_script();
      $param_scipt->setAttr_name($attr['name'] . "_" . $suf);
      $param_scipt->setValue($attr['value']);
      array_push($param_opt_script, $param_scipt);
      }
      return $param_opt_script;
      }

      function Set_Params_Attr_Values($conn, $db_query, $all_params, $id_task) {
      for ($i = 0; $i < sizeof($all_params); $i++) {
      // var_dump($all_params[$i]->getAttr_name().": ".$all_params[$i]->getValue());
      $attr_name = $all_params[$i]->getAttr_name();
      $attr_value = $all_params[$i]->getValue();
      $add_attr_value = $db_query->ADD_PARAM_ATTR_VALUES($id_task, $attr_name, $attr_value);
      $conn->query($add_attr_value);
      }
      } */
}
