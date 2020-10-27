(function($) {
    $(".selectpicker.linkable").on("change", function(e) {
        e.preventDefault();
        window.location.href = $(this)
            .find("option:selected")
            .attr("data-link");
    });
})(jQuery);
