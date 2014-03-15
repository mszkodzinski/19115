Chart = {
    labels: {},
    init: function () {
        var self = this;
        Api.call({
            action: getLabels,
            success: function (data) {
                self.labels = data;
            }
        });
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
    drawPie: function (labels, values, title, container) {
        var data = [];
        $.each(labels, function (k, v) {
            data.push([labels[k], values[k]]);
        })
        var data = google.visualization.arrayToDataTable(data);

        var options = {
            title: title,
            legend: 'none',
            pieHole: 0.2,
            pieSliceText: 'label'/*,
            slices: {
                3: {offset: 0.1},
                4: {offset: 0.15}
            }*/
        };

        var chart = new google.visualization.PieChart(container);
        chart.draw(data, options);
    }
}