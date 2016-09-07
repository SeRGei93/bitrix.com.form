/**
 * Bitrix component form (webgsite.ru)
 * Компонент для битрикс, создание форм
 *
 * @author    Falur <ienakaev@ya.ru>
 * @link      https://github.com/falur/bitrix.com.form
 * @copyright 2015 - 2016 webgsite.ru
 * @license   GNU General Public License http://www.gnu.org/licenses/gpl-3.0.html
 */

var falurForm = {
    errorFieldMsg: '',
    
    send: function(form, errorFieldMsg)
    {
        var $form = $(form);
        var $msg = $(form + '-msg');
        
        this.errorFieldMsg = errorFieldMsg;
        
        if (falurForm.validate($form)) {
            return false;
        }

        falurForm.sendRequest($form, $msg);

        return false;
    },
    serializeForm: function (form)
    {
        if (!!window.FormData) {
            return new FormData(document.forms[form.attr('id')]);
        }
        
        var data = form.serializeArray();
        
        data.map(function (element) {
            var $e = $('[name="'+element.name+'"]');
            
            element.required = $e.attr('required') ? true : false;
        });
        
        return data;
    },
    validateEmail: function (email) 
    {
        var re = /\S+@\S+\.\S+/;
        return re.test(email);
    },
    validateAddError: function ($pe, $e, errorFieldMsgInput)
    {
        var errorFieldMsg = !errorFieldMsgInput || errorFieldMsgInput === '' 
                            ? this.errorFieldMsg 
                            : errorFieldMsgInput;
        
        $pe.addClass('has-error');
        if ($e.siblings('.help-block').length === 0) {
            $e.after('<span class="help-block"><strong>'+errorFieldMsg+'</strong></span>');
        }

        $e.on('keypress', function () {
            if ( $e.val().length > 0  ) {
                $pe.removeClass('has-error');
                $e.siblings('.help-block').remove();
            }
        });
    },
    validate: function (form)
    {
        notValidate = false;
        
        form.find('.uk-form-controls input, .uk-form-controls textarea').each(function() {
            var $e = $(this);
            var $pe = $e.parent();
            var isEmpty = $e.is('[required]') && '' === $e.val();

            switch ($e.attr('type')) {
                case 'email': 
                    if (isEmpty && falurForm.validateEmail($e.val())) {
                        falurForm.validateAddError($pe, $e);
                        notValidate = true;
                    }
                    break;

                default: 
                    if (isEmpty) {
                        falurForm.validateAddError($pe, $e);
                        notValidate = true;
                    }
                    break;
            }
        });
        
        return notValidate;
    },
    clearForm: function (form)
    {
        var inputs = 'input:text, input[type="file"], input[type="email"], input[type="password"], textarea';
        
        form.find(inputs).each(function () {
            $(this).val(''); 
        });
    },
    refreshCaptcha: function (form)
    {
        $.ajax({
            url: window.location,
            dataType: 'json',
            data: {
                'refresh_captcha' : 'Y'
            },
            method: 'POST'
        }).done(function (respone) {
            form.find('.captcha_sid').val(respone.code)
            form.find('.captcha_word').val('');
            form.find('.captcha_img').attr('src', '/bitrix/tools/captcha.php?captcha_code=' + respone.code)
        }).fail(function (jqXHR, textStatus) {
            console.log(jqXHR, textStatus);
        });
    },
    sendRequest: function (form, msg, successMsg, errorMsg)
    {
        var tpl = '<div class="alert {{CLASS}}"><p>{{MSG}}</p></div>';
        
        $.ajax({
            type: 'POST',
            url: window.location,
            dataType: 'json',
            processData: false,
            contentType: false,
            data: falurForm.serializeForm(form)
        })
        .done(function( response ) {            
            if ('ok' === response.type) {
                msg.html(tpl.replace('{{MSG}}', response.msg).replace('{{CLASS}}', 'alert-success'));
        
                falurForm.clearForm(form);
            } else {
                msg.html(tpl.replace('{{MSG}}', response.msg).replace('{{CLASS}}', 'alert-danger'));
                
                falurForm.refreshCaptcha(form);
            }
        })
        .fail(function( jqXHR, textStatus ) {
            console.log(jqXHR, textStatus);
        });
    }
};
