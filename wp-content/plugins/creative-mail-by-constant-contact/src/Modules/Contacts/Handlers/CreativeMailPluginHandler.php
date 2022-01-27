<?php

namespace CreativeMail\Modules\Contacts\Handlers;

define('CE4WP_CE_EVENTTYPE', 'WordPress - Creative Mail Form');

use CreativeMail\Managers\RaygunManager;
use CreativeMail\Modules\Contacts\Models\ContactModel;
use CreativeMail\Modules\Contacts\Models\OptActionBy;

class CreativeMailPluginHandler extends BaseContactFormPluginHandler
{
    public function convertToContactModel($contact)
    {
        $contactModel = new ContactModel();

        $contactModel->setEventType(CE4WP_CE_EVENTTYPE);

        $contactModel->setOptIn(false);
        $contactModel->setOptOut(false);
        if (!empty($contact['consent'])) {
            $consent_bool = $contact['consent'] == 'true';
            $contactModel->setOptIn($consent_bool);
        }
        $contactModel->setOptActionBy(OptActionBy::Visitor);

        if (!empty($contact['email'])) {
            $contactModel->setEmail($contact['email']);
        }
        if (!empty($contact['first_name'])) {
            $contactModel->setFirstName($contact['first_name']);
        }
        if (!empty($contact['last_name'])) {
            $contactModel->setLastName($contact['last_name']);
        }
        if (!empty($contact['telephone'])) {
            $contactModel->setPhone($contact['telephone']);
        }
        if (!empty($contact['list_id'])) {
            $contactModel->setListId((int)$contact['list_id']);
        }
        return $contactModel;
    }

    public function ceHandleCreativeEmailSubmission($data)
    {
        try {
            $this->upsertContact($this->convertToContactModel($data));
        } catch (\Exception $exception) {
            RaygunManager::get_instance()->exception_handler($exception);
        }
    }

    public function registerHooks()
    {
        add_action('ce4wp_contact_submission', array($this, 'ceHandleCreativeEmailSubmission'), 10, 1);
    }

    public function unregisterHooks()
    {
        remove_action('ce4wp_contact_submission', array($this, 'ceHandleCreativeEmailSubmission'));
    }

    public function get_contacts($limit = null)
    {
        if (!is_int($limit) || $limit <= 0) {
            $limit = null;
        }
        //Get form submissions from the wp_ce4wp_contacts table
        global $wpdb;
        $contactsArray = array();

        //get contacts
        $contactsQuery = 'SELECT * FROM wp_ce4wp_contacts';
        $contactsResult = $wpdb->get_results($wpdb->prepare($contactsQuery));

        foreach ($contactsResult as $contact) {
            $contactModel = null;
            try {
                $contact = $this->convertToContactModel(json_decode(json_encode($contact), true));
                if (!empty($contact->getEmail())) {
                    array_push($contactsArray, $contact);
                }

            } catch (\Exception $exception) {
                RaygunManager::get_instance()->exception_handler($exception);
                continue;
            }
            if (isset($limit) && count($contactsArray) >= $limit) {
                break;
            }
        }
        if (!empty($contactsArray)) {
            return $contactsArray;
        }
        return null;
    }
}
