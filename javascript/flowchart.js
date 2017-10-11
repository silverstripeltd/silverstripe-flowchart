$(function () {
    var scrollTo = function (element) {
        $('html, body').animate({
            scrollTop: element.offset().top
        }, 500);
    };

    var hideQuestion = function (questionElement) {
        questionElement.addClass('flowchart__question--hidden');

        var selected = questionElement.find('.flowchart__question-responses button.selected').first();

        if (selected.length) {
            hideQuestion(
                $('.flowchart__question[data-flowchart-question-id="' + selected.attr('data-next-question-id') + '"]')
            );

            selected.removeClass('selected');
        }
    };

    $('.flowchart__question-responses button').on('click', function (ev) {
        var next = $(this).attr('data-next-question-id');
        var nextQuestion = null;
        var selected = $(this).parent().find('.selected').first();

        if (selected.length) {
            hideQuestion(
                $('.flowchart__question[data-flowchart-question-id="' + selected.attr('data-next-question-id') + '"]')
            );

            selected.removeClass('selected');
        }

        if (next) {
            nextQuestion = $('.flowchart__question[data-flowchart-question-id="' + next + '"]');
        }

        if (nextQuestion) {
            nextQuestion.removeClass('flowchart__question--hidden');
            $(this).addClass('selected');

            if (nextQuestion.attr('data-flowchart-end')) {
                $('.flowchart__form').show();
            }

            scrollTo(nextQuestion);
        }
    });

    $('.flowchart__question-content-more').on('click', function (ev) {
        ev.preventDefault();
        var info = $(this).parent().parent().find('.flowchart__question-info');

        if (info) {
            if (info.is(':visible')) {
                info.slideUp();
            } else {
                info.slideDown();
            }
        }
    });

    $('.flowchart__form').on('submit', function (ev) {
        ev.preventDefault();

        var url = document.location.protocol + '//' + document.location.hostname + '/_flowchart/form';
        var vote = null;
        var feedback = null;
        var token = null;
        var id = $(this).attr('data-flowchart-id');

        if ($('input:radio[name="vote"]')) {
            vote = $('input:radio[name="vote"]:checked').val();
        }

        if ($('textarea[name="feedback"]')) {
            feedback = $('textarea[name="feedback"]').val();
        }

        if (feedback || vote) {
            token = $('input[name="securityToken"]').val();

            var postData = {
                vote: vote,
                feedback: feedback,
                token: token,
                id: id,
            };

            $.post(url, postData, function (data) {
                if (data.error) {
                    $('.flowchart__form-message').html(data.error);
                } else {
                    $('.flowchart__form-message').html(data.message);
                }
            });
        }
    });
});
