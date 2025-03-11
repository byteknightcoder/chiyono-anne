jQuery(function ($) {

    function getStepsData(formId) {
        var form = $("#" + formId),
                steps = form.find(".step"),
                stepNr,
                stepTitle,
                stepsData = [];

        $.each(steps, function (index, step) {
            var stepObj = {};

            stepObj.number = index + 1;
            stepObj.title = $(step).find("legend").text();

            stepsData.push(stepObj);
        });

        return stepsData;
    }


    function buildSteps(formId, data) {
        var form = $("#" + formId),
                wrapper = $("<ul class='form__steps'>"),
                //customwrapper = ("<div class='wrapper_steps'>") ,
                step = "<li class='form__step'>",
                container;

        $.each(data, function (index, item) {
            container = $("<div class='form__step-container'>");
            container.append("<span class='form__step-nr'>" + item.number + "</span>");
            container.append("<span class='form__step-title'>" + item.title + "</span>");

            //added
            //wrapper.wrap(customwrapper);

            wrapper.append(step);

            if (index === 0) {
                wrapper.children(".form__step").addClass("is-active");
            }

            wrapper.children(".form__step:last-child").append(container);

            container = $("<div class='form__step-container'>");
        });

        form.before(wrapper);
    }


    function foldForm(formId) {
        var form = $("#" + formId),
                steps = form.find(".step");

        $.each(steps, function (index, step) {
            if (index === 0) {
                $(step).addClass("is-active");
            } else {
                $(step).hide();
            }
        });
    }


    function setVisibilityButtons(formId) {
        var form = $("#" + formId),
                steps = form.find(".step"),
                firstStep = $(steps[0]),
                lastStep = $(steps[steps.length - 1]),
                submitBtn = $(form.find(".btn_submit")),
                prevBtn = $(form.find(".js-prev")),
                nextBtn = $(form.find(".js-next"));

        if (firstStep.hasClass("is-active")) {
            submitBtn.hide();
            prevBtn.hide();
            nextBtn.show();
        } else if (lastStep.hasClass("is-active")) {
            submitBtn.show();
            prevBtn.show();
            nextBtn.hide();
        } else {
            submitBtn.hide();
            prevBtn.show();
            nextBtn.show();
        }
    }


    function nextStep(formId) {
        var form = $("#" + formId),
                currentStep = $(form.find(".step.is-active")),
                nextStep = currentStep.next(".step"),
                currentStepTop = $(form.prev().find(".form__step.is-active")),
                nextStepTop = currentStepTop.next(".form__step");

        currentStep.removeClass("is-active").hide();
        nextStep.addClass("is-active").show();
        currentStepTop.removeClass("is-active");
        nextStepTop.addClass("is-active");
    }


    function prevStep(formId) {
        var form = $("#" + formId),
                currentStep = $(form.find(".step.is-active")),
                prevStep = currentStep.prev(".step"),
                currentStepTop = $(form.prev().find(".form__step.is-active")),
                prevStepTop = currentStepTop.prev(".form__step");

        currentStep.removeClass("is-active").hide();
        prevStep.addClass("is-active").show();
        currentStepTop.removeClass("is-active");
        prevStepTop.addClass("is-active");
    }


    function initForms() {
        var steppedFormSelector = ".form--stepped",
                formId,
                stepsData;
        $(steppedFormSelector).each(function () {
            formId = $(this).attr("id");

            // Get the staps data -- step numbers and titles
            stepsData = getStepsData(formId);

            // Build the steps navigation
            buildSteps(formId, stepsData);

            // Hide all but the first steps
            foldForm(formId);

            // Show / hide relevant buttons
            setVisibilityButtons(formId);

        });
        $(steppedFormSelector).css('opacity', 1);
    }
    ;
	
	$.fn.autoKana('#user_lastname', '#billing_last_name_kana', {katakana: true});
    $.fn.autoKana('#user_firstname', '#billing_first_name_kana', {katakana: true});

    // Initialize form validation
    /*$(".validate").validate({
     errorPlacement: function(error, element) {         
     error.insertBefore(element);
     },
     success: function(element) {
     if (element.parent().hasClass("checkboxes") || element.parent().hasClass("radiobuttons") ) {
     $(element).remove();
     }
     }
     });*/


    // Initialize all stepped forms
    initForms();

    // Next Step
    $("body").on("click", ".js-next:not(.disabled)", function (event) {
        event.preventDefault();
        var errors = [];
        var formId = 'bookedForm';// $(this).closest("form").attr("id");

        if ($('#step2').hasClass('is-active')) { 
            var showRequiredError = false;
            $('.confirm-box').find('input').each(function (i, field) {
                var required = $(this).attr('required');
                if (required && $(field).attr('type') != 'hidden' && $(field).val() == '') {
                    showRequiredError = true;
                }
            });

            if (showRequiredError) {
                $(".ch-step2 div.msg2").css("display", "block");
                $('.ch-step2 div.msg2').html('<i class="booked-icon booked-icon-alert" style="color:#E35656"></i>&nbsp;&nbsp;&nbsp;' + booked_js_vars.i18n_fill_out_required_fields);
                scrollToFormTop(formId);
                return false;
            }

            var re = new RegExp();
            re = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
            
            var email = $("#email").val();
            var sp = email.indexOf(" ");
            if (re.test($("#email").val()) && sp < 0) {
                //true
            } else {
                $(".ch-step2 div.msg2").css("display", "block");
                $('.ch-step2 div.msg2').html('<i class="booked-icon booked-icon-alert" style="color:#E35656"></i>&nbsp;&nbsp;&nbsp;' + 'メールアドレスの形式が間違っています');
                scrollToFormTop(formId);
                return false;
            }
            //get data input
            var user_lastname = $("#user_lastname").val();
            var user_firstname = $("#user_firstname").val();
            var billing_last_name_kana = $("#billing_last_name_kana").val();
            var billing_first_name_kana = $("#billing_first_name_kana").val();
            var password = $("#b_password").val();
            var account_birth_data = $("#account_birth").val();
            var phone = $("#phone").val();
            var cancel_datetime = $(".ch-term-text").attr('cancel-datetime');
            var n = $("#is_register:checked").length;
            var is_register = n;
            var dataString = '&user_lastname=' + user_lastname + '&user_firstname=' + user_firstname + '&billing_last_name_kana=' + billing_last_name_kana + '&billing_first_name_kana=' + billing_first_name_kana + '&email=' + email + '&phone=' + phone + '&is_register=' + is_register + '&cancel_datetime=' + cancel_datetime + '&password=' + password+'&account_birth_data='+account_birth_data;
            var n = booked_js_vars.ajax_url.indexOf('?');
            if (n > 0) {
                var url_call = booked_js_vars.ajax_url + '&action=booked_fill_yourinfo_form_steps';
            } else {
                var url_call = booked_js_vars.ajax_url + '?action=booked_fill_yourinfo_form_steps';
            }
            $.ajax({
                action: 'booked_fill_yourinfo_form_steps',
                url: url_call,
                type: 'post',
                dataType: "json",
                data: dataString
            }).done(function (response) { //
                $('.ch-name-info').html(response.user_lastname + ' ' + response.user_firstname);
                $('.ch-kananame-info').html(response.billing_last_name_kana + ' ' + response.billing_first_name_kana);
                $('.ch-email-info').html(response.email);
                $('.ch-phone-info').html(response.phone);
                $('.ch-term-text').html(response.cancel_datetime);
                $(".ch-step2 div.msg2").css("display", "none");
                //insert image to step3
                if (response.p_image != '') {
                    $("#app-form").append('<div class="acf-field ch_portfolio_image"><div class="acf-label"><label>' + response.title_area_image + '</label></div><div class="acf-input"><img src="' + response.p_image + '" alt="image"><span class="delete_photo ch_delete_photo">X</span></div></div>');
                    $(".ch_area_info").append('<div class="acf-field ch_portfolio_image"><div class="acf-label"><label>' + response.title_area_image + '</label></div><div class="acf-input"><img src="' + response.p_image + '" alt="image"></div></div>');
                }
            });
        }
        if ($('#step3').hasClass('is-active')) {
            var showRequiredError = false;
            $('.booked-form').find('input,textarea,select').each(function (i, field) {

                var required = $(this).attr('required');

                if (required && $(field).attr('type') == 'hidden') {
                    var fieldParts = $(field).attr('name');
                    fieldParts = fieldParts.split('---');
                    fieldName = fieldParts[0];
                    fieldNumber = fieldParts[1].split('___');
                    fieldNumber = fieldNumber[0];

                    if (fieldName == 'radio-buttons-label') {
                        var radioValue = false;
                        $('input:radio[name="single-radio-button---' + fieldNumber + '[]"]:checked').each(function () {
                            if ($(this).val()) {
                                radioValue = $(this).val();
                            }
                        });
                        if (!radioValue) {
                            showRequiredError = true;
                        }
                    } else if (fieldName == 'checkboxes-label') {
                        var checkboxValue = false;
                        $('input:checkbox[name="single-checkbox---' + fieldNumber + '[]"]:checked').each(function () {
                            if ($(this).val()) {
                                checkboxValue = $(this).val();
                            }
                        });
                        if (!checkboxValue) {
                            showRequiredError = true;
                        }
                    }

                } else if (required && $(field).attr('type') != 'hidden' && $(field).val() == '') {
                    showRequiredError = true;
                }

            });

            if (showRequiredError) {
                $('form#newAppointmentForm p.status').show().html('<i class="booked-icon booked-icon-alert" style="color:#E35656"></i>&nbsp;&nbsp;&nbsp;' + booked_js_vars.i18n_fill_out_required_fields);
                return false;
            }
            $("input[name='action']").val('booked_fill_form_steps');
            var form_data = $("#newAppointmentForm").serialize(); //Encode form elements for submission
            $.ajax({
                action: 'booked_fill_form_steps',
                url: booked_js_vars.ajax_url,
                type: 'post',
                data: form_data
            }).done(function (response) { //
                $("input[name='action']").val('booked_add_appt');
                if (response)
                {
                    $('.ch_area_question').show();
                    $('.ch-aq').html(response);
                }
                else {
                    $('.ch_area_question').hide();
                }
                $("#bookedForm .btn--2").css("display", "inline-block");
                $("#bookedForm .status").css("display", "none");
                var service = $("#app-form .calendar-name").text();
                $("#bookedForm .ch-service").html('<font style="vertical-align: inherit;"><font style="vertical-align: inherit;">' + service + '</font></font>');
            });
        }
        nextStep(formId);
        setVisibilityButtons(formId);

        scrollToFormTop(formId);

    });

//submit form
    $("body").on("click", ".btn--2", function (event) {
        $("#woof_html_buffer").css("display", "block");
        $("#submit-request-appointment").click();
    });

    // Prev Step
    $("body").on("click", ".js-prev", function (event) {

        event.preventDefault();
        $("#bookedForm .btn--2").css("display", "none");
        var formId = 'bookedForm';// $(this).closest("form").attr("id");

        prevStep(formId);
        setVisibilityButtons(formId);

        scrollToFormTop(formId);
    });

    function scrollToFormTop(formId)
    {
        $("html, body").animate({scrollTop: $('#' + formId).offset().top - 52}, 1000);
    }
    //wrap ul steps
    $('ul.form__steps').wrap('<div class="form__steps_wrap"></div>');

    //remove portfolio image on step 3
    $('body').on('click', 'span.ch_delete_photo', function (e) {
        e.preventDefault();
        var delete_el = $(this);
        if (confirm(gl_remove_photo_text))
        {
            $('body').LoadingOverlay('show');
            $.ajax({
                type: "post",
                url: gl_ajax_url,
                data: {action: 'remove_booking_photo'},
                dataType: 'json',
            }).done(function (response) {
                delete_el.closest('.ch_portfolio_image').remove();
                $('body').LoadingOverlay('hide');
            });
        }
    });
    if($('#reservationFormCustomer').length){
        $('body').on('change', '#login-info #email', function (e) {
            e.preventDefault();
            var email=$('#login-info #email').val();
            if (email!='')
            {
                //$('body').LoadingOverlay('show');
                $.ajax({
                    type: "post",
                    url: gl_ajax_url,
                    data: {action: 'ch_check_email_exists',email:email}
                }).success(function (response) {
                    if(response!='ok'){
                        $('.msg_exist_email').show();
                        $('#login-info #email').val('');
                    }else{
                        $('.msg_exist_email').hide();
                    }
                    //$('body').LoadingOverlay('hide');
                });
            }
        });
    }
});