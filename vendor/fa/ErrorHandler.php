<?php

namespace fa;

class ErrorHandler
{

    public function __construct()
    {
        if (DEBUG) {
            error_reporting(-1);
        } else {
            error_reporting(0);
        }
        set_exception_handler([$this, 'exceptionHandler']);
        set_error_handler([$this, 'errorHandler']);
        ob_start();
        register_shutdown_function([$this, 'fatalErrorHandler']);
    }

    public function exceptionHandler(\Throwable $e)
    {
        $this->logError($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
        $this->displayError($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
    }

    protected function logError($code = '', $message = '', $file = '', $line = '')
    {
        file_put_contents(LOGS . '/errors.log', "[" . date('Y-m-d H:i:s') . "] Code: {$code} | Message: {$message} | File: {$file} | Line: {$line}\n", FILE_APPEND);
    }

    protected function displayError($code = '', $message = '', $file = '', $line = '')
    {
        echo "[" . date('Y-m-d H:i:s') . "] Code: {$code} | Message: {$message} | File: {$file} | Line: {$line}\n";
        die;
    }

    public function errorHandler($code = '', $message = '', $file = '', $line = '')
    {
        $this->logError($code, $message, $file, $line);
        $this->displayError($code, $message, $file, $line);
    }

    public function fatalErrorHandler()
    {
        $error = error_get_last();
        if (!empty($error) && $error['type'] & (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR)) {
            $this->logError($error['type'], $error['message'], $error['file'], $error['line']);
            ob_end_clean();
            $this->displayError($error['type'], $error['message'], $error['file'], $error['line']);
        } else {
            ob_end_flush();
        }
    }

}
