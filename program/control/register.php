<?php

namespace cmcblog;

use cmc\ui\dynframe;
use cmc\ui\widgets\button,
    cmc\ui\widgets\input,
    cmc\ui\widgets\label,
    cmc\ui\widgets\checkbox;

/**
 * 
 * login screen management
 *
 * @author Benoit@calmarsoft.com
 */
class registerFrame extends dynframe {

    const className = __CLASS__;

    protected $_widgetdef = array(
        'bt_register' => array(button::factory, 'register'),
        'name' => array(input::factory, 'r_name'),
        'email' => array(input::factory, 'r_email'),
        'pass1' => array(input::factory, 'r_pass_1'),
        'pass2' => array(input::factory, 'r_pass_2'),
        'cond' => array(checkbox::factory, 'r_conditions'),
        'errortext' => array(label::factory, 'errortext'),
    );

    // panel name handled by this class (at view level)
    static public function getId() {
        return "register";
    }

    // name of frame at programmer level
    public function getName() {
        return 'regf';
    }

    public function viewInitialUpdate($view) {
        $this->clientSetValidationCB('register.validChange', 300);
        $this->w('name')->addValidation('nonEmpty');
        $this->w('pass1')->addValidation('numChars', 6,24);
//        $this->w('pass2')->addValidation('numChars', 6,24);        
        $this->w('pass2')->addValidation('equals', 'pass1');
        $this->w('email')->addValidation('email');
        $this->w('cond')->addValidation('True');
        $this->AddClickEvent('bt_register', array($this, 'btRegister'));
    }

    public function viewUpdate($view, $sess) {
        $this->w('errortext')->setCaption('');
    }

    public function btRegister($view) {
        // auth object
        $auth = \cmc\sess()->getUserAuth();
        // get input fields
        $user = $this->w('name')->getValue();
        $pass = $this->w('pass1')->getValue();
        $email = $this->w('email')->getValue();

        $auth->do_register($user, $pass, $email);
        if (!$auth->is_logged())
            $text = "Registration failed";
        else {
            $text = 'Welcome, ' . $auth->userName();
            // redirect to main view
            $view->setRedirect('/postList', false);
        }
        // set result text
        $this->w('errortext')->setCaption($text);
        $view->setRedirectBack('/postList');
    }

}
