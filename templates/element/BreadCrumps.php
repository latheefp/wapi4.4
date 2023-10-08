 <?php
    $this->Breadcrumbs->setTemplates([
        'wrapper' => '<ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"{{attrs}}>{{content}}</li></ol>',
        'item' => '<li class="breadcrumb-item" {{attrs}}><a href="{{url}}"{{innerAttrs}}>{{title}}</a></li>{{separator}}'
    ]);

//     echo $this->Breadcrumbs->render(
//        ['class' => 'breadcrumbs-trail'],
//        ['separator' => '<i class="fa fa-angle-right"></i>']   
//             
//    );
     echo $this->Breadcrumbs->render();
?>