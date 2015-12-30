<?php

namespace WyszukiwarkaRegon\Exception;

class SearchException extends RegonException
{
    /**
     * @param string $message Exception message
     * @param int $code
     */
    public function __construct(
        $message,
        $code
    ) {
        parent::__construct($message, $code);
    }
}
