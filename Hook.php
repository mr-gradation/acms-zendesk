<?php

namespace Acms\Plugins\Zendesk;

class Hook
{
    /**
     * POSTモジュール処理前
     * $thisModuleのプロパティを参照・操作するなど
     *
     * @param \ACMS_POST $thisModule
     */
    public function beforePostFire($thisModule)
    {
        $moduleName = get_class($thisModule);

        if ($moduleName !== 'ACMS_POST_Form_Submit') {
            return;
        }
        if (!$thisModule->Post->isValidAll()) {
            return;
        }
        $formCode = $thisModule->Post->get('id');
        try {
            $engine = new Engine($formCode);
            $engine->send();
            if ($engine->isMail() === false) {
                $thisModule->Post->set('AdminTo', '');
            }
        } catch (\Exception $e) {
            echo ('ACMS Warning: Zendesk plugin, ' . $e->getMessage());
            exit();
        }
    }
}
