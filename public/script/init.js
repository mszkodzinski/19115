var Hackathon19115 = {
    init: function(){
        this.datepicker();
        this.chart();
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

    }
};

$(function () {
    Hackathon19115.init();
});