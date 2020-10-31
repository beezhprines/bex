/* GLOBALS */

let isoDateFormat = "YYYY-MM-DD",
    calendar = "#calendar",
    calendarInput = "#calendar-value",
    $loader = $("#loader");

$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
    }
});

$(document)
    .ajaxStart(function() {
        $loader.show();
    })
    .ajaxStop(function() {
        $loader.hide();
    });

/* DATEPICKER */

var setWeek = function(startDate, endDate) {
    window.location.href = `/calendar?startDate=${startDate}&endDate=${endDate}`;
};

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

/* END DATEPICKER */

/* SELECTPICKER SETTINGS */

(function($) {
    $(".selectpicker.linkable").on("change", function(e) {
        e.preventDefault();
        window.location.href = $(this)
            .find("option:selected")
            .attr("data-link");
    });
})(jQuery);
/* END SELECTPICKER SETTINGS */

/* OTHERS */
(function($) {
    $(document).on("scroll", function() {
        if ($(this).scrollTop() < $(window).height()) {
            $("#up-button").hide();
        } else {
            $("#up-button").show();
        }
    });

    $("#up-button").on("click", function() {
        $("html, body").animate({ scrollTop: 0 }, "fast");
    });

    $("input[type='checkbox']").on("change", function() {
        $(this)
            .prev("input[type='hidden']")
            .val($(this).is(":checked") ? 1 : 0);
    });

    $(".week-control").on("click", function() {
        let startDate = $(this).attr("data-startDate");
        let endDate = $(this).attr("data-endDate");
        setWeek(startDate, endDate);
    });
})(jQuery);
