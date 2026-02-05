/**
 * Initialize DataTable with column filters
 * @param {string} tableId - ID of the table element
 * @param {object} options - Additional DataTable options
 */
function initDataTableWithFilters(tableId, options = {}) {
    const table = $(`#${tableId}`);
    
    if (!table.length) {
        console.error(`Table #${tableId} not found`);
        return;
    }

    // Default configuration
    const defaultConfig = {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json',
            search: "Rechercher:",
            lengthMenu: "Afficher _MENU_ entrées",
            info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
            infoEmpty: "Affichage de 0 à 0 sur 0 entrées",
            infoFiltered: "(filtré à partir de _MAX_ entrées totales)",
            paginate: {
                first: "Premier",
                last: "Dernier",
                next: "Suivant",
                previous: "Précédent"
            }
        },
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        responsive: true,
        autoWidth: false,
        stateSave: true,
        stateDuration: 60 * 60 * 24, // 24 hours
        initComplete: function () {
            const api = this.api();
            
            // Add filters to each column header
            api.columns().every(function (index) {
                const column = this;
                const header = $(column.header());
                
                // Skip if column has class 'no-filter'
                if (header.hasClass('no-filter')) {
                    return;
                }
                
                // Create filter container
                const filterDiv = $('<div class="datatable-filter mt-1"></div>');
                
                // Get unique values from the column
                const uniqueValues = [];
                const valueMap = new Map();
                
                column.data().unique().sort().each(function (d, j) {
                    // Clean HTML tags for comparison
                    const cleanValue = $('<div>').html(d).text().trim();
                    
                    if (cleanValue && !valueMap.has(cleanValue)) {
                        valueMap.set(cleanValue, d);
                        uniqueValues.push({ clean: cleanValue, original: d });
                    }
                });
                
                // Create select filter if there are values
                if (uniqueValues.length > 0 && uniqueValues.length < 50) {
                    const select = $('<select class="form-select form-select-sm"><option value="">Tous</option></select>')
                        .on('change', function () {
                            const val = $(this).val();
                            column.search(val ? '^' + $.fn.dataTable.util.escapeRegex(val) + '$' : '', true, false).draw();
                        })
                        .on('click', function(e) {
                            e.stopPropagation();
                        });
                    
                    uniqueValues.forEach(function (item) {
                        select.append('<option value="' + item.clean + '">' + item.clean + '</option>');
                    });
                    
                    filterDiv.append(select);
                } else if (uniqueValues.length >= 50) {
                    // Use text input for columns with many values
                    const input = $('<input type="text" class="form-control form-control-sm" placeholder="Filtrer..." />')
                        .on('keyup change', function () {
                            const val = $(this).val();
                            column.search(val, true, false).draw();
                        })
                        .on('click', function(e) {
                            e.stopPropagation();
                        });
                    
                    filterDiv.append(input);
                }
                
                header.append(filterDiv);
            });
        }
    };

    // Merge with custom options
    const config = { ...defaultConfig, ...options };

    // Initialize DataTable
    const dataTable = table.DataTable(config);
    
    return dataTable;
}

/**
 * Initialize simple DataTable without column filters
 */
function initSimpleDataTable(tableId, options = {}) {
    const table = $(`#${tableId}`);
    
    if (!table.length) {
        console.error(`Table #${tableId} not found`);
        return;
    }

    const defaultConfig = {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
        },
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
        responsive: true,
        autoWidth: false
    };

    const config = { ...defaultConfig, ...options };
    return table.DataTable(config);
}

/**
 * Export DataTable to Excel
 */
function exportTableToExcel(tableId, filename = 'export') {
    const table = $(`#${tableId}`).DataTable();
    const data = table.buttons.exportData();
    
    // Create workbook and worksheet
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet([data.header, ...data.body]);
    
    XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
    XLSX.writeFile(wb, `${filename}_${new Date().getTime()}.xlsx`);
}
