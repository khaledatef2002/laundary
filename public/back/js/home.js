function load_invoices_pi_chart()
{
    const ctx = $("#invoices_pi_chart")[0].getContext('2d')

    if(invoice_pi_chart)
        invoice_pi_chart.destroy()

    invoice_pi_chart = new Chart(ctx,
        {
            type: 'doughnut',
            data: {
                labels: [
                    lang.draft, lang.unpaid, lang.partially_paid, lang.paid
                ],
                datasets: [{
                    label: lang.count,
                    data: [draft_invoices, unpaid_invoices, partially_invoices, paid_invoices],
                    backgroundColor: [
                        'rgba(0, 0, 0, 0.8)',
                        'rgba(247, 75, 75, 0.7)',
                        'rgba(247,184,75, 0.7)',
                        'rgba(5, 215, 68, 0.7)'
                    ]
                }]
            }
        }
    )
}

function load_invoices_bar_chart()
{
    const ctx = $("#invoices_bar_chart")[0].getContext('2d')

    if(invoice_bar_chart)
        invoice_bar_chart.destroy()

    invoice_bar_chart = new Chart(ctx,
        {
            type: 'bar',
            data: {
                labels: Object.keys(money_per_day),
                datasets: [{
                    label: lang.count,
                    data: Object.values(money_per_day),
                    backgroundColor: [
                        'rgba(41,156,219, 0.8)',
                    ]
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        }
    )
}

function init()
{
    load_invoices_pi_chart()
    load_invoices_bar_chart()
}

init()