<html>
    <head>
        <meta charset="utf-8" />
        <title>{{ $settings->title }} - Invoice</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
        <meta content="Themesbrand" name="author" />
        <!-- App favicon -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
    
        <link rel="icon" href="{{ asset($settings->logo) }}">
        
        {{-- RTL FILES --}}
        @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
            <!-- Bootstrap Css -->
            <link href="{{ asset('back') }}/css/bootstrap-rtl.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        @else
            <!-- Bootstrap Css -->
            <link href="{{ asset('back') }}/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        @endif
        
        <!-- custom Css-->
        <link href="{{ asset('back') }}/css/invoice_template.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <div class="card border-0 rounded-3 col-lg-4 col-11 p-3">
            <div class="image-container">
                <img src="{{ asset($settings->logo) }}">
            </div>
            <p class="fw-bold text-center fs-5 mt-1 mb-0">{{ $settings->title }}</p>
            <hr>
            
            <div class="d-flex align-items-center justify-content-between mb-1">
                <div>
                    <p class="mb-0">
                        <span class="fw-bold">To: </span>
                        {{ $invoice->client->name }} 
                        @if ($invoice->client->email)
                            - {{ $invoice->client->email }}
                        @endif
                    </p>
                    <p class="mb-0">
                        <span class="fw-bold">Phone: </span>
                        {{ $invoice->client->phone }}
                    </p>
                    <p class="mb-0">
                        <span class="fw-bold">Address: </span>
                        {{ $invoice->client->address }}
                    </p>
                </div>
                <div>
                    <p class="mb-0">#{{ $invoice->invoice_number }}</p>
                    <p class="mb-0">{{ date("M d, Y", strtotime($invoice->due_date)) }}</p>
                </div>
            </div>
            <table class="w-100 table table-striped text-center my-2">
                <tr class="table-dark">
                    <th class="border">Service</th>
                    <th class="border">Quantity</th>
                    <th class="border">Price Per Unit</th>
                    <th class="border">Discount</th>
                    <th class="border">Subtotal</th>
                </tr>
                @foreach ($invoice->services as $service)
                    <tr>
                        <td class="border">{{ $service->service->title }}</td>
                        <td class="border">{{ $service->quantity }}</td>
                        <td class="border">{{ $service->price }}</td>
                        <td class="border">{{ $service->discount_amount }}</td>
                        <td class="border">{{ $service->total_amount }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" style="background: transparent;border: 0;box-shadow: none;"></td>
                    <td class="bg-dark text-white border" colspan="2">Total:</td>
                    <td class="border">{{ $invoice->subtotal }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="background: transparent;border: 0;box-shadow: none;"></td>
                    <td class="bg-dark text-white border" colspan="2">Additional Discount:</td>
                    <td class="border">{{ $invoice->discount_amount }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="background: transparent;border: 0;box-shadow: none;"></td>
                    <td class="bg-dark text-white border" colspan="2">Paid:</td>
                    <td class="border">{{ $invoice->paid_amount }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="background: transparent;border: 0;box-shadow: none;"></td>
                    <td class="bg-dark text-white border" colspan="2">Remaining:</td>
                    <td class="border">{{ $invoice->total_amount - $invoice->paid_amount }}</td>
                </tr>
            </table>
            @if (isset($action) && $action == "view")
                <a href="{{ route('dashboard.invoice.template.download', $invoice) }}" class="text-decoration-underline fw-bold text-center mt-2">Download PDF</a>
            @endif
        </div>
        <script src="{{ asset('back') }}/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('back/libs/jquery/jquery-3.6.4.min.js') }}"></script>
    </body>
</html>