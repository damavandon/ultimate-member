
jQuery(document).ready(function ($) {
    const PAYAMITO_UM_OTPTIME = payamito_um_form.resend_time;
    const PAYAMITO_UM_SEND_OTP = $("#payamito_um_send_otp")[0];
    const PAYAMITO_UM_NOUNCE = payamito_um_form.nonce;
    const PAYAMITO_UM_VALIDATE = $("#payamito_um_login_btn");
    const PAYAMITO_UM_OTP = $("#payamito_um_OTP");
    const FORM_ID = $("[name='form_id']").val();
    const FIELD_VALUE = $("#payamito_um_otp_field").val();
    const FIELD =$("#"+FIELD_VALUE);

    if (FORM_ID !== undefined && FIELD !== undefined && PAYAMITO_UM_OTPTIME !== undefined  && PAYAMITO_UM_NOUNCE !== undefined) {

        if (FIELD !== undefined) {
            $(FIELD).intlTelInput({})

            payamito_um_get_country();
            payamito_maybe_show_otp();
            $("#um-submit-btn").on('click', function () {
             
                let country = $("[name='country']");
                let value = payamito_um_get_country();
                country.val(value)

            });
            $(PAYAMITO_UM_SEND_OTP).click(function () {

                var country = payamito_um_get_country();
                let form_id = $("[name='form_id']").val();
                if (validate_field(FIELD)) {
                    Spinner(type = "start");
                    $.ajax({
                        url: payamito_um_form.ajaxurl,
                        type: 'POST',
                        data: {
                            'action': "payamito_um_validation",
                            'nonce': payamito_um_form.nonce,
                            "field": FIELD.val(),
                            "mode": "send_otp",
                            "country": country,
                            'form_id': form_id,
                        }
                    }).done(function (r, s) {
                        if (s == 'success' && r != '0' && r != "" && typeof r === 'object') {
                            notification(r.e, r.message)
                            if (r.e == 1) {
                                payamito_um_timer();
                                
                            }
                        }
                    }).fail(function () {

                    })
                        .always(function (r, s) {
                            Spinner(type = "close");
                        });
                }

            });

            function payamito_um_get_country() {

                iti = $(FIELD).intlTelInput("getSelectedCountryData");
                iti = "+" + iti.dialCode;
                $("payamito_um_country").val(iti);
                return iti
            }


            function validate_field(field) {
            
                $([document.documentElement, document.body]).animate({
                    scrollTop: field.offset().top - 35
                }, 1000);
                if (field.val() === null || !field.val().trim().length) {
                    notification(0, payamito_um_form.invalid)
                    return false;
                }
                return true;
            }
            function notification(ty = -1, m) {
                switch (ty) {
                    case ty = -1:
                        iziToast.error({
                            timeout: 10000,
                            title: payamito_um_form.error,
                            message: m,
                            displayMode: 2
                        });
                        break;
                    case ty = 0:
                        iziToast.warning({
                            timeout: 10000,
                            title: payamito_um_form.warning,
                            message: m,
                            displayMode: 2
                        });
                        break;
                    case ty = 1:
                        iziToast.success({
                            timeout: 10000,
                            title: payamito_um_form.success,
                            message: m,
                            displayMode: 2
                        });
                }
            }
            function Spinner(type = "start") {

                if (type == "start") {
                    $.LoadingOverlay("show", { 'progress': true });
                    $("form").bind("keypress", function (e) {
                        if (e.keyCode == 13) {
                            return false;
                        }
                    });
                } else {
                    $.LoadingOverlay("hide");
                }
            }
            function payamito_maybe_show_otp(){
                
                let first_send=payamito_getcookie('pum_first_send');
                if(first_send=='1'){
                    $("#payamito_um_otp_container").css("display","block");
                }

            }
            function payamito_getcookie(cname) {

                let name = cname + "=";
                let decodedCookie = decodeURIComponent(document.cookie);
                let ca = decodedCookie.split(';');
                for(let i = 0; i <ca.length; i++) {
                  let c = ca[i];
                  while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                  }
                  if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                  }
                }
                return "";
              }
            function payamito_um_timer() {
                var timer = PAYAMITO_UM_OTPTIME;
                var innerhtml = PAYAMITO_UM_SEND_OTP.innerHTML;
                $(".payamito_um_send_otp").prop('disabled', true);
                $(".payamito_um_send_otp").css('cursor', 'wait');
                var Interval = setInterval(function () {
    
                    seconds = parseInt(timer);
                    seconds = seconds < 10 ? "0" + seconds : seconds;
                    PAYAMITO_UM_SEND_OTP.innerHTML = seconds + ":" + payamito_um_form.second;
                    
                    if (--timer <= 0) {
                        timer = 0;
                        $(".payamito_um_send_otp").removeAttr('disabled');
                        $(".payamito_um_send_otp").css('cursor', 'grab');
                        PAYAMITO_UM_SEND_OTP.innerHTML = innerhtml;
                        clearInterval(Interval);
                    }
                }, 1000);
            }
            if (PAYAMITO_UM_VALIDATE !== undefined) {

                PAYAMITO_UM_VALIDATE.click(function () {
                   
                    if (validate_field(FIELD)) {

                        let form_id = $("[name='form_id']").val();
                        let otp_value = PAYAMITO_UM_OTP.val();
                        Spinner(type = "start");
                        $.post({
                            url: payamito_um_form.ajaxurl,
                            data: {
                                'action': "payamito_um_validation",
                                'nonce': payamito_um_form.nonce,
                                "field": FIELD.val(),
                                "country": payamito_um_get_country(),
                                "mode": "validation",
                                "otp": otp_value,
                                'form_id': form_id

                            }
                        }).done(function (r, s) {
                            if (s == 'success' && r != '0' && r != "") {
                                notification(r.e, r.message)
                                $("#payamito_um_otp_container").css("display","block");
                                if (r.e == 1 && r.re != "" && r.re!==false) {

                                    document.cookie = "pum_first_send=0; "
                                    window.location.replace(r.re);
                                }
                                if(r.re==false){
                                    document.cookie = "pum_first_send=1; "
                                    payamito_um_timer();
                                }
                            }
                        }).fail(function () { })
                            .always(function (r, s) {
                                Spinner(type = "close");
                            });
                    }
                });
            }
        }
    }
});