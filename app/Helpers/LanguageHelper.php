<?php

/**
 * Lists all the available languages in the resources/lang folder
 * @return array|false
 */
function get_all_available_languages() {
    $languages = array_map('basename', array_diff(glob(resource_path('lang') . '/*', GLOB_ONLYDIR), array('..', '.')));
    return $languages;
}
