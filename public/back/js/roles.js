$("#create-role-form").submit(function(e){
    e.preventDefault()

    var formData = new FormData(this)
    var submit_button = $(this).find("button[type='submit']")
    submit_button.prop("disabled", true)

    $.ajax({
        url: "/dashboard/roles",
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            $("input:not([name='_token']):not([type=['checkbox'])").val("")
            $("input:not([name='_token'][type=['checkbox']])").prop("checked", false)
            submit_button.prop("disabled", false)
            Swal.fire({
                text: lang.create_message,
                icon: "error"
            });
        },
        error: function(xhr) {
            var errors = xhr.responseJSON.errors;
            var firstKey = Object.keys(errors)[0];
            Swal.fire({
                text: errors[firstKey][0],
                icon: "error"
            });
            submit_button.prop("disabled", false)
        }
    });
})

function remove(form) {
    var formData = new FormData(form);
    
    var submit_button = $(form).find("button[type='submit']")
    submit_button.prop("disabled", true)
    
    let role_id = $(form).attr("data-id")
    
    $.ajax({
        url: "/dashboard/roles/" + role_id,  // Laravel route to handle name change
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            Swal.fire({
                text: lang.delete_message,
                icon: "success"
            });
            submit_button.prop("disabled", false)
            table.ajax.reload(null, false)
        },
        error: function(xhr) {
            var errors = xhr.responseJSON.errors;
            var firstKey = Object.keys(errors)[0];
            Swal.fire({
                text: errors[firstKey][0],
                icon: "error"
            });
            submit_button.prop("disabled", false)
        }
    });
}

$("#edit-role-form").submit(function(e){
    e.preventDefault()

    var formData = new FormData(this)

    var submit_button = $(this).find("button[type='submit']")
    submit_button.prop("disabled", true)

    let role_id = $(this).attr("data-id")

    $.ajax({
        url: "/dashboard/roles/" + role_id,
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            Swal.fire({
                text: lang.update_message,
                icon: "success"
            });
            submit_button.prop("disabled", false)
        },
        error: function(xhr) {
            var errors = xhr.responseJSON.errors;
            var firstKey = Object.keys(errors)[0];
            Swal.fire({
                text: errors[firstKey][0],
                icon: "error"
            });
            submit_button.prop("disabled", false)
        }
    });
})