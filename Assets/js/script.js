$(function () {

    $("#datepicker").datepicker();

});

function post(path, params, method) {
    method = method || "post"; // Set method to post by default if not specified.

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

    for(var key in params) {
        if(params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
        }
    }

    document.body.appendChild(form);
    form.submit();
}

function fillUpFormLabors()
{
    var input=document.getElementById('speedCode');

    if (input.value.length==3)
    {
        var name=document.getElementById('laborName');
        var desc=document.getElementById('laborDescription');
        var qua=document.getElementById('laborQuantity');
        var cost=document.getElementById('laborCost');
        var arr = [];

        $.ajax({
            type: "GET",
            url: "/suggester.php",
            data: "laborTemplate&term=" + input.value,
            success: function(msg){
                reports = eval( '('+msg+')' );
                name.value = reports[1];
                qua.value = reports[2];
                desc.value = reports[3];
                cost.value = reports[4];
            }
        });
    }
}