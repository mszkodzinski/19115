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
        labels.unshift('');
        values.unshift('');

        var wrapper = new google.visualization.ChartWrapper({
            chartType: 'ColumnChart',
            dataTable: [labels,
                values],
            options: {'title': title},
            containerId: container
        });
        wrapper.draw();
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
            title: title
        };

        var chart = new google.visualization.LineChart($('#' + container)[0]);
        chart.draw(data, options);
    },
    drawPie: function (labels, values, title, container) {
        labels.unshift('');
        values.unshift('');
        var data = [];
        $.each(labels, function (k, v) {
            data.push([labels[k], values[k]]);
        });

        var data = google.visualization.arrayToDataTable(data);

        var options = {
            title: title,
//            legend: 'none',
            pieHole: 0.2/*,
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
            height: 350
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
    }
}