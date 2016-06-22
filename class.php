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

use Bitrix\Main\Application;
use Bitrix\Main\Loader;

class FormComponent extends CBitrixComponent
{
    /**
     * Массив с ошибками валидации
     *
     * @var array
     */
    protected $errorsValidate = [];

    /**
     * Массив с индетивикаторами загруженных файлов
     *
     * @var array
     */
    protected $files = [];


    /**
     * Возарвщвет глобальный класс приложения битрикс
     *
     * @global CMain $APPLICATION
     * @return CMain
     */
    protected function gApp()
    {
        global $APPLICATION;
        return $APPLICATION;
    }

    /**
     * Запрос
     *
     * @return Bitrix\Main\Request
     */
    protected function request()
    {
        if (isset($this->request)) {
            return $this->request;
        }

        return Application::getInstance()->getContext()->getRequest();
    }

    /**
     * Ответ
     *
     * @return Bitrix\Main\Response
     */
    protected function response()
    {
        return Application::getInstance()->getContext()->getResponse();
    }

    /**
     * Сервер
     *
     * @return Bitrix\Main\Server
     */
    protected function server()
    {
        return Application::getInstance()->getContext()->getServer();
    }

    /**
     * Отдает json ответ
     *
     * @param array $result
     */
    protected function jsonResponse(array $result)
    {
        $this->gApp()->RestartBuffer();
        $this->response()->addHeader('Content-Type: application/json; charset=utf-8');
        echo json_encode($result);
        exit;
    }

    /**
     * Производит проверку данных
     */
    protected function validate()
    {
        $validate = true;
        $request = $this->request();

        foreach ($this->getFormFields() as $field) {
            if ($field['REQUIRED'] == 'Y' && !$request->getPost($field['NAME']) && $field['TYPE'] != 'file') {
                $this->errorsValidate[] = $arParams['ERROR_FIELD_MSG'] . ': ' . $field['LABEL'];

                $validate = false;
            }

            if ($field['REQUIRED'] == 'Y' && $field['TYPE'] == 'file' && !isset($_FILES[$field['NAME']])) {
                $this->errorsValidate[] = $arParams['ERROR_FIELD_MSG'] . ': ' . $field['LABEL'];

                $validate = false;
            }
        }

        if ($this->arParams['IS_USE_CAPTCHA'] == 'Y') {
            $captcha = new CCaptcha();
            if (!$captcha->CheckCode($request->getPost('captcha_word'), $request->getPost('captcha_sid'))) {
                $validate = false;
                $this->errorsValidate[] = $this->arParams['ERROR_CAPTCHA_MSG'];
            }
        }
       
        return $validate;
    }

    /**
     * Возвращает массив с ошибками валидации
     *
     * @return array
     */
    protected function getErrorsValidate()
    {
        return $this->errorsValidate;
    }

    /**
     * Проверяет запрос на post и ajax
     *
     * @return boolean
     */
    protected function isPostData()
    {
        return $this->request()->isPost() && $this->request()->isAjaxRequest();
    }

    /**
     * Возвращает массив с полями формы
     *
     * @return array
     */
    protected function getFormFields()
    {
        $formFields = [];

        foreach ($this->arParams['FORM_FIELDS'] as $field) {
            if (empty($field)) {
                continue;
            }

            list($name, $label, $isRequired, $type, $htmlClass) = explode('|', $field);
            $formFields[] = [
                'NAME' => $name,
                'LABEL' => $label,
                'REQUIRED' => $isRequired,
                'TYPE' => $type,
                'HTML_CLASS' => $htmlClass
            ];
        }

        return $formFields;
    }

    /**
     * Отправляет письмо на почту по почтовому шаблону
     */
    protected function sendMail()
    {
        $arFields = [];

        foreach ($this->getFormFields() as $field) {
            $arFields[$field['NAME']] = $this->request()->getPost($field['NAME']);
        }

        foreach ($_FILES as $id => $file) {
            $this->files[$id] = CFile::SaveFile($file, 'mailattachments');
        }

        CEvent::Send(
            $this->arParams['EVENT_TYPE'],
            SITE_ID,
            $arFields,
            'Y',
            $this->arParams['EVENT_ID'],
            $this->files
        );
    }

    protected function getMacrosValue($macros)
    {
        if (stripos($macros, '#') !== false) {
            $fieldName = trim($macros, '#');

            if (isset($this->files[$fieldName])) {
                return CFile::MakeFileArray($this->files[$fieldName]);
            } else {
                return $this->request()->getPost($fieldName);
            }
        }

        return $macros;
    }

    /**
     * Сохраняет результаты в инфоблок
     */
    protected function saveInIblock()
    {
        if ($this->arParams['IS_SAVE_TO_IBLOCK'] == 'Y') {
            Loader::includeModule('iblock');

            $mapping = $this->getAddIblockMapping();

            if (!empty($mapping)) {
                $fields = [];

                foreach ($mapping as $iblockField => $formField) {
                    if (stripos($iblockField, 'PROPERTY_') !== false) {
                        $propName = str_replace('PROPERTY_', '', $iblockField);
                        $fields['PROPERTY_VALUES'][$propName] = $this->getMacrosValue($formField);
                    } else {
                        $fields[$iblockField] = $this->getMacrosValue($formField);
                    }
                }

                $fields['IBLOCK_ID'] = $this->arParams['IBLOCK_ID'];

                if (!isset($fields['NAME'])) {
                    throw new Exception('NAME is required field');
                }
            } else {
                $data = CEventMessage::GetByID($this->arParams['EVENT_ID'])->Fetch();
                $text = $data['MESSAGE'];

                foreach ($this->getFormFields() as $field) {
                    $text = str_replace(
                        '#'.$field['NAME'].'#',
                        $this->request()->getPost($field['NAME']),
                        $text
                    );
                }

                $fields = [
                    'NAME' => $data['SUBJECT'] . ': ' . date('d.m.Y H:i:s'),
                    'IBLOCK_SECTION_ID' => false,
                    'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                    'PREVIEW_TEXT' => $text
                ];

                foreach ($this->files as $id => $file) {
                    if ('PREVIEW_PICTURE' == $id || 'DETAIL_PICTURE' == $id) {
                        $fields[$id] = $file;
                    } else {
                        $fields['PROPERTY_VALUES'][$id] = $file;
                    }
                }
            }

            $fields['IBLOCK_SECTION_ID'] = isset($fields['IBLOCK_SECTION_ID'])
                ? $fields['IBLOCK_SECTION_ID']
                : false;

            $fields['CODE'] = isset($fields['CODE']) ? $fields['CODE'] : CUtil::translit($fields['NAME'], 'ru');

            $el = new CIBlockElement;
            $el->Add($fields);
        }
    }

    protected function getAddIblockMapping()
    {
        if (!isset($this->arParams['ADD_IBLOCK_MAPPING']) || empty($this->arParams['ADD_IBLOCK_MAPPING'])) {
            return [];
        }

        $res = [];

        foreach ($this->arParams['ADD_IBLOCK_MAPPING'] as $item) {
            list($iblock, $field) = explode('|', $item);

            $res[$iblock] = $field;
        }

        return $res;
    }

    public function executeComponent()
    {
        if ($this->arParams['IS_USE_CAPTCHA'] == 'Y') {
            include_once $this->server()->getDocumentRoot() . '/bitrix/modules/main/classes/general/captcha.php';
            $this->arResult['CAPTCHA_CODE'] = htmlspecialchars($this->gApp()->CaptchaGetCode());
        }

        if ($this->isPostData()) {
            if ($this->request()->getPost('refresh_captcha') == 'Y') {
                $this->jsonResponse([
                    'code' => htmlspecialchars($this->gApp()->CaptchaGetCode())
                ]);

                return;
            }

            if ($this->arParams['FORM_ID'] != $this->request()->getPost('FORM_ID')) {
                return;
            }

            if (!$this->validate()) {
                $this->jsonResponse([
                    'msg' => implode('<br>', $this->getErrorsValidate()),
                    'type' => 'error'
                ]);

                return;
            }

            $this->sendMail();
            $this->saveInIblock();
            $this->jsonResponse([
                'msg' => $this->arParams['SUCCESS_MSG'],
                'type' => 'ok'
            ]);

            return;
        }
        
        $this->arResult['FORM_FIELDS'] = $this->getFormFields();
        $this->arResult['FORM_FIELDS_HIDDEN'] = [
            'EVENT_ID' => $this->arParams['EVENT_ID'],
            'FORM_ID' => $this->arParams['FORM_ID']
        ];

        $this->includeComponentTemplate();
    }
}
