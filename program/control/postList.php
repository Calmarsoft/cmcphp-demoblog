<?php
namespace cmcblog;

use cmc\ui\dynframe;
// used components
use cmc\ui\widgets\area, cmc\ui\widgets\label,
    cmc\ui\widgets\compositelist;

class postListFrame extends dynframe {

    const className = __CLASS__;
    var $count = 0;

    protected $_widgetdef = array(
        'cn-on' => array(area::factory, 'cn-on'),
        'cn-off' => array(area::factory, 'cn-off'),
        'user' => array(label::factory, 'user'),
        
        'posts' => array(compositelist::factory, 'postlist'),
    );
    
    
    private $postElems = array(
        'h3/a' => 'title',
        'h3/a/@href' => 'detail/${id}',
        'span[2]' => 'author',
        'div' => '!body',
        'span[1]' => 'modified',
    );

    static public function getId() {
        return 'postlist';
    }

    public function getName() {
        return 'postlistInstance';
    }

    // view is upon update, static part
    public function viewStaticUpdate($view) {        
    }
    // when material is calculated in the session
    public function viewInitialUpdate($view) {
        $this->w('posts')->setCompositeMap($this->postElems, array('id'));  
    }
    
    /**
     * 
     * @param \cmc\ui\view $view
     * @param type $sess
     */
    public function viewUpdate($view, $sess) {        
        $auth = \cmc\sess()->getUserAuth();

        // check if logout param is present
        $logout = \cmc\sess()->getParam('logout');
        if ($logout !== null) {
            $auth->logout();
            $view->setRedirectBack('/postlist');
            return;
        }   
        
        // alter display depending on 'logged' status
        $log = $auth->is_logged();
        if (!$log)
            $this->viewRemoveElements($view, 'user-auth');
        $this->w('cn-on')->setVisible($log);
        $this->w('cn-off')->setVisible(!$log);
        
        if ($log) {            
            $auth = \cmc\sess()->getUserAuth();
            $this->w('user')->setHtml($auth->userName());
        }
        
        // use this to see the xpath value for each subelement
        //$this->w('posts')->dump_lineElems($view);
        
        
        // bind posts list
        $this->w('posts')->setDataQuery($sess, 'postlist');     
        
    }

    /******* EVENTS ******/

}

