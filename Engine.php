<?php

namespace Acms\Plugins\Zendesk;

use DB;
use SQL;
use Field;
use Field_Validation;
use Zendesk\API\HttpClient as ZendeskAPI;

class Engine
{
    /**
     * @var \Field
     */
    protected $formField;

    /**
     * @var \Field
     */
    protected $config;

    /**
     * Engine constructor.
     * @param string $code
     */
    public function __construct($code)
    {
        $field = $this->loadFrom($code);
        if (empty($field)) {
            throw new \RuntimeException('Not Found Form.');
        }
        $this->formField = $field;
        $this->config = $field->getChild('mail');
    }

    /**
     * Send
     */
    public function send()
    {
        // 有効でない場合は使用しない
        if ($this->config->get('zendesk_enable') !== 'true') {
          return;
        }

        // 初期設定
        $subdomain = $this->config->get('zendesk_subdomain');
        $username  = $this->config->get('zendesk_username');
        $token     = $this->config->get('zendesk_token');
        $client = new ZendeskAPI($subdomain);
        $client->setAuth('basic', ['username' => $username, 'token' => $token]);

        // 件名と本文を取得
        $subject_tpl = $this->addTemplate($this->config->get('zendesk_subject'));
        $body_tpl    = $this->addTemplate($this->config->get('zendesk_body'));
        $ticket["subject"] = build(setGlobalVars($subject_tpl), Field_Validation::singleton('post'));
        $ticket["comment"]["body"]    = build(setGlobalVars($body_tpl), Field_Validation::singleton('post'));
        
        // ユーザを取得
        $requester_name_tpl = $this->addTemplate($this->config->get('zendesk_requester_name'));
        $requester_email_tpl = $this->addTemplate($this->config->get('zendesk_requester_email'));
        $ticket["requester"]["name"] = build(setGlobalVars($requester_name_tpl), Field_Validation::singleton('post'));
        $ticket["requester"]["email"]    = build(setGlobalVars($requester_email_tpl), Field_Validation::singleton('post'));
        
        // カスタムフィールドを設定
        $custom_fields_id = $this->config->getArray('zendesk_custom_fields_id');
        $custom_fields_value = $this->config->getArray('zendesk_custom_fields_value');
        $custom_fields_array = array_combine ( $custom_fields_id, $custom_fields_value );
        if ($custom_fields_array) {
            foreach ($custom_fields_array as $key => $value) {
                $value = $this->addTemplate($value);
                $value = build(setGlobalVars($value), Field_Validation::singleton('post'));
                $custom_fields[] = ["id" => $key, "value" => $value];
            }
            $ticket["custom_fields"] = $custom_fields;
        }

        // ブランドを設定
        if ($this->config->get('zendesk_brand_enable') == 'true') {
            $brand_id = $this->addTemplate($this->config->get('zendesk_brand_id'));
            $brand_id = build(setGlobalVars($brand_id), Field_Validation::singleton('post'));
            if ($brand_id) {
                $ticket["brand_id"] = $brand_id;
            }
        }

        // チケットを送信
        $newTicket = $client->tickets()->create($ticket);

    }

    /**
     * @param string $code
     * @return bool|Field
     */
    protected function loadFrom($code)
    {
        $DB = DB::singleton(dsn());
        $SQL = SQL::newSelect('form');
        $SQL->addWhereOpr('form_code', $code);
        $row = $DB->query($SQL->get(dsn()), 'row');

        if (!$row) {
            return false;
        }
        $Form = new Field();
        $Form->set('code', $row['form_code']);
        $Form->set('name', $row['form_name']);
        $Form->set('scope', $row['form_scope']);
        $Form->set('log', $row['form_log']);
        $Form->overload(unserialize($row['form_data']), true);

        return $Form;
    }

    /**
     * 
     */
    public function isMail()
    {
        if ($this->config->get('zendesk_brand_enable') == 'true') {
            $brand_id = $this->addTemplate($this->config->get('zendesk_brand_id'));
            $brand_id = build(setGlobalVars($brand_id), Field_Validation::singleton('post'));
            if ($brand_id) {
                if ($this->config->get('zendesk_brand_mail_disable') == 'true') {
                    return false;
                }
            }
        }
        return true; 
    }

    /**
     * 
     */
    protected function addTemplate($tpl)
    {
        return '<!-- BEGIN_MODULE Form --><!-- BEGIN step#result -->' . $tpl . '<!-- END step#result --><!-- END_MODULE Form -->'; 
    }
}