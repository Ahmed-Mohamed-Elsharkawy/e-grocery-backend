<?php $__env->startSection('content'); ?>
   
        <?php if(isset($type)): ?>
            <?php echo $__env->make('mail/'.$type, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>
        
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.mail', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp81/htdocs/projects/eGrocerAdminPanel-1.9.8/resources/views/mail.blade.php ENDPATH**/ ?>