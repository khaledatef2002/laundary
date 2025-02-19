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

    service_table.rows().every(function(index){
        const data = this.data()
        const tempDiv = $("<div>").html(data[5])
        const editIcon = tempDiv.find('.edit_service');

        const serviceId = editIcon.data('service-id');
        const discount = editIcon.data('discount');
        const discountType = editIcon.data('discount-type');
        const price = data[1]
        const quantity = data[2]

        formData.append(`services[${index}][service_id]`, serviceId);
        formData.append(`services[${index}][price]`, price);
        formData.append(`services[${index}][quantity]`, quantity);
        formData.append(`services[${index}][discount]`, discount);
        formData.append(`services[${index}][discount_type]`, discountType);
    })

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

    if(invoice_status == 'draft')
    {
        service_table.rows().every(function(index){
            const data = this.data()
            const tempDiv = $("<div>").html(data[5])
            const editIcon = tempDiv.find('.edit_service');
    
            const serviceId = editIcon.data('service-id');
            const discount = editIcon.data('discount');
            const discountType = editIcon.data('discount-type');
            const price = data[1]
            const quantity = data[2]

            const invoice_service_id = editIcon.data("invoice-service-id")

            if(invoice_service_id)
                formData.append(`services[${index}][invoice_service_id]`, invoice_service_id);
            else
                formData.append(`services[${index}][invoice_service_id]`, -1);
                
            formData.append(`services[${index}][service_id]`, serviceId);
            formData.append(`services[${index}][price]`, price);
            formData.append(`services[${index}][quantity]`, quantity);
            formData.append(`services[${index}][discount]`, discount);
            formData.append(`services[${index}][discount_type]`, discountType);
        })
    }
    else if(invoice_status != 'canceled') 
    {
        payment_table.rows().every(function(index){
            const data = this.data()
            const tempDiv = $("<div>").html(data[5])
            const editIcon = tempDiv.find('.edit_payment');

            const amount = data[0]
            const date = data[1]

            const invoice_payment_id = editIcon.data("invoice-payment-id")

            if(invoice_payment_id)
                formData.append(`payments[${index}][invoice_payment_id]`, invoice_payment_id);
            else
                formData.append(`payments[${index}][invoice_payment_id]`, -1);
    
            formData.append(`payments[${index}][amount]`, amount);
            formData.append(`payments[${index}][date]`, date);
        })
    }

    $.ajax({
        url: "/dashboard/invoices/" + invoice_id,
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            if(invoice_status != 'draft' && invoice_status != 'canceled')
            {
                const data = response

                $("#invoice_status").text(data.text)

                $("#invoice_status").removeClass("bg-secondary")
                $("#invoice_status").removeClass("bg-warning")
                $("#invoice_status").removeClass("bg-success")

                if(data.status == "unpaid")
                {
                    $("#invoice_status").addClass("bg-secondary")
                }
                else if(data.status == "partially_paid")
                {
                    $("#invoice_status").addClass("bg-warning")
                }
                else if(data.status == "paid")
                {
                    $("#invoice_status").addClass("bg-success")
                }

                invoice_status = data.status

                if(data.overtime)
                {
                    $("#invoice_overtime").removeClass('d-none')
                }
                else
                {
                    $("#invoice_overtime").addClass('d-none')
                }
            }

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

document.getElementById("openAddServiceModal")?.addEventListener('click', () => {
    $('#AddServiceModal select[name="service_id"]').select2({
        ...service_select_common_data,
        dropdownParent: document.getElementById('AddServiceModal')
    }).on('select2:select', function (e) {
        const data = e.params.data;
        const price =  data.price

        $("#AddServiceModal input[name='price']").val(price)

        calc_add_service()
    });

    AddServiceModal.show()

})

document.getElementById("AddServiceModal")?.addEventListener('shown.bs.modal', () => {
    clear_add_service_form()
})

function clear_add_service_form()
{
    $("#AddServiceModal select[name='service_id']").val(null).trigger('change')
    $("#AddServiceModal input[name='quantity']").val("1")
    $("#AddServiceModal input[name='price']").val("0")
    $("#AddServiceModal input[name='discount']").val("0")
    $("#AddServiceModal select[name='discount_type']").val("fixed")
}

function calc_discount_amount(discount_type, discount, price, quantity = 1)
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
document.querySelector('#AddServiceModal select#discount_type')?.addEventListener('change', () => calc_add_service())

document.querySelectorAll('#EditServiceModal input').forEach(input => {
    input.addEventListener('input', () => calc_edit_service())
})

document.querySelector('#EditServiceModal select#discount_type')?.addEventListener('change', () => calc_edit_service())

document.querySelector('#AddServiceModal button.save')?.addEventListener('click', function() {
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

            const row_data = [
                service_name,
                price,
                quantity,
                discount_amount,
                (price * quantity) - discount_amount
            ]

            if(invoice_status == 'draft')
                row_data.push(`
                    <div class="d-flex gap-3 px-4">
                        <i class="ri-edit-box-line text-info fs-2 edit_service" data-service-id="${service_id}" data-discount="${discount}" data-discount-type="${discount_type}" role="button"></i>
                        <i class="ri-delete-bin-line text-danger fs-2 remove_service" role="button"></i>
                    </div>
                `)

            service_table.row.add(row_data).draw(false)

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

$('#payment_table tbody').on('click', '.remove_payment', function () {
    const row = payment_table.row($(this).closest('tr'));
    const rowData = row.data();
    
    row.remove().draw(false);
});

document.querySelector('#AddServiceModal button.save-add-new')?.addEventListener('click', function() {
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

            const row_data = [
                service_name,
                price,
                quantity,
                discount_amount,
                (price * quantity) - discount_amount
            ]

            if(invoice_status == 'draft')
                row_data.push(`
                    <div class="d-flex gap-3 px-4">
                        <i class="ri-edit-box-line text-info fs-2 edit_service" data-service-id="${service_id}" data-discount="${discount}" data-discount-type="${discount_type}" role="button"></i>
                        <i class="ri-delete-bin-line text-danger fs-2 remove_service" role="button"></i>
                    </div>
                `)

            service_table.row.add(row_data).draw(false)
            
            Swal.fire({
                text: "Your changes has been saved successfully",
                icon: "success"
            });
            submit_button.prop("disabled", false)
            clear_add_service_form()
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

    const discount = $("form input[name='discount']").val() ?? 0
    const discount_type = $("form select[name='discount_type']").val() ?? 'fixed'

    const discount_amount = calc_discount_amount(discount_type, discount, total)

    document.querySelector("span#total").innerText = total - discount_amount
}

let selected_row = null

document.getElementById("EditServiceModal")?.addEventListener('hidden.bs.modal', () => {
    selected_row = null
})

$('#services_table tbody').on('click', '.edit_service', function () {
    selected_row = $(this).closest('tr')
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

        calc_edit_service()
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

document.querySelector('#EditServiceModal button.save')?.addEventListener('click', function() {
    const service_id = document.querySelector("#EditServiceModal select[name='service_id']").value
    const service_name = document.querySelector("#EditServiceModal select[name='service_id']").innerText
    const quantity = document.querySelector("#EditServiceModal input[name='quantity']").value ?? 0
    const price = document.querySelector("#EditServiceModal input[name='price']").value ?? 0
    const discount = document.querySelector("#EditServiceModal input[name='discount']").value ?? 0
    const discount_type = document.querySelector("#EditServiceModal select[name='discount_type']").value ?? 'fixed'
    const csrf = document.querySelector("input[name='_token']").value

    const invoice_service_id = selected_row.find(".edit_service").attr("data-invoice-service-id")

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

            const row = service_table.row(selected_row)
            const rowData = row.data()

            rowData[0] = service_name
            rowData[1] = price
            rowData[2] = quantity
            rowData[3] = discount_amount
            rowData[4] = (price * quantity) - discount_amount

            if(invoice_status == 'draft')
                rowData[5] = `
                        <div class="d-flex gap-3 px-4">
                            <i class="ri-edit-box-line text-info fs-2 edit_service" ${invoice_service_id ? "data-invoice-service-id='" + invoice_service_id + "'" : ''} data-service-id="${service_id}" data-discount="${discount}" data-discount-type="${discount_type}" role="button"></i>
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

$("#cancel_button").click(function(){
    let button = $(this)
    Swal.fire({
        title: "Do you really want to cancel this invoice?",
        text: "All payment history will be lost!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Cancel",
        confirmButtonColor: "red",
    }).then((result) => {
        if (result.isConfirmed) {
            const invoice_id = $("#edit-invoice-form").attr("data-id")
            const csrf = document.querySelector("input[name='_token']").value

            button.prop("disabled", true)

            $.ajax({
                url: `/dashboard/invoice/${invoice_id}/cancel`,
                method: 'POST',
                data: {_token: csrf},
                success: function(response) {
                    location.reload()
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
    });
})

$("#draft_button").click(function(){
    let button = $(this)
    Swal.fire({
        title: "Do you really want to set this invoice as draft?",
        text: "All payment history will be lost!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Draft",
        confirmButtonColor: "black",
    }).then((result) => {
        if (result.isConfirmed) {
            const invoice_id = $("#edit-invoice-form").attr("data-id")
            const csrf = document.querySelector("input[name='_token']").value
            
            button.prop("disabled", true)

            $.ajax({
                url: `/dashboard/invoice/${invoice_id}/draft`,
                method: 'POST',
                data: {_token: csrf},
                success: function(response) {
                    location.reload()
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
    });
})

$("#confirm_button").click(function(){
    const invoice_id = $("#edit-invoice-form").attr("data-id")
    let button = $(this)

    const csrf = document.querySelector("input[name='_token']").value
    
    button.prop("disabled", true)

    $.ajax({
        url: `/dashboard/invoice/${invoice_id}/confirm`,
        method: 'POST',
        data: {_token: csrf},
        success: function(response) {
            location.reload()
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


// Payment
const AddPaymentModal = new bootstrap.Modal('#AddPaymentModal')
const EditPaymentModal = new bootstrap.Modal('#EditPaymentModal')

document.getElementById("openAddPaymentModal")?.addEventListener('click', () => AddPaymentModal.show())

document.getElementById("AddPaymentModal")?.addEventListener('shown.bs.modal', () => {
    clear_add_payment_form()
})

function clear_add_payment_form()
{
    $("#AddPaymentModal input[name='amount']").val("0")
    $("#AddPaymentModal input[name='date']").val($("#AddPaymentModal input[name='date']").attr("data-deafult-date"))
}
document.querySelector('#AddPaymentModal button.save')?.addEventListener('click', function() {
    const amount = document.querySelector("#AddPaymentModal input[name='amount']").value
    const date = document.querySelector("#AddPaymentModal input[name='date']").value
    const csrf = document.querySelector("input[name='_token']").value
    const invoice_id = document.querySelector("form#edit-invoice-form").getAttribute("data-id")

    var submit_button = $(this)
    submit_button.prop("disabled", true)

    $.ajax({
        url: "/dashboard/check-add-payment/" + invoice_id,
        method: 'POST',
        data: {
            amount, date, _token: csrf
        },
        success: function(response) {
            let total_before = 0
            payment_table.rows().every(function(){
                const rowData = this.data();
                total_before += Number(rowData[0])
            })

            const total_required = Number($("span#total").text())

            if(total_required < total_before + Number(amount))
            {
                Swal.fire({
                    text: `By applying these changes the total paid amount will be ${total_before + Number(amount)} which is more than needed.`,
                    icon: "error"
                });

                submit_button.prop("disabled", false)
                return false
            }

            const row_data = [
                amount,
                date,
                `
                    <div class="d-flex gap-3 px-4">
                        <i class="ri-edit-box-line text-info fs-2 edit_payment" role="button"></i>
                        <i class="ri-delete-bin-line text-danger fs-2 remove_payment" role="button"></i>
                    </div>
                `
            ]

            payment_table.row.add(row_data).draw(false)

            AddPaymentModal.hide()
            Swal.fire({
                text: "Your changes has been saved successfully",
                icon: "success"
            });
            submit_button.prop("disabled", false)
            calculate_total()
            calculate_paid_remaining()
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

let selected_row_payment = null

document.getElementById("EditPaymentModal")?.addEventListener('hidden.bs.modal', () => {
    selected_row_payment = null
})

$('#payment_table tbody').on('click', '.edit_payment', function () {
    selected_row_payment = $(this).closest('tr')
    const row = payment_table.row($(this).closest('tr'));
    const rowData = row.data();

    const amount = rowData[0]
    const date = rowData[1]

    $("#EditPaymentModal input[name='amount']").val(amount)
    $("#EditPaymentModal input[name='date']").val(date)
    $("#EditPaymentModal input[name='date']").attr("data-deafult-date", date)

    EditPaymentModal.show()
});

document.querySelector('#EditPaymentModal button.save')?.addEventListener('click', function() {
    const amount = document.querySelector("#EditPaymentModal input[name='amount']").value ?? 0
    const date = document.querySelector("#EditPaymentModal input[name='date']").value
    const csrf = document.querySelector("input[name='_token']").value

    const invoice_id = document.querySelector("form#edit-invoice-form").getAttribute("data-id")
    const invoice_payment_id = selected_row_payment.find(".edit_payment").attr("data-invoice-payment-id")

    var submit_button = $(this)
    submit_button.prop("disabled", true)

    $.ajax({
        url: "/dashboard/check-add-payment/" + invoice_id + (invoice_payment_id ? '/' + invoice_payment_id : ''),
        method: 'POST',
        data: {
            amount, date, _token: csrf
        },
        success: function(response) {
            const row = payment_table.row(selected_row_payment)
            const rowData = row.data()

            let total_before = 0
            payment_table.rows().every(function(){
                const rowData = this.data();
                total_before += Number(rowData[0])
            })

            total_before -= Number(rowData[0])

            const total_required = Number($("span#total").text())

            if(total_required < total_before + Number(amount))
            {
                Swal.fire({
                    text: `By applying these changes the total paid amount will be ${total_before + Number(amount)} which is more than needed.`,
                    icon: "error"
                });

                submit_button.prop("disabled", false)
                return false
            }

            rowData[0] = amount
            rowData[1] = date
            
            row.data(rowData).draw()

            Swal.fire({
                text: "Your changes has been saved successfully",
                icon: "success"
            });
            submit_button.prop("disabled", false)
            EditPaymentModal.hide()
            calculate_total()
            calculate_paid_remaining()
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

function calculate_paid_remaining()
{
    let total_paid = 0
    payment_table.rows().every(function(){
        const rowData = this.data();
        total_paid += Number(rowData[0])
    })

    const required = Number($("span#total").text())

    $("span#paid").text(total_paid)
    $("span#remaining").text(required - total_paid)
}