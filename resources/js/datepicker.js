let isoDateFormat = "YYYY-MM-DD";
let calendar = "#calendar";
let calendarInput = "#calendar-value";

var betweenDates = function(startDate, endDate) {
    var dates = [];

    var currDate = moment(startDate).startOf("day");
    var lastDate = moment(endDate).startOf("day");
    dates.push(currDate.clone().toDate());

    while (currDate.add(1, "days").diff(lastDate) < 0) {
        dates.push(currDate.clone().toDate());
    }

    dates.push(lastDate.clone().toDate());

    return dates;
};

var setWeek = function(startDate, endDate) {
    var daterange = startDate + ":" + endDate;

    $.post("/calendar", {
        startDate: startDate,
        endDate: endDate
    })
        .done(function(data) {
            $(calendarInput).val(daterange);
            var selected = $(document)
                .find(calendar)
                .find("td.active");
            selected.siblings("td").addClass("active");

            window.location.reload();
        })
        .fail(function(error) {
            toastr.error(error.responseJSON.message);
        })
        .always(function() {});
};

(function($) {
    // init week
    var week = {
        start: $(calendarInput)
            .val()
            .split(":")[0],
        end: $(calendarInput)
            .val()
            .split(":")[1]
    };

    $(document)
        .find("#week-prev")
        .on("click", function() {
            setWeek(
                moment(week.start)
                    .add(-1, "week")
                    .format(isoDateFormat),
                moment(week.end)
                    .add(-1, "week")
                    .format(isoDateFormat)
            );
        });

    $(document)
        .find("#week-next")
        .on("click", function() {
            setWeek(
                moment(week.start)
                    .add(1, "week")
                    .format(isoDateFormat),
                moment(week.end)
                    .add(1, "week")
                    .format(isoDateFormat)
            );
        });

    // init calendar datepicker widget
    $(document)
        .find(calendar)
        .datepicker({
            format: "yyyy-mm-dd",
            weekStart: 1,
            endDate: moment()
                .isoWeek(moment().isoWeek())
                .format(isoDateFormat),
            maxViewMode: 1,
            language: "ru",
            multidate: false
        });

    // set week dates to widget
    $(document)
        .find(calendar)
        .datepicker("setDates", betweenDates(week.start, week.end));

    var selected = $(document)
        .find(calendar)
        .find("td.active");
    selected.siblings("td").addClass("active");

    // save selected week to session
    $(document)
        .find(calendar)
        .on("changeDate", function(e) {
            var startDate = moment(e.date)
                .startOf("isoWeek")
                .format(isoDateFormat);
            var endDate = moment(e.date)
                .endOf("isoWeek")
                .format(isoDateFormat);

            setWeek(startDate, endDate);
        });

    // style week on mouse over
    $(document)
        .find(calendar)
        .on("mouseover", function() {
            var weeks = $(this).find(".table-condensed tbody tr");
            weeks.on("mouseover", function() {
                $(this)
                    .find("td")
                    .addClass("highlighted");
            });
            weeks.on("mouseout", function() {
                $(this)
                    .find("td")
                    .removeClass("highlighted");
            });
        });
})(jQuery);
