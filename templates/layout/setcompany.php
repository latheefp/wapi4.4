<!doctype html>
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
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="<?= Configure::read('app.description') ?>" name="description" />
        <meta content="Themesbrand" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="/images/favicon.ico">
        <?=
        $this->Html->css([
             '/plugins/bootstrap/js/bootstrap.bundle.min',
//            '/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min',
            '/plugins/select2/css/select2',
            '/plugins/select2-bootstrap4-theme/select2-bootstrap4.min',
//            '/plugins/icheck-bootstrap/icheck-bootstrap.min',
//            '/plugins/jqvmap/jqvmap.min',
            '/css/adminlte.min',
//            '/plugins/overlayScrollbars/css/OverlayScrollbars.min',
//            '/plugins/daterangepicker/daterangepicker',
//            '/plugins/summernote/summernote-bs4.min',
            '/css/application',
//            '/plugins/DataTables/datatables',
//            '/plugins/DataTables/DataTables-1.11.1/css/dataTables.bootstrap4.min', //DT
//            '/plugins/DataTables/Buttons-2.0.0/css/buttons.dataTables.min',
//            '/plugins/DataTables/Buttons-2.0.0/css/buttons.bootstrap4.min',
//            '/plugins/DataTables/Select-1.3.3/css/select.bootstrap4.min', //DT
            '/plugins/toastr/toastr.min'
        ]);
        ?>
        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>
    </head>
    <body>

        <?php
        echo $this->Flash->render();
       echo $this->fetch('content');
        $this->Html->script([
            '/plugins/jquery/jquery.min',
            '/plugins/bootstrap/js/bootstrap.bundle.min',
//            '/plugins/DataTables/datatables.min',
//            '/plugins/DataTables/DataTables-1.11.1/js/jquery.dataTables.min', //DT
//            '/plugins/DataTables/DataTables-1.11.1/js/dataTables.bootstrap4.min', //DT
//            '/plugins/DataTables/Buttons-2.0.0/js/dataTables.buttons.min', //DT
//            '/plugins/DataTables/Buttons-2.0.0/js/buttons.bootstrap4.min', //DT
//            '/plugins/DataTables/Buttons-2.0.0/js/buttons.html5.min', //DT
//            '/plugins/DataTables/pdfmake-0.1.36/pdfmake.min',
//            '/plugins/DataTables/pdfmake-0.1.36/vfs_fonts',
//            '/plugins/DataTables/Select-1.3.3/js/dataTables.select', //DT
//            '/plugins/DataTables/Select-1.3.3/js/select.bootstrap4.min', //DT
//            '/plugins/DataTables/JSZip-2.5.0/jszip.min',
            '/plugins/toastr/toastr.min',
            '/plugins/select2/js/select2.full.min',
//            '/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min',
//            '/plugins/jquery-ui/jquery-ui.min',
//            '/plugins/moment/moment.min.js',
//            '/plugins/chart.js/Chart.min',
//            '/plugins/sparklines/sparkline',
//            '/plugins/jqvmap/jquery.vmap.min',
//            '/plugins/jqvmap/maps/jquery.vmap.usa',
//            '/plugins/jquery-knob/jquery.knob.min',
//            '/plugins/moment/moment.min',
//            '/plugins/daterangepicker/daterangepicker',
//            '/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min',
//            '/plugins/summernote/summernote-bs4.min',
//            '/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min',
//            '/js/adminlte',
//            '/js/select2-tab-fix.min',
//            '/js/application',
            '/js/autologout'
        ]);
        ?>
        <?= 
        $this->fetch('script') ?>
        <?=
        $this->Html->scriptBlock(sprintf(
                        'var csrfToken = %s;',
                        json_encode($this->request->getAttribute('csrfToken'))
        ));
        ?>

       
        <?= $this->element('footer'); ?>
    </body>

</html>
