
var loginForms = (function (window, document, undefined, $) {

    return function () {
        var isOpenForm = {
            login: false,
            register: false,
            passwordRecovery: false
        };

        var scope = {
            init: function () {
                $(document).on('click','.sign-up-btn,.sign-in-btn,.sign_in_btn,.close-sign-in',function () {
                    if(!isOpenForm.login){
                        isOpenForm.login=true;
                        if($('.form-login').length === 0) {
                            scope.getForm('login');
                        }
                        $('.form-login').parents('.container-blackout-popup-window').show();
                    }else {
                        isOpenForm.login=false;
                        $('.form-login').parents('.container-blackout-popup-window').hide();
                    }
                });

                $(document).on('click','.sign-in-btn,.sign-up-btn,.close-sign-up',function () {
                    if(!isOpenForm.register){
                        isOpenForm.register=true;
                        if($('.form-register').length === 0) {
                            scope.getForm('register');
                        }
                        $('.form-register').parents('.container-blackout-popup-window').show();
                    }else {
                        isOpenForm.register=false;
                        $('.form-register').parents('.container-blackout-popup-window').hide();
                    }
                });

                $(document).on('click','.recovery-btn,.sign-in-btn,.sign-up-btn,.close-recovery-btn',function () {
                    if(!isOpenForm.passwordRecovery){
                        isOpenForm.passwordRecovery=true;
                        if($('.form-password-recovery').length === 0 && $(this).hasClass('recovery-btn')) {
                            scope.getForm('password-recovery');
                        }
                        $('.form-password-recovery').parents('.container-blackout-popup-window').show();
                    }else {
                        isOpenForm.passwordRecovery=false;
                        $('.form-password-recovery').parents('.container-blackout-popup-window').hide();
                    }
                });
                this.initHandlers();
                this.initSendFormHandlers();
            },
            getForm: function (formType) {
                $.get('/site/' + formType, {}, function (response) {
                    $('body').append(response);
                });
            },
            sendForm: function (path, data) {
              $.post('/site/' + path, data, function (response) {
                  if(response.success === true) {
                      location.href = response.redirect;
                      return true;
                  }
                  $('.form-' + path).parents('.container-blackout-popup-window').replaceWith(response);
              })
            },
            initHandlers: function () {
                $(document).click(function (e) {
                    if(isOpenForm.login){
                        if ($(e.target).closest(".form-login,.sign-in-btn,.sign_in_btn").length) return;
                        isOpenForm.login=false;
                        $('.form-login').parents('.container-blackout-popup-window').hide();
                        e.stopPropagation();
                    }
                });
                $(document).click(function (e) {
                    if(isOpenForm.register){
                        if ($(e.target).closest(".form-register,.sign-up-btn").length) return;
                        isOpenForm.register=false;
                        $('.form-register').parents('.container-blackout-popup-window').hide();
                        e.stopPropagation();
                    }
                });
                $(document).click(function (e) {
                    if(isOpenForm.passwordRecovery){
                        if ($(e.target).closest(".recovery-btn,.form-password-recovery").length){
                            $('.form-login').parents('.container-blackout-popup-window').hide();
                            $('.form-register').parents('.container-blackout-popup-window').hide();
                            isOpenForm.login=false;
                            isOpenForm.register=false;
                            return;
                        }
                        isOpenForm.passwordRecovery=false;
                        $('.form-password-recovery').parents('.container-blackout-popup-window').hide();
                        e.stopPropagation();
                    }
                });
            },
            initSendFormHandlers: function () {
                $(document).on('click', '#btn-login',function () {
                    var form = $('#login-form').serialize();
                    scope.sendForm('login', form);
                });
                $(document).on('click', '#btn-register',function () {
                    var form = $('#register-form').serialize();
                    scope.sendForm('register', form);
                });
                $(document).on('click', '#btn-password-recovery',function () {
                    var form = $('#password-recovery-form').serialize();
                    scope.sendForm('password-recovery', form);
                    setTimeout(function () {
                        $('.form-password-recovery').parents('.container-blackout-popup-window').show()
                    }, 200)
                });
            }

        };

        return scope;
    }

}(window,document,undefined,jQuery));

var forms = loginForms();
forms.init();
