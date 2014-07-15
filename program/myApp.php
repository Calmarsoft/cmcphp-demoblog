<?php

namespace cmcblog;

use \cmc\app;
use \cmc\sess;
use \cmc\db\dataenv;

// CMC framework inclusion
if ($config == 'prod')
    include_once('cmc.phar');
else
    include_once('cmc/index.php');

// frames of process
require_once 'control/postList.php';       // for the posts list view
require_once 'control/newpost.php';
require_once 'control/postdetail.php';

require_once 'control/login.php';       // for the posts list view
require_once 'control/register.php';       // for the posts list view

require_once 'backend/userAuth.php';

/**
 *
 * Application definition
 * 
 */
class myApp extends app {

    // here is the frameset for the application
    private $_frameclasses = array(
        postListFrame::className, // sample frame
        loginFrame::className,
        registerFrame::className,
        addpostFrame::className,
        postDetailFrame::className,
    );

    /*
     * those functions are mantarory to setup application
     */

    static function current($ClassName = __CLASS__) {
        return parent::current($ClassName);
    }

    protected function getFrameClasses() {
        return array_merge(parent::getFrameClasses(), $this->_frameclasses);
    }

    // override init to link the application to our customized session object
    protected function initialize() {
        MySess::current($this);     // to have correct session type
        parent::initialize();
    }

}

/**
 * Application's data environment
 */
class MyDataEnv extends dataenv {

    // the direct queries
    private $_myQueries = array(
        // this one is the post complete list
        'postlist' => 'SELECT p.id as id, u.id as uid, title, body, name AS author, modified FROM `posts` p 
                                                          INNER JOIN `user` u WHERE p.author = u.id order by created desc',
        // this one is for selecting one post
        'apost' => 'SELECT p.id as id, u.id as uid, title, body, name AS author, modified FROM `posts` p 
                                                          INNER JOIN `user` u WHERE p.author = u.id AND p.id = ? order by created desc'    );
    // the 'table' kind data (can perform direct operations like seek, update, insert,...)
    private $_myTables = array(
        'user' => array(), // table of users
        'posts' => array(), // table of posts
    );

    public function getQueryMap() {
        return $this->_myQueries;
    }

    public function getTables() {
        return $this->_myTables;
    }

}

/**
 * a custom session class
 */
class MySess extends sess {

    private $_mydataEnv;        // our data environment
    private $_userAuth;

    static function current($app, $ClassName = __CLASS__) {
        return parent::current($app, $ClassName);
    }

    public function __construct(app $app) {
        parent::__construct($app);
        $this->_mydataEnv = new MyDataEnv();
        $this->_userAuth = new userAuth();
    }

    protected function initialize() {
        parent::initialize();            
        $locData = array(
            'en' => array('errLogin' => 'Incorrect login, please check parameters',
                          'errRegExist' => 'Registration failed: a name or email already exists',
                          'msgWelcome' => 'Welcome, %1'
                )
        );
        $this->setTextData($locData);        
    }

    public function getDataEnv() {
        return $this->_mydataEnv;
    }

    /**
     * 
     * @return \cmcblog\userAuth
     */
    public function getUserAuth() {
        return $this->_userAuth;
    }
}

/*
 *  Some shortcuts in our namespace
 */

/**
 * 
 * @return \cmcblog\MySess
 */
function sess() {
    return \cmc\sess();
}

// gets our data envirnoment
function dataenv() {
    return sess()->getDataEnv();
}

// gets the current query
function qry() {
    return sess()->getRequest();
}
