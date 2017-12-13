
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

                $(document).on('click','.close-sign-in,.close-sign-up,.close-recovery-btn,.close-notif-message',function () {
                   scope.closeForm();
                });


                /*if(($.cookie('showForm') || null) === null){
                    var showForm = setTimeout(function () {

                        var date = new Date();
                        var minutes = 3600;
                        date.setTime(date.getTime() + (minutes * 60 * 1000));
                        $.cookie('showForm', JSON.stringify({value:'true'}), {expires: date, path: '/', domain: main.getDomainName(), secure: true});

                        scope.resetForm();
                        var $html = scope.getForm('login?with-message=true');
                        __$containerForms.html($html).show();

                    },20000)
                }*/

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
                  __$containerForms.html(response);
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
                });
            },
            saveTimezoneOffset: function () {
                var date = new Date();
                var currentTimeZoneOffsetInHours = -date.getTimezoneOffset() / 60;
                scope.setCookie('timezone_offset', currentTimeZoneOffsetInHours, {expires: 3600 * 12 * 30})
            },

            setCookie: function (name, value, options) {
                options = options || {};

                var expires = options.expires;

                if (typeof expires == "number" && expires) {
                    var d = new Date();
                    d.setTime(d.getTime() + expires * 1000);
                    expires = options.expires = d;
                }
                if (expires && expires.toUTCString) {
                    options.expires = expires.toUTCString();
                }

                value = encodeURIComponent(value);

                var updatedCookie = name + "=" + value;

                for (var propName in options) {
                    updatedCookie += "; " + propName;
                    var propValue = options[propName];
                    if (propValue !== true) {
                        updatedCookie += "=" + propValue;
                    }
                }

                document.cookie = updatedCookie;
            },

        };

        return scope;
    }

}(window,document,undefined,jQuery));

var forms = loginForms();
forms.init();
forms.saveTimezoneOffset();
