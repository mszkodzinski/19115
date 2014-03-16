var map = $('#map'),
    chart = $('#visualization'),
    container = $('#container');

var Hackathon19115 = {
    filters: {
        date: [0, 0]
    },
    init: function(page){
        switch (page) {
            case 'charts':
                Chart.init(function () {
                    Hackathon19115.initFilter();
//            Hackathon19115.chart();
                });
                break;
            case 'maps':
                this.map();
                this.webResponse();
                break;
            case 'main':
                Chart.init(function () {
                    Hackathon19115.main();
                });
                break;
        }
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
        $('ul.date-type a').click(function () {
            var val = $(this).parent().attr('data-filter'),
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
                   // $('#date-from,#date-to').hide();
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
                   // $('#date-from,#date-to').hide();
                    break;
                case 'all':
                    $('#date-from').val('2013-01-01');
                    $('#date-to').val(year + '-' + month + '-' + day);
                  //  $('#date-from,#date-to').hide();
                    break;
                case 'own':
                    $('#date-from,#date-to').show();
                    break;
            }
            Hackathon19115.filters.date[0] = $('#date-from').val();
            Hackathon19115.filters.date[1] = $('#date-to').val();
            Hackathon19115.chart();
        });
        $('ul.date-type .active a').trigger('click');
    },
    chart: function(){
        Api.call({
            action: 'getData',
            data: {
                groupby: 'source',
                filter: Hackathon19115.filters,
                sortby: 'value',
                order: 'desc'
            },
            success: function (data) {
                Chart.showList(Chart.getLabels('source', data.label), data.value, 'source');
            }
        });
        Api.call({
            action: 'getData',
            data: {
                groupby: 'district',
                filter: Hackathon19115.filters,
                sortby: 'value',
                order: 'desc'
            },
            success: function (data) {
                Chart.drawColumn(Chart.getLabels('district', data.label, true), data.value, null, 'district');
            }
        });
        Api.call({
            action: 'getData',
            data: {
                groupby: 'year_month',
                filter: Hackathon19115.filters
            },
            success: function (data) {
                Chart.drawPie(data.label, data.value, null, 'year');
            }
        });
        Api.call({
            action: 'getData',
            data: {
                groupby: 'organization',
                filter: Hackathon19115.filters
            },
            success: function (data) {
                Chart.drawPie(data.label, data.value, null, 'organization');
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
        Api.call({
            action: 'getData',
            data: {
                groupby: 'year_month_day',
                filter: Hackathon19115.filters
            },
            success: function (data) {
                Chart.drawLine(data.label, data.value, null, 'day-by-day');
            }
        });
    },
    main: function () {
        Api.call({
            action: 'getData',
            data: {
                groupby: 'district',
                sortby: 'value',
                order: 'desc',
                limit: 5
            },
            success: function (data) {
                Chart.showList(Chart.getLabels('district', data.label, true), data.value, 'district', true);
            }
        });
        Api.call({
            action: 'getData',
            data: {
                groupby: 'status'
            },
            success: function (data) {
                Chart.drawPie(Chart.getLabels('status', data.label, true), data.value, null, 'type');
            }
        });
    },
    map: function(){
        if (GBrowserIsCompatible()) {
            var map = new GMap2(document.getElementById('map'));
            map.setCenter(new GLatLng(52.233333, 21.016667), 11);
            map.addControl(new GSmallMapControl());
            map.addControl(new GMapTypeControl());

            var icons = [
                './image/icons/kran.png',
                './image/icons/animal.png',
                './image/icons/kran.png',
                './image/icons/kran.png',
                './image/icons/kran.png',
                './image/icons/kran.png',
                './image/icons/kran.png',
                './image/icons/kran.png',
            ];

            var data = [
                {
                    points: [52.16842458731105, 21.033786862794823],
                    description: 'Dziura w drodze',
                    type: 0
                },
                {
                    points: [52.31398490225165, 21.030714290470254],
                    description: 'Wybita szyba',
                    type: 1
                },
                {
                    points: [52.15897774006118, 21.107284782140656],
                    description: 'Wypadek na drodze',
                    type: 2
                }
            ];

            function createMarker(data) {
                console.log('data: ', data);
                // Set up our GMarkerOptions object
                var baseIcon = new GIcon(G_DEFAULT_ICON);
                    baseIcon.image = icons[data.type];
                var markerOptions = { icon: baseIcon };
                var point = new GLatLng(data.points[0], data.points[1]);
                var marker = new GMarker(point, markerOptions);

                GEvent.addListener(marker, 'click', function() {
                    marker.openInfoWindowHtml(data.description);
                });
                return marker;
            }

            for (var i = 0; i < data.length; i++) {
                map.addOverlay(createMarker(data[i]));
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