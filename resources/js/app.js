window._ = require('lodash');

import $ from 'jquery';
window.$ = window.jQuery = $;
global.$ = global.jQuery = $;
import 'bootstrap';
import 'startbootstrap-sb-admin-2/js/sb-admin-2';
import 'jquery-validation';
import 'jquery-validation/dist/additional-methods';

$.validator.setDefaults({
    errorElement: 'span',
    errorClass: 'invalid-feedback',
    errorPlacement: function (error, element) {
        element.closest('.form-group, .col-sm').append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
        $(element).closest('.form-group, .col-sm').addClass('is-invalid');
        $(element).next('span.select2').addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
        $(element).closest('.form-group, .col-sm').removeClass('is-invalid');
        $(element).next('span.select2').removeClass('is-invalid');
    }
});

$.validator.addMethod("requiredIfEquals", function(value, element, params) {
    if ($(params[0].val() === params[1])) {
        return value.trim().length > 0;
    }
    return true;
}, "Required if {0} equals {1}");

$.validator.addMethod("requiredIfMysql", function(value, element) {
    if ($("#database_type").val() === "mysql") {
        return value.trim().length > 0;
    }
    return true;
}, 'Required if database type is "MySQL"');
