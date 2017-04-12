var TableManaged = function () {

    return {

        //main function to initiate the module
        init: function () {      
            if (!jQuery().dataTable) {
                return;
            }
            // 我的员工页面-我的员工table  id="table-myStaff"
            $('#table-myStaff').dataTable({
                "aoColumns": [
                    { "bSortable": true },
                    null,
                    null,
                    null,
                    null,
                    null,
    				{ "bSortable": false }
                ],
                "aLengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "全部"] // change per page values here
                ],
                // set the initial value
                "iDisplayLength": 5,
                "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "_MENU_ 条记录每页",
                    "oPaginate": {
                        "sPrevious": "上一页",
                        "sNext": "下一页"
                    }
                },
                "aoColumnDefs": [{
                        'bSortable': true,
                        'aTargets': [0]
                    }
                ]
            });

            jQuery('#table-myStaff_wrapper .dataTables_filter input').addClass("m-wrap medium"); // modify table search input
            jQuery('#table-myStaff_wrapper .dataTables_length select').addClass("m-wrap small"); // modify table per page dropdown
            // jQuery('#table-myStaff_wrapper .dataTables_length select').select2(); // initialzie select2 dropdown


            // 借款项目-借款项目table  id="table-loanItem"
            $('#table-loanItem').dataTable({
                "aoColumns": [
                    { "bSortable": true },
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    // null,
                    null   
                ],
                "aLengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "全部"]
                ],
                // set the initial value
                "iDisplayLength": 5,
                "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "_MENU_ 条记录每页",
                    "oPaginate": {
                        "sPrevious": "上一页",
                        "sNext": "下一页"
                    }
                },
                "aoColumnDefs": [{
                        'bSortable': true,
                        'aTargets': [0]
                    }
                ]
            });

            jQuery('#table-loanItem_wrapper .dataTables_filter input').addClass("m-wrap medium");
            jQuery('#table-loanItem_wrapper .dataTables_length select').addClass("m-wrap small");
            

            // 还款项目-还款项目table  id="table-repayItem"
            $('#table-repayItem').dataTable({
                "aoColumns": [
                    { "bSortable": true },
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null   
                ],
                "aLengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "全部"]
                ],
                // set the initial value
                "iDisplayLength": 5,
                "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "_MENU_ 条记录每页",
                    "oPaginate": {
                        "sPrevious": "上一页",
                        "sNext": "下一页"
                    }
                },
                "aoColumnDefs": [{
                        'bSortable': true,
                        'aTargets': [0]
                    }
                ]
            });

            jQuery('#table-repayItem_wrapper .dataTables_filter input').addClass("m-wrap medium");
            jQuery('#table-repayItem_wrapper .dataTables_length select').addClass("m-wrap small");


            // 支付项目-支付项目table  id="table-payItem"
            $('#table-payItem').dataTable({
                "aoColumns": [
                    { "bSortable": true },
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null   
                ],
                "aLengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "全部"]
                ],
                // set the initial value
                "iDisplayLength": 5,
                "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "_MENU_ 条记录每页",
                    "oPaginate": {
                        "sPrevious": "上一页",
                        "sNext": "下一页"
                    }
                },
                "aoColumnDefs": [{
                        'bSortable': true,
                        'aTargets': [0]
                    }
                ]
            });

            jQuery('#table-payItem_wrapper .dataTables_filter input').addClass("m-wrap medium");
            jQuery('#table-payItem_wrapper .dataTables_length select').addClass("m-wrap small");


            $('#table-withdraw').dataTable({
                "aoColumns": [
                    { "bSortable": false },
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                ],
                "aLengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "All"] // change per page values here
                ],
                // set the initial value
                "iDisplayLength": 5,
                "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "_MENU_ 条记录每页",
                    "oPaginate": {
                        "sPrevious": "上一页",
                        "sNext": "下一页"
                    }
                },
                "aoColumnDefs": [{
                        'bSortable': false,
                        'aTargets': [0]
                    }
                ]
            });

            // jQuery('#table-withdraw .group-checkable').change(function () {
            //     var set = jQuery(this).attr("data-set");
            //     var checked = jQuery(this).is(":checked");
            //     jQuery(set).each(function () {
            //         if (checked) {
            //             $(this).attr("checked", true);
            //         } else {
            //             $(this).attr("checked", false);
            //         }
            //     });
            //     jQuery.uniform.update(set);
            // });

            jQuery('#table-withdraw_wrapper .dataTables_filter input').addClass("m-wrap medium"); // modify table search input
            jQuery('#table-withdraw_wrapper .dataTables_length select').addClass("m-wrap small"); // modify table per page dropdown


        }
    };
}();