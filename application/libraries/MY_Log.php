<?php

/**
 * Logger
 *
 * @date 01.04.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class MY_Log extends CI_Log {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Write cli log (level not matter)
     * @param string $msg - log message
     * @param string∕bool $file_name - name file log (if not string - 'log-'.date('Y-m-d'))
     * @param string $level - level log (config settings not matter)
     * @return boolean
     */
    public function write_cli_log($msg, $file_name, $level = 'INFO') {
        
        $level = is_string($level) ? strtoupper($level) : '';
        
        $file_name = is_string($file_name) ? strtolower($file_name) : 'log-'.date('Y-m-d');

        $filepath = $this->_log_path . 'cli/' . $file_name. '.log';
        $message = '';

        if (!$fp = @fopen($filepath, FOPEN_WRITE_CREATE)) {
            return FALSE;
        }

        $message .= $level . ' ' . (($level == 'INFO') ? ' -' : '-') . ' ' . date($this->_date_fmt) . ' --> ' . $msg . PHP_EOL;

        flock($fp, LOCK_EX);
        fwrite($fp, $message);
        flock($fp, LOCK_UN);
        fclose($fp);
        
        return TRUE;
    }

}
