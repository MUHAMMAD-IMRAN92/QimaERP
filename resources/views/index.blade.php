<style>
    body {
        padding: 0;
        margin: 0;
    }

    html {
        -webkit-text-size-adjust: none;
        -ms-text-size-adjust: none;
    }

    @media only screen and (max-device-width: 680px),
    only screen and (max-width: 680px) {
        *[class="table_width_100"] {
            width: 96% !important;
        }

        *[class="border-right_mob"] {
            border-right: 1px solid #dddddd;
        }

        *[class="mob_100"] {
            width: 100% !important;
        }

        *[class="mob_center"] {
            text-align: center !important;
        }

        *[class="mob_center_bl"] {
            float: none !important;
            display: block !important;
            margin: 0px auto;
        }

        .iage_footer a {
            text-decoration: none;
            color: #929ca8;
        }

        img.mob_display_none {
            width: 0px !important;
            height: 0px !important;
            display: none !important;
        }

        img.mob_width_50 {
            width: 40% !important;
            height: auto !important;
        }
    }

    .table_width_100 {
        width: 680px;
    }

    .button {
        background-color: #4CAF50;
        /* Green */
        border: none;

        padding: 16px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        transition-duration: 0.4s;
        cursor: pointer;
    }

    .button1 {

        color: black;
        border: 2px solid #4CAF50;
    }

    .button1:hover {
        background-color: #4CAF50;
        color: white;
    }

</style>

<!--
Responsive Email Template by @keenthemes
A component of Metronic Theme - #1 Selling Bootstrap 3 Admin Theme in Themeforest: http://j.mp/metronictheme
Licensed under MIT
-->

<div id="mailsub" class="notification" align="center">
    <!--<div align="center">
       <img src="http://talmanagency.com/wp-content/uploads/2014/12/cropped-logo-new.png" width="250" alt="Metronic" border="0"  /> 
    </div> -->
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="min-width: 320px;">
        <tr>
            <td align="center" bgcolor="#eff3f8">


                <!--[if gte mso 10]>
<table width="680" border="0" cellspacing="0" cellpadding="0">
<tr><td>
<![endif]-->

                <table border="0" cellspacing="0" cellpadding="0" class="table_width_100" width="100%"
                    style="max-width: 680px; min-width: 300px;">
                    <tr>
                        <td>
                            <!-- padding -->
                        </td>
                    </tr>
                    <!--header -->
                    <tr>
                        <td align="center" bgcolor="#ffffff">
                            <!-- padding -->
                            <table width="90%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center">
                                        <a href="#" target="_blank"
                                            style="color: #596167; font-family: Arial, Helvetica, sans-serif; float:left; width:100%; padding:20px;text-align:center; font-size: 13px;">

                                            <h1>QIMA COFFEE</h1>

                                        </a>
                                    </td>
                                    <td align="right">
                                </tr>
                                <td align="center" bgcolor="#fbfcfd">

                                    <table width="90%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td>
                                                Dear Admin,<br />
                                                It is stated that i have forgot my login Password.
                                                So, I'm unable to login and continoue to work.Kindly reset my login
                                                password.So, i can continue.<br /><br>
                                                Name : {{ $test->first_name . $test->last_name }}<br /><br>
                                                Email : {{ $test->email }}<br /><br>
                                                Organization : Qima Coffee<br />

                                            </td>
                                        </tr>
                                        <br><br>
                                        <tr>
                                            <td align="center">
                                                <div style="line-height: 24px;">
                                                    <button class="button button1"> <a
                                                            style="color: white ; text-decoration:none"
                                                            href="{{ url('admin/reset_view/' . $test->user_id) }}">Reset
                                                            Password</a></button>
                                                </div>

                                            </td>
                                        </tr>

                                    </table>

                                </td>
                    </tr>

                    <tr>
                        <td class="iage_footer" align="center" bgcolor="#ffffff">


                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" style="padding:20px;flaot:left;width:100%; text-align:center;">
                                        <font face="Arial, Helvetica, sans-serif" size="3" color="#96a5b5"
                                            style="font-size: 13px;">
                                            <span
                                                style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #96a5b5;">
                                                2015 © QIMA-COFFEE. ALL Rights Reserved.
                                            </span>
                                        </font>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>


            </td>
        </tr>
    </table>
