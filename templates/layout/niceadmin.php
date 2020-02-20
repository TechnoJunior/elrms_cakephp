<!DOCTYPE html>
<html>
    <head>
        <?= $this->Html->charset();?>
        <?= $this->Html->meta('viewport','width=device-width, initial-scale=1.0');?>
        <?= $this->Html->meta('description','The Collector Office of Mumbai City maintains the land details and individual land holding details for each owner. ');?>
        <?= $this->Html->meta('author','NIC');?>
        <?= $this->Html->meta('keyword',['eLRMS','LRMS','Land Revenue']);?>
        <?= $this->Html->meta('favicon.ico','img/favicon.png',['type' => 'icon']);?>
        <title><?php echo $title?></title>
        <!-- Bootstrap styles -->
        <?= $this->Html->css(['bootstrap.min','bootstrap-theme','elegant-icons-style','font-awesome.min','daterangepicker','bootstrap-datepicker','bootstrap-colorpicker'])?>
        <!-- Custom styles -->
        <?= $this->Html->css(['style','style-responsive'])?>
    </head>
    <body>
        <section id="container" class="">
            <?= $this->Flash->render() ?>
            <?= $this->element('header')?>
            <?= $this->element('aside')?>
            <?= $this->fetch('content') ?>
        </section>
        <!-- Javascript codes -->
        <?= $this->Html->script(['jquery','bootstrap.min','jquery.scrollTo.min','jquery.nicescroll','scripts','contactform'])?>
    </body>
</html>