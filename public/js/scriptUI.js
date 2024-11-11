$(document).ready(function() {
    $(".datepicker").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        buttonText: "Calendar"
    });
    
    $(".datepicker").inputmask("99-99-9999", { 
        // mask: '99-99-9999',
        // alias: 'datetime',
        placeholder: "dd-mm-yyyy",
        // inputFormat: 'dd-mm-yyyy'
    });

    $("#no_bukti").inputmask({
        casing: 'upper'
    })

    $('#id_pelanggan').select2({
        placeholder: "Pilih Pelanggan",
        allowClear: true,
        dropdownParent: $("#dialog"),
        ajax: {
            url: 'dataPelanggan',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term }
            },
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        const nama_pelanggan = item.nama_pelanggan.toUpperCase()
                        return {
                            id: item.id,
                            text: nama_pelanggan
                        };
                    })
                };
            },
            cache: true
        },
    })


    initAutoNumeric()
    // resetAutoNumericCount()
    upper()
    autoSum()
    grandTotal()
    highlight()
})



function initAutoNumeric() {
    $('.count').each(function() {
        if (!$(this).data('autoNumeric')) {
            new AutoNumeric(this, {
                decimalCharacter: '.',
                digitGroupSeparator: ',',
                decimalPlaces: 2,
                alwaysAllowDecimalCharacter: true,
                decimalCharacterAlternative: ','
            });
        }
    });
    $('.quantity').each(function() {
        if (!$(this).data('autoNumeric')) {
            new AutoNumeric(this, {
                decimalCharacter: '.',
                digitGroupSeparator: ',',
                decimalPlaces: 2,
                alwaysAllowDecimalCharacter: true,
                decimalCharacterAlternative: ','
            });
        }
    });

    $('.harga').each(function() {
        if (!$(this).data('autoNumeric')) {
            new AutoNumeric(this, {
                decimalCharacter: '.',
                digitGroupSeparator: ',',
                decimalPlaces: 2,
                alwaysAllowDecimalCharacter: true,
                decimalCharacterAlternative: ','
            });
        }
    });

    $('.total2').each(function() {
        if (!$(this).data('autoNumeric')) {
            new AutoNumeric(this, {
                decimalCharacter: '.',
                digitGroupSeparator: ',',
                decimalPlaces: 2,
                alwaysAllowDecimalCharacter: true,
                decimalCharacterAlternative: ','
            });
        }
    });
}

function resetAutoNumericCount(){
    new AutoNumeric($('.count').get(0), {
        decimalCharacter: '.',
        digitGroupSeparator: ',',
        decimalPlaces: 2,
        alwaysAllowDecimalCharacter: true,
        decimalCharacterAlternative: ','
    }).set(0)
}

function upper(){
    $(".nama_barang").inputmask({
        casing: 'upper'
    })
}

function autoSum(row) {
    const qty = AutoNumeric.getNumber(row.find('.quantity').get(0))
    const price = AutoNumeric.getNumber(row.find('.harga').get(0))
    const t = qty * price;
    // const t = qty * Math.round(price).toFixed(1)
    console.log(t);
    
    row.find('.total2').val(t)
    new AutoNumeric(row.find('.total2').get(0), {
        decimalCharacter: '.',
        digitGroupSeparator: ',',
        decimalPlaces: 2,
        alwaysAllowDecimalCharacter: true,
        decimalCharacterAlternative: ','
    }).set(t)
}
// class='ui-state-highlight'

function grandTotal() {
    let grandTotal = 0
    $("#detailTable tbody tr").each(function(){
        const rowTotal = AutoNumeric.getNumber($(this).find('.total2').get(0))
        grandTotal += rowTotal
    })
    console.log(grandTotal);
    
    $(".count").val(grandTotal)
    new AutoNumeric($('.count').get(0), {
        decimalCharacter: '.',
        digitGroupSeparator: ',',
        decimalPlaces: 2,
        alwaysAllowDecimalCharacter: true,
        decimalCharacterAlternative: ','
    }).set(grandTotal)
}

function highlight() {
    let searchValue
    const postData = $('#grid').jqGrid('getGridParam', 'postData')
    
    if (postData.filters) {
        try {
            const dataFilters = JSON.parse(postData.filters)
            dataFilters.rules.forEach(rule => {
                searchValue = rule.data
            });
        } catch (error) {
            console.error("Error parsing filters:", error);
        }
    } else {
        console.error("filters is undefined or not available");
    }

    if (searchValue) {
        $("#grid").find("td").each(function() {
            var cellHtml = $(this).html();
            var regex = new RegExp("(" + searchValue + ")", "gi");
            $(this).html(cellHtml.replace(regex, "<span style='background-color: yellow;'>$1</span>"));
        })
    }
}
