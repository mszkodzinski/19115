Chart = {
    labels: {},
    init: function (callback) {
        var self = this;
        Api.call({
            action: 'getLabels',
            success: function (data) {
                self.labels = data;
                if ($.isFunction(callback)) {
                    callback();
                }
            }
        });
    },
    getLabels: function(type, ids) {
        var result = [],
            self = this;
        $.each(ids, function(k, v) {
            result.push(self.labels[type][v] !== undefined ? self.labels[type][v] : '');
        });
        return result;
    },
    drawColumn: function (labels, values, title, container) {
        var wrapper,
            lab = [''],
            val = [''];

        $.each(labels, function(k, v) {
            if (labels[k] != '') {
                lab.push(labels[k]);
                val.push(values[k]);
            }
        });

        wrapper = new google.visualization.ChartWrapper({
            chartType: 'ColumnChart',
            dataTable: [lab,
                val],
            options: {
                title: title,
                backgroundColor: '#4e5d6c',
                colors: ['#df691a', '#5cb85c', '#f0ad4e', '#d9534f', '#5bc0de'],
                legend: {
                    position: 'bottom'
                }
            },
            containerId: container
        });
        wrapper.draw();
    },
    drawBar: function (labels, values, title, container) {
        labels.unshift('');
        values.unshift('');
        var data = [];
        $.each(labels, function (k, v) {
            data.push([labels[k], values[k]]);
        });
        data = google.visualization.arrayToDataTable(data);

        var options = {
            title: title,
            legend: 'none',
            backgroundColor: '#4e5d6c',
            colors:['#df691a', '#5cb85c', '#f0ad4e', '#d9534f', '#5bc0de']
//            vAxis: {title: 'Year',  titleTextStyle: {color: 'red'}}
        };

        var chart = new google.visualization.BarChart($('#' + container)[0]);
        chart.draw(data, options);
    },
    drawLine: function (labels, values, title, container) {
        labels.unshift('');
        values.unshift('');
        var data = [];
        $.each(labels, function (k, v) {
            data.push([labels[k], values[k]]);
        });
        var data = google.visualization.arrayToDataTable(data);

        var options = {
            title: null,
            backgroundColor: '#4e5d6c',
            legend: 'none',
            colors:['#df691a', '#5cb85c', '#f0ad4e', '#d9534f', '#5bc0de'],
            lineWidth: 5,
            tooltip: {textStyle: {color: '#333'}, showColorCode: true},
            vAxes:[{title:title,textStyle:{color: '#eee'},titleTextStyle: {color: '#eee'}, baselineColor:'#eee'}],
            hAxes:[{textStyle:{color: '#eee'}, baselineColor:'#eee'}]
        };

        var chart = new google.visualization.LineChart($('#' + container)[0]);
        chart.draw(data, options);
    },
    drawPie: function (labels, values, title, container) {
        var data = [];
        $.each(labels, function (k, v) {
            if (labels[k] != '') {
                data.push([labels[k], values[k]]);
            }
        });
        data.unshift(['','']);

        var data = google.visualization.arrayToDataTable(data);

        var options = {
            title: title,
            backgroundColor: '#4e5d6c',
            fontSize: 15,
            width: 500,
            colors:['#df691a', '#5cb85c', '#f0ad4e', '#d9534f', '#5bc0de'],
//            legend: 'none',
            legend: {textStyle: {color:'#eee'}},
            pieHole: 0.6,
            pieSliceBorderColor: '#4e5d6c',
            fontName: 'Helvetica'/*,
             //            slices: {
             //                3: {offset: 0.1},
             //                4: {offset: 0.15}
             //            }*/
        };

        var chart = new google.visualization.PieChart($('#' + container)[0]);
        chart.draw(data, options);
    },
    drawCalendar: function (labels, values, title, container) {
        var dataTable = new google.visualization.DataTable();
        dataTable.addColumn({ type: 'date', id: 'Date' });
        dataTable.addColumn({ type: 'number', id: 'Won/Loss' });
        $.each(labels, function (k, v) {
            var d = v.split('-');
            dataTable.addRows([
                [ new Date(parseInt(d[0], 10), parseInt(d[1], 10) - 1, parseInt(d[2], 10)), values[k] ]
            ]);
        });
        var chart = new google.visualization.Calendar($('#' + container)[0]);
        var options = {
            title: title,
            height: 350,
            backgroundColor: '#4e5d6c',
            colors:['#df691a', '#5cb85c', '#f0ad4e', '#d9534f', '#5bc0de']
        };
        chart.draw(dataTable, options);
    },
    drawMap: function (startPos, markers) {
        if (GBrowserIsCompatible()) {
            var map = new GMap2(document.getElementById("map"));
            map.setCenter(new GLatLng(startPos[0], startPos[1]), startPos[2]);

            for (var i = 0; i < markers.length; i++) {
                var point = new GLatLng(markers[i][0], markers[i][1]);
                map.addOverlay(new GMarker(point));
            }
        }
    },
    showList: function (labels, values, container, hideSum) {
        var c = $('#' + container),
            sum = 0,
            max = 0;
        hideSum = hideSum || false;
        c.find('li').remove();
        $.each(labels, function (k, v) {
            if (labels[k].length) {
                var vv = parseInt(values[k], 10);
                sum += vv;
                if (vv > max) {
                    max = vv;
                }
                c.append('<li class="list-group-item"><span class="badge">' + values[k] + '</span>' + labels[k] + '' +
                    '<div class="progress"><div class="progress-bar progress-bar-info" style="width: 0"></div></div></li>');
            }
        });
        if (!hideSum) {
            c.append('<li class="list-group-item"><span class="badge">' + sum + '</span>Łącznie</li>');
        }
        c.find('.progress-bar').each(function () {
            var w = (100 * parseInt($(this).parents('li').find('.badge').text()) / max);
            $(this).css('width', w + '%');
        });
    },
    showStats: function (data, container) {
        var c = $('#' + container);
        c.find('.sum30 .value').text(data['sum30']);
        c.find('.sum .value').text(data['sum']);
        c.find('.diff30 .value').text(data['diff60p'] + '%');
        c.find('.diff30').removeClass('text-success text-danger');
        if (data['diff60p'] > 0) {
            c.find('.diff30').addClass('text-success');
        } else {
            c.find('.diff30').addClass('text-danger');
        }
    }
}