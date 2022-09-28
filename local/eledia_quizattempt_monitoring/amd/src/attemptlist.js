define(['jquery', 'core/log', 'core/str'], function($, log, getstr) {

    return {
        init: function () {
            $(document).ready(function () {

                var overduestr = getstr.get_string('overdue', 'quiz').then(function (string) {
                    overduestr = string;
                }).fail(function () {
                    overduestr = 'overdue';
                });

                var generalbox = $('thead input[type=checkbox]'),
                    rowboxes = $('tbody input[type=checkbox]'),
                    rowcells = $('tbody tr td');

                // Add highlighting on reload.
                rowboxes.each(function () {
                    if (this.checked) {
                        $(this).parents('tr').addClass('table-secondary');
                    }
                });

                // Switch states of row boxes according to state of general box.
                generalbox.on('change', function () {
                    var generalboxchecked = generalbox.prop('checked');
                    rowboxes.each(function () {
                        if (generalboxchecked && !this.checked) {
                            $(this).click();
                        } else if (!generalboxchecked && this.checked) {
                            $(this).click();
                        }
                    });
                });

                // Highlight rows of checked boxes.
                rowboxes.on('change', function () {
                    if (this.checked) {
                        $(this).parents('tr').addClass('table-secondary');
                    } else {
                        $(this).parents('tr').removeClass('table-secondary');
                    }
                });

                // Prevent loop of death...
                rowboxes.on('click', function (event) {
                    event.stopPropagation();
                });

                // Toggle checkboxes when clicking inside row.
                rowcells.on('click', function () {
                    $(this).parents('tr').find('input[type=checkbox]').click();
                });

                // Prevent toggling checkboxes when clicking links.
                rowcells.find('a').on('click', function (event) {
                    event.stopPropagation();
                });

                $('span.timeleft_timer').each(function () {
                    var element = $(this);
                    updateTimeleftTimer(element);
                    setInterval(function () {
                            updateTimeleftTimer(element);
                        },
                        1000);
                });

                function updateTimeleftTimer(element) {
                    var timeleft = element.attr('data-timeleft'),
                        content = overduestr;

                    if (timeleft > 0) {

                        var minutes_temp = Math.floor(timeleft / 60),
                            hours = Math.floor(minutes_temp / 60),
                            minutes = minutes_temp % 60,
                            seconds = timeleft % 60;

                        if (('' + hours).length < 2) {
                            hours = '0' + hours;
                        }

                        if (('' + minutes).length < 2) {
                            minutes = '0' + minutes;
                        }

                        if (('' + seconds).length < 2) {
                            seconds = '0' + seconds;
                        }

                        content = hours + ':' + minutes + ':' + seconds;
                        element.attr('data-timeleft', timeleft - 1);
                    }

                    element.text(content);
                }

                $('.tabledrawercontrols .showmoreattempt').on('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    $(this).parents('.tabledrawercontainer').find('.tabledrawercontent').removeClass('displayinitial');
                    $(this).removeClass('d-inline-block').addClass('d-none');
                    $(this).parent().find('.showlessattempt').removeClass('d-none').addClass('d-inline-block');
                });
                $('.tabledrawercontrols .showlessattempt').on('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    $(this).parents('.tabledrawercontainer').find('.tabledrawercontent').addClass('displayinitial');
                    $(this).removeClass('d-inline-block').addClass('d-none');
                    $(this).parent().find('.showmoreattempt').removeClass('d-none').addClass('d-inline-block');
                });

                $('#showallgradeinfo').on('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    $('.tabledrawercontrols .showmoreattempt').trigger('click');
                    $(this).removeClass('d-inline-block').addClass('d-none');
                    $('#hideallgradeinfo').removeClass('d-none').addClass('d-inline-block');
                });

                $('#hideallgradeinfo').on('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    $('.tabledrawercontrols .showlessattempt').trigger('click');
                    $(this).removeClass('d-inline-block').addClass('d-none');
                    $('#showallgradeinfo').removeClass('d-none').addClass('d-inline-block');
                });
            });
        }
    };
});
