<!-- JAVASCRIPT -->
<script src="{{ asset('back') }}/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('back') }}/libs/simplebar/simplebar.min.js"></script>
<script src="{{ asset('back') }}/libs/node-waves/waves.min.js"></script>
<script src="{{ asset('back') }}/libs/feather-icons/feather.min.js"></script>
<script src="{{ asset('back') }}/js/pages/plugins/lord-icon-2.1.0.js"></script>
<script src="{{ asset('back') }}/js/plugins.js"></script>

<!-- apexcharts -->
<script src="{{ asset('back') }}/libs/apexcharts/apexcharts.min.js"></script>

<!-- Vector map-->
<script src="{{ asset('back') }}/libs/jsvectormap/js/jsvectormap.min.js"></script>
<script src="{{ asset('back') }}/libs/jsvectormap/maps/world-merc.js"></script>

<!--Swiper slider js-->
<script src="{{ asset('back') }}/libs/swiper/swiper-bundle.min.js"></script>

<!-- Dashboard init -->
<script src="{{ asset('back') }}/js/pages/dashboard-ecommerce.init.js"></script>

{{-- Sweet Alert 2 --}}
<script src="{{ asset('back/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>

{{-- Jquery --}}
<script src="{{ asset('back/libs/jquery/jquery-3.6.4.min.js') }}"></script>

{{-- Datatables --}}
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<!-- dropzone js -->
<script src="{{ asset('back/libs/dropzone/dropzone-min.js') }}"></script>

{{-- Select 2 --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<!-- ckeditor -->
<script src="{{ asset('back/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>

@yield('additional-js-libs')

<!-- App js -->
<script src="{{ asset('back') }}/js/app.js"></script>
<script src="{{ asset('back') }}/js/custom.js"></script>

@yield('custom-js')