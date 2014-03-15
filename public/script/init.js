var map = $('#map'),
    chart = $('#visualization'),
    container = $('#container');

var Hackathon19115 = {
    init: function(){
        this.datepicker();
        this.chart();
        this.map();
        this.webResponse();
    },
    datepicker: function(){
        var checkin = $('#date-from').datepicker({
            onRender: function(date) {
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
            checkout.hide();
        }).data('datepicker');
    },
    chart: function(){
        Api.call({
            action: 'getData',
            data: {
                groupby: 'source'
            },
            success: function (data) {
                Chart.drawColumn(data.label, data.value, 'Źródła', 'source');
            }
        });
        Api.call({
            action: 'getData',
            data: {
                groupby: 'year'
            },
            success: function (data) {
                Chart.drawColumn(data.label, data.value, 'Lata', 'year');
            }
        });
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
    webResponse: function(){
        var w = container.width();

        function setSize(w){
            map.width(w);
            chart.width(w);
        }

        window.onresize = function(event) {
            var w = container.width();

            setSize(w);
        };

        setSize(w);

    }
};

$(function () {
    Hackathon19115.init();
});