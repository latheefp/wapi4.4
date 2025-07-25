<?php
$session = $this->request->getSession();
$group_id = $session->read('Config.ugroup_id');
?>
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
             with Font Awesome Free 5.15.3 icons -->
        <li class="nav-item ">
            <a href="/dashboards" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                    Dashboard
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
        </li>

        <li class="nav-item ">
            <a href="/chats" class="nav-link">
                <i class="nav-icon fas fa-comments"></i>
                <p>
                    Chat Console<i class="right fas fa-angle-left"></i>
                </p>
            </a>
        </li>

        <li class="nav-item ">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-bullhorn"></i>
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

        <li class="nav-item ">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-address-book"></i>
                <p>
                    Contacts
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>

            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="/contacts/index" class="nav-link">
                        <i class="fas fa-file-alt nav-icon"></i>
                        <p>Saved Lists</p>
                    </a>
                </li>
            </ul>

            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="/contacts/blockedlist" class="nav-link">
                        <i class="fas fa-user-slash nav-icon"></i>
                        <p>Blocked List</p>
                    </a>
                </li>
            </ul>
        </li>



        <li class="nav-item ">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-money-bill-alt"></i>
                <p>
                    Invoice and Payment
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="/invoices/" class="nav-link">
                        <i class="fas fa-file-invoice-dollar nav-icon"></i>
                        <p>Invoices</p>
                    </a>
                </li>
            </ul>

        </li>



        <li class="nav-item ">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-chart-line"></i> <!-- Changed icon to fa-chart-line -->
                <p>
                    Reports
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="/reports/" class="nav-link">
                        <i class="fas fa-chart-bar nav-icon"></i> <!-- Changed icon to fa-chart-bar -->
                        <p>Usage Report</p>
                    </a>
                </li>
            </ul>
        </li>




        <li class="nav-item ">
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

                if ($group_id == 1) {  //Below menus are only for super users.
                    ?>

                    <li class="nav-item">
                        <a href="/accounts" class="nav-link">
                            <i class="fas fa-id-card nav-icon"></i>
                            <p>Accounts</p>
                        </a>
                    </li>
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
      
                    <li class="nav-item">
                        <a href="/settings/pricing/" class="nav-link">
                            <i class="fas fa-tags nav-icon"></i>
                            <p>Rate Card</p>
                        </a>
                    </li>

           
                    <?php
                }
                ?>
            </ul>
        </li>
    </ul>
</nav>