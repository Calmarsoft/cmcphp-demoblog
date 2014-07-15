<?php

namespace cmcblog;

/**
 *
 * handles the user authentication
 * 
 */
class userAuth {

    private $userName, $is_admin, $email, $userid;

    public function is_logged() {
        return is_string($this->userName);
    }

    public function logout() {
        $this->userName = null;
        $this->is_admin = null;
        $this->email = null;
        $this->userid = null;
    }

    public function is_admin() {
        return ($this->is_logged() && $this->is_admin);
    }

    public function userName() {
        return $this->userName;
    }

    public function userId() {
        return $this->userid;
    }
    
    private function validPwd($password, $pwHash) {
        $calc = hash('sha1', $password);
        if ($calc === $pwHash)
            return true;
        return false;
    }

    private function encodePwd($password) {
        return hash('sha1', $password);
    }

    public function do_login($login, $password) {
        $this->logout();

        $db = \cmc\sess()->getDataEnv()->getConnectedDB();
        if ($db) {
            $table = $db->gettable('user');
            if ($table) {
                $table->setFilter(array('name' => $login));
                $row = $table->next();
                if ($row && $this->validPwd($password, $row['password'])) {
                    $this->userName = $row['name'];
                    $this->email = $row['email'];
                    $this->is_admin = $row['is_admin'];
                    $this->userid = $row['id'];
                }
            }
        }
    }

    public function do_register($login, $password, $email) {
        // Additional checks
        
        $db = \cmc\sess()->getDataEnv()->getConnectedDB();
        if ($db) {
            $table = $db->gettable('user');
            if ($table) {
                try {
                    $table->insertData(array(
                        'name' => $login,
                        'password' => $this->encodePwd($password),
                        'email' => $email
                            )
                    );
                } catch (\cmc\db\DatabaseException $e) {
                    return false;
                }
            }
        }
        return $this->do_login($login, $password);
    }

}
