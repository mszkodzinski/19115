Api = {
    defaultOptions: {
        loading: true,
        showError: true,
        data: {},
        async: true,
        type: 'POST',
        success: function () {
        },
        error: function () {
        },
        complete: function () {
        },
        apiError: function () {
        }
    },
    call: function (customOptions) {
        var k, options = {},
            self = this;
        for (k in this.defaultOptions) {
            options[k] = this.defaultOptions[k];
        }
        for (k in customOptions) {
            options[k] = customOptions[k];
        }
        $.ajax({
            url: '/api.php?action=' + options.action,
            type: options.type,
            dataType: 'json',
            async: options.async,
            data: options.data,
            success: function (data) {
                if (data.status) {
                    options.success(data.data, data.result, data.error);
                } else {
                    options.apiError(data.data, data.result, data.error);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                options.error();
            },
            complete: function () {
                options.complete();
            }
        });
    },
    test: function () {
        this.call({
            'action': 'getData',
            data: {
                filter: {
                    name: 1,
                    abc: 2
                }
            },
            success: function (data) {
                console.log(data);
            }
        });
    }
}