<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Cm_Tawkto extends Module
{
    public function __construct()
    {
        $this->name = 'cm_tawkto';
        $this->tab = 'advertising_marketing';
        $this->version = '2.0.0';
        $this->author = 'sotastudi';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '9.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('CM. Chat Tawk.to');
        $this->description = $this->l('Widget de Tawk.to configurable por idioma y tienda.');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayBeforeBodyClosingTag');
    }

    public function uninstall()
    {
        return Configuration::deleteByName('CM_TAWKTO_PROFILE_ID')
            && Configuration::deleteByName('CM_TAWKTO_WIDGET_ID')
            && parent::uninstall();
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submit' . $this->name)) {
            $languages = Language::getLanguages(false);
            $profile_ids = [];
            $widget_ids = [];

            foreach ($languages as $lang) {
                $id_lang = (int) $lang['id_lang'];
                // PrestaShop adds "_" between the name and the language ID
                $profile_ids[$id_lang] = Tools::getValue('CM_TAWKTO_PROFILE_ID_' . $id_lang);
                $widget_ids[$id_lang] = Tools::getValue('CM_TAWKTO_WIDGET_ID_' . $id_lang);
            }

            $res1 = Configuration::updateValue('CM_TAWKTO_PROFILE_ID', $profile_ids);
            $res2 = Configuration::updateValue('CM_TAWKTO_WIDGET_ID', $widget_ids);

            if ($res1 && $res2) {
                $output .= $this->displayConfirmation($this->l('Configuración guardada correctamente'));
            } else {
                $output .= $this->displayError($this->l('Error al guardar la configuración'));
            }
        }

        return $output . $this->renderForm();
    }

    protected function renderForm()
    {
        $languages = Language::getLanguages(false);

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Configuración de Widget Tawk.to'),
                    'icon' => 'icon-cog',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Widget ID'),
                        'name' => 'CM_TAWKTO_WIDGET_ID',
                        'lang' => true,
                        'desc' => $this->l('ID del Widget (ej: default).'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Profile ID'),
                        'name' => 'CM_TAWKTO_PROFILE_ID',
                        'lang' => true,
                        'desc' => $this->l('ID Profile proporcionado por Tawk.to.'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->languages = $languages;
        $helper->submit_action = 'submit' . $this->name;

        foreach ($languages as $lang) {
            $id_lang = (int) $lang['id_lang'];
            $helper->fields_value['CM_TAWKTO_PROFILE_ID'][$id_lang] = Configuration::get('CM_TAWKTO_PROFILE_ID', $id_lang);
            $val_widget = Configuration::get('CM_TAWKTO_WIDGET_ID', $id_lang);
            $helper->fields_value['CM_TAWKTO_WIDGET_ID'][$id_lang] = $val_widget ? $val_widget : 'default';
        }

        return $helper->generateForm([$fields_form]);
    }

    public function hookDisplayBeforeBodyClosingTag($params)
    {
        $id_lang = (int) $this->context->language->id;
        $id_shop = (int) $this->context->shop->id;

        $tawkto_profile_id = Configuration::get('CM_TAWKTO_PROFILE_ID', $id_lang, null, $id_shop);
        $tawkto_widget_id = Configuration::get('CM_TAWKTO_WIDGET_ID', $id_lang, null, $id_shop);

        if (empty($tawkto_profile_id)) {
            return '';
        }

        if (empty($tawkto_widget_id)) {
            $tawkto_widget_id = 'default';
        }

        $this->context->smarty->assign([
            'cm_tawkto_profile_id' => $tawkto_profile_id,
            'cm_tawkto_widget_id' => $tawkto_widget_id,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/footer.tpl');
    }
}
