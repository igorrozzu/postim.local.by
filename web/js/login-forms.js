
var loginForms = (function (window, document, undefined, $) {

    return function () {
        var forms = {
                login: {
                    isOpen:false,
                    storage:null
                },
                register: {
                    isOpen:false,
                    storage:null
                },
                passwordRecovery: {
                    isOpen:false,
                    storage:null
                }
        };

        var __$containerForms = $('.container-blackout-popup-window');

        var scope = {
            init: function () {
                $(document).on('click','.sign-in-btn,.sign_in_btn',function () {
                    if(!forms.login.isOpen){
                        scope.resetForm();
                        forms.login.isOpen=true;
                        forms.login.storage = forms.login.storage || scope.getForm('login');
                        __$containerForms.html(forms.login.storage).show();

                    }
                });

                $(document).on('click','.sign-up-btn',function () {
                    if(!forms.register.isOpen){
                        scope.resetForm();
                        forms.register.isOpen=true;
                        forms.register.storage = forms.register.storage || scope.getForm('register');
                        __$containerForms.html(forms.register.storage).show();

                    }
                });

                $(document).on('click','.recovery-btn',function () {
                    if(!forms.passwordRecovery.isOpen){
                        scope.resetForm();
                        forms.passwordRecovery.isOpen=true;
                        forms.passwordRecovery.storage = forms.passwordRecovery.storage || scope.getForm('password-recovery');
                        __$containerForms.html(forms.passwordRecovery.storage).show();
                    }
                });

                $(document).on('click','.close-sign-in,.close-sign-up,.close-recovery-btn',function () {
                   scope.closeForm();
                });

                this.initSendFormHandlers();
            },
            getForm: function (formType) {
                var rez=null;
                $.ajax({
                    url: '/site/' + formType,
                    type: "GET",
                    async:false,
                    success: function (response) {
                        rez=response;
                    }
                });
                return rez;

            },
            sendForm: function (path, data) {
              $.post('/site/' + path, data, function (response) {
                  if(response.success === true) {
                      location.href = response.redirect;
                      return true;
                  }
                  __$containerForms.replaceWith(response);
              })
            },
            closeForm:function () {
                scope.resetForm();
                __$containerForms.hide();
            },
            resetForm:function () {
                forms.login.isOpen=false;
                forms.register.isOpen=false;
                forms.passwordRecovery.isOpen=false;
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
