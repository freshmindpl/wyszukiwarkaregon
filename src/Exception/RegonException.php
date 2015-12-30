<?php

namespace WyszukiwarkaRegon\Exception;

class RegonException extends \RuntimeException
{
    /**
     * @param string $message Exception message
     * @param int $code
     * @param \Exception $previous Previous exception (if any)
     */
    public function __construct(
        $message,
        $code = 0,
        \Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
