var map = $('#map'),
    chart = $('#visualization'),
    container = $('#container');

var Hackathon19115 = {
    filters: {
        date: [0, 0]
    },
    init: function(){
        Chart.init(function () {
            Hackathon19115.initFilter();
//            Hackathon19115.chart();
        });

//        this.map();
//        this.webResponse();
    },
    initFilter: function(){
        var checkin = $('#date-from').datepicker({
            dateFormat: 'yy-mm-dd',
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
            dateFormat: 'yy-mm-dd',
            onRender: function(date) {
                return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
            }
        }).on('changeDate', function(ev) {
            checkout.hide();
        }).data('datepicker');
        $('#date-type').change(function () {
            var val = $(this).val(),
                date = new Date(),
                year = date.getFullYear(),
                month = date.getMonth() + 1,
                day = date.getDate();
            if (month < 10) {
                month = '0' + month;
            }
            if (day < 10) {
                day = '0' + day;
            }
            switch (val) {
                case 'year':
                    $('#date-from').val(year + '-01-01');
                    $('#date-to').val(year + '-12-31');
                    $('#date-from,#date-to').hide();
                    break;
                case 'month':
                    $('#date-from').val(year + '-' + month + '-01');
                    date.setMonth(date.getMonth() + 1);
                    date.setDate(1);
                    date.setDate(date.getDate() - 1);
                    month = date.getMonth() + 1;
                    day = date.getDate();
                    if (month < 10) {
                        month = '0' + month;
                    }
                    if (day < 10) {
                        day = '0' + day;
                    }
                    $('#date-to').val(year + '-' + month + '-' + day);
                    $('#date-from,#date-to').hide();
                    break;
                case 'all':
                    $('#date-from').val('2013-01-01');
                    $('#date-to').val(year + '-' + month + '-' + day);
                    $('#date-from,#date-to').hide();
                    break;
                case 'own':
                    $('#date-from,#date-to').show();
                    break;
            }
            Hackathon19115.filters.date[0] = $('#date-from').val();
            Hackathon19115.filters.date[1] = $('#date-to').val();
            Hackathon19115.chart();
        });
        $('#date-type').trigger('change');
    },
    chart: function(){
        Api.call({
            action: 'getData',
            data: {
                groupby: 'source',
                filter: Hackathon19115.filters
            },
            success: function (data) {
                Chart.drawColumn(Chart.getLabels('source', data.label), data.value, 'Źródła', 'source');
            }
        });
        Api.call({
            action: 'getData',
            data: {
                groupby: 'district',
                filter: Hackathon19115.filters
            },
            success: function (data) {
                Chart.drawColumn(Chart.getLabels('district', data.label, true), data.value, 'Dzielnice', 'district');
            }
        });
        Api.call({
            action: 'getData',
            data: {
                groupby: 'year',
                filter: Hackathon19115.filters
            },
            success: function (data) {
                Chart.drawPie(data.label, data.value, 'Lata', 'year');
            }
        });
        Api.call({
            action: 'getData',
            data: {
                groupby: 'year_month_day',
                filter: Hackathon19115.filters
            },
            success: function (data) {
                Chart.drawCalendar(data.label, data.value, 'Dni', 'calendar');
            }
        });
    },
    map: function(){
        if (GBrowserIsCompatible()) {
            var map = new GMap2(document.getElementById("map"));
            map.setCenter(new GLatLng(52.233333, 21.016667), 12);
            map.addControl(new GSmallMapControl());
            map.addControl(new GMapTypeControl());

            // Create a base icon for all of our markers that specifies the
            // shadow, icon dimensions, etc.
            var baseIcon = new GIcon(G_DEFAULT_ICON);
            baseIcon.shadow = "http://www.google.com/mapfiles/shadow50.png";
            baseIcon.iconSize = new GSize(20, 34);
            baseIcon.shadowSize = new GSize(37, 34);
            baseIcon.iconAnchor = new GPoint(9, 34);
            baseIcon.infoWindowAnchor = new GPoint(9, 2);

            var dataMarkers = [
                [52.16842458731105, 21.033786862794823, 'Dziura w drodze'],
                [52.31398490225165, 21.030714290470254, 'Wybita szyba'],
                [52.15897774006118, 21.107284782140656, 'Pies na drodze'],
                [52.2429427626408, 21.108422247641645, 'Wypadek na drodze']
            ];

            // Creates a marker whose info window displays the letter corresponding
            // to the given index.
            function createMarker(point, index, desc) {
                // Create a lettered icon for this point using our icon class
                var letter = String.fromCharCode("A".charCodeAt(0) + index);
                var letteredIcon = new GIcon(baseIcon);
                letteredIcon.image = "http://www.google.com/mapfiles/marker" + letter + ".png";

                // Set up our GMarkerOptions object
                markerOptions = { icon:letteredIcon };
                var marker = new GMarker(point, markerOptions);

                GEvent.addListener(marker, "click", function() {
                    marker.openInfoWindowHtml('Opis zdarzenia: ' + desc);
                });
                return marker;
            }



            for (var i = 0; i < dataMarkers.length; i++) {
                var point = new GLatLng(dataMarkers[i][0], dataMarkers[i][1]);
                map.addOverlay(createMarker(point, i, dataMarkers[i][2]));
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