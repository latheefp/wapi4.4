<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->
        <li class="nav-item menu-open">
            <a href="/dashboard" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                    Dashboard
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
        </li>

        <li class="nav-item menu-open">
            <a href="/uis/index" class="nav-link">
                <i class="nav-icon fas fa-comment-alt"></i>
                <p>
                    Chat Console<i class="right fas fa-angle-left"></i>
                </p>
            </a>
        </li>


        <li class="nav-item menu-open">
            <a href="#" class="nav-link">
                <i class="nav-icon fas  fa-bullhorn"></i>
                <p>
                    Messages
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="/Templates/" class="nav-link">
                        <i class="fas fa-file-alt nav-icon"></i>
                        <p>Templates</p>
                    </a>
                </li>
            </ul>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="/campaigns/index" class="nav-link">
                        <i class="fas fa-list-alt nav-icon"></i>
                        <p>Campaigns</p>
                    </a>
                </li>
            </ul>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="/campaigns/schedules" class="nav-link">
                        <i class="fas fa-clock nav-icon"></i>
                        <p>Schedules</p>
                    </a>
                </li>
            </ul>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="/campaigns/streams" class="nav-link">
                        <i class="fas fa-stream nav-icon"></i>
                        <p>Streams</p>
                    </a>
                </li>
            </ul>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="/settings/apis" class="nav-link">
                        <i class="fas fa-wind nav-icon"></i>
                        <p>API</p>
                    </a>
                </li>
            </ul>

        </li>


        <li class="nav-item menu-open">
            <a href="#" class="nav-link">
                <i class="nav-icon fas  fa-address-book"></i>
                <p>
                    Contacts
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="/contacts/index" class="nav-link">
                        <i class="fas fa-address-card nav-icon"></i>
                        <p>Contacts</p>
                    </a>
                </li>
            </ul>
        </li>


        <li class="nav-item menu-open">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-cogs"></i>
                <p>
                    System
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="/settings/listusers" class="nav-link">
                        <i class="fas fa-users-cog nav-icon"></i>
                        <p>Users</p>
                    </a>
                </li>

                <?php
                $session = $this->request->getSession();
                $group_id = $session->read('Auth.User.ugroup_id');
                if ($group_id == 1) {  //Below menus are only for super users. 
                    ?>
                    <li class="nav-item">
                        <a href="/settings/listgroups" class="nav-link">
                            <i class="fas fa-users nav-icon"></i>
                            <p>Groups</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/settings/permissions" class="nav-link">
                            <i class="fas fa-user-shield nav-icon"></i>
                            <p>Permissions</p>
                        </a>
                    </li>
                <?php }
                ?>





            </ul>
        </li>
    </ul>
</nav>

