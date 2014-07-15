<?php

namespace cmcblog;

use cmc\ui\dynframe;
use cmc\ui\widgets\button,
    cmc\ui\widgets\label,
    cmc\ui\widgets\input;
/**
 * 
 * login screen management
 *
 * @author Benoit@calmarsoft.com
 */
class addpostFrame extends dynframe {

    const className = __CLASS__;

    protected $_widgetdef = array(
        'bt_update' => array(button::factory, 'update'),
        'title' => array(input::factory, 'title'),
        'body' => array(input::factory, 'body'),
        'errortext' => array(label::factory, 'errortext'),        
    );

    // panel name handled by this class (at view level)
    static public function getId() {
        return 'addpost';
    }

    // name of frame at programmer level
    public function getName() {
        return 'addpost';
    }
    
    // called by framework to check session validity
    // login must be valid for this page
    public function bIsSessionValid($view, $sess) {
        // auth object
        $auth = \cmc\sess()->getUserAuth();
        // Must be connected!
        if (!$auth->is_logged()) {
            $view->setRedirect('/postList', false);
            return false;
        }
        return true;
    }

    public function viewInitialUpdate($view) {
        $this->w('title')->addValidation('minChars', 20);
        $this->w('body')->addValidation('minChars', 30);

        $this->AddClickEvent('bt_update', array($this, 'btUpdate'));
    }

    // on each refresh, reset error text
    public function viewUpdate($view, $sess) {
        $this->w('title')->setValue('');
        $this->w('body')->setValue('');
        $this->w('errortext')->setCaption('');
    }

    // a new post is submitted
    public function btUpdate($view) {   
        $auth = \cmc\sess()->getUserAuth();
        
        $db = \cmc\sess()->getDataEnv()->getConnectedDB();
        if ($db) {
            $table = $db->gettable('posts');
            if ($table) {
                try {
                    $table->insertData(array(
                        'author' => $auth->userId(),
                        'title' => $this->w('title')->getValue(),
                        'body' => $this->w('body')->getValue(),
                        'modified' => date("Y-m-d H:i:s")
                            )
                    );
                } catch (\cmc\db\DatabaseException $e) {
                    return false;
                }
            }
        }           
        $view->setRedirect('/postList', false);
    }

}
