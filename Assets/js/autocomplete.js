/**
 * Created by strem on 16.09.2015.
 */

$(document).ready(function () {
    $(function () {
        $("#devicename").autocomplete({
            source: '/suggester.php?devicename',
            selectFirst: true,
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });
    $(function () {
        $("#devicemanufac").autocomplete({
            source: '/suggester.php?devicemanufac',
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });
    $(function () {
        $("#devicemodel").autocomplete({
            source: '/suggester.php?devicemodel',
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });
    $(function () {
        $("#deviceserial").autocomplete({
            source: '/suggester.php?deviceserial',
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });
    $(function () {
        $("#devicetype").autocomplete({
            source: '/suggester.php?devicetype',
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });
    $(function () {
        $("#devicecategory").autocomplete({
            source: '/suggester.php?devicecategory',
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });
    $(function () {
        $("#devicedescription").autocomplete({
            source: '/suggester.php?devicedescription',
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });
    $(function () {
        $("#deviceowner").autocomplete({
            source: '/suggester.php?deviceowner',
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });
    $(function () {
        $("#ordername").autocomplete({
            source: '/suggester.php?ordername',
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });
    $(function () {
        $("#clientname").autocomplete({
            source: '/suggester.php?clientname',
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });
    $(function () {
        $("#organizationName").autocomplete({
            source: '/suggester.php?organizationName',
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });
    $(function () {
        $("#FirstName").autocomplete({
            source: '/suggester.php?FirstName',
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });
    $(function () {
        $("#LastName").autocomplete({
            source: '/suggester.php?LastName',
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });
    $(function () {
        $("#laborName").autocomplete({
            source: '/suggester.php?laborName',
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });
    $(function () {
        $("#laborDescription").autocomplete({
            source: '/suggester.php?laborDescription',
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });
    $(function () {
        $("#speedCode").autocomplete({
            source: '/suggester.php?speedCode',
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });
    $(function () {
        $("#paymentName").autocomplete({
            source: '/suggester.php?paymentName',
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });
    $(function () {
        $("#paymentFrom").autocomplete({
            source: '/suggester.php?paymentFrom',
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });
    $(function () {
        $("#paymentTo").autocomplete({
            source: '/suggester.php?paymentTo',
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });
    $(function () {
        $("#paymentDescription").autocomplete({
            source: '/suggester.php?paymentDescription',
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });
    $(function () {
        $("#query").autocomplete({
            source: '/suggester.php?serchQuery',
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });

    $(function () {
        $("#query").autocomplete({
            source: '/suggester.php?serchQuery',
            minLength: 1,
            delay: 300,
            noCache: false
        });
    });

});