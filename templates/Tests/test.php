<ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">

                        <?php
                       // $user = $this->Authentication->getIdentity();
                        $session = $this->request->getSession();
                        debug($session->read('Auth.username'));
                       // $session->write('Auth.company',"test1123");
                        debug($this->request->getSession()->read('User.name'));
                        debug($session->read('Auth.company'));
                        $name = $this->request->getSession()->read();
                        debug($name);

                        
                        
                        $session->write('Config.language', 'en');
                        
                        debug($session->read('Config.company'));
                        
                        if ($session->read('Ugroup.id') == 1) {  //only for user can switch the firm
                            echo $this->AccountMenu->buildlist([
                                'selected' => $session->read('Auth.User.account_id')
                            ]);
                        }
                        ?>

                    </li>
                </ul>