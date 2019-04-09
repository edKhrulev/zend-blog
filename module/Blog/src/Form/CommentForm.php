<?php


namespace Blog\Form;

use Zend\Form\Form;

class CommentForm extends Form
{
    public function __construct($name = null)
    {
        // We will ignore the name provided to the constructor
        parent::__construct('comment');

        $this->setAttribute('method', 'post');

        $this->addElements();
    }

    protected function addElements() {

        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'name',
            'type' => 'text',
            'options' => [
                'label' => 'Username',
            ],
        ]);
        $this->add([
            'name' => 'email',
            'type' => 'email',
            'options' => [
                'label' => 'Email',
            ],
        ]);
        $this->add([
            'name' => 'desc',
            'type' => 'textarea',
            'options' => [
                'label' => 'Description',
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Go',
                'id'    => 'submitbutton',
            ],
        ]);
    }
}