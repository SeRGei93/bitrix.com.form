<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * Bitrix component form (webgsite.ru)
 * Компонент для битрикс, создание форм
 *
 * @author    Falur <ienakaev@ya.ru>
 * @link      https://github.com/falur/bitrix.com.form
 * @copyright 2015 - 2016 webgsite.ru
 * @license   GNU General Public License http://www.gnu.org/licenses/gpl-3.0.html
 */
?>

<div id="<?= $arParams['FORM_ID'] ?>-msg"></div>

<form method="post" id="<?= $arParams['FORM_ID'] ?>">
<? foreach ($arResult['FORM_FIELDS_HIDDEN'] as $name => $value): ?>
    <input type="hidden" name="<?= $name ?>" value="<?= $value ?>">
<? endforeach; ?>

<? foreach ($arResult['FORM_FIELDS'] as $field): ?>
    <? if ($field['TYPE'] == 'file'): ?>
    <div class="form-group">
        <label for="<?= $field['NAME'] ?>">
            <?= $field['LABEL'] ?>:
        </label>

        <input type="file"
               id="<?= $field['NAME'] ?>"
               name="<?= $field['NAME'] ?>"
               <?= $field['REQUIRED'] == 'Y' ? 'required' : '' ?>
        >
    </div>
    <? elseif ($field['TYPE'] == 'textarea'): ?>
    <div class="form-group">
		<label for="<?= $field['NAME'] ?>">
            <?= $field['LABEL'] ?>:
        </label>
        
		<textarea
			class="form-control"
			name="<?= $field['NAME'] ?>"
			cols="30"
			rows="10"
            <?= $field['REQUIRED'] == 'Y' ? 'required' : '' ?>
        ></textarea>
	</div>
    <? else: ?>
    <div class="form-group">
		<label for="<?= $field['NAME'] ?>">
            <?= $field['LABEL'] ?>:
        </label>
        
		<input type="<?= $field['TYPE'] ?>"
			   class="form-control <?= $field['HTML_CLASS'] ?>"
			   id="<?= $field['NAME'] ?>"
			   name="<?= $field['NAME'] ?>"
               <?= $field['REQUIRED'] == 'Y' ? 'required' : '' ?>
        >
	</div>
    <? endif; ?>
<? endforeach; ?>

<? if ($arParams['IS_USE_CAPTCHA'] == 'Y'): ?>
<div class="form-group">
    <input type="hidden"
            name="captcha_sid"
            class="captcha_sid"
            value="<?= $arResult["CAPTCHA_CODE"] ?>"
    >

    <img src="/bitrix/tools/captcha.php?captcha_code=<?= $arResult["CAPTCHA_CODE"]?>"
          width="180"
          height="40"
          alt="CAPTCHA"
          class="captcha_img"
    >
     
    <input name="captcha_word"
             data-name="Текст с картинки"
             placeholder="Текст с картинки"
             type="text"
             required
             class="form-control captcha_word">
</div>
<? endif; ?>

<button class="btn btn-danger"
        onclick="return falurForm.send('#<?= $arParams['FORM_ID'] ?>', '<?= $arParams['ERROR_FIELD_MSG'] ?>')"
        type="submit"
>Отправить</button>
</form>