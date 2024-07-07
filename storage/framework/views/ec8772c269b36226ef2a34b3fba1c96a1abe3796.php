<?php
    $logo="";
    $app_name="";
    $support_email="";
    $support_number="";
    $google_place_api_key = "";
    $currency = "";
    $purchase_code = "";
    $logo_full_path = "";
    $delivery_boy_bonus_settings = 0;
    $isDemoMode = 0;

    $website_url = "";
    $copyright_details = "";



    // Firebase keys
    $apiKey = "";
    $authDomain = "";
    $projectId = "";
    $storageBucket = "";
    $messagingSenderId = "";
    $appId = "";
    $measurementId = "";


    if(isInstalled()){
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

        $logo = \App\Models\Setting::get_value('logo') ?? "";
        if($logo!==""){
            $logo_full_path =  url('/').'/storage/'.$logo;
        }else{
            $logo_full_path =  asset('images/favicon.png');
        }

        $google_place_api_key = \App\Models\Setting::get_value('google_place_api_key') ?? "";
        $currency = \App\Models\Setting::get_value('currency') ?? "$";
        $purchase_code = \App\Models\Setting::get_value('purchase_code') ?? "";

        $website_url = \App\Models\Setting::get_value('website_url') ?? "";
        $copyright_details = \App\Models\Setting::get_value('copyright_details') ?? "";

        $delivery_boy_bonus_settings = \App\Models\Setting::get_value('delivery_boy_bonus_settings') ?? 0;

        // Firebase keys
        $apiKey = \App\Models\Setting::get_value('apiKey') ?? "";
        $authDomain = \App\Models\Setting::get_value('authDomain') ?? "";
        $projectId = \App\Models\Setting::get_value('projectId') ?? "";
        $storageBucket = \App\Models\Setting::get_value('storageBucket') ?? "";
        $messagingSenderId = \App\Models\Setting::get_value('messagingSenderId') ?? "";
        $appId = \App\Models\Setting::get_value('appId') ?? "";
        $measurementId = \App\Models\Setting::get_value('measurementId') ?? "";
        $isDemoMode = isDemoMode() ?? 0;
    }
?>

<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="<?php echo e(app()->isLocale('ar') ? 'rtl' : ''); ?>"> 
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />

    <title><?php echo e($app_name??'eGrocer'); ?></title>
    <link rel="shortcut icon" href="<?php echo e($logo_full_path); ?>">

<!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/js/tinymce/content.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/js/tinymce/skin.min.css')); ?>">
   
<!--    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">-->

    <link rel="stylesheet" href="<?php echo e(asset('assets/vendors/iconly/bold.css')); ?>">

    <link rel="stylesheet" href="<?php echo e(asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendors/bootstrap-icons/bootstrap-icons.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/app.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/boostrap_vue.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/pages/form-element-select.css')); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- Auth -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/pages/auth.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/pages/error.css')); ?>">
    <!-- Styles -->

    <link rel="stylesheet" href="<?php echo e(asset('assets/css/custom/common.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/dark-mode/app-dark.css')); ?>">
  
    <?php if(isDemoMode()): ?>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-CZZ7MV8RBB"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'G-CZZ7MV8RBB');
        </script>
    <?php endif; ?>

</head>
<body >

<div id="app">
    <router-view></router-view>
</div>

<!--You can comment this or remove these 3 lines so popup update will stop-->
<?php if(auth()->user() && auth()->user()->role_id==1): ?>
    <?php echo $__env->make('vendor.laraupdater.notification', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="<?php echo e(asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/mazer.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/extensions/form-element-select.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce/tinymce.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce/theme.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce/model.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce/icons.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce/anchor/plugin.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce/autolink/plugin.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce/charmap/plugin.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce/codesample/plugin.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce/emoticons/plugin.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce/image/plugin.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce/link/plugin.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce/lists/plugin.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce/media/plugin.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce/searchreplace/plugin.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce/table/plugin.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce/visualblocks/plugin.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce/wordcount/plugin.min.js')); ?>"></script>

<script>
    window.baseUrl = '<?php echo e(url('/')); ?>';
    window.appName = "<?php echo e($app_name); ?>";
    window.supportEmail = "<?php echo e($support_email); ?>";
    window.supportNumber = "<?php echo e($support_number); ?>";
    window.MapApiKey = "<?php echo e($google_place_api_key); ?>";
    window.appLogo = "<?php echo e($logo); ?>";
    window.currency = "<?php echo e($currency); ?>";
    window.isInstalled = "<?php echo e(isInstalled()); ?>";
    window.purchase_code = "<?php echo e($purchase_code); ?>";

    window.websiteUrl = "<?php echo e($website_url); ?>";
    window.copyrightDetails = "<?php echo e($copyright_details); ?>";


    window.deliveryBoyBonusSettings = "<?php echo e($delivery_boy_bonus_settings); ?>";
    window.isDemo = "<?php echo e($isDemoMode); ?>";
    window.currentVersion = "<?php echo e(currentVersion()); ?>";

    <?php if(auth()->guard()->check()): ?>
    /* Login*/
    window.UserPermissions = <?php echo json_encode(Auth::user()->allPermissions, true); ?>;
    window.Role = "<?php echo Auth::user()->role->name; ?>";
    <?php else: ?>
    /* Not Login*/
    window.UserPermissions = [];
    window.Role = '';
    <?php endif; ?>
</script>
<script src="<?php echo e(asset('js/app.js')); ?>"></script>





<?php if(isInstalled()): ?>

<?php endif; ?>
<!--Web Push-->
<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase.js"></script>

<?php

    $lang = config('app.locale');
    $lang = $lang ?? 'en';
    // \Log::info("lang file ".$lang);
    $file =  file_get_contents(resource_path('lang/' . $lang . '.json'));

?>

<!--php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear-->

<script>

    let lang = JSON.stringify(<?php  echo $file; ?>);
    localStorage.setItem('language', lang);


    <?php if($apiKey!='' && $authDomain!='' && $authDomain!='' && $projectId!='' && $storageBucket!='' && $messagingSenderId!='' && $appId!='' && $measurementId!=''): ?>

        var firebaseConfig = {
            apiKey: "<?php echo e($apiKey); ?>",
            authDomain: "<?php echo e($authDomain); ?>",
            projectId: "<?php echo e($projectId); ?>",
            storageBucket: "<?php echo e($storageBucket); ?>",
            messagingSenderId: "<?php echo e($messagingSenderId); ?>",
            appId: "<?php echo e($appId); ?>",
            measurementId: "<?php echo e($measurementId); ?>"
        };

        var firebaseCheck =  firebase.initializeApp(firebaseConfig);

        /*console.log("option => ", firebaseCheck.options.messagingSenderId);
        console.log("firebaseCheck => ", firebaseCheck);*/

        if ('Notification' in window) {

            if (firebase.messaging.isSupported()) {
                console.log("Notification 2");
                const messaging = firebase.messaging();
                startFCM();

                function startFCM() {
                    messaging
                        .requestPermission()
                        .then(function () {
                            return messaging.getToken()
                        })
                        .then(function (response) {
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });
                            $.ajax({
                                url: '<?php echo e(route("fcmToken")); ?>',
                                type: 'POST',
                                data: {
                                    token: response
                                },
                                dataType: 'JSON',
                                success: function (response) {
                                    //alert('Token stored.');
                                },
                                error: function (error) {
                                    //alert(error);
                                },
                            });
                        }).catch(function (error) {
                        //alert(error);
                    });
                }

                messaging.onMessage(function (payload) {
                    //console.log("onMessage : ",payload);
                    //const notification = payload.notification;
                    const notification = JSON.parse(payload.data.data);

                    //prevent notification on other tabs
                    if (document.hidden) {
                        return false;
                    }

                    const title = notification.title;
                    const options = {
                        body: notification.body,
                        icon: notification.icon,
                    };
                    new Notification(title, options);
                });
            }
        }

    <?php endif; ?>
</script>

</body>
</html>
<?php /**PATH /opt/lampp81/htdocs/projects/eGrocerAdminPanel-1.9.8/resources/views/welcome.blade.php ENDPATH**/ ?>