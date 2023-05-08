//图表单独显示（底部链接是否显示）
function OnlyShowCharts(url,Params) {
    return (url && Params)?{
        credits: {
            text: '图表单独显示',
            href: url + '?Params=' + JSON.stringify(Params)
        }
    }:{
        credits: {
            enabled: false
        }
    };
}

//3D饼状图
function pie_3d(highcharts,url,Params) {
    return Object.assign({
        chart: {
            type: 'pie',
            //zoomType: 'xy',
            panning: true,
            panKey: 'shift',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {text: highcharts.title},
        //tooltip: {pointFormat: '{series.name} : <b>{point.percentage:.2f}%</b>'},
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: highcharts.mark,// + '占比',
            data: highcharts.data
        }]
    },OnlyShowCharts(url,Params));
}

//饼状图
function pie_basic(highcharts,url,Params) {
    return Object.assign({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            //zoomType: 'xy',
            panning: true,
            panKey: 'shift'
        },
        title: {text: highcharts.title},
        // tooltip: {
        //     headerFormat: '{series.name}<br>',
        //     pointFormat: '{point.name} : <b>{point.percentage:.2f}%</b>'
        // },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                    //format: '<b>{point.name}</b>: {point.percentage:.2f} %',
                    //style: {color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'}
                }
            }
        },
        series: [{
            type: 'pie',
            name: highcharts.mark,// + '占比',
            data: highcharts.data
        }]
    },OnlyShowCharts(url,Params));
}

//3D环形图
function pie_donut_3d(highcharts,url,Params) {
    return Object.assign({
        chart: {
            type: 'pie',
            //zoomType: 'xy',
            panning: true,
            panKey: 'shift',
            options3d: {enabled: true, alpha: 45}
        },
        title: {text: highcharts.title},
        // tooltip: {
        //     headerFormat: '{series.name}<br>',
        //     pointFormat: '{point.name} : <b>{point.percentage:.2f}%</b>'
        // },
        plotOptions: {pie: {innerSize: 100, depth: 45}},
        series: [{
            name: highcharts.mark,// + '占比',
            data: highcharts.data
        }]
    },OnlyShowCharts(url,Params));
}

//柱状图
function column_rotated_labels(highcharts,url,Params) {
    return Object.assign({
        chart: {
            type: 'column',
            //zoomType: 'xy',
            panning: true,
            panKey: 'shift'
        },
        title: {text: highcharts.title},
        subtitle: {text: ''},
        xAxis: {
            type: 'category',
            labels: {
                rotation: -45,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {text: highcharts.mark}
        },
        legend: {enabled: false},
        tooltip: {pointFormat: highcharts.mark + ' : <b>{point.y:.0f}</b>'},
        series: [{
            name: '',
            data: highcharts.data,
            dataLabels: {
                enabled: true,
                rotation: -90,
                color: '#FFFFFF',
                align: 'right',
                format: '{point.y:.0f}',
                y: 10,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }]
    },OnlyShowCharts(url,Params));
}

//直线图
function line_basic(highcharts,url,Params) {
    return Object.assign({
        chart: {
            //zoomType: 'xy',
            panning: true,
            panKey: 'shift'
        },
        title: {text: highcharts.title},
        xAxis: {type: 'category'},
        yAxis: {title: {text: highcharts.mark}},
        tooltip: {pointFormat: highcharts.mark + ' : <b>{point.y:.0f}</b>'},
        legend: {enabled: false},
        series: [{
            name: '',
            data: highcharts.data
        }]
    },OnlyShowCharts(url,Params));
}

//走势图
function line_time_series(highcharts,url,Params) {
    return Object.assign({
        chart: {
            //zoomType: 'xy',
            panning: true,
            panKey: 'shift'
        },
        title: {text: highcharts.title},
        subtitle: {text: document.ontouchstart === undefined ? '鼠标拖动可以进行缩放': '手势操作进行缩放'},
        xAxis: {type: 'category'},
        tooltip: {pointFormat: highcharts.mark + ' : <b>{point.y:.0f}</b>'},
        yAxis: {
            min: 0,
            title: {text: highcharts.mark}
        },
        legend: {enabled: false},
        plotOptions: {
            area: {
                fillColor: {
                    linearGradient: {
                        x1: 0,
                        y1: 0,
                        x2: 0,
                        y2: 1
                    },
                    stops: [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                    ]
                },
                marker: {radius: 2},
                lineWidth: 1,
                states: {hover: {lineWidth: 1}},
                threshold: null
            }
        },
        series: [{
            type: 'area',
            name: '',
            data: highcharts.data
        }]
    },OnlyShowCharts(url,Params));
}