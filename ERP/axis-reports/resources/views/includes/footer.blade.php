<!-- global scripts -->
<script src="bower_components/jquery/dist/jquery.js"></script>
<script src="bower_components/tether/dist/js/tether.js"></script>
<script src="bower_components/bootstrap/dist/js/bootstrap.js"></script>
<script src="bower_components/PACE/pace.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.0.0/lodash.min.js"></script>
<script src="scripts/components/jquery-fullscreen/jquery.fullscreen-min.js"></script>
<script src="bower_components/jquery-storage-api/jquery.storageapi.min.js"></script>
<script src="bower_components/wow/dist/wow.min.js"></script>
<script src="scripts/functions.js"></script>
<script src="scripts/colors.js"></script>
<script src="scripts/left-sidebar.js"></script>
<script src="scripts/navbar.js"></script>
<script src="scripts/horizontal-navigation-1.js"></script>
<script src="scripts/horizontal-navigation-2.js"></script>
<script src="scripts/horizontal-navigation-3.js"></script>
<script src="scripts/main.js"></script>
<script src="https://cdn.datatables.net/1.10.10/js/jquery.dataTables.min.js"></script>
<script src="scripts/tables-datatable.js"></script>

<script src="bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="bower_components/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.js"></script>
{{--<script src="scripts/forms-pickers.js"></script>--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>



<script>

    $(document).ready(function() {

        $('select').select2();

        $('body').attr('data-palette', 'palette-5');

        $('.axis-table').dataTable( {
            "scrollX": true,
            "bPaginate": false,
            "bInfo" : false
        } );

        $('.date-picker').datepicker({
            orientation: 'bottom left',
            format: 'd-m-yyyy',
            autoclose: 'on'
        });

        $(".export_button").click(function (e) {
            var data = $('form').serialize();
            var url = $(this).data("url");
            window.location.href = url+"?"+data

        });

    } );

</script>


@yield('script')