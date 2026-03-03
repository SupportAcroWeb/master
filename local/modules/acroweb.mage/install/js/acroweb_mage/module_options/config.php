<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
    die();
}

return [
    'css' => './module_options.css',
    'js' => './module_options.js',
    'rel' => ['jquery', 'ui', 'core'],
    'skip_core' => false,
];