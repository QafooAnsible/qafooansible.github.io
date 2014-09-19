$(function () {
    var packages = $('h4.package-name');
    var timer;
    $('input#search').keyup(function () {
        clearTimeout(timer);
        var ms = 350; // milliseconds
        var needle = $(this).val().toLowerCase(), show;
        timer = setTimeout(function () {
            packages.each(function () {
                var $packageName = $(this);
                var $package = $packageName.parent();
                show = $packageName.text().toLowerCase().indexOf(needle) != -1;
                $package.stop(true, true);
                switch(true) {
                    case show && $package.hasClass("is-shown"):
                        // Nothing to do
                    break;
                    case show && !$package.hasClass("is-shown"):
                        // Animate in
                        $package.css({
                            display: "inherit",
                            x: "-50px",
                            opacity: 0
                        });
                        $package.transition({
                            x: 0,
                            opacity: 1
                        }, 500, "snap")
                        .addClass("is-shown");
                    break;
                    case !show && !$package.hasClass("is-shown"):
                        // Nothing to do
                    break;
                    case !show && $package.hasClass("is-shown"):
                        // Animate out
                        $package.transition({
                            x: "-50px",
                            opacity: 0
                        }, 500, "snap")
                        .transition({
                            display: "none"
                        }, 1)
                        .removeClass("is-shown");
                    break;
                }
            });
        }, ms);
    })
    $('input#search').change(function () {
        window.location.hash = "!/" + $(this).val().toLowerCase();
    });
    $(window).on("hashchange", function () {
        var $input = $('input#search');
        if (window.location.hash.indexOf("#!/") == 0) {
            $input.val(window.location.hash.replace(/#!\//, "").toLowerCase());
            $input.trigger("keyup");
        } else {
            var $anchor = $("h3[id='" + window.location.hash.replace(/^#/, "") + "']");
            if ($anchor.length != $anchor.filter(":visible").length) {
                $input.val("").trigger("keyup");
                $anchor.get(0).scrollIntoView();
            }
        }
    });
    $(window).trigger("hashchange");
    // If page is loaded with a hash focus the search field
    if (window.location.hash.length != 0) {
        $('input#search').focus();
    }
});
