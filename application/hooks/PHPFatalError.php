<?php

class PHPFatalError {

    public function setHandler() {
        if (ENVIRONMENT !== 'development')
            register_shutdown_function(array($this, 'handleShutdown'));
    }

    public function handleShutdown() {

        if (($error = error_get_last())) {
            
            // crutch
            // @see https://bugs.php.net/bug.php?id=66763
            // @see https://github.com/piwik/piwik/issues/6465 
            // etc.
            if(isset($error['message']) && strpos($error['message'], 'HTTP_RAW_POST_DATA') !== FALSE)
                exit();
            
            if (!ob_get_length()) {
                ob_start();
            }

            $buffer = ob_get_contents();
            ob_end_clean();


            $heading = '500 Ошибка сервера';

            $_error = & load_class('Exceptions', 'core', 'MY_');

            echo $_error->show_error($heading, $error);

            ob_flush();

            $buffer = ob_get_contents();
            ob_end_clean();
            exit();
        }
    }

}
