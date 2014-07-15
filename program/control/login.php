<?php

namespace cmcblog;


use cmc\ui\dynframe;
use cmc\ui\widgets\button,cmc\ui\widgets\input,cmc\ui\widgets\label;

/**
 * 
 * login screen management
 *
 * @author Benoit@calmarsoft.com
 */
class loginFrame extends dynframe {

    const className = __CLASS__;

    // the visual components we can work with
    protected $_widgetdef = array(
        'bt_login' => array(button::factory, 'login'),
        'in_login' => array(input::factory, 's_userid'),
        'in_passwd' => array(input::factory, 's_password'),
        'errortext' => array(label::factory, 'errortext'),
    );

    // panel name handled by this class (at view level)
    static public function getId() {
        return "login";
    }
    // name of frame at programmer level
    public function getName() {
        return 'logf';
    }

    // on first init, setup component additinal properties
    public function viewInitialUpdate($view) {
        $this->clientSetValidationCB('login.validChange', 300);
        $this->w('in_login')->addValidation('nonEmpty'); 
        $this->w('in_passwd')->addValidation('nonEmpty');          
        $this->AddClickEvent('bt_login', array($this, 'btLoginClick'));
    }
    
    // on refresh, clear error text
    public function viewUpdate($view, $sess) {      
        $this->w('errortext')->setCaption('');        
    }

    // login click => check login
    public function btLoginClick($view) {
        // auth object
        $auth = \cmc\sess()->getUserAuth();
        // get input fields
        $user = $this->w('in_login')->getValue();
        $pass = $this->w('in_passwd')->getValue();
        
        $auth->do_login($user, $pass);
        if (!$auth->is_logged())
            $text = \cmc\sess()->translate('errLogin');
        else {
            $text = \cmc\sess()->translateFmt('msgWelcome', $auth->userName());
            // redirect to main view
            $view->setRedirectBack('/postList');
        }
        // set result text
        $this->w('errortext')->setCaption($text);
    }

}
