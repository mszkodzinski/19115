var Hackathon19115 = {
    init: function(){
        this.datepicker();
        this.chart();
        this.map();
        this.onResize();
    },
    datepicker: function(){
        var nowTemp = new Date();
        var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

        var checkin = $('#date-from').datepicker({
            onRender: function(date) {
                return date.valueOf() < now.valueOf() ? 'disabled' : '';
            }
        }).on('changeDate', function(ev) {
            if (ev.date.valueOf() > checkout.date.valueOf()) {
                var newDate = new Date(ev.date)
                newDate.setDate(newDate.getDate() + 1);
                checkout.setValue(newDate);
            }
            checkin.hide();
            $('#date-to')[0].focus();
        }).data('datepicker');
        var checkout = $('#date-to').datepicker({
            onRender: function(date) {
                return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
            }
        }).on('changeDate', function(ev) {
            console.log('date: ', ev);
            checkout.hide();
        }).data('datepicker');
    },
    chart: function(){
        function drawVisualization() {
            var wrapper = new google.visualization.ChartWrapper({
                chartType: 'ColumnChart',
                dataTable: [['', 'Telefon', 'Mobile', 'Formularz'],
                    ['', 700, 300, 400]],
                options: {'title': 'Typ zgłoszenia'},
                containerId: 'visualization'
            });
            wrapper.draw();
        }

        google.setOnLoadCallback(drawVisualization);
    },
    map: function(){
        if (GBrowserIsCompatible()) {
            var map = new GMap2(document.getElementById("map"));
            map.setCenter(new GLatLng(52.233333, 21.016667), 12);

            var dataMarkers = [
                [52.16842458731105, 21.033786862794823],
                [52.31398490225165, 21.030714290470254],
                [52.15897774006118, 21.107284782140656],
                [52.2429427626408, 21.108422247641645]
            ];

            for (var i = 0; i < dataMarkers.length; i++) {
                var point = new GLatLng(dataMarkers[i][0], dataMarkers[i][1]);
                map.addOverlay(new GMarker(point));
            }
        }
    },
    onResize: function(){
        var map = document.getElementById("map"),
            chart = document.getElementById("visualisation"),
            container = document.getElementById("container");

        window.onresize = function(event) {
            var w = container.offsetWidth + 'px';
            map.style.width = w;
            chart.style.width = w;
            console.log('resize: ', w);
        };
    }
};

Hackathon19115.init();