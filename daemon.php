<?php

/**
 * @author jmunoz
 */
include ('db/db_connect.php');
include ('db/db_query.php');
include ('entity/param_script.php');
include ('controller/DaemonController.php');

$db_query = new db_query();
$conn = new db_connect();
$conn = $conn->DB_Connection();
$daemon = new DaemonController();
$php_decrypt_url = 'http://172.16.1.167/php_decrypt_ws/index.php/';

$error_path = 'logs/error/';
$output_path = 'logs/output/';

// $output = array();
// $return = array();
// $file = 'opt.txt';
$flag = true;
$process_ls = array();

echo 'Running...!!!';

while ($flag) {
    sleep(5);
    $task_ls = $daemon->Get_Task_List($conn, $db_query); // Loading task and update status  


    for ($i = 0; $i < count($task_ls); $i++) {
        $task = $task_ls[$i];
        $script_lang = $task['script_language'];
        $path = $task['script_path'];
        $id_task = $task['id'];
        $id_user = $task['userexecute'];
        $id_config = $task['id_config'];
        if ($script_lang == 'JAVA') {
            $script_lang = 'java -jar';
        }

        $cmd = strtolower($script_lang) . " " . $path . " " . $id_task . " " . $id_user . " " . $id_config . " " . $php_decrypt_url;

        echo "------------\n";
        echo $cmd . "\n";

//        echo "------" . $cmd . "------\n";

        if (file_exists($output_path . 'output_task' . $task['id']) && file_exists($error_path . 'error_task_' . $task['id'])) {
            unlink($output_path . 'output_task' . $task['id']);
            unlink($error_path . 'error_task_' . $task['id']);
        }

        $process = proc_open($cmd, array(
            array('pipe', 'r'), // stdin is a pipe that the child will read from
//            array('file', 'input_task_' . $task['id'], 'a'), // stdout is a pipe that the child will write to
            array('file', $output_path . 'output_task' . $task['id'], 'a'),
            array('file', $error_path . 'error_task_' . $task['id'], 'a')), $pipes); // stderr is a file to write to

        $pid = proc_get_status($process)['pid'];
        echo('PID: ' . $pid . ' / ' . " Task:" . $task['id'] . "-->\033[32m Started \033[0m" . "\n");
        echo "------------\n";

        $process_task = array($process, $task['id']); //Setting array with process and task_id

        if (!in_array($process_task, $process_ls)) {
            array_push($process_ls, $process_task);
            $add_task_log = $db_query->ADD_TASK_LOG($task['id'], proc_get_status($process)['pid'], $task['userexecute'], 2/* proc_get_status($process)['running'] */);
            $conn->query($add_task_log);
        }
    }

//Checking process status
    $j = 0;
    if (count($process_ls) > 0) {
        foreach ($process_ls as $process) {
            if (!proc_get_status($process[0])['running']) {

                $pid = proc_get_status($process[0])['pid'];
                $status = proc_get_status($process[0])['running'];
                echo('PID: ' . $pid . ' / ' . " Task:" . $process[1] . " -->\033[31m Done \033[0m" . "\n");
                if (file_exists($error_path . 'error_task_' . $process[1]) && filesize($error_path . 'error_task_' . $process[1]) != 0) {
                    $error = file_get_contents($error_path . 'error_task_' . $process[1]);
                } else {
                    $error = "success";
                }

                $updt_task_log = $db_query->UPDATE_TASK_LOG($pid, addslashes($error), $status);
                $conn->query($updt_task_log);

//                $err_file = $error_path.'error_task_' . $process[1];
//                unlink($err_file);

                unset($process_ls[$j]); //remove process from process list
                $process_ls = array_values($process_ls); //order process lsit
            }
            $j++;
        }
    }
}
?>
