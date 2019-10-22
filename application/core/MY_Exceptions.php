<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!class_exists('CI_Exceptions'))
    require ( BASEPATH . 'core' . DIRECTORY_SEPARATOR . 'Exceptions.php');

class MY_Exceptions extends CI_Exceptions {

    public $layout = 'views/front/';

    /**
     * 404 Page Not Found Handler
     *
     * @access	private
     * @param	string	the page
     * @param 	bool	log error yes/no
     * @return	string
     */
    public function show_404($page = '', $log_error = TRUE) {
        $heading = "404 Страница не найдена";
        $message = "Увы, но страницы с таким адресом у нас нет. Попробуйте посетить главную страницу и воспользоваться поиском.";

        // By default we log this, but allow a dev to skip it
        if ($log_error) {
            log_message('error', '404 Page Not Found --> ' . $page);
        }

        echo $this->show_error($heading, $message, 'error_404', 404);
        exit;
    }

    /**
     * General Error Page
     *
     * This function takes an error message as input
     * (either as a string or an array) and displays
     * it using the specified template.
     *
     * @access	private
     * @param	string	the heading
     * @param	string	the message
     * @param	string	the template name
     * @param 	int		the status code
     * @return	string
     */
    public function show_error($heading, $message, $template = 'error_general', $status_code = 500) {
        set_status_header($status_code);
        
        if (in_array(ENVIRONMENT, ['development','beta']))
            vdump ($message);

        if (is_array($message)) {
//            vdump($message);
            if (isset($message['type']))
                log_message('ERROR', 'Severity: '
                        . (isset($message['type']) ? $message['type'] : "")
                        . '  --> ' . (isset($message['message']) ? $message['message'] : "")
                        . ' ' . (isset($message['file']) ? $message['file'] : "")
                        . ' ' . (isset($message['line']) ? $message['line'] : ""));
            else
                log_message('ERROR', 'Severity: ' . 'undefined' . '  --> ' . implode(' | ', $message), '');
//        $this->log_exception($message['type'], $message['message'], $message['file'], $message['line']);   
        } else {
            log_message('ERROR', 'Severity: ' . 'undefined' . '  --> ' . $message);
        }

//        $message = '<p>' . implode('</p><p>', (!is_array($message)) ? array($message) : $message) . '</p>';
        if ($status_code !== 404)
            $message = 'У нас возникли некоторые неполадки, мы уже работаем над их устранением. Попробуйте зайти к нам позднее.';

        if (ob_get_level() > $this->ob_level + 1) {
            ob_end_flush();
        }
        ob_start();
        include( APPPATH . $this->layout . 'errors/' . $template . '.php');
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }

    /**
     * Native PHP error handler
     *
     * @access	private
     * @param	string	the error severity
     * @param	string	the error string
     * @param	string	the error filepath
     * @param	string	the error line number
     * @return	string
     */
    function show_php_error($severity, $message, $filepath, $line) {

        if (!in_array(ENVIRONMENT, ['development','beta']))
            return FALSE;

        $severity = (!isset($this->levels[$severity])) ? $severity : $this->levels[$severity];

        $filepath = str_replace("\\", "/", $filepath);

        // For safety reasons we do not show the full file path
        if (FALSE !== strpos($filepath, '/')) {
            $x = explode('/', $filepath);
            $filepath = $x[count($x) - 2] . '/' . end($x);
        }

        if (ob_get_level() > $this->ob_level + 1) {
            ob_end_flush();
        }
        ob_start();
        include( APPPATH . $this->layout . 'errors/error_php.php');
        $buffer = ob_get_contents();
        ob_end_clean();
        echo $buffer;
    }

}
