jQuery(document).ready(function($) {

    // Toggle sections
    $(document).on("click", ".adl-toggle", function (e) {
        e.preventDefault();
        e.stopPropagation();
    
        const $toggle = $(this);
        const $content = $toggle.next(".adl-content");
    
        // Prevent multiple clicks during animation
        if ($content.is(":animated")) {
          return false;
        }
    
        // Close all other sections first
        $(".adl-toggle").not($toggle).removeClass("adl-open");
        $(".adl-content").not($content).slideUp(300);
    
        // Toggle current section
        $toggle.toggleClass("adl-open");
        $content.slideToggle(300);
    
        return false;
      });

    // Watch adl_post_meta[index][value] select fields and update the corresponding input field
    $('#adl_post_meta_container').on('change', '.adl_table_columns', function() {
        var $input = $(this).closest('.adl_post_meta').find('input');
        $input.val($(this).val());
    });

    $('#adl_src_table').val(function() {
        var table = $(this).val();

        // does it start with wp_?
        if (!table.startsWith('wp_')) {
            $('#adl_load_post_type').show();
        } else {
            $('#adl_load_post_type').hide();
        }
    });

    function fetchColumnsAndPopulateSelect(table, $select) {
        $.ajax({
            url: adl.ajax_url,
            type: 'POST',
            data: {
                action: 'adl_preview_data',
                _ajax_nonce: adl.nonce,
                table: table
            },
            success: function(response) {
                if (response.success) {
                    $select.empty();

                    // Add default option to "Select Column"
                    $select.append(new Option('Select column', ''));
                    response.data.columns.forEach(function(column) {
                        $select.append(new Option(column, column));
                    });
                }
            }
        });
    }

    $('#adl_src_table').change(function() {
        var table = $(this).val();
        
        if (!table.startsWith('wp_')) {
            $('#adl_load_post_type').show();
        } else {
            $('#adl_load_post_type').hide();
        }

        // Set adl_dest_table to the selected table if it exists in the list of tables
        if ($('#adl_dest_table option[value="' + table + '"]').length) {
            $('#adl_dest_table').val(table);
        }

        if (table) {
            // Populate all select fields with columns from the selected table
            fetchColumnsAndPopulateSelect(table, $('#adl_post_title'));
            fetchColumnsAndPopulateSelect(table, $('#adl_post_content'));
            fetchColumnsAndPopulateSelect(table, $('#adl_post_date'));
            fetchColumnsAndPopulateSelect(table, $('.adl_table_columns'));

            // Fetch preview data
            $.ajax({
                url: adl.ajax_url,
                type: 'POST',
                data: {
                    action: 'adl_preview_data',
                    _ajax_nonce: adl.nonce,
                    table: table
                },
                success: function(response) {
                    if (response.success) {
                        var columns = response.data.columns;
                        var rows = response.data.rows;

                        var tableHtml = '<div class="adl-data-preview-container"><table class="wp-list-table widefat fixed striped">';
                        tableHtml += '<thead><tr>';
                        columns.forEach(function(column) {
                            tableHtml += '<th>' + column + '</th>';
                        });
                        tableHtml += '</tr></thead><tbody>';
                        
                        rows.forEach(function(row) {
                            tableHtml += '<tr>';
                            columns.forEach(function(column) {
                                tableHtml += '<td>' + row[column] + '</td>';
                            });
                            tableHtml += '</tr>';
                        });

                        tableHtml += '</tbody></table></div>';
                        $('#adl-data-preview').html(tableHtml);

                        // Show Load Data section
                        $('#adl_load_data').show();

                        // Open preview section
                        $('#adl-preview .adl-toggle').next('.adl-content').slideDown();
                    } else {
                        $('#adl-data-preview').html('<p>Error: ' + response.data + '</p>');
                    }
                },
                error: function() {
                    $('#adl-data-preview').html('<p>Error loading data preview.</p>');
                }
            });
        } else {
            $('#adl-data-preview').html('');
            $('#adl_load_data').hide();
            $('#adl_load_post_ype').hide();
            // Close preview section
            $('#adl-preview .adl-toggle').next('.adl-content').slideUp();
        }
    });

    $('#adl_post_meta_container').off('click', '.adl_add_meta').on('click', '.adl_add_meta', function() {
        var metaIndex = $('.adl_post_meta').length;
        var newMetaField = `
            <div class="adl_post_meta grid-meta">
                <select name="adl_post_meta[${metaIndex}][value]" class="adl_table_columns">
                    <!-- Options populated by JS -->
                </select>
                <input type="text" name="adl_post_meta[${metaIndex}][name]" placeholder="Meta Key" class="regular-text">
                <button type="button" class="button button-secondary adl_remove_meta">Remove</button>
            </div>
        `;
        $('#adl_post_meta_container').append(newMetaField);
        
        // Populate options for the newly added select field
        fetchColumnsAndPopulateSelect($('#adl_src_table').val(), $(`#adl_post_meta_container .adl_post_meta:last .adl_table_columns`));
    });

    $('#adl_post_meta_container').on('click', '.adl_remove_meta', function() {
        $(this).closest('.adl_post_meta').remove();
    });

    // Check if no table is selected
    if (!$('#adl_src_table').val() || $('#adl_src_table').val() === 'Select table') {
        $('#adl-data-preview').html('<p>Select a table to preview data.</p>');
        // Hide Load Data section
        $('#adl_load_data').hide();
    } else {
        $('#adl_src_table').trigger('change');
    }
});