<!DOCTYPE html>
<!--
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 4 & Angular 8
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
Renew Support: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<html lang="en" >
    <!-- begin::Head -->

    <!-- Mirrored from keenthemes.com/metronic/preview/demo6/custom/pages/user/login-3.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 02 Oct 2019 11:54:34 GMT -->
    <!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
    <head>
        <meta charset="utf-8"/>

        <title>QIMA</title>
        <meta name="description" content="Login page example">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!--begin::Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700|Roboto:300,400,500,600,700">        <!--end::Fonts -->


        <!--begin::Page Custom Styles(used by this page) -->
        <link rel="stylesheet" href="{{asset('admin_assets/dist/assets/css/pages/login/login-3.css')}}">

        <!--end::Page Custom Styles -->

        <!--begin::Global Theme Styles(used by all pages) -->
        <link rel="stylesheet" href="{{asset('admin_assets/dist/assets/plugins/global/plugins.bundle.css')}}">
        <link rel="stylesheet" href="{{asset('admin_assets/dist/assets/css/style.bundle.css')}}">
        <!--end::Global Theme Styles -->

        <!--begin::Layout Skins(used by all pages) -->
        <!--end::Layout Skins -->
        <link rel="shortcut icon" href="{{url('/admin_assets/favicon.png')}}" type="image/x-icon">
        <!--        <link rel="shortcut icon" href="https://keenthemes.com/metronic/themes/metronic/theme/default/demo6/dist/assets/media/logos/favicon.ico" />-->

        <!-- Hotjar Tracking Code for keenthemes.com -->
        <script>
            (function (h, o, t, j, a, r) {
                h.hj = h.hj || function () {
                    (h.hj.q = h.hj.q || []).push(arguments)
                };
                h._hjSettings = {hjid: 1070954, hjsv: 6};
                a = o.getElementsByTagName('head')[0];
                r = o.createElement('script');
                r.async = 1;
                r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
                a.appendChild(r);
            })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');
        </script>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-37564768-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', 'UA-37564768-1');
        </script>    </head>
    <!-- end::Head -->

    <!-- begin::Body -->
    <body  class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-aside--minimize kt-page--loading"  >


        <!-- begin:: Page -->
        <div class="kt-grid kt-grid--ver kt-grid--root kt-page">
            <div class="kt-grid kt-grid--hor kt-grid--root  kt-login kt-login--v3 kt-login--signin" id="kt_login">
                <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" style="background-image: url(../../../../../themes/metronic/theme/default/demo6/dist/assets/media/bg/bg-3.jpg);">
                    <div class="kt-grid__item kt-grid__item--fluid kt-login__wrapper">


                        @yield('content')




                    </div>
                </div>
            </div>	
        </div>

        <!-- end:: Page -->


        <!-- begin::Global Config(global config for global JS sciprts) -->
        <script>
            var KTAppOptions = {"colors": {"state": {"brand": "#22b9ff", "light": "#ffffff", "dark": "#282a3c", "primary": "#5867dd", "success": "#34bfa3", "info": "#36a3f7", "warning": "#ffb822", "danger": "#fd3995"}, "base": {"label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"], "shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]}}};
        </script>
        <!-- end::Global Config -->

        <!--begin::Global Theme Bundle(used by all pages) -->
        <script src="../../../../../themes/metronic/theme/default/demo6/dist/assets/plugins/global/plugins.bundle.js" type="text/javascript"></script>
        <script src="../../../../../themes/metronic/theme/default/demo6/dist/assets/js/scripts.bundle.js" type="text/javascript"></script>
        <!--end::Global Theme Bundle -->


        <!--begin::Page Scripts(used by this page) -->
        <script src="../../../../../themes/metronic/theme/default/demo6/dist/assets/js/pages/custom/login/login-general.js" type="text/javascript"></script>
        <!--end::Page Scripts -->
    </body>
</html>
