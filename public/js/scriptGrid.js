$(document).ready(function() {
    let selectedRow = null
    let activeGrid = '#grid'

    $('#grid').jqGrid({
        url: 'dataPenjualan',
        datatype: "json",
        mtype: "GET",
        caption: "Data Penjualan",
        colNames: ['No. Bukti', 'Tgl. Bukti', 'Pelanggan', 'Total'],
        colModel: [
            // { name: 'id', align:'center', hidden:true },
            { name: 'no_bukti', align:'center' },
            { name: 'tgl_bukti', align:'center', formatter: 'date', formatoptions: { newformat: 'd-m-Y' } },
            { name: 'nama_pelanggan', align:'center' },
            { name: 'total', align:'center', formatter: 'currency' },
        ],
        pager: "#pager",
        toolbar: [true, 'top'],
        height: 'auto',
        autowidth: true,
        rowNum: 10,
        rowList: [5, 10, 20],
        rownumbers: true,
        viewrecords: true,
        sortname: 'no_bukti',
        sortorder: 'asc',
        onSelectRow: function(id_penjualan) {
            // let selectedRowId = $(this).getCell(id_penjualan, 'id')
            // let selectedRowId = $(this).jqGrid('getGridParam', 'selrow')
            $("#grid_detail").jqGrid('setGridParam', {
                url: "dataDetail/" + id_penjualan,
                datatype: "json",
            }).trigger("reloadGrid")
            gridDetail(id_penjualan)
        },
        loadComplete: function() {
            if (selectedRow == null) {  
                const firstRow = $("#grid").jqGrid('getDataIDs')[0];
                if (firstRow) {
                    $("#grid").jqGrid('setSelection', firstRow); 
                }
            } else {
                // $('#grid').trigger('reloadGrid');
                $("#grid").jqGrid('setSelection', selectedRow)
            }
            highlight()
        }
    })

    $('#grid').jqGrid('filterToolbar', { 
        searchOnEnter: false, 
        stringResult: true, 
        defaultSearch: "cn",
        beforeSearch: function(){
            // $("#grid").trigger('reloadGrid')
            selectedRow = null
        },
        afterSearch: function(){
            setTimeout(() => {
                var selectedRowId = $("#grid").jqGrid('getGridParam', 'selrow')
                console.log(selectedRowId);
                
                if (!selectedRowId) {
                    $("#grid_detail").jqGrid('setGridParam', {
                        url: 'dataDetail/' + selectedRowId,
                        datatype: 'json'
                    }).trigger('reloadGrid')
                }
                // if (selectedRowId != null) {
                //     $("#grid_detail").jqGrid('setGridParam', {
                //         url: 'dataDetail/' + selectedRowId,  
                //         datatype: 'json'
                //     }).trigger('reloadGrid');
                // } else {
                //     console.log(selectedRowId);
                //     $("#grid_detail").jqGrid('clearGridData')
                // }
            }, 800)
        }
    });

    $("#grid").jqGrid('navGrid', '#pager', { edit: false, add: false, del: false, search: false, refresh: false } )

    function gridDetail(id_penjualan) {
        $('#grid_detail').jqGrid({
            url: 'dataDetail/' + id_penjualan,
            datatype: "json",
            mtype: "GET",
            caption: "Data Detail Penjualan",
            colNames: ['id_barang', 'No. Bukti', 'Nama Barang', 'Quantity', 'Harga', 'Total'] ,
            colModel: [
                {name: 'id_barang', align:'center', sortable: true, hidden:true},
                {name: 'no_bukti', align:'center'},
                {name: 'nama_barang', align:'center', sortable: true},
                {name: 'quantity', align:'center', sortable: true},
                {name: 'harga', align:'center', sortable: true, formatter:"currency"},
                {name: 'total', align:'center', sortable: true, formatter:"currency"},
            ],
            height: 'auto',
            autowidth: true,
            sortname: 'id',
            sortorder: 'asc',
            footerrow:true,
            rownumbers: true,
            userDataOnFooter: true,
            loadComplete: function() {
                var total = 0
                var ids = $("#grid_detail").jqGrid('getDataIDs');
                for (var i = 0; i < ids.length; i++) {
                    var rowData = $("#grid_detail").jqGrid('getCell', ids[i], 'total')
                    total += parseFloat(rowData.replace(/[^0-9.-]+/g,""));
                }
                $("#grid_detail").jqGrid('footerData', 'set', { total: total.toFixed(2) }) // di set footer untuk dibagian kolom total isinya nilai total
            }
        })
    }

    $("#grid").jqGrid('navGrid', '#pager', { edit: false, add: false, del: false, search: false, refresh: false } )

    $("#grid").jqGrid('navButtonAdd', '#pager', {
        caption : "Add",
        buttonicon : "ui-icon-plus",
        onClickButton : function() {
            $("#formData").trigger("reset")
            $("#id_pelanggan").html('<option value = "" selected ></option>')
            $("#detailTable tbody").empty()
            $(".count").val(null)
            resetAutoNumericCount()
            
            const newRow = `<tr>
                <td><input type="text" name="nama_barang[]" class="nama_barang" required></td>
                <td><input type="text" name="quantity[]" class="quantity" required></td>
                <td><input type="text" name="harga[]" class="harga" required></td>
                <td><input type="text" name="total2" class="total2" readonly></td>
                <td><input type="button" name="delete_row" class="delete_row" value="delete"></td>
            </tr>`;
            $("#formData #detailTable tbody").append(newRow)
            upper()
            initAutoNumeric()

            $("#dialog").dialog({
                modal: true,
                height: 450,
                width: "auto",
                title: "ADD NEW DATA PENJUALAN",
                buttons: {
                    "Save": function () {
                        // const totalPenjualan = AutoNumeric.getNumber($(".count").get(0))
                        let formData = $('#formData').serializeArray()
                        const sname = $("#grid").jqGrid('getGridParam', 'sortname')
                        const sorder = $("#grid").jqGrid('getGridParam', 'sortorder')
                        formData.push(
                            { name: "_token", value: $("meta[name='csrf-token']").attr("content") },
                            { name: "sname", value: sname },
                            { name: "sorder", value: sorder },
                        )
                        
                        $.ajax({
                            type: "POST",
                            url: "dataPenjualan",
                            data: formData,
                            success: function(data) {
                                const count = parseInt(data.count)
                                const rowNum = $('#grid').jqGrid('getGridParam', 'rowNum')
                                const page = Math.ceil(count / rowNum)
                                const currentPage = $('#grid').getGridParam('page')

                                if (data) {
                                    selectedRow = data.id
                                    $("#dialog").dialog("close");
                                    $("#formData").trigger("reset");
                                    $(".count").val(null)
                                    resetAutoNumericCount()

                                    if (page >= currentPage) {
                                        $("#grid").trigger("reloadGrid")
                                    }
                                    // $("#grid").trigger("reloadGrid")
                                    setTimeout(() => { $('#grid').trigger('reloadGrid', { page: page }) }, 900);
                                }
                            },
                            error: function (err){
                                alert(err.responseText); 
                            },
                        })
                    },  
                    "Cancel": function () { 
                        $(this).dialog("close")
                        $("#grid").trigger("reloadGrid")
                        $(".count").val(null)
                        resetAutoNumericCount()
                    }
                }
            });
        }
    });

    $("#grid").jqGrid('navButtonAdd', '#pager', {
        caption: "Edit",
        buttonicon: "ui-icon-pencil",
        onClickButton: function() {                
            var selectedRowId = $("#grid").jqGrid('getGridParam', 'selrow');
            if (selectedRowId) {
                $.ajax({
                    type: "GET",
                    url: "dataPenjualan/" + selectedRowId,
                    success: function(data) {
                        $("#no_bukti").val(data.data[0].no_bukti);
                        $(".datepicker").val(data.data[0].tgl_bukti)
                        $("#id_pelanggan").html('<option value = "'+data.data[0].id_pelanggan+'" selected >'+data.data[0].nama_pelanggan+'</option>');
                        $(".count").val(data.data[0].total)

                        $("#detailTable tbody").empty()
                        data.dataDetail.forEach(item => {
                            const newRow = `<tr>
                                <td><input type="text" name="nama_barang[]" class="nama_barang" value="${item.nama_barang}" required></td>
                                <td><input type="text" name="quantity[]" class="quantity" value="${item.quantity}" required></td>
                                <td><input type="text" name="harga[]" class="harga" value="${item.harga}" required></td>
                                <td><input type="text" name="total2" class="total2" value="${item.total}" readonly></td>
                                <td><input type="button" name="delete_row" class="delete_row" value="delete"></td>
                            </tr>`;
                            $("#detailTable tbody").append(newRow);
                        })
                        upper()
                        initAutoNumeric()

                        $("#dialog").dialog({
                            modal: true,
                            height: "auto",
                            width: "auto",
                            title: "EDIT DATA PENJUALAN",
                            buttons: {
                                "Save": function() {
                                    let formData = $("#formData").serializeArray()
                                    const sname = $("#grid").jqGrid('getGridParam', 'sortname')
                                    const sorder = $("#grid").jqGrid('getGridParam', 'sortorder')

                                    formData.push(
                                        { name: "_token", value: $("meta[name='csrf-token']").attr("content") },
                                        { name: "sname", value: sname },
                                        { name: "sorder", value: sorder },
                                    )

                                    $.ajax({
                                        type: "PUT",
                                        url: "dataPenjualan/" + selectedRowId,
                                        data: formData,
                                        success: function(res){
                                            const rowNum = $("#grid").jqGrid('getGridParam', 'rowNum')
                                            const currentPage = $("#grid").jqGrid('getGridParam', 'page')
                                            const page = Math.ceil(parseInt(res.count) / rowNum)
                                            // const s = $("#grid").jqGrid('getGridParam', 'filters')
                                            // $('#grid').jqGrid('getGridParam', {
                                            //     search: false,
                                            //     postData: {
                                            //         _search: false,
                                            //         rows: 10,
                                            //         page: 1,
                                            //         sidx: 'no_bukti',
                                            //         sord: 'asc',
                                            //         filters: null
                                            //     },
                                            //     page: 1
                                            // })
                                            console.log(res);
                                            console.log(rowNum);
                                            console.log(currentPage);
                                            console.log(page);
                                            
                                            if (res) {
                                                selectedRow = selectedRowId

                                                $("#dialog").dialog("close")
                                                $("#formData").trigger("reset")
                                                $(".count").val(null)
                                                resetAutoNumericCount()

                                                if (page >= currentPage) {
                                                    $("#grid").trigger("reloadGrid")
                                                }
                                                setTimeout(() => {
                                                    $('#grid').trigger('reloadGrid', { page: page })
                                                }, 800)
                                            }
                                        },
                                        error: function(e){ alert(e.responseText) }
                                    })
                                },
                                "Cancel": function() {
                                    $(this).dialog("close")
                                    $("#grid").trigger("reloadGrid")
                                    $(".count").val(null)
                                    resetAutoNumericCount()
                                }
                            }
                        })
                    },
                    error: function(er){
                        alert(er.responseText)
                    }
                });
            } else { alert("Please select a row to edit.") }
        }
    });

    $("#grid").jqGrid('navButtonAdd', '#pager', {
        caption: "Delete",
        buttonicon: "ui-icon-trash",
        onClickButton: function() {
            var selectedRowId = $("#grid").jqGrid('getGridParam', 'selrow')
            const sname = $("#grid").jqGrid('getGridParam', 'sortname')
            const sorder = $("#grid").jqGrid('getGridParam', 'sortorder')

            if (selectedRowId) {
                if (confirm("Are you sure?")) {
                    $.ajax({
                        type: "DELETE",
                        url: "dataPenjualan/" + selectedRowId,
                        data: { sname: sname, sorder: sorder, _token: $("meta[name='csrf-token']").attr("content") },
                        success: function(res) {
                            const currentPage = $("#grid").jqGrid('getGridParam', 'page')
                            const rowNum = $("#grid").jqGrid('getGridParam', 'rowNum')
                            const newPage = Math.ceil(parseInt(res.count) / rowNum)
                            
                            if (res) {
                                selectedRow = res.id
                                if (newPage < currentPage) {
                                    setTimeout(() => {
                                        $('#grid').trigger('reloadGrid', { page: newPage })
                                    }, 800)
                                }
                                $("#grid").trigger("reloadGrid")
                            } else { alert("gagal menghapus data detail" + res.message) }
                        },
                        error: function(err) {
                            alert("Error: " + err.message);
                        }
                    });
                }
            } else { alert("Please select a row to delete.") }
        }
    })

    $("#grid").jqGrid('navButtonAdd', '#pager', {
        caption: "Export",
        buttonicon: "ui-icon-extlink",
        onClickButton: function() {
            const startVal = 1
            const endVal = $("#grid").getGridParam("records")
            $("#start").val(startVal)
            $("#end").val(endVal)

            $("#modal").dialog({
                modal: true,
                title: "Export Data",
                height: 200,
                width: "auto",
                buttons: {
                    "Export": function() {
                        const sname = $("#grid").jqGrid('getGridParam','sortname')
                        const sorder = $("#grid").jqGrid('getGridParam','sortorder')
                        const start = parseInt($("#start").val())
                        const end = parseInt($("#end").val())
                        const filters = $('#grid').getGridParam("postData").filters;
                        
                        $.ajax({
                            url: "exportData",
                            type: "GET",
                            data: { start: start, end: end, sname: sname, sorder: sorder, filters: filters },
                            xhrFields: { responseType: 'blob' },
                            success: function(data, status, xhr){
                                const url = window.URL.createObjectURL(new Blob([data]))
                                const a = document.createElement('a')
                                a.href = url
                                a.download = 'data_penjualan.xlsx'
                                document.body.appendChild(a)
                                a.click();
                                a.remove();
                                window.URL.revokeObjectURL(url)
                            },
                            error: function() {
                                alert("Terjadi kesalahan saat mengunduh file.")
                            }
                        })
                        $(this).dialog("close");
                    },
                    "Cancel" : function () { $(this).dialog("close") }
                }
            })
        }
    })

    $("#grid").jqGrid('navButtonAdd', '#pager', {
        caption: "Report",
        buttonicon: "ui-icon-print",
        onClickButton: function(){
            const startVal = 1
            const endVal = $("#grid").getGridParam("records")
            $("#start").val(startVal)
            $("#end").val(endVal)

            $('#modal').dialog({
                modal: true,
                title: "Report Data",
                heigh: 200,
                width: "auto",
                buttons: {
                    "Report": function(){
                        const sname = $("#grid").jqGrid('getGridParam','sortname')
                        const sorder = $("#grid").jqGrid('getGridParam','sortorder')
                        const filters = $('#grid').getGridParam("postData").filters;
                        const start = parseInt($("#start").val())
                        const end = parseInt($("#end").val())

                        $.ajax({
                            url: "reportData",
                            type: "GET",
                            data: { start: start, end: end, sname: sname, sorder: sorder, filters: filters },
                            success: function(){
                                let params = {
                                    start: start,
                                    end: end,
                                    sname: sname,
                                    sorder: sorder,
                                    filters: filters
                                }
                                let queryString = new URLSearchParams(params).toString()
                                let url = `reportData?${queryString}`
                                window.open(url, '_blank')
                            },
                            error: function(){
                                alert("CAn't report data!")
                            }
                        })
                        $(this).dialog("close")
                    },
                    "Cancel": function(){ $(this).dialog("close") }
                }
            })
        }
    })



    $("#t_grid").append(`<div style="padding: 4px">
        <label for="globalSearch">Global Search</label>
        <input id="globalSearch" class="ui-widget-content ui-corner-all" name="globalSearch" type="text" placeholder="search..." 
        style="margin-left:5px"
        />
    </div>`)

    $("#globalSearch").on('keyup', function(){
        selectedRow = null
        const searchValue = $(this).val()
        const filters = {
            groupOp: "OR",
            rules: [
                { field: 'no_bukti', op: 'cn', data: searchValue },
                { field: 'tgl_bukti', op: 'cn', data: searchValue },
                { field: 'tb_pelanggan.nama_pelanggan', op: 'cn', data: searchValue },
                { field: 'total', op: 'cn', data: searchValue }
            ]
        }

        $("#grid").jqGrid('setGridParam', {
            search: true,
            postData: { filters: JSON.stringify(filters) },
            page: 1
        }).trigger("reloadGrid")

        setTimeout( function () {
            const selectedRowId = $("#grid").jqGrid('getGridParam', 'selrow')
            console.log(selectedRowId);
            
            if (!selectedRowId) {
                $("#grid_detail").jqGrid('setGridParam', {
                    url: 'dataDetail/' + selectedRow, 
                    datatype: 'json'
                }).trigger('reloadGrid');
            }
        }, 800)
    })

    $("#gsh_grid_rn").append(`<a title="Reset Search Value" tabindex="0" id="clearAll" class="clearsearchclass">x</a>`)
        
    $(document).on('click', '#clearAll', function() {
        $('#gs_no_bukti').val('')
        $('#gs_tgl_bukti').val('')
        $('#gs_nama_pelanggan').val('')
        $('#gs_total').val('')
        $('#globalSearch').val('')

        $('#grid').jqGrid('setGridParam', {
            search: false,
            postData: {
                _search: false,
                rows: 10,
                page: 1,
                sidx: 'no_bukti',
                sord: 'asc',
                filters: null
            },
            page: 1
        }).trigger('reloadGrid')
    });

    $("#add_row").click(function() {
        const newRow = '<tr>' +
            '<td><input type="text" name="nama_barang[]" class="nama_barang" required></td>' +
            '<td><input type="text" name="quantity[]" class="quantity" required></td>' +
            '<td><input type="text" name="harga[]" class="harga" required></td>' +
            '<td><input type="text" name="total2" class="total2" readonly></td>' +
            '<td><input type="button" name="delete_row" class="delete_row" value="delete"></td>'
        '</tr>'
        $("#detailTable tbody").append(newRow)
        upper()
        initAutoNumeric()
    })

    $('#detailTable').on('input', '.quantity, .harga', function(){
        const row = $(this).closest('tr')           
        autoSum(row)
        grandTotal()
    })

    $("#detailTable").on('click', '.delete_row', function() {
        var rowCount = $("#detailTable tbody tr").length                    
        if (rowCount > 1) {
            $(this).closest('tr').remove()
            grandTotal()
        } else {
            alert("Kamu harus memiliki minimal 1 barang")
        }
    })

    $(document).keyup(function(e) {
        // console.log(e);
        e.preventDefault()
        if (activeGrid !== undefined) {
            var gridArr = $(activeGrid).getDataIDs();
            var selrow = $(activeGrid).getGridParam("selrow");
            var curr_index = 0;
            var currentPage = $(activeGrid).getGridParam('page')
            var lastPage = $(activeGrid).getGridParam('lastpage')
            var row = $(activeGrid).jqGrid('getGridParam', 'postData').rows

            for (var i = 0; i < gridArr.length; i++) {
                if (gridArr[i] == selrow) curr_index = i;
            }

            if (e.keyCode == 33) {
                if (currentPage > 1) {
                    $(activeGrid).jqGrid('setGridParam', { "page": currentPage - 1 }).trigger('reloadGrid')
                }
            } else if (e.keyCode == 34) {
                if (currentPage !== lastPage) {
                    $(activeGrid).jqGrid('setGridParam', { "page": currentPage + 1 }).trigger('reloadGrid')
                }
            } else if (e.keyCode == 38) {
                if (curr_index - 1 >= 0)
                $(activeGrid)
                .resetSelection()
                .setSelection(gridArr[curr_index - 1])
            } else if (e.keyCode == 40) {
                if (curr_index + 1 < gridArr.length)
                $(activeGrid)
                .resetSelection()
                .setSelection(gridArr[curr_index + 1])
            }
        }
    })

})

// http://192.168.3.13/example-app/public/dataPenjualan
