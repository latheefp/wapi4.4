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
    <body class="hold-transition sidebar-collapse layout-top-nav">
        <div class="wrapper">

            <nav class="main-header navbar navbar-expand-md navbar-dark  info-color">
                <div class="container">
                    <a href="/" class="navbar-brand">
                        <!--<img src="../../dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">-->
                        <span class="brand-text font-weight-light"><?= Configure::read('app.name') ?></span>
                    </a>
                    <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse order-3" id="navbarCollapse">

                        <ul class="navbar-nav">

                            <li class="nav-item">
                                <a href="/" class="nav-link">Home</a>
                            </li>
                            <li class="nav-item">
                                <a href="/contact" class="nav-link">Contact</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Services</a>
                                <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                                    <li><a href="/users/login" class="dropdown-item">Login</a></li>
                                    <li><a href="/pricing" class="dropdown-item">Pricing</a></li>
                                   
                                </ul>
                            </li>
                        </ul>

                        <form class="form-inline ml-0 ml-md-3">
                            <div class="input-group input-group-sm">
                                <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                                <div class="input-group-append">
                                    <button class="btn btn-navbar" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

               
                </div>
            </nav>




            <div class="content-wrapper">

                


                <div class="content">
                    <div class="container">
                        <div class="row">

                            <?php
                            echo $this->Flash->render();
                            echo $this->fetch('content');
                            ?>

                        </div>

                    </div>
                </div>

            </div>

            <footer class="main-footer">

<!--                <div class="float-right d-none d-sm-inline">
                    Anything you want
                </div>-->
                  <?= $this->element('footer'); ?>
               
            </footer>
        </div>


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







        </script>




    </body>
</html>
