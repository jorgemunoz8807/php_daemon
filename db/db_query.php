<?php

/**
 * Description of db_query
 *
 * @author jmunoz
 */
class db_query {

    function __construct() {
        
    }

    /**
     * Getting all task to execute
     * @return string
     */
    function GET_TASK_LIST() {
        $sql = "SELECT task.*"
                . " FROM `a_task` task"
                . " WHERE"
                . " task.status = 1;";
        return $sql;
    }

    function GET_DYNAMIC_VALUES($id_task) {
        $sql = "SELECT param_name, param_value, param_secure"
                . " FROM `a_script_dynamic_params`"
                . " WHERE"
                . " id_task = $id_task;";
        return $sql;
    }

    /**
     * Update task
     * @param type $id_task
     * @return string
     */
    function UPDATE_TASK_LIST($id) {
        $sql = "UPDATE `a_task`"
                . "SET "
                . "`status` = 0"
                . " WHERE "
                . "id = '$id';";
        return $sql;
    }

    /**
     * Getting attribute
     * @param type $script_id
     * @return string
     */
    function GET_SCRIPT_PARAM_ATTR_VALUES($id_script_config) {
        $sql = "SELECT sp.param_name, spv.param_value "
                . "FROM `a_script_param_values` spv, "
                . "`a_script_params` sp "
                . "WHERE "
                . " spv .id_script_config = '19' "
                . " AND spv.id_script_param = sp.id;";
        return $sql;
    }

    /**
     * 
     * @param type $id_script_config
     * @return string
     */
    function GET_RESOURCES_VALUES($id_script_config, $inpt_opt) {
        $sql = "SELECT ra.`name` , rv.`value`"
                . "FROM "
                . "    `a_script_resources_" . $inpt_opt . "` ri, "
                . "    `a_resources` r, "
                . "    `a_resources_values` rv, "
                . "    `a_resources_attributes` ra "
                . "WHERE "
                . "    ri.id_script_config = '" . $id_script_config . "' "
                . "        AND ri.id_resource = r.id "
                . "        AND rv.id_resource = r.id "
                . "        AND ra.id = rv.id_attribute;";
        return $sql;
    }

    function ADD_PARAM_ATTR_VALUES($id_task, $attr_name, $attr_value) {
        $sql = "INSERT INTO `a_script_dynamic_params` "
                . "SET id_task = '" . $id_task . "', "
                . "param_name = '" . $attr_name . "', "
                . "param_value = '" . $attr_value . "';";

        return $sql;
    }

    function ADD_TASK_LOG($id_task, $pid, $userexecute, $status) {
        $sql = "INSERT INTO `a_task_log`"
                . " (`id_task`, `pid`, `error`, `userexecute`, `status`)"
                . " VALUES($id_task, $pid, 'running', $userexecute, $status)";
//        var_dump($sql);
//        die;
        return $sql;
    }

    function UPDATE_TASK_LOG($pid, $error, $status) {
        $sql = "   UPDATE `a_task_log` "
                . "SET `error` = '" . $error . "', "
                . "`status` = IF(error = 'Success', 1, 4) "
                . "WHERE `pid` = $pid "
                . "AND `status` = 2;";
//        var_dump($sql);
        return $sql;
    }

}

?>