<?php $this->view('partials/head'); ?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h3><span data-i18n="module_marketplace.module_marketplace"></span> <span id="total-count" class='label label-primary'></span>  <span id="module_marketplace_refresh"></span>  <span id="module_marketplace_message"></span></h3>
            <table class="table table-striped table-condensed table-bordered" id="marketplace-table">
                <thead>
                    <tr>
                        <th data-i18n="module" data-colname='module'></th>
                        <th data-i18n="enabled" data-colname='enabled'></th>
                        <th data-i18n="installed" data-colname='installed'></th>
                        <th data-i18n="module_marketplace.installed_version" data-colname='installed_version'></th>
                        <th data-i18n="module_marketplace.latest_version" data-colname='latest_version'></th>
                        <th data-i18n="module_marketplace.update_available" data-colname='update_available'></th>
                        <th data-i18n="module_marketplace.maintainer" data-colname='maintainer'></th>
                        <th data-i18n="module_marketplace.custom_override" data-colname='custom_override'></th>
                        <th data-i18n="module_marketplace.core" data-colname='core'></th>
                        <th data-i18n="module_marketplace.date_downloaded" data-colname='date_downloaded'></th>
                        <th data-i18n="module_marketplace.date_updated" data-colname='date_updated'></th>
                        <th data-i18n="module_marketplace.module_location" data-colname='module_location'></th>
                        <th data-i18n="module_marketplace.url" data-colname='url'></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-i18n="listing.loading" colspan="12" class="dataTables_empty"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>

	$(document).on('appUpdate', function(e){
		var oTable = $('.table').DataTable();
		oTable.ajax.reload();
		return;
	});

	$(document).on('appReady', function(e, lang) {
        
        // Create refresh button
        $('#module_marketplace_refresh').html('<button id="MMRefresh" class="btn btn-info btn-sm">'+i18n.t("module_marketplace.update_module_data")+'</button>');

        $.ajax({
            type: "GET",
            url: appUrl + '/module_marketplace/get_module_data', // API page to get JSON from
            dataType: 'json',
            success: function (obj, textstatus) {
                    var row_count = 0;
                    $('#marketplace-table').DataTable({
                        data: obj,
                        "order": [[ 0, "asc" ]],
                        columns: [ // What colums to put the data into
                            { "data" : "module"},
                            { "data" : "enabled"},
                            { "data" : "installed"},
                            { "data" : "installed_version"},
                            { "data" : "latest_version"},
                            { "data" : "update_available"},
                            { "data" : "maintainer"},
                            { "data" : "custom_override"},
                            { "data" : "core"},
                            { "data" : "date_downloaded"},
                            { "data" : "date_updated"},
                            { "data" : "module_location"},
                            { "data" : "url"}
                        ],
                        createdRow: function(nRow, aData, iDataIndex) {

                            // Add row count
                            row_count = parseInt(row_count) + 1;

                            // View button
                            var colvar=$('td:eq(0)', nRow).html();
                            var installed=$('td:eq(2)', nRow).html();
                            var latest_version=$('td:eq(4)', nRow).html();
                            var maintainer=$('td:eq(6)', nRow).html();
                            if(installed == 1 ){
                                $('td:eq(0)', nRow).html('<div class="machine"><a onclick="viewModule(\''+colvar+'\')" class="btn btn-default btn-xs">'+colvar+'</a></div>')
                            } else if(installed == 0) {
                                $('td:eq(0)', nRow).html('<div class="machine"><a onclick="getInstall(\''+maintainer+'/'+colvar+':'+latest_version.replace("v", "^")+'\')" class="btn btn-default btn-xs">'+colvar+'</a></div>')
                            }

                            // Format enabled
                            var colvar=$('td:eq(1)', nRow).html();
                            colvar = colvar == '1' ? i18n.t('yes') :
                            (colvar === '0' ? i18n.t('no') : '')
                            $('td:eq(1)', nRow).text(colvar)

                            // Format installed
                            installed = installed == '1' ? i18n.t('yes') :
                            (installed === '0' ? i18n.t('no') : '')
                            $('td:eq(2)', nRow).text(installed)

                            // Format update_available
                            var colvar=$('td:eq(5)', nRow).html();
                            colvar = colvar == '1' ? mr.label(i18n.t('yes'), 'success') :
                            (colvar === '0' ? i18n.t('no') : '')
                            $('td:eq(5)', nRow).html(colvar)

                            // Format custom_override
                            var colvar=$('td:eq(7)', nRow).html();
                            colvar = colvar == '1' ? i18n.t('yes') :
                            (colvar === '0' ? i18n.t('no') : '')
                            $('td:eq(7)', nRow).text(colvar)

                            // Format core
                            var colvar=$('td:eq(8)', nRow).html();
                            colvar = colvar == '1' ? i18n.t('yes') :
                            (colvar === '0' ? i18n.t('no') : '')
                            $('td:eq(8)', nRow).text(colvar)

                            // Format time
                            var colvar = parseInt($('td:eq(9)', nRow).html());
                            if (colvar){
                                var date = new Date(colvar * 1000);
                                $('td:eq(9)', nRow).html('<span title="'+moment(date).fromNow()+'">'+moment(date).format('llll')+'</span>');
                            }

                            // Format time
                            var colvar = parseInt($('td:eq(10)', nRow).html());
                            if (colvar){
                                var date = new Date(colvar * 1000);
                                $('td:eq(10)', nRow).html('<span title="'+moment(date).fromNow()+'">'+moment(date).format('llll')+'</span>');
                            }

                            // Format repo link
                            var colvar = $('td:eq(12)', nRow).html();
                            if (colvar){
                                $('td:eq(12)', nRow).html('<a target="_blank" href="'+colvar+'">'+colvar+'</a>');
                            }
                        }
                    });

                    $("#total-count").append(row_count)
            },
            error: function (obj, textstatus) {
                alert(obj.msg);
            }
        });
	});

    // Refresh cached module data
    $('#module_marketplace_refresh').click(function (e) {

        // Disable the button
        $('#MMRefresh').addClass('disabled');
        $('#module_marketplace_message').text(i18n.t("module_marketplace.module_data_updating"));

        // Get JSON of all serial numbers
        $.getJSON(appUrl + '/module_marketplace/refresh_module_info', function (processdata) {
            if (processdata == "status:good"){
                $('#module_marketplace_message').text(i18n.t("module_marketplace.module_data_refreshed"));
            } else if (processdata.startsWith("status:bad-")){
                $('#module_marketplace_message').text(i18n.t("module_marketplace.module_data_error"));
            }
        });
    });

    // Get module info via API and display in modal
    function viewModule(module){
        $.getJSON(appUrl + '/module_marketplace/get_module_info/' + module, function(data, status){

            var rows = "";

            $.each(data, function(i,d){

                // Don't process path
                if (i == "path"){
                    return true;
                }

                try { // Try to count the provides
                    d = Object.keys(data[i]).length
                } catch(err) {d = 0}

                rows = rows + '<tr><th>'+i18n.t('module_marketplace.'+i)+'</th><td>'+d+'</td></tr>';
            });
   
            // Create large modal
            $('#myModal .modal-dialog').addClass('modal-sm');
            $('#myModal .modal-title')
                .empty()
                .append(i18n.t('module_marketplace.module_overview')+" - <br>"+module)
            $('#myModal .modal-body') 
                .css('padding-top','0px')
                .empty()
                .append($('<div style="max-width:375px;">')
                    .append($('<h2>')
                        .css('margin-top','10px')
                        .append(i18n.t('module_marketplace.provided_uis')))
                    .append($('<table>')
                        .addClass('table table-striped table-condensed')
                        .append($('<tbody id="modal_table">')
                            .append(rows))));
            
            // Sort the table's elements
            sortTable(modal_table)

            $('#myModal button.ok').text(i18n.t("dialog.close"));

            // Set ok button
            $('#myModal button.ok')
                .off()
                .click(function(){$('#myModal').modal('hide')});

            $('#myModal').modal('show');
        });
    }

    // Get install string and display in modal
    function getInstall(module){
        // Create large modal
        $('#myModal .modal-dialog').addClass('modal-lg');
        $('#myModal .modal-title')
            .empty()
            .append(i18n.t('module')+" "+i18n.t('module_marketplace.install_command'))
        $('#myModal .modal-body') 
            .css('padding-top','0px')
            .empty()
            .append($('<div>')
                .append($('<h5>')
                    .append(i18n.t('module_marketplace.install_info'))
                    .append('<br><br><br>'))
                .append($('<code>')
                    .append("COMPOSER=composer.local.json composer require "+module))
                .append($('<br><br><code>')
                    .append("composer update --no-dev"))
                    .append('<br><br>'))
                .append($('<h5>')
                    .append(i18n.t('module_marketplace.install_more_info')+" ")
                    .append($('<a target="_blank" href="https://github.com/munkireport/munkireport-php/wiki/Module-Overview#adding-custom-modules">MunkiReport Wiki</a>')));

        $('#myModal button.ok').text(i18n.t("dialog.close"));

        // Set ok button
        $('#myModal button.ok')
            .off()
            .click(function(){$('#myModal').modal('hide')});

        $('#myModal').modal('show');
    }

    // Function to sort tables
    function sortTable(table) {
      var rows, switching, i, x, y, shouldSwitch;
      switching = true;
      /* Make a loop that will continue until
      no switching has been done: */
      while (switching) {
        // Start by saying: no switching is done:
        switching = false;
        rows = table.rows;
        /* Loop through all table rows */
        for (i = 0; i < (rows.length - 1); i++) {
          // Start by saying there should be no switching:
          shouldSwitch = false;
          /* Get the two elements you want to compare,
          one from current row and one from the next: */
          x = rows[i].getElementsByTagName("th")[0];
          y = rows[i + 1].getElementsByTagName("th")[0];
          // Check if the two rows should switch place:
          if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
            // If so, mark as a switch and break the loop:
            shouldSwitch = true;
            break;
          }
        }
        if (shouldSwitch) {
          /* If a switch has been marked, make the switch
          and mark that a switch has been done: */
          rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
          switching = true;
        }
      }
    }
</script>

<?php $this->view('partials/foot')?>
