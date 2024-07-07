<h1 style="line-height: 24px; margin-bottom:15px; font-size: 20px;" ><?php echo e(ucwords($subject)); ?>  </h1>

<?php if($status == \App\Models\DeliveryBoy::$statusRegistered): ?>
    <p style="line-height: 24px; margin-bottom:15px;">
        Dear, <?php echo e($delivery_boy->name); ?>

    </p>
    <p style="line-height: 24px; margin-bottom: 15px;">
        Congratulations for submitting your request, we just received your request.
        We will review your submission and confirm your registration.
    </p>
    <p style="line-height: 24px;margin-bottom:15px;">
        Once we approve your profile, we will mail you to let you know the approval status.
        Thank you for being part of the success of our business.
    </p>
<?php endif; ?>

<?php if($status == \App\Models\DeliveryBoy::$statusRejected): ?>
    <p style="line-height: 24px; margin-bottom:15px;">
        Dear, <?php echo e($delivery_boy->name); ?>

    </p>

    <p style="line-height: 24px; margin-bottom: 15px;">
        We have reviewed your profile,
        Unfortunately it does not meet our criteria and your profile has been not approved/<?php echo e($status_name); ?> at this time by
        <?php echo e($app_name); ?> administrator.
    </p>

    <?php if(isset($delivery_boy->remark) && $delivery_boy->remark != ""): ?>
        <p style="line-height: 24px;margin-bottom:15px;">
            <strong>Reason : </strong> <?php echo e($delivery_boy->remark); ?>

        </p>
    <?php endif; ?>

    <p style="line-height: 24px;margin-bottom:15px;">
        However, we are eagerly awaiting your re-submission and appreciate your contributions to the success of the business.
        Or you can directly contact or email to the admin by given below details.
    </p>
<?php endif; ?>

<?php if($status == \App\Models\DeliveryBoy::$statusDeactivated): ?>
    <p style="line-height: 24px; margin-bottom:15px;">
        Dear, <?php echo e($delivery_boy->name); ?>

    </p>

    <p style="line-height: 24px; margin-bottom: 15px;">
        We have reviewed your profile,
        Unfortunately it does not meet our criteria and your profile has been not approved/<?php echo e($status_name); ?> at this time by
        <?php echo e($app_name); ?> administrator.
    </p>

    <?php if(isset($delivery_boy->remark) && $delivery_boy->remark != ""): ?>
        <p style="line-height: 24px;margin-bottom:15px;">
            <strong>Reason : </strong> <?php echo e($delivery_boy->remark); ?>

        </p>
    <?php endif; ?>

    <p style="line-height: 24px;margin-bottom:15px;">
        However, you can directly contact or email to the admin by given below details.
    </p>
<?php endif; ?>

<?php if($status == \App\Models\DeliveryBoy::$statusActive): ?>
    <p style="line-height: 24px; margin-bottom:15px;">
        Dear, <?php echo e($delivery_boy->name); ?>

    </p>

    <p style="line-height: 24px; margin-bottom: 15px;">
        Congratulations on your new venture! It sounds like an exciting opportunity,
        and I'm looking forward to watching your progress as the business develops.
    </p>

    <p style="line-height: 24px;margin-bottom:15px;">
        If there is anything at all I can do to promote your new business, please let me know.
        I'd be glad to assist however I can if I can be of help.
    </p>

    <h1 style="line-height: 24px; margin-bottom:15px; font-size: 20px;"> Your profile details </h1>

    <table border="1" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff">
        <tr>
            <td align="left"> Name </td>
            <td align="left"> <?php echo e($delivery_boy->name); ?> </td>

            <td align="left"> Date of Birth </td>
            <td align="left"> <?php echo e($delivery_boy->dob); ?> </td>
        </tr>
        <tr>
            <td align="left"> Email </td>
            <td align="left"> <?php echo e($delivery_boy->email); ?> </td>

            <td align="left"> Mobile </td>
            <td align="left"> <?php echo e($delivery_boy->mobile); ?> </td>
        </tr>
    </table>

<?php endif; ?>

<?php /**PATH /opt/lampp81/htdocs/projects/eGrocerAdminPanel-1.9.8/resources/views/mail/delivery_boy_status.blade.php ENDPATH**/ ?>