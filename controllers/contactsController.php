<?php

require_once './models/contacts.php';

class ContactsController
{

    public function add()
    {
        require_once './views/contacts/add.php';
    }

    public function customers()
    {
        $method = new Contacts();
        $customers = $method->showCustomers();

        require_once './views/contacts/customers.php';
    }

    public function edit_customer()
    {
        require_once './views/contacts/edit_customer.php';
    }

    public function providers()
    {
        $method = new Contacts();
        $providers = $method->showProviders();

        require_once './views/contacts/providers.php';
    }

    public function edit_provider()
    {
        require_once './views/contacts/edit_provider.php';
    }

}
