<?php

/**
 * Displays a check if extension is enabled or a red x if not
 * @param $ext string Extension Name
 * @return string Font Awesome formatted response
 */
function extension_check($ext) {
    if (extension_loaded($ext))
    {
        return "<i class='fa fa-check text-success'></i>";
    } else {
        return "<i class='fa fa-exclamation-triangle text-danger'></i>";
    }
}

/**
 * Displays a list of available timezones in an options array
 * @return array $options
 */
function list_all_available_timezones() {
    $default = date_default_timezone_get(); // Save
    $timestamp = time();
    $zones = [];
    foreach (timezone_identifiers_list() as $zone) {
        date_default_timezone_set($zone);
        $zones[$zone] = $zone . " " . 'UTC/GMT '. date('P', $timestamp);
    }
    date_default_timezone_set($default); // Restore
    return $zones;
}
