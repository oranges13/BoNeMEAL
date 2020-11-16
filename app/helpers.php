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
