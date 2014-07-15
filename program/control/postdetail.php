<?php

namespace cmcblog;

use cmc\ui\dynframe;
use cmc\ui\widgets\button,
    cmc\ui\widgets\label,
    cmc\ui\widgets\input,
    cmc\ui\widgets\area;

/**
 * 
 * login screen management
 *
 * @author Benoit@calmarsoft.com
 */
class postdetailFrame extends dynframe {

    const className = __CLASS__;

    protected $_widgetdef = array(
        'bt_back' => array(button::factory, 'back'),
        'bt_del' => array(button::factory, 'delete'),
        'title' => array(label::factory, 'title'),
        'body' => array(label::factory, 'body'),
        'bt_upd' => array(button::factory, 'modify'),
        'bt_cancel' => array(button::factory, 'cancel'),
        'bt_save' => array(button::factory, 'save'),
        'in_title' => array(input::factory, 'in_title'),
        'in_body' => array(input::factory, 'in_body'),
        'editarea' => array(area::factory, 'editarea'),
        'viewarea' => array(area::factory, 'viewarea'),
    );

    // panel name handled by this class (at view level)
    static public function getId() {
        return 'detail';
    }

    // name of frame at programmer level
    public function getName() {
        return 'detail';
    }

    // binds our widgets to events
    public function viewInitialUpdate($view) {
        $this->AddClickEvent('bt_back', array($this, 'btBack'));
        // delete is special: binds to a local method, and a server method; allows client side confirmation
        $this->clientAddClickCB('bt_del', 'app.confirmdel');
        $this->AddEventListener('click', 'bt_del', array($this, 'btDelete'));

        $this->AddClickEvent('bt_upd', array($this, 'btUpdate'));
        $this->AddClickEvent('bt_cancel', array($this, 'btCancel'));
        $this->AddClickEvent('bt_save', array($this, 'btSave'));
    }

    // this page is valid with or without login
    public function bIsSessionValid($view, $sess) {
        return true;
    }

    // ID of the post from the REST path
    public function currentId() {
        $params = sess()->getParams();
        $id = $params[0];
        return $id;
    }

    /**
     * 
     * some private functions to get post data, check user, and update visibility
     * 
     * */
    private $postOwner;

    // reads current post, and test if the user owns the post
    private function getCurrentPost($view) {
        $id = $this->currentId();
        $this->postOwner = false;
        $auth = sess()->getUserAuth();

        $r = sess()->getDataEnv()->getQueryFirst('apost', array($id));
        if (!$r)
            $view->setRedirectBack('/postList');
        //updated whenever the user owns the post
        $this->postOwner = $auth->userId() == $r['uid'];
        return $r;
    }

    // updates object visibility depending on update or view mode       
    private function UpdateMode($upd) {
        $this->w('editarea')->setVisible($upd);
        $this->w('bt_cancel')->setVisible($upd);
        $this->w('bt_save')->setVisible($upd);

        $this->w('viewarea')->setVisible(!$upd);
        $this->w('bt_back')->setVisible(!$upd);
        $this->w('bt_del')->setVisible(!$upd && $this->postOwner);
        $this->w('bt_upd')->setVisible(!$upd && $this->postOwner);
    }

    // reads and view the current post
    private function readPost($view) {
        $r = $this->getCurrentPost($view);
        if (!$r)
            return $r;

        $this->UpdateMode(false);
        $this->w('title')->setCaption($r['title']);
        $this->w('body')->setHtml($r['body']);
        $this->w('in_title')->setValue('');
        $this->w('in_body')->setValue('');        
        return $r;
    }

    // refresh: go back to view mode
    public function viewUpdate($view, $sess) {
        $r = $this->readPost($view);
        if (!$r)
            return;
    }

    // bt_del click: executes the 'delete' order
    public function btDelete($view) {
        $this->getCurrentPost();
        $id = $this->currentId();
        if ($id && $this->postOwner) {
            $table = dataenv()->getQueryDS('posts');
            $table->deleteData(array('id' => $id));
        }
        $view->setRedirect('/postList');
    }

    // bt_back
    public function btBack($view) {
        // set result text        
        $view->setRedirect('/postList');
    }

    // bt_upd click: switch to update mode
    public function btUpdate($view) {
        $r = $this->getCurrentPost($view);
        if (!$r || !$this->postOwner)
            return;
        $this->w('in_title')->setValue($r['title']);
        $this->w('in_body')->setValue($r['body']);
        $this->UpdateMode(true);
    }

    // bt_cancel click: go back to view mode
    public function btCancel($view) {
        $this->UpdateMode(false);
    }

    // bt_save click: performs the update
    public function btSave($view) {
        $this->getCurrentPost();
        $id = $this->currentId();
        // post valid and owned by user
        if ($id && $this->postOwner) {
            $table = dataenv()->getQueryDS('posts');
            $table->updateData(array('id' => $id,
                'title' => $this->w('in_title')->getValue(),
                'body' => $this->w('in_body')->getValue(),
                'modified' => date("Y-m-d H:i:s")
            ));
            // reads data back...
            $this->readPost($view);
        }
    }

}
