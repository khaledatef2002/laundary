$('.auto-image-show input[type="file"]').on('change', function() {
    var file = this.files[0];
    var input = $(this)
    if (file) {
        var reader = new FileReader();

        reader.onload = function(e) {
            input.closest(".auto-image-show").find("img").attr('src', e.target.result);
        }

        reader.readAsDataURL(file);
    }
});

// Remove Action
function remove_button(button){
    Swal.fire({
        title: "Do you really want to delete this person?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Delete",
        confirmButtonColor: "red",
    }).then((result) => {
        if (result.isConfirmed) {
            remove(button.closest("form"))
        }
    });
}


// login
$("form#login-form").submit(function(e){
    e.preventDefault();

    var form = $(this)
    var formData = new FormData(this);
    
    var submit_button = $(this).find("button[type='submit']")
    submit_button.prop("disabled", true)

    $.ajax({
        url: "login",  // Laravel route to handle name change
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            window.location = "/dashboard"
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