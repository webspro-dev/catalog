<?php

namespace Websbro\DemoCatalog;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

class EventHandler
{
    public static function onAfterCacheClear(Event $event): EventResult
    {
        return new EventResult(EventResult::SUCCESS);
    }
}
