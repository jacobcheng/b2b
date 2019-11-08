<?php

namespace app\admin\behavior;

use app\admin\model\sales\Contact;

class AddContact
{
    public function run(&$params)
    {
        if (isset($params['contact'])) {
            $ids = array_diff(array_column($params['contacts'],'id'),array_column($params['contact'], 'id'));
            if ($ids) {
                Contact::destroy($ids);
            }
            foreach ($params['contact'] as $param){
                Contact::update($param);
            }
        } else {
            Contact::destroy(array_column($params['contacts'], 'id'));
        }

        if (isset($params['addcontact'])) {
            $params->contacts()->saveAll($params['addcontact']);
        }

        if (!Contact::get($params->contact_id)) {
            $id = Contact::where(['client_id' => $params->id])->limit(1)->value('id');
            $params->save(['contact_id' => $id]);
        }
    }
}
