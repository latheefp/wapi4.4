<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php use Cake\Core\Configure; ?> 
  <title><?= Configure::read('app.name')?> <?= Configure::read('app.version') ?> | Log in</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/css/adminlte.min.css">
   <link rel="stylesheet" href="/plugins/toastr/toastr.min.css">
   <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
</head>
<body class="hold-transition login-page">
    <?php
        echo  $this->Flash->render();
        echo  $this->fetch('content') ;
       
          ?>
    
    <!-- jQuery -->
    <script src="/plugins/jquery/jquery.min.js"></script>


    <script src="/js/facebook.js"></script>

    <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=your-pixel-id&ev=PageView&noscript=1"/>
    <!-- Bootstrap 4 -->
    <script src="/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="/js/adminlte.min.js"></script>
    <script src="/plugins/toastr/toastr.min.js"></script>
     <?= $this->Html->scriptBlock(sprintf(
            'var csrfToken = %s;',
            json_encode($this->request->getAttribute('csrfToken'))
        ));
        ?>
    <script>
         <?=   $this->fetch('script')  ?>   
   </script>
        
</body></html>
