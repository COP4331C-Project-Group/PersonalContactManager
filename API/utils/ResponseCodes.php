<?php
    enum ResponseCodes : int
    {
        case NOT_FOUND = 404;
        case CONFLICT = 409;
        case OK = 200;
        case NO_CONTENT = 204;
        case CREATED = 201;
        case METHOD_NOT_ALLOWED = 405;
    }
?>