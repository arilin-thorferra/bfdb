<?php

function handler($e)
{
    // TODO handle this differently if not in debug mode!
    Context::debug($e->getMessage());
    Context::debug($e->getTraceAsString());
    echo "<div style='color:#900'><p><b>Uncaught exception!</b></p><pre>\n";
    echo Context::get('debugmsg');
    echo "</pre>\n";
}

set_exception_handler('handler');
