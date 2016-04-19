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
    send: function(form, errorFieldMsg)
    {
        var $form = $(form);
        var $msg = $(form + '-msg');
        
        if (falurForm.validate($form, errorFieldMsg)) {
            return false;
        }

        falurForm.sendRequest($form, $msg);

        return false;
    },
    serializeForm: function (form)
    {
        var data = form.serializeArray();
        
        data.map(function (element) {
            var $e = $('[name="'+element.name+'"]');
            
            element.required = $e.attr('required') ? true : false;
        });
        
        return data;
    },
    validate: function (form, errorFieldMsg)
    {
        notValidate = false;
        
        form.find('.form-control').each(function () {
			var $e = $(this);
			
            if ( 'required' === $e.attr('required') && '' === $e.val() ) {
                var $pe = $e.parent();
                
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

                notValidate = true;
            }
        });
        
        return notValidate;
    },
    clearForm: function (form)
    {
        form.find('input:text, input[type="email"], input[type="password"], textarea').each(function () {
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
        var tpl = '<div class="alert {{CLASS}}"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><p>{{MSG}}</p></div>';
        
        $.ajax({
            type: 'POST',
            url: window.location,
            dataType: 'json',
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
