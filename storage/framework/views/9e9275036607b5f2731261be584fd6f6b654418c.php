
<h1 style="line-height: 24px; margin-bottom:15px; font-size: 20px;" ><?php echo e(ucwords($subject)); ?>  </h1>

<?php
    $currency = \App\Models\Setting::get_value('currency') ?? '$';
?>

<?php if(isset($assign) && $assign == true): ?>

    <?php if(isset($seller) && !empty($seller)): ?>

        <p style="line-height: 24px; margin-bottom:15px;">
            Dear, <?php echo e($seller->name); ?>

        </p>

        <p style="line-height: 24px; margin-bottom: 15px;">
            You order, #<?php echo e($order->id); ?> is just assigned a delivery boy.
        </p>

        <h1 style="line-height: 24px; margin-bottom:15px; font-size: 20px;"> Delivery Boy details </h1>

        <table border="1" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff">
            <tr>
                <td align="left"> Name </td>
                <td align="left"> <?php echo e($deliveryBoy->name); ?> </td>

                <td align="left"> Date of Birth </td>
                <td align="left"> <?php echo e($deliveryBoy->dob); ?> </td>
            </tr>
            <tr>
                <td align="left"> Email </td>
                <td align="left"> <?php echo e($deliveryBoy->email); ?> </td>

                <td align="left"> Mobile </td>
                <td align="left"> <?php echo e($deliveryBoy->mobile); ?> </td>
            </tr>
        </table><br>
    <?php else: ?>
        <p style="line-height: 24px; margin-bottom:15px;">
            Dear, <?php echo e($deliveryBoy->name); ?>

        </p>
        <p style="line-height: 24px; margin-bottom: 15px;">
          You have just assigned new order, #<?php echo e($order->id); ?>.
        </p>
    <?php endif; ?>

    <p style="line-height: 24px; margin-bottom: 15px;">
        Estimated delivery time is <?php echo e($order->delivery_time); ?>

    </p>

    <p style="line-height: 24px; margin-bottom: 15px;">
        <strong>
            Delivery address :-
        </strong><br>
        <?php echo e($order->address); ?>

    </p>

    <a href="<?php echo e($redirect_url); ?>" style="color: #ffffff; text-decoration: none;">
        <table border="0" align="center" width="180" cellpadding="0" cellspacing="0" bgcolor="#37a279" style="margin-bottom:20px;">
            <tr>
                <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
            </tr>
            <tr>
                <td align="center" style="color: #ffffff; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 22px; letter-spacing: 2px;">
                    <!-- main section button -->
                    <div style="line-height: 22px;">
                        Check it out
                    </div>
                </td>
            </tr>
            <tr>
                <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
            </tr>
        </table>
    </a>

<?php else: ?>
    <?php if($user_type == 0): ?>
        

        <?php if($order->active_status == \App\Models\OrderStatusList::$received): ?>

            <p style="line-height: 24px; margin-bottom:15px;">
                Dear, <?php echo e($user->name); ?>

            </p>

            <p style="line-height: 24px; margin-bottom: 15px;">
                Your order has been received.
            </p>
            <p style="line-height: 24px;margin-bottom:15px;">
                This notification is just a friendly reminder (not a bill or a second charge) that on <?php echo e($order->created_at); ?> you placed an order from
                <?php echo e($app_name); ?>.
                The charge will appear on your bill.
                This is just a reminder to help you recognise the charge.
            </p>

            <div class="row">
                <p class="h6 ">Product Details:</p>
                <div class="row">
                    <div class="col-xs-12 table-responsive">
                        <table class="table">
                            <thead class="text-center">
                            <tr>
                                <th>Sr No.</th>
                                <th>Product Code</th>
                                <th>Name</th>
                                <th>Unit</th>
                                <th>Price</th>
                                <th>Tax <?php echo e($currency); ?> (%)</th>
                                <th>Qty</th>
                                <th>SubTotal ( <?php echo e($currency); ?> )</th>
                            </tr>
                            </thead>
                            <tbody class="text-center">
                            <?php
                                $total_tax_amount = 0;
                                $total_quantity = 0;
                                $total_sub_total = 0;
                                $order_items = $order->items;
                            ?>
                            <?php $__currentLoopData = $order_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($index+1); ?><br></td>
                                    <td><?php echo e($item->product_id); ?><br></td>
                                    <td><?php echo e($item->product_name); ?><br></td>
                                    <td><?php echo e($item->variant_name); ?><br></td>
                                    <td><?php echo e($item->price); ?></td>
                                    <td><?php echo e($item->tax_amount. "  (" .$item->tax_percentage."%)"); ?><br></td>
                                    <td><?php echo e($item->quantity); ?><br></td>
                                    <td><?php echo e($item->sub_total); ?><br></td>
                                    <?php
                                        $total_tax_amount = $total_tax_amount + $item->quantity;
                                        $total_quantity = $total_quantity + $item->quantity;
                                        $total_sub_total = $total_sub_total + $item->sub_total;
                                    ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot class="text-center">
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>Total</th>
                                <td><?php echo e($total_tax_amount); ?><br></td>
                                <td><?php echo e($total_quantity); ?><br></td>
                                <td><?php echo e($total_sub_total); ?><br></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <p><b>Payment Method : </b> <?php echo e(strtoupper($order->payment_method)); ?></p>
                <div class="table-responsive">
                    <table>
                        <tbody>
                        <tr>
                            <th>Total Order Price (<?php echo e($currency); ?>)</th>
                            <td><?php echo e($order->total); ?></td>
                        </tr>
                        <tr>
                            <th>Delivery Charge (<?php echo e($currency); ?>)</th>
                            <td><?php echo e($order->delivery_charge); ?></td>
                        </tr>
                        <tr>
                            <th>Discount <?php echo e($currency); ?> (%)</th>
                            <?php
                                $discount_in_rupees = 0;
                                if ( $order->discount > 0) {
                                    $discounted_amount = $order->total * $order->discount / 100;
                                    $final_total = $order->total - $discounted_amount;
                                    $discount_in_rupees = $order->total - $final_total;
                                }
                            ?>
                            <td><?php echo e('- ' . $discount_in_rupees . ' (' . $order->discount . '%)'); ?></td>
                        </tr>
                        <tr>
                            <th>Promo (<?php echo e($order->promo_code); ?>) Discount (<?php echo e($currency); ?>)</th>
                            <td><?php echo e('- ' . $order->promo_discount); ?></td>
                        </tr>
                        <tr>
                            <th>Wallet Used (<?php echo e($currency); ?>)</th>
                            <td><?php echo e('- ' . $order->wallet_balance); ?></td>
                        </tr>
                        <tr>
                            <th>Final Total (<?php echo e($currency); ?>)</th>
                            <td><?php echo e('= ' . ceil($order->final_total)); ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <p style="line-height: 24px; margin-bottom: 15px;">
                We would like to take this opportunity to thank you for your business and look forward to serving you in the future.
            </p>
        <?php elseif(in_array($order->active_status, array(
                                                \App\Models\OrderStatusList::$processed,
                                                \App\Models\OrderStatusList::$shipped,
                                                \App\Models\OrderStatusList::$outForDelivery
                                             )
                        )
            ): ?>
            <p style="line-height: 24px; margin-bottom:15px;">
                Dear, <?php echo e($user->name); ?>

            </p>

            <p style="line-height: 24px; margin-bottom: 15px;">
                Your order has been <?php echo e($status_name); ?>.
            </p>

            <p style="line-height: 24px; margin-bottom: 15px;">
                This notification is just a friendly reminder (not a bill or a second charge) that on <?php echo e($order->created_at); ?> you placed an order from
                <?php echo e($app_name); ?>.
                The charge will appear on your bill.
                This is just a reminder to help you recognise the charge.
            </p>
            <p style="line-height: 24px; margin-bottom: 15px;">
                Order summary
                #<?php echo e($order->id); ?>

                Final Total - <?php echo e($currency); ?> <?php echo e($order->final_total); ?>

                We would like to take this opportunity to thank you for your business and look forward to serving you in the future.
            </p>
        <?php elseif($order->active_status == \App\Models\OrderStatusList::$delivered): ?>
            <p style="line-height: 24px; margin-bottom:15px;">
                Dear, <?php echo e($user->name); ?>

            </p>

            <p style="line-height: 24px; margin-bottom: 15px;">
                Your order has been <?php echo e($status_name); ?>.
            </p>

            <p style="line-height: 24px; margin-bottom: 15px;">
                This notification is just a friendly reminder (not a bill or a second charge) that on <?php echo e($order->created_at); ?> you placed an order from
                <?php echo e($app_name); ?>.
                The charge will appear on your bill.
            </p>

            

        <?php elseif(in_array($order->active_status, array(
                                                \App\Models\OrderStatusList::$cancelled,
                                                \App\Models\OrderStatusList::$returned,
                                             )
                        )
        ): ?>

            <p style="line-height: 24px; margin-bottom:15px;">
                Dear, <?php echo e($user->name); ?>

            </p>

            <p style="line-height: 24px; margin-bottom: 15px;">
                Sorry to see your order #<?php echo e($order->id); ?> belonging to <?php echo e($user->name); ?> has been
                <?php echo e($status_name); ?> and if cash on delivery - message will be - that you haven't been charged for it if order prepaid - message will be - You will get refund within few business days.
            </p>
            <div class="d-flex justify-content-between">
                <p><b>Payment Method : </b> <?php echo e(strtoupper($order->payment_method)); ?></p>
                <div class="table-responsive">
                    <table>
                        <tbody>
                        <tr>
                            <th>Total Order Price (<?php echo e($currency); ?>)</th>
                            <td><?php echo e($order->total); ?></td>
                        </tr>
                        <tr>
                            <th>Delivery Charge (<?php echo e($currency); ?>)</th>
                            <td><?php echo e($order->delivery_charge); ?></td>
                        </tr>
                        <tr>
                            <th>Discount <?php echo e($currency); ?> (%)</th>
                            <?php
                                $discount_in_rupees = 0;
                                if ( $order->discount > 0) {
                                    $discounted_amount = $order->total * $order->discount / 100;
                                    $final_total = $order->total - $discounted_amount;
                                    $discount_in_rupees = $order->total - $final_total;
                                }
                            ?>
                            <td><?php echo e('- ' . $discount_in_rupees . ' (' . $order->discount . '%)'); ?></td>
                        </tr>
                        <tr>
                            <th>Promo (<?php echo e($order->promo_code); ?>) Discount (<?php echo e($currency); ?>)</th>
                            <td><?php echo e('- ' . $order->promo_discount); ?></td>
                        </tr>
                        <tr>
                            <th>Wallet Used (<?php echo e($currency); ?>)</th>
                            <td><?php echo e('- ' . $order->wallet_balance); ?></td>
                        </tr>
                        <tr>
                            <th>Final Total (<?php echo e($currency); ?>)</th>
                            <td><?php echo e('= ' . ceil($order->final_total)); ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        
        <?php endif; ?>
    <?php else: ?>
        
        <?php if($role == \App\Models\Role::$roleSeller): ?>

            <p style="line-height: 24px; margin-bottom:15px;">
                Dear, <?php echo e($seller->name); ?>

            </p>

            <?php if($order->active_status == \App\Models\OrderStatusList::$received): ?>
                <p style="line-height: 24px; margin-bottom:15px;">
                    You have just received new order, #<?php echo e($order->id); ?>

                </p>
            <?php else: ?>
                <p style="line-height: 24px; margin-bottom:15px;">
                    <?php echo e($subject); ?>

                </p>
            <?php endif; ?>

            <?php
                $redirect_url = (isset($order->id)) ? url('/seller/orders/view/'.$order->id): url('/seller/orders');
            ?>
        <?php elseif($role == \App\Models\Role::$roleDeliveryBoy): ?>

            <p style="line-height: 24px; margin-bottom:15px;">
                Dear, <?php echo e($deliveryBoy->name); ?>

            </p>

            <p style="line-height: 24px; margin-bottom:15px;">
                You have just assigned new order, #<?php echo e($order->id); ?>

            </p>

            <p style="line-height: 24px; margin-bottom:15px;">

                Estimated delivery time is <?php echo e($order->delivery_time); ?>

            </p>

            <p style="line-height: 24px; margin-bottom:15px;">
                <?php echo e($subject); ?>

            </p>

            <?php
                $redirect_url = (isset($order->id)) ? url('/delivery_boy/orders/view/'.$order->id): url('/delivery_boy/orders');
            ?>
        <?php else: ?>

            <p style="line-height: 24px; margin-bottom:15px;">
                Dear, <?php echo e($admin->name); ?>

            </p>

            <?php if($order->active_status == \App\Models\OrderStatusList::$received): ?>
                <p style="line-height: 24px; margin-bottom:15px;">
                    You have just received new order, #<?php echo e($order->id); ?>

                </p>
            <?php else: ?>
                <p style="line-height: 24px; margin-bottom:15px;">
                    <?php echo e($subject); ?>

                </p>
            <?php endif; ?>
            <?php
                $redirect_url = (isset($order->id)) ? url('/orders/view/'.$order->id): url('/orders');
            ?>
        <?php endif; ?>

        <a href="<?php echo e($redirect_url); ?>" style="color: #ffffff; text-decoration: none;">
            <table border="0" align="center" width="180" cellpadding="0" cellspacing="0" bgcolor="#37a279" style="margin-bottom:20px;">
                <tr>
                    <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" style="color: #ffffff; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 22px; letter-spacing: 2px;">
                        <!-- main section button -->
                        <div style="line-height: 22px;">
                            Check it out
                        </div>
                    </td>
                </tr>
                <tr>
                    <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
                </tr>
            </table>
        </a>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH /opt/lampp81/htdocs/projects/eGrocerAdminPanel-1.9.8/resources/views/mail/order_status.blade.php ENDPATH**/ ?>