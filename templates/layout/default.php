<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php

        use Cake\Core\Configure;

if (isset($titleforlayout)) {
            $pageTitle = $titleforlayout;
        } else {
            $pageTitle = $this->fetch('title');
        }
        ?> 
        <title><?= Configure::read('app.name') ?> <?= Configure::read('app.version') ?> | <?= $pageTitle ?></title>
        <?= $this->Html->meta('icon') ?>

        <!-- Google Font: Source Sans Pro -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="/plugins/fontawesome-free/css/all.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

        <?=
        $this->Html->css([
            '/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min',
            '/plugins/select2/css/select2',
            '/plugins/select2-bootstrap4-theme/select2-bootstrap4.min',
            '/plugins/icheck-bootstrap/icheck-bootstrap.min',
            '/plugins/jqvmap/jqvmap.min',
            '/css/adminlte.min',
            '/plugins/overlayScrollbars/css/OverlayScrollbars.min',
            '/plugins/daterangepicker/daterangepicker',
            '/plugins/summernote/summernote-bs4.min',
            '/css/application',
            '/plugins/DataTables/datatables',
            '/plugins/DataTables/DataTables-1.11.1/css/dataTables.bootstrap4.min', //DT
            '/plugins/DataTables/Buttons-2.0.0/css/buttons.dataTables.min',
            '/plugins/DataTables/Buttons-2.0.0/css/buttons.bootstrap4.min',
            '/plugins/DataTables/Select-1.3.3/css/select.bootstrap4.min', //DT
            '/plugins/toastr/toastr.min',
            '/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min',
            '/plugins/ekko-lightbox/ekko-lightbox',
        ]);
        ?>
        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>

    </head>
    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">
            <!-- Preloader -->
            <div class="preloader flex-column justify-content-center align-items-center">
                <img class="animation__shake" src="/img/WaJunction.png" alt="<?php Configure::read('app.name') ?> <?= Configure::read('app.version') ?>" height="60" width="60">
            </div>

            <!-- Navbar -->
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="index3.html" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="#" class="nav-link">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/logout">Logout</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="#" class="nav-link">Account: <?= $this->request->getSession()->read('Config.company'); ?> </a>
                    </li> 
                </ul>

                <!-- Right navbar links -->
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">

                        <?php
                        $session = $this->request->getSession();

                        if ($session->read('Auth.ugroup_id') == 1) {  //only for user can switch the firm
                            echo $this->AccountMenu->buildlist([
                                'selected' => $session->read('Config.account_id')
                            ]);
                        }
                        ?>

                    </li>
                </ul>
            </nav>
            <!-- /.navbar -->

            <!-- Main Sidebar Container -->
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <!-- Brand Logo -->
                <a href="index3.html" class="brand-link">
                    <img src="/img/Wajunction.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                    <span class="brand-text font-weight-light"><?= Configure::read('app.name') ?> <?= Configure::read('app.version') ?> </span>
                </a>

                <!-- Sidebar -->
                <div class="sidebar">
                    <!-- Sidebar user panel (optional) -->
                    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div class="image">
                            <img src="/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                        </div>
                        <div class="info">
                            <a href="#" class="d-block"><?= $this->request->getSession()->read('Auth.name'); ?></a>
                        </div>
                    </div>

                    <!-- SidebarSearch Form -->
                    <div class="form-inline">
                        <div class="input-group" data-widget="sidebar-search">
                            <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                            <div class="input-group-append">
                                <button class="btn btn-sidebar">
                                    <i class="fas fa-search fa-fw"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Sidebar Menu -->
                    <?= $this->element('sidebar_menu') ?>
                    <!-- /.sidebar-menu -->
                </div>
                <!-- /.sidebar -->
            </aside>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h4 class="m-0"> <?= $pageTitle ?></h4>
                            </div><!-- /.col -->
                            <div class="col-sm-6">
                                <?= $this->element('BreadCrumps') ?>
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div><!-- /.container-fluid -->
                </div>
                <!-- /.content-header -->

                <!-- Main content -->
                <section class="content">
                    <div class="row" data-select2-id="20">
                        <div class="col-xl-12" data-select2-id="19">
                            <div class="card" data-select2-id="18">
                                <div class="card-body" data-select2-id="17">
                                    <p class="card-title-desc"></p>
                                    <?php
                                    echo $this->Flash->render();
                                    echo $this->fetch('content');
                                    ?>
                                </div>
                            </div>
                        </div>
                </section>

                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
            <?= $this->element('footer'); ?>

            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
                <!-- Control sidebar content goes here -->
            </aside>
            <!-- /.control-sidebar -->
        </div>
        <!-- ./wrapper -->
        <?=
        $this->Html->script([
            '/plugins/jquery/jquery.min',
            '/plugins/bootstrap/js/bootstrap.bundle.min',
//            '/plugins/DataTables/datatables.min',
            '/plugins/DataTables/DataTables-1.11.1/js/jquery.dataTables.min', //DT
            '/plugins/DataTables/DataTables-1.11.1/js/dataTables.bootstrap4.min', //DT
            '/plugins/DataTables/Buttons-2.0.0/js/dataTables.buttons.min', //DT
            '/plugins/DataTables/Buttons-2.0.0/js/buttons.bootstrap4.min', //DT
            '/plugins/DataTables/Buttons-2.0.0/js/buttons.html5.min', //DT
            '/plugins/DataTables/pdfmake-0.1.36/pdfmake.min',
            '/plugins/DataTables/pdfmake-0.1.36/vfs_fonts',
            '/plugins/DataTables/Select-1.3.3/js/dataTables.select', //DT
            '/plugins/DataTables/Select-1.3.3/js/select.bootstrap4.min', //DT
            '/plugins/DataTables/JSZip-2.5.0/jszip.min',
            '/plugins/toastr/toastr.min',
            '/plugins/select2/js/select2.full.min',
            '/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min',
            '/plugins/jquery-ui/jquery-ui.min',
            '/plugins/moment/moment.min.js',
            '/plugins/chart.js/Chart.min',
            '/plugins/sparklines/sparkline',
            '/plugins/jqvmap/jquery.vmap.min',
            '/plugins/jqvmap/maps/jquery.vmap.usa',
            '/plugins/jquery-knob/jquery.knob.min',
            '/plugins/moment/moment.min',
            '/plugins/daterangepicker/daterangepicker',
            '/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min',
            '/plugins/summernote/summernote-bs4.min',
            '/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min',
            '/plugins/ekko-lightbox/ekko-lightbox.min',
            '/js/adminlte',
            '/js/select2-tab-fix.min',
            '/js/application',
            '/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js'
                //   '/js/autologout'
        ]);
        ?>

        <script>
            $.widget.bridge('uibutton', $.ui.button)
        </script>
        <?= $this->fetch('script') ?>
        <?=
        $this->Html->scriptBlock(sprintf(
                        'var csrfToken = %s;',
                        json_encode($this->request->getAttribute('csrfToken'))
        ));
        ?>
        <script  type="text/javascript">
            $(function () {
                $('.select2bs4').select2({
                    theme: 'bootstrap4'
                });

                $(document).on('click', '[data-toggle="lightbox"]', function (event) {
                    event.preventDefault();
                    $(this).ekkoLightbox();
                });
// Initialize tooltip component

                $(function () {
                    $('[data-toggle="tooltip"]').tooltip()
                })

// Initialize popover component
                $(function () {
                    $('[data-toggle="popover"]').popover()
                })








                /*** add active class and stay opened when selected ***/
                var url = window.location;

// for sidebar menu entirely but not cover treeview
                $('ul.nav-sidebar a').filter(function () {
                    if (this.href) {
                        return this.href == url || url.href.indexOf(this.href) == 0;
                    }
                }).addClass('active');

// for the treeview
                $('ul.nav-treeview a').filter(function () {
                    if (this.href) {
                        return this.href == url || url.href.indexOf(this.href) == 0;
                    }
                }).parentsUntil(".nav-sidebar > .nav-treeview").addClass('menu-open').prev('a').addClass('active');


            });
            
            

            $('.datepicker input').datepicker({
                format: "yyyy-mm-dd",
                weekStart: 0,
                todayBtn: true,
                clearBtn: true
            });


            function switchcompany(id) {
                $.ajax({
                    type: "GET",
                    url: '/settings/switchCompany/' + id,
                    success: function (data) {
                        var obj = JSON.parse(data);
                        var status = obj.status;
                        var msg = obj.msg;
                        if (status == "success") {
                            toastr.success(msg);
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                        } else {
                            toastr.info(msg);
                        }
                    }
                })
            }







        </script>
    </body>
</html>

<style>
    .dropdown-menu {
        z-index: 9999; /* Adjust the value as needed */
    }
</style>
