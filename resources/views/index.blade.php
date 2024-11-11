    <!-- try {
       \DB::connection()->getPDO();
       echo \DB::connection()->getDatabaseName();
        } catch (\Exception $e) {
        echo 'None';
    } -->
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Jqgrid Deknaaa</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.0/themes/base/jquery-ui.css">
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/jquery-ui.css') }}" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/trirand/ui.jqgrid.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/style.css') }}" />

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script> 
    <script src="{{ asset('js/trirand/i18n/grid.locale-en.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/trirand/jquery.jqGrid.min.js') }}" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.8.1/autoNumeric.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>
    <!-- <script src="https://unpkg.com/inputmask@4.0.4/dist/inputmask/dependencyLibs/inputmask.dependencyLib.js"></script>
    <script src="https://unpkg.com/inputmask@4.0.4/dist/inputmask/inputmask.js"></script>
    <script src="https://unpkg.com/inputmask@4.0.4/dist/inputmask/inputmask.date.extensions.js"></script> -->
    <script src="{{ asset('js/scriptUI.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/scriptGrid.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/highlight.js') }}" type="text/javascript"></script>
</head>
<body>
    <div>
        <table id="grid"></table>
        <div id="pager"></div>

        <br><br><br><br><br>
        <table id="grid_detail"></table>

        <div id="dialog" title="Form Master" style="display:none;">
            <form id="formData">
                <div id="data_master">
                    <input type="hidden" id="id" name="id">
                    <label for="no_bukti">No. Bukti:</label>
                    <input type="text" id="no_bukti" name="no_bukti" class="ipt" readonly>
                    <br>
                    <label for="tgl_bukti">Tgl. Bukti:</label>
                    <input type="text" id="tgl_bukti" class="datepicker ipt" name="tgl_bukti" data-inputmask="'alias': 'datetime', 'inputFormat': 'dd-mm-yyyy'" placeholder="dd-mm-yyyy" required>
                    <!-- <input type="text" id="tgl_bukti" class="datepicker ipt" name="tgl_bukti" placeholder="dd-mm-yyyy" required> -->
                    <br>
                    <label for="id_pelanggan">Pelanggan:</label>
                    <select name="id_pelanggan" id="id_pelanggan" class="js-example-basic-single" required></select>
                    <br>
                    <label for="total">Total:</label>
                    <input class="count ipt2" type="text" name="total" readonly>
                </div>
                <br><br>

                <div>
                    <input type=button name="add_row" id="add_row" value="Tambah Barang"> 
                </div>
                <br>

                <div id="data_detail">
                    <table id="detailTable">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Quantity</th>
                                <th>Harga</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <input type="hidden" id="id_barang" name="id_barang">
                                <td><input type="text" name="nama_barang[]" class="nama_barang" required></td>
                                <td><input type="text" name="quantity[]" class="quantity" required></td>
                                <td><input type="text" name="harga[]" class="harga" required></td>
                                <td><input type="text" name="total2" class="total2" readonly></td>
                                <td><input type="button" name="delete_row" class="delete_row" value="delete"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>                
            </form>
        </div>

        <div id="modal" style="display:none">
            <label for="start">Dari:</label>
            <input type="text" id="start" class="ui-widget-content ui-corner-all" name="start">
            <label for="end">Sampai:</label>
            <input type="text" id="end" class="ui-widget-content ui-corner-all" name="end">
        </div>
    </div>
</body>
</html>

