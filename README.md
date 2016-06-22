# Компонент bitrix для построения форм

```php
$APPLICATION->IncludeComponent(
	"falur:form", 
	"bootstrap", 
	array(
		"ERROR_CAPTCHA_MSG" => "Неверно введён код с картинки",
		"ERROR_FIELD_MSG" => "Это поле обязательно для заполнения",
		"ERROR_MSG" => "При отправке произошли ошибки, попробуйте позже",
        "EVENT_TYPE" => "FEEDBACK_FORM",
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
		"ADD_IBLOCK_MAPPING" => array(
            "NAME|#NAME#",
            "ACTIVE|N"
        )
	),
	false
);
```

`FORM_FIELDS` - массив описывающий форму

Пример

`NAME(name и id элемента формы)|Имя(label)|Y(Обязательно ли к заполнению)|text(Тип поля)|text(класс поля)`

Сейчас доступны 2 типа - это file и textarea, все остальные будут подставлены в аттрибут type тега input

`ADD_IBLOCK_MAPPING` - массив описывающий как сохранять информацию в инфоблок

Пример

`NAME(Имя поля в инфоблоке)|#NAME(Имя поля в форме)#`

Для заполнения свойств указывать `PROPERTY_CODE` 