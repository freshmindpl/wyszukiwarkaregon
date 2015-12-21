<?php

namespace WyszukiwarkaRegon\Enum;

class GetValue
{
    const DATA_STATUS = 'StanDanych';
    const ERROR_CODE = 'KomunikatKod';
    const ERROR_MESSAGE = 'KomunikatTresc';
    const SESSION_STATUS = 'StatusSesji';
    const SERVICE_STATUS = 'StatusUslugi';
    const SERVICE_MESSAGE = 'KomunikatUslugi';

    const SEARCH_ERROR_CAPTCHA = 1;
    const SEARCH_ERROR_INVALIDARGUMENT = 2;
    const SEARCH_ERROR_NOTFOUND = 4;
    const SEARCH_ERROR_NOTAUTHORIZED = 5;
    const SEARCH_ERROR_SESSION = 7;
}
