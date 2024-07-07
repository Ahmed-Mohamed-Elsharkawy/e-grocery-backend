<?php
    $app_name = \App\Models\Setting::get_value('app_name');
    if($app_name == "" || $app_name == null){
        $app_name = "eGrocer";
    }
    $support_email = \App\Models\Setting::get_value('support_email');
    if($support_email == "" || $support_email == null){
        $support_email = "";
    }
    $support_number = \App\Models\Setting::get_value('support_number');
    if($support_number == "" || $support_number == null){
        $support_number = "";
    }
    $logo = \App\Models\Setting::get_value('logo');
    if($logo!==""):
        $logo_full_path =  url('/').'/storage/'.$logo;
    else:
        $logo_full_path =  asset('images/favicon.png');
    endif
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns:v="urn:schemas-microsoft-com:vml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
    <link href='https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700' rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Quicksand:300,400,700' rel="stylesheet">
    <title><?php echo e($app_name); ?></title>
    <link rel="shortcut icon" href="<?php echo e($logo_full_path); ?>">
    <style type="text/css">
        body {
            width: 100%;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            mso-margin-top-alt: 0px;
            mso-margin-bottom-alt: 0px;
            mso-padding-alt: 0px 0px 0px 0px;
        }
        p,
        h1,
        h2,
        h3,
        h4 {
            margin-top: 0;
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;
        }

        span.preheader {
            display: none;
            font-size: 1px;
        }

        html {
            width: 100%;
        }

        table {
            font-size: 14px;
            border: 0;
        }
        /* ----------- responsivity ----------- */

        @media  only screen and (max-width: 640px) {
            /*------ top header ------ */
            .main-header {
                font-size: 20px !important;
            }
            .main-section-header {
                font-size: 28px !important;
            }
            .show {
                display: block !important;
            }
            .hide {
                display: none !important;
            }
            .align-center {
                text-align: center !important;
            }
            .no-bg {
                background: none !important;
            }
            /*----- main image -------*/
            .main-image img {
                width: 440px !important;
                height: auto !important;
            }
            /* ====== divider ====== */
            .divider img {
                width: 440px !important;
            }
            /*-------- container --------*/
            .container590 {
                width: 440px !important;
            }
            .container580 {
                width: 400px !important;
            }
            .main-button {
                width: 220px !important;
            }
            /*-------- secions ----------*/
            .section-img img {
                width: 320px !important;
                height: auto !important;
            }
            .team-img img {
                width: 100% !important;
                height: auto !important;
            }
        }

        @media  only screen and (max-width: 479px) {
            /*------ top header ------ */
            .main-header {
                font-size: 18px !important;
            }
            .main-section-header {
                font-size: 26px !important;
            }
            /* ====== divider ====== */
            .divider img {
                width: 280px !important;
            }
            /*-------- container --------*/
            .container590 {
                width: 280px !important;
            }
            .container590 {
                width: 280px !important;
            }
            .container580 {
                width: 260px !important;
            }
            /*-------- secions ----------*/
            .section-img img {
                width: 280px !important;
                height: auto !important;
            }
        }
    </style>
</head>


<body class="respond" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<!-- pre-header -->
<table style="display:none!important;">
    <tr>
        <td>
            <div style="overflow:hidden;display:none;font-size:1px;color:#ffffff;line-height:1px;font-family:Arial;maxheight:0px;max-width:0px;opacity:0;">
                Welcome to <?php echo e($app_name ?? \App\Models\Setting::get_value('app_name')); ?>!
            </div>
        </td>
    </tr>
</table>
<!-- pre-header end -->

<!-- header -->
<table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff">
    <tr>
        <td align="center">
            <table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590">
                <tr>
                    <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>
                </tr>
                <tr>
                    <td align="center">
                        <table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590">
                            <tr>
                                <td align="left" height="70" style="height:70px;">
                                    <a href="<?php echo e(url('/')); ?>" style="display: block; border-style: none !important; border: 0 !important;">
                                        <img width="100" border="0" style="display: block; width: 100px;" src="<?php echo e($logo_full_path); ?>" alt="" />
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<!-- end header -->

<!-- big image section -->
<table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff" class="bg_color">

    <tr>
        <td align="center">
            <table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590">

                <tr>
                    <td align="left" style="color: #343434; font-size: 24px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 3px; line-height: 35px;"
                        class="main-header">
                        <!-- section text ======-->

                        <div style="line-height: 35px">

                            Welcome to the <span style="color: #37a279;"><?php echo e($app_name); ?></span>

                        </div>
                    </td>
                </tr>

                <tr>
                    <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
                </tr>

                <tr>
                    <td align="center">
                        <table border="0" width="40" align="center" cellpadding="0" cellspacing="0" bgcolor="eeeeee">
                            <tr>
                                <td height="2" style="font-size: 2px; line-height: 2px;">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
                </tr>

                <tr>
                    <td align="left">
                        <table border="0" width="590" align="center" cellpadding="0" cellspacing="0" class="container590">
                            <tr>
                                <td align="left" style="color: #888888; font-size: 16px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px;">
                                    <!-- section text ======-->


                                    <?php echo $__env->yieldContent('content'); ?>


<!--                                    <p style="line-height: 24px; margin-bottom:15px;">
                                        Firstname,
                                    </p>
                                    <p style="line-height: 24px;margin-bottom:15px;">
                                        Great news, you will now be the first to see exclusive previews of our latest collections, hear about news from the Abacus!
                                        community and get the most up to date news in the world of fashion.
                                    </p>
                                    <p style="line-height: 24px; margin-bottom:20px;">
                                        You can access your account at any point using the link below.
                                    </p>-->






                                    <br><br><br>
                                    <p style="line-height: 24px">
                                        Thanks & Regards,<br>
                                         <?php echo e($app_name); ?>

                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td height="40" style="font-size: 40px; line-height: 40px;">&nbsp;</td>
    </tr>

</table>
<!-- end section -->

<!-- contact section -->

<!-- end section -->

<!-- footer ====== -->
<table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="f4f4f4">
    <tr>
        <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>
    </tr>
    <tr>
        <td align="center">
            <table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590">
                <tr>
                    <td>
                        <table border="0" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="container590">
                            <tr>
                                <td align="left" style="color: #aaaaaa; font-size: 14px; font-family: 'Work Sans', Calibri, sans-serif; line-height: 24px;">
                                    <div style="line-height: 24px;">
                                        <span style="color: #333333;">
                                            @ 2022 <?php echo e($app_name); ?>. All Right Reserved
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </table>


                    </td>
                </tr>

            </table>
        </td>
    </tr>
    <tr>
        <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>
    </tr>
</table>
<!-- end footer ====== -->



</body>
</html>
<?php /**PATH /opt/lampp81/htdocs/projects/eGrocerAdminPanel-1.9.8/resources/views/layouts/mail.blade.php ENDPATH**/ ?>