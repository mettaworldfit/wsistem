<?php

class ContactsController
{

    public function add()
    {
        require_once './views/contacts/add.php';
    }

    public function customers()
    {
       
        require_once './views/contacts/customers.php';
    }

    public function edit_customer()
    {
        require_once './views/contacts/edit_customer.php';
    }

    public function providers()
    {
     
        require_once './views/contacts/providers.php';
    }

    public function edit_provider()
    {
        require_once './views/contacts/edit_provider.php';
    }

}
