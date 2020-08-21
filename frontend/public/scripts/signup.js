$(document).ready(function() {

    $('input[type="range"]').rangeslider({
        polyfill: false,
        onSlide: function(position, value) {
            $("section.signup section.inputs .slider-amount span").text(value.toLocaleString('da-DK'));
            $("section.signup section.inputs .slider-amount-val").val(value);
        }
    });

    let quoteId = 0;
    const applicationFormHeightOffset = $("#application-form").height() / 2 - 50;
    const recurringVisitorHeightOffset = $("#recurring-visitor").height() / 2 - 50;
    
    if (applicationFormHeightOffset < 0) {
        heightOffset = recurringVisitorHeightOffset;
    } else {
        heightOffset = applicationFormHeightOffset;
    }
    const loadingForm = `
    <div id="loadingForm">
        <div class="wrapper" style="padding-top: ${heightOffset}px">
            <h3>Vent venligst</h3>
            <div class="loading-bars">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>`;

    // Basic signup
    $(".signup form#signup").submit( (e) => {
        e.preventDefault();
        $(".signup #application-form").prepend(loadingForm).fadeIn();
        $(".signup form input").removeClass('error');
        $(".signup form input").removeClass('mb-3');
        $(".signup form input").addClass('mb-3');
        $(".signup form span.error").remove();
        $("#loadingForm").fadeIn();

        $.ajax({
            url: '/app/ajax/quote.php',
            type: 'POST',
            data: $('.signup form#signup').serialize() + '&task=submitApplication',
            success: function(rawData) {
                $(".signup #application-form #loadingForm").remove();
                let data = JSON.parse(rawData);
                if (data.message == 'failed') {
                    if (data.reason == 'validation error') {
                        Object.keys(data.errors).forEach( (item) => {
                            let elem = $('.signup form input[name=' + item + ']');
                            elem.addClass('error');
                            elem.removeClass('mb-3');
                            elem.after('<span class="error mb-3">' + data.errors[item][0] + '</span>');
                        });
                    } else if (data.reason == 'duplication error') {
                        $("#application-form").hide();
                        $("#confirm-identity").removeClass('d-none').fadeIn();
                        quoteId = data.param.quoteId;
                    }
                } else if (data.message == 'success') {
                    window.location.assign('/quote/recommendations/');
                }
            }
        });
    });

    // Return to basic signup
    $(".back-to-application").click( (e) => {
        $(".signup .card").hide();
        $("#application-form").removeClass('d-none').fadeIn();
    });

    // Send confirm code button
    $("#send-confirm-mail").click( (e) => {
        e.preventDefault();
        $(".signup #confirm-identity").prepend(loadingForm).fadeIn();
        $(".signup #confirm-identity #loadingForm h3").text('Sender bekræftelseskode');
        if (quoteId > 0) {
            $.ajax({
                url: '/app/ajax/quote.php',
                type: 'GET',
                data: 'quoteId=' + quoteId + '&task=sendConfirmCode',
                success: function(rawData) {
                    $(".signup #confirm-identity #loadingForm").remove();
                    $("#send-confirm-mail").hide();
                    $("#enter-code-container").removeClass('d-none').fadeIn();
                    console.log(rawData);
                    /*const data = JSON.parse(rawData);
                    console.log(rawData);
                    if (data.status != 'success') {
                        
                    }*/
                }
            });
        }
    });

    // resend confirm code tag
    $("#resend-confirm-code").click( (e) => {
        e.preventDefault();
        $(".signup #confirm-identity").prepend(loadingForm).fadeIn();
        $(".signup #confirm-identity #loadingForm h3").text('Gensender bekræftelseskode');
        if (quoteId > 0) {
            $.ajax({
                url: '/app/ajax/quote.php',
                type: 'GET',
                data: 'quoteId=' + quoteId + '&task=sendConfirmCode',
                success: function(rawData) {
                    $(".signup #confirm-identity #loadingForm h3").text('Bekræftelseskode sendt.');
                    $(".signup #confirm-identity #loadingForm .loading-bars").hide();
                    setTimeout(
                        () => {
                            $(".signup #confirm-identity #loadingForm").fadeOut('medium', () => $(this).remove());
                        }, 1200
                    );
                    //const data = JSON.parse(rawData);
                    console.log(rawData);
                    /*if (data.status != 'success') {
                        
                    }*/
                }
            });
        }
    });
    
    // Submit confirm code
    $("#confirm-code-container form#code-form").submit( (e) => {
        e.preventDefault();
        const codeField = $("#confirm-code-container form#code-form input");
        $(".signup #confirm-identity span.error").remove();
        codeField.removeClass('error');
        codeField.addClass('mb-3');
        $(".signup #confirm-identity").prepend(loadingForm).fadeIn();
        if (quoteId > 0) {
            $.ajax({
                url: '/app/ajax/quote.php',
                type: 'POST',
                data: $('#confirm-code-container form#code-form').serialize() + '&quoteId=' + quoteId + '&task=validateConfirmCode',
                success: function(rawData) {
                    $(".signup #confirm-identity #loadingForm").remove();
                    $("#loadingForm").hide();
                    $("#send-confirm-mail").hide();
                    $("#enter-code-container").fadeIn();
                    const data = JSON.parse(rawData);
                    if (data.status == 'success') {
                        window.location.assign('quote.php');
                    } else if (data.status == 'failed') {
                        if (data.reason == 'code expired') {
                            codeField.removeClass('mb-3');
                            codeField.addClass('error');
                            codeField.after('<span class="error mb-3">Bekræftelseskoden er udløbet.</span>');
                        } else if (data.reason == 'invalid code') {
                            codeField.removeClass('mb-3');
                            codeField.addClass('error');
                            codeField.after('<span class="error mb-3">Bekræftelseskoden er ugyldig.</span>');
                        } else if (data.reason == 'code no longer active') {
                            codeField.removeClass('mb-3');
                            codeField.addClass('error');
                            codeField.after('<span class="error mb-3">Bekræftelseskoden er allerede brugt.</span>');
                        } else {
                            codeField.removeClass('mb-3');
                            codeField.addClass('error');
                            codeField.after('<span class="error mb-3">Der skete en fejl. Prøv igen om 10 minutter.</span>');
                        }
                    }
                }
            });
        }
    });
});