# Компонент bitrix для построения форм

```php
$APPLICATION->IncludeComponent(
	"falur:form", 
	"bootstrap", 
	array(
		"ERROR_CAPTCHA_MSG" => "Неверно введён код с картинки",
		"ERROR_FIELD_MSG" => "Это поле обязательно для заполнения",
		"ERROR_MSG" => "При отправке произошли ошибки, попробуйте позже",
		"EVENT_ID" => "8",
		"FORM_FIELDS" => array(
			1 => "NAME|Имя|Y|text|text",
			2 => "PHONE|Телефон|Y|text",
			3 => "MSG|Сообщение|N|textarea",
		),
		"FORM_ID" => "feedback",
		"IBLOCK_ID" => "1",
		"IS_SAVE_TO_IBLOCK" => "Y",
		"IS_USE_CAPTCHA" => "Y",
		"SUCCESS_MSG" => "Данные успешно отправлены",
		"COMPONENT_TEMPLATE" => "bootstrap"
	),
	false
);
```
