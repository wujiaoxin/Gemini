var Charts = function () {

    return {
        //main function to initiate the module
        // init: function () {
        //     App.addResponsiveHandler(function () {
        //          Charts.initPieCharts(); 
        //     });           
        // },

        initCharts: function () {
            if (!jQuery.plot) {
                return;
            }

            var data = [];
            var totalPoints = 250;


            //myShop页近30天订单金额走势图 id="chart_orderTotal"

            function chart2() {
                // var orderTotal =[];
                var plot = $.plot($("#chart_orderTotal"), [{
                            data: orderTotal,
                            label: "订单金额"
                        }
                    ], {
                        series: {
                            lines: {
                                show: true,
                                lineWidth: 2,
                                fill: true,
                                fillColor: {
                                    colors: [{
                                            opacity: 0.05
                                        }, {
                                            opacity: 0.01
                                        }
                                    ]
                                }
                            },
                            points: {
                                show: true
                            },
                            shadowSize: 2
                        },
                        grid: {
                            hoverable: true,
                            clickable: true,
                            tickColor: "#eee",
                            borderWidth: 0
                        },
                        colors: ["#d12610", "#37b7f3", "#52e136"],
                        xaxis: {
                            ticks: 11,
                            tickDecimals: 0
                        },
                        yaxis: {
                            ticks: 11,
                            tickDecimals: 0
                        }
                    });


                function showTooltip(x, y, contents) {
                    $('<div id="tooltip">' + contents + '</div>').css({
                            position: 'absolute',
                            display: 'none',
                            top: y + 15,
                            left: x - 100,
                            border: '1px solid #333',
                            padding: '4px',
                            color: '#fff',
                            'border-radius': '3px',
                            'background-color': '#333',
                            opacity: 0.80
                        }).appendTo("body").fadeIn(200);
                }

                var previousPoint = null;
                $("#chart_orderTotal").bind("plothover", function (event, pos, item) {
                    $("#x").text(pos.x.toFixed(2));
                    $("#y").text(pos.y.toFixed(2));

                    if (item) {
                        if (previousPoint != item.dataIndex) {
                            previousPoint = item.dataIndex;

                            $("#tooltip").remove();
                            var x = item.datapoint[0].toFixed(2),
                                y = item.datapoint[1].toFixed(2);

                            showTooltip(item.pageX, item.pageY, item.series.label + " of " + x + " = " + y);
                        }
                    } else {
                        $("#tooltip").remove();
                        previousPoint = null;
                    }
                });
            }
            
            chart2();
        }

        
    };

}();