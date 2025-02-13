function remove(form) {
    var formData = new FormData(form);
    
    var submit_button = $(form).find("button[type='submit']")
    submit_button.prop("disabled", true)
    
    let invoice_id = $(form).attr("data-id")
    
    $.ajax({
        url: "/dashboard/invoices/" + invoice_id,  // Laravel route to handle name change
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            Swal.fire({
                text: "This invoice has been deleted successfully!",
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

$("#create-invoice-form").submit(function(e){
    e.preventDefault()

    var formData = new FormData(this)
    var submit_button = $(this).find("button[type='submit']")
    submit_button.prop("disabled", true)

    $.ajax({
        url: "/dashboard/invoices",
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            window.location = response.redirectUrl
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

$("#edit-invoice-form").submit(function(e){
    e.preventDefault()

    var formData = new FormData(this)
    var submit_button = $(this).find("button[type='submit']")
    submit_button.prop("disabled", true)

    const invoice_id = $(this).attr("data-id")

    $.ajax({
        url: "/dashboard/invoices/" + invoice_id,
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            Swal.fire({
                text: "Your changes has been saved successfully",
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


const AddServiceModal = new bootstrap.Modal('#AddServiceModal')

document.getElementById("openAddServiceModal").addEventListener('click', () => AddServiceModal.show())

document.getElementById("AddServiceModal").addEventListener('shown.bs.modal', () => {
    clear_add_form()
})

function clear_add_form()
{
    $("#AddServiceModal select[name='service_id']").val(null).trigger('change')
    $("#AddServiceModal input[name='quantity']").val("1")
    $("#AddServiceModal input[name='price']").val("0")
    $("#AddServiceModal input[name='discount']").val("0")
    $("#AddServiceModal select[name='discount_type']").val("fixed")
}

function calc_discount_amount(discount_type, discount, price, quantity)
{
    let discount_amount = 0

    if(discount_type == 'fixed')
    {
        discount_amount =  Math.min(discount, price * quantity);
    }
    else
    {
        discount_setup = Math.round((discount * price * quantity)) / 100;
        discount_amount = Math.min(discount_setup , price * quantity);
    }

    return discount_amount
}

function calc_service()
{
    const quantity = document.querySelector("input[name='quantity']").value ?? 0
    const price = document.querySelector("input[name='price']").value ?? 0
    const discount = document.querySelector("input[name='discount']").value ?? 0
    const discount_type = document.querySelector("select[name='discount_type']").value ?? 'fixed'

    let discount_amount = calc_discount_amount(discount_type, discount, price, quantity)

    document.querySelector('#AddServiceModal span#discount').innerText = discount_amount > 0 ? '-' + discount_amount : 0
    document.querySelector("#AddServiceModal span#subtotal").innerText = price * quantity
}

document.querySelectorAll('#AddServiceModal input').forEach(input => {
    input.addEventListener('input', () => calc_service())
})

document.querySelector('#AddServiceModal select#discount_type').addEventListener('change', () => calc_service())

document.querySelector('#AddServiceModal button.save').addEventListener('click', function() {
    const service_id = document.querySelector("#AddServiceModal select[name='service_id']").value
    const service_name = document.querySelector("#AddServiceModal select[name='service_id']").innerText
    const quantity = document.querySelector("input[name='quantity']").value ?? 0
    const price = document.querySelector("input[name='price']").value ?? 0
    const discount = document.querySelector("input[name='discount']").value ?? 0
    const discount_type = document.querySelector("select[name='discount_type']").value ?? 'fixed'
    const csrf = document.querySelector("input[name='_token']").value

    var submit_button = $(this)
    submit_button.prop("disabled", true)

    $.ajax({
        url: "/dashboard/check-add-service",
        method: 'POST',
        data: {
            service_id, quantity, price, discount, discount_type, _token: csrf
        },
        success: function(response) {
            let discount_amount = calc_discount_amount(discount_type, discount, price, quantity)

            service_table.row.add([
                `service_name`,
                price,
                quantity,
                discount_amount,
                (price * quantity) - discount_amount,
                `
                    <div class="d-flex gap-3 px-4">
                        <i class="ri-edit-box-line text-info fs-2" role="button"></i>
                        <i class="ri-delete-bin-line text-danger fs-2 remove_service" role="button"></i>
                    </div>
                `
            ]).draw(false)
            AddServiceModal.hide()
            Swal.fire({
                text: "Your changes has been saved successfully",
                icon: "success"
            });
            submit_button.prop("disabled", false)
            calculate_total()
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

$('#services_table tbody').on('click', '.remove_service', function () {
    var row = service_table.row($(this).closest('tr'));
    var rowData = row.data();
    
    row.remove().draw(false);
});

document.querySelector('#AddServiceModal button.save-add-new').addEventListener('click', function() {
    const service_id = document.querySelector("#AddServiceModal select[name='service_id']").value
    const service_name = document.querySelector("#AddServiceModal select[name='service_id']").innerText
    const quantity = document.querySelector("input[name='quantity']").value ?? 0
    const price = document.querySelector("input[name='price']").value ?? 0
    const discount = document.querySelector("input[name='discount']").value ?? 0
    const discount_type = document.querySelector("select[name='discount_type']").value ?? 'fixed'
    const csrf = document.querySelector("input[name='_token']").value

    var submit_button = $(this)
    submit_button.prop("disabled", true)

    $.ajax({
        url: "/dashboard/check-add-service",
        method: 'POST',
        data: {
            service_id, quantity, price, discount, discount_type, _token: csrf
        },
        success: function(response) {
            let discount_amount = calc_discount_amount(discount_type, discount, price, quantity)

            service_table.row.add([
                `service_name`,
                price,
                quantity,
                discount_amount,
                (price * quantity) - discount_amount,
                `
                    <div class="d-flex gap-3 px-4">
                        <i class="ri-edit-box-line text-info fs-2" role="button"></i>
                        <i class="ri-delete-bin-line text-danger fs-2" role="button"></i>
                    </div>
                `
            ]).draw(false)
            Swal.fire({
                text: "Your changes has been saved successfully",
                icon: "success"
            });
            submit_button.prop("disabled", false)
            clear_add_form()
            calculate_total()
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

function calculate_total()
{
    let total = 0;

    service_table.rows().every(function(){
        const data = this.data();

        total += data[4];
    })

    document.querySelector("span#total").innerText = total
}