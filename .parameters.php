<?php

/**
 * Bitrix component form (webgsite.ru)
 * Компонент для битрикс, создание форм
 *
 * @author    Falur <ienakaev@ya.ru>
 * @link      https://github.com/falur/bitrix.com.form
 * @copyright 2015 - 2016 webgsite.ru
 * @license   GNU General Public License http://www.gnu.org/licenses/gpl-3.0.html
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock')) {
    return;
}

$arIBlock = array();
$rsIBlock = CIBlock::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y'));
while ($arr = $rsIBlock->Fetch()) {
    $arIBlock[$arr['ID']] = '[' . $arr['ID'] . '] ' . $arr['NAME'];
}

$arEventTypes = array();
$rsEventTypes = CEventType::GetList();
while ($arr = $rsEventTypes->Fetch()) {
    $arEventTypes[$arr['EVENT_NAME']] = '[' . $arr['EVENT_NAME'] . '] ' . $arr['NAME'];
}

$arEvents = array();
$arFilter = isset($arCurrentValues['EVENT_TYPE']) 
            ? array('TYPE_ID' => $arCurrentValues['EVENT_TYPE'])
            : array();

$rsEvents = CEventMessage::GetList($by = 'site_id', $order = 'desc', $arFilter);
while ($arr = $rsEvents->Fetch()) {
    $arEvents[$arr['ID']] = '[' . $arr['ID'] . '] ' . $arr['SUBJECT'];
}

$arComponentParameters = array (
	'GROUPS' => array(),
	'PARAMETERS' => array (
        'SUCCESS_MSG' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Сообщение о успешной отправке формы',
            'TYPE' => 'STRING',
            'DEFAULT' => 'Данные успешно отправлены',
        ),
        'ERROR_MSG' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Сообщение о ошибке отправки',
            'TYPE' => 'STRING',
            'DEFAULT' => 'При отправке произошли ошибки, попробуйте позже',
        ),
        'ERROR_FIELD_MSG' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Сообщение валидатора о том что поле не заполнено',
            'TYPE' => 'STRING',
            'DEFAULT' => 'Это поле обязательно для заполнения',
        ),
        'ERROR_CAPTCHA_MSG' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Сообщение ошибки ввода капчи',
            'TYPE' => 'STRING',
            'DEFAULT' => 'Неверно введён код с картинки',
        ),
        'IS_SAVE_TO_IBLOCK' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Сохранять результат в инфоблок',
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
        ),
        'IBLOCK_ID' => array(
			'PARENT' => 'BASE',
			'NAME' => 'Инфоблок в который будет сохраняться результат',
			'TYPE' => 'LIST',
			'VALUES' => $arIBlock,
		),
        'EVENT_TYPE' => array(
            'PARENT' => 'BASE',
			'NAME' => 'Тип почтового шаблона',
			'TYPE' => 'LIST',
			'VALUES' => $arEventTypes,
            "REFRESH" => "Y",
         ),
        'EVENT_ID' => array(
            'PARENT' => 'BASE',
			'NAME' => 'Почтовый шаблон',
			'TYPE' => 'LIST',
			'VALUES' => $arEvents,
         ),
        'IS_USE_CAPTCHA' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Использовать капчу',
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
        ),
        'FORM_ID' => array(
            'PARENT' => 'BASE',
            'NAME' => 'ID компонента (любая уникальная строка)',
            'TYPE' => 'STRING',
            'DEFAULT' => 'feedback',
        ),
        'FORM_FIELDS' => array(
            'PARENT' => 'BASE',
			'NAME' => 'Список полей формы (Имя|Метка|Обязательно|Тип поля|Класс тега)',
			'TYPE' => 'LIST',
			'VALUES' => array(),
            'MULTIPLE' => 'Y',
            'ADDITIONAL_VALUES' => 'Y',
            'DEFAULT' => array(
                'NAME|Имя|Y|text|text',
                'PHONE|Телефон|Y|text',
                'MSG|Сообщение|N|textarea',
            )
        ),
	),
);
