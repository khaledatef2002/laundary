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
const EditServiceModal = new bootstrap.Modal('#EditServiceModal')

document.getElementById("openAddServiceModal").addEventListener('click', () => {
    $('#AddServiceModal select[name="service_id"]').select2({
        ...service_select_common_data,
        dropdownParent: document.getElementById('AddServiceModal')
    }).on('select2:select', function (e) {
        const data = e.params.data;
        const price =  data.price

        $("#AddServiceModal input[name='price']").val(price)

        calc_service()
    });

    AddServiceModal.show()

})

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

function calc_add_service()
{
    const quantity = document.querySelector("#AddServiceModal input[name='quantity']").value ?? 0
    const price = document.querySelector("#AddServiceModal input[name='price']").value ?? 0
    const discount = document.querySelector("#AddServiceModal input[name='discount']").value ?? 0
    const discount_type = document.querySelector("#AddServiceModal select[name='discount_type']").value ?? 'fixed'

    let discount_amount = calc_discount_amount(discount_type, discount, price, quantity)

    document.querySelector('#AddServiceModal span#discount').innerText = discount_amount > 0 ? '-' + discount_amount : 0
    document.querySelector("#AddServiceModal span#subtotal").innerText = price * quantity
}

function calc_edit_service()
{
    const quantity = document.querySelector("#EditServiceModal input[name='quantity']").value ?? 0
    const price = document.querySelector("#EditServiceModal input[name='price']").value ?? 0
    const discount = document.querySelector("#EditServiceModal input[name='discount']").value ?? 0
    const discount_type = document.querySelector("#EditServiceModal select[name='discount_type']").value ?? 'fixed'

    let discount_amount = calc_discount_amount(discount_type, discount, price, quantity)

    document.querySelector('#EditServiceModal span#discount').innerText = discount_amount > 0 ? '-' + discount_amount : 0
    document.querySelector("#EditServiceModal span#subtotal").innerText = price * quantity
}

document.querySelectorAll('#AddServiceModal input').forEach(input => {
    input.addEventListener('input', () => calc_add_service())
})
document.querySelector('#AddServiceModal select#discount_type').addEventListener('change', () => calc_add_service())

document.querySelectorAll('#EditServiceModal input').forEach(input => {
    input.addEventListener('input', () => calc_edit_service())
})

document.querySelector('#EditServiceModal select#discount_type').addEventListener('change', () => calc_edit_service())

document.querySelector('#AddServiceModal button.save').addEventListener('click', function() {
    const service_id = document.querySelector("#AddServiceModal select[name='service_id']").value
    const service_name = document.querySelector("#AddServiceModal select[name='service_id']").innerText
    const quantity = document.querySelector("#AddServiceModal input[name='quantity']").value ?? 0
    const price = document.querySelector("#AddServiceModal input[name='price']").value ?? 0
    const discount = document.querySelector("#AddServiceModal input[name='discount']").value ?? 0
    const discount_type = document.querySelector("#AddServiceModal select[name='discount_type']").value ?? 'fixed'
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
                service_name,
                price,
                quantity,
                discount_amount,
                (price * quantity) - discount_amount,
                `
                    <div class="d-flex gap-3 px-4">
                        <i class="ri-edit-box-line text-info fs-2 edit_service" data-service-id="${service_id}" data-discount="${discount}" data-discount-type="${discount_type}" role="button"></i>
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
    const row = service_table.row($(this).closest('tr'));
    const rowData = row.data();
    
    row.remove().draw(false);
});

document.querySelector('#AddServiceModal button.save-add-new').addEventListener('click', function() {
    const service_id = document.querySelector("#AddServiceModal select[name='service_id']").value
    const service_name = document.querySelector("#AddServiceModal select[name='service_id']").innerText
    const quantity = document.querySelector("#AddServiceModal input[name='quantity']").value ?? 0
    const price = document.querySelector("#AddServiceModal input[name='price']").value ?? 0
    const discount = document.querySelector("#AddServiceModal input[name='discount']").value ?? 0
    const discount_type = document.querySelector("#AddServiceModal select[name='discount_type']").value ?? 'fixed'
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
                service_name,
                price,
                quantity,
                discount_amount,
                (price * quantity) - discount_amount,
                `
                    <div class="d-flex gap-3 px-4">
                        <i class="ri-edit-box-line text-info fs-2 edit_service" data-service-id="${service_id}" data-discount="${discount}" data-discount-type="${discount_type}" role="button"></i>
                        <i class="ri-delete-bin-line text-danger fs-2 remove_service" role="button"></i>
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

$('#services_table tbody').on('click', '.edit_service', function () {
    const row = service_table.row($(this).closest('tr'));
    const rowData = row.data();
    const service_id = $(this).attr("data-service-id")
    const discount = $(this).attr("data-discount")
    const discount_type = $(this).attr("data-discount-type")
    const discount_amount = calc_discount_amount(discount_type, discount, rowData[1], rowData[2])

    $('#EditServiceModal select[name="service_id"]').select2({
        ...service_select_common_data,
        dropdownParent: document.getElementById('EditServiceModal')
    }).on('select2:select', function (e) {
        const data = e.params.data;
        const price =  data.price

        $("#EditServiceModal input[name='price']").val(price)

        calc_service()
    });

    const option = new Option(rowData[0], service_id, true, true);
    $('#EditServiceModal select[name="service_id"]').append(option).trigger('change');

    $("#EditServiceModal input[name='price']").val(rowData[1]);
    
    $("#EditServiceModal input[name='quantity']").val(rowData[2]);

    $("#EditServiceModal input[name='discount']").val(discount);
    
    $("#EditServiceModal select[name='discount_type']").val(discount_type);
    
    $("#EditServiceModal span#discount").text(discount_amount);

    $("#EditServiceModal span#subtotal").text(Number(rowData[1]) * Number(rowData[2]));

    EditServiceModal.show()
});

document.querySelector('#EditServiceModal button.save').addEventListener('click', function() {
    const service_id = document.querySelector("#EditServiceModal select[name='service_id']").value
    const service_name = document.querySelector("#EditServiceModal select[name='service_id']").innerText
    const quantity = document.querySelector("#EditServiceModal input[name='quantity']").value ?? 0
    const price = document.querySelector("#EditServiceModal input[name='price']").value ?? 0
    const discount = document.querySelector("#EditServiceModal input[name='discount']").value ?? 0
    const discount_type = document.querySelector("#EditServiceModal select[name='discount_type']").value ?? 'fixed'
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

            const row = service_table.row($(`#services_table tr td [data-service-id='${service_id}']`).closest('tr'))
            const rowData = row.data()

            rowData[0] = service_name
            rowData[1] = price
            rowData[2] = quantity
            rowData[3] = discount_amount
            rowData[4] = (price * quantity) - discount_amount
            rowData[5] = `
                    <div class="d-flex gap-3 px-4">
                        <i class="ri-edit-box-line text-info fs-2 edit_service" data-service-id="${service_id}" data-discount="${discount}" data-discount-type="${discount_type}" role="button"></i>
                        <i class="ri-delete-bin-line text-danger fs-2 remove_service" role="button"></i>
                    </div>
                `
            
            row.data(rowData).draw()

            Swal.fire({
                text: "Your changes has been saved successfully",
                icon: "success"
            });
            submit_button.prop("disabled", false)
            EditServiceModal.hide()
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