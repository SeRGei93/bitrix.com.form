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

$arComponentDescription = array(
	'NAME' => GetMessage('FALUR_COM_FORM_NAME'),
	'DESCRIPTION' => GetMessage('FALUR_COM_FORM_DESCRIPTION'),
	'ICON' => '/images/icon.gif',
	'SORT' => 3,
	'CACHE_PATH' => 'N',
	'PATH' => array(
		'ID' => 'falur',
	),
);
