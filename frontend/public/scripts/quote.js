$(document).ready( () => {

    $('input[type="range"]').rangeslider({
        polyfill: false,
        onSlide: function(position, value) {
            $(".slider-amount span").text(value.toLocaleString('da-DK'));
            $(".slider-amount-val").val(value);
        }
    });

    $("#quote-settings").hide();
    $("#select-recommendations").addClass('active');
    
    $("#page-selection .page").click(function() {
        if ($(this).hasClass('active')) {
            return;
        }
        $("#page-selection .page").removeClass('active');
        $(this).addClass('active');

        if ($(this).data('page') == 'recommendations') {
            $("#quote-settings").hide();
            $("#recommendations").fadeIn();
        } else {
            $("#recommendations").hide();
            $("#quote-settings").fadeIn();
        }
        
    });

    $("#quote-settings form").submit ((e) => {
        e.preventDefault();

        $("#quote-settings form input").removeClass('error');
        $("#quote-settings form span.error").remove();
        $("#quote-settings form button").attr('disabled', true);
        $.ajax({
            url: '/app/ajax/quote.php',
            type: 'POST',
            data: $('#quote-settings form').serialize() + '&task=updateQuoteInformation',
            success: function(rawData) {
                $("#quote-settings form button").attr('disabled', false);
                const data = JSON.parse(rawData);
                if (data.message == 'failed') {
                    if (data.reason == 'validation error') {
                        Object.keys(data.errors).forEach( (item) => {
                            let elem = $('#quote-settings form input[name=' + item + ']');
                            elem.addClass('error');
                            elem.after('<span class="error my-1">' + data.errors[item][0] + '</span>');
                        });
                    } else if (data.reason == 'duplication error') {
                        console.log('Error - duplication');
                        $("#applicationForm").hide();
                        $("#confirmIdentify").fadeIn();
                        quoteId = data.param.quoteId;
                    }
                } else if (data.message == 'success') {
                    window.location.assign('/quote/recommendations/');
                }
            }
        });
    });
});