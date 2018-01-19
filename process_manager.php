<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProcessManager
 *
 * @author jmunoz
 */
class process_manager {

    protected $toRun = array();
    protected $pipes = array();
    protected $childProcs = array();
    protected $childResults = array();
    protected $startTimes = array();
    //protected $pause = 1000;
    protected $maxParallel = 0;

    /**
     * @todo add some options, f.e. throw an exception if any command can not start, or buffers for pipes
     */
    public function __construct() {
        
    }

    /**
     * Runs commands in parallel, waiting until all of them terminate.
     * @param array $commands array of strings. Do not forget to escape them while building them!
     * @param int $maxParallel set to 0 for no-limit
     * @param int $poll in microseconds, how long to sleep between polling for process termination
     * @return array
     * @throws \Exception
     */
    public function runParallel(array $commands, $maxParallel = 2, $poll = 1000000) {
        if (!count($commands)) {
            throw new \Exception("Can not run in parallel 0 commands");
        }
        $this->startChildren($commands, $maxParallel);
        do {
            // it's a good idea to sleep a while before we check pipes for the 1st time
            usleep($poll);
        } while ($this->waitFor() > 0);
        return $this->getResults();
    }

    /**
     * Starts commands in parallel - with a maximum parallel level (other commands are queued).
     * @param array $commands array of strings. Do not forget to escape them while building them!
     * @param int $maxParallel set to 0 for no-limit
     * @return int the number of processes started
     *
     * @see runParallel for an example loop using this function
     */
    public function startChildren(array $commands, $maxParallel = 0) {
        $this->toRun = $commands;
        $commandCount = count($this->toRun);
        $this->startTimes = array();
        $this->pipes = array();
        $this->childProcs = array();
        $this->childResults = array_fill(0, $commandCount, null);
        if ($maxParallel <= 0 || $maxParallel > $commandCount) {
            $maxParallel = $commandCount;
        }
        $this->maxParallel = $maxParallel;
        $started = 0;
        for ($i = 0; $i < $maxParallel; $i++) {
            if ($this->startChild($i)) {
                $started++;
            }
        }
        return $started;
    }

    /**
     * Checks if there are any child commands finished. If there are any queued, starts them
     * @return int number of running processes
     *
     * @see runParallel for an example loop using this function
     */
    public function waitFor() {
        $running = 0;
        $time = microtime(true);
        for ($i = 0; $i < count($this->childProcs); $i++) {
            if ($this->childProcs[$i] !== false) {
                $status = proc_get_status($this->childProcs[$i]);
                if ($status['running'] == false) {
                    $this->childResults[$i] = array_merge($status, array(
                        'output' => stream_get_contents($this->pipes[$i][1]),
                        'error' => stream_get_contents($this->pipes[$i][2]),
                        'return' => proc_close($this->childProcs[$i]),
                        'starttime' => $this->startTimes[$i],
                        'stoptime' => $time
                    ));
                    $this->childProcs[$i] = false;
                } else {
                    $this->childResults[$i] = $status;
                    $running++;
                }
            }
        }
        $started = count($this->childProcs);
        if ($started < count($this->toRun) && $running < $this->maxParallel) {
            for ($i = $running, $j = $started; $i < $this->maxParallel; $i++, $j++) {
                if ($this->startChild($j)) {
                    $running++;
                }
            }
        }
        return $running;
    }

    /**
     * Checks if child process i is running
     * @param int $i
     * @return bool
     */
    public function isRunning($i) {
        if ($i >= count($this->childProcs) || $this->childProcs[$i] == false)
            return false;
        $status = proc_get_status($this->childProcs[$i]);
        return $status['running'];
    }

    /**
     * Returns true if child process i was started (at least tried to)
     * @param int $i
     * @return bool
     */
    public function wasStarted($i) {
        return ( $i < count($this->childProcs) && $i >= 0 );
    }

    /**
     * Get results for either one process or all of them.
     * It can be used to retrieve f.e. the pid of a particular command, after waitFor has been called at least once
     * @param integer $i
     * @return array
     */
    public function getResults($i = null) {
        return $i !== null ? $this->childResults[$i] : $this->childResults;
    }

    protected function startChild($i) {
        $this->startTimes[$i] = microtime(true);
        $this->pipes[$i] = null;
        $this->childProcs[$i] = proc_open(
                $this->toRun[$i],
                /// @todo test if error pipe should use 'a' or 'w' on linux
                array(array('pipe', 'r'), array('pipe', 'w'), array('pipe', 'w')), $this->pipes[$i]
        );
        fclose($this->pipes[$i][0]);
        if (!$this->childProcs[$i]) {
            return false;
        }
        return true;
    }

}
