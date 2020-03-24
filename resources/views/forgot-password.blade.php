<!doctype html>
<html>
    <head>
        <meta name="viewport" content="width=device-width" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Lupa Password</title>
        <style>
        /* -------------------------------------
            GLOBAL RESETS
        ------------------------------------- */
        /*All the styling goes here*/
        img {
            border: none;
            -ms-interpolation-mode: bicubic;
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 50%;
        }

        body {
            background-color: #f6f6f6;
            font-family: sans-serif;
            -webkit-font-smoothing: antialiased;
            font-size: 14px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        table {
            border-collapse: separate;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            width: 100%; }
            table td {
            font-family: sans-serif;
            font-size: 14px;
            vertical-align: top;
        }

        /* -------------------------------------
            BODY & CONTAINER
        ------------------------------------- */
        .body {
            background-color: #f6f6f6;
            width: 100%;
        }

        /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
        .container {
            display: block;
            margin: 0 auto !important;
            /* makes it centered */
            max-width: 580px;
            padding: 10px;
            width: 580px;
        }

        /* This should also be a block element, so that it will fill 100% of the .container */
        .content {
            box-sizing: border-box;
            display: block;
            margin: 0 auto;
            max-width: 580px;
            padding: 10px;
        }

        /* -------------------------------------
            HEADER, FOOTER, MAIN
        ------------------------------------- */
        .main {
            background: #ffffff;
            border-radius: 3px;
            width: 100%;
        }

        .wrapper {
            box-sizing: border-box;
            padding: 20px;
        }

        .content-block {
            padding-bottom: 10px;
            padding-top: 10px;
        }

        .footer {
            clear: both;
            margin-top: 10px;
            text-align: center;
            width: 100%;
        }
            .footer td,
            .footer p,
            .footer span,
            .footer a {
            color: #999999;
            font-size: 12px;
            text-align: center;
        }

        /* -------------------------------------
            TYPOGRAPHY
        ------------------------------------- */
        h1,
        h2,
        h3,
        h4 {
            color: #000000;
            font-family: sans-serif;
            font-weight: bold;
            line-height: 1;
            margin: 0;
            margin-bottom: 10px;
        }

        h1 {
            font-size: 35px;
            font-weight: 300;
            text-align: center;
            text-transform: capitalize;
        }

        p,
        ul,
        ol {
            font-family: sans-serif;
            font-size: 14px;
            font-weight: normal;
            margin: 0;
            margin-bottom: 15px;
        }

        p li,
        ul li,
        ol li {
            list-style-position: outside;
            margin-left: 5px;
            mso-special-format: bullet;
        }

        a {
            color: #3498db;
            text-decoration: underline;
        }

        /* -------------------------------------
          BUTTONS
        ------------------------------------- */
        .btn {
            box-sizing: border-box;
            width: 100%; }
        .btn > tbody > tr > td {
            padding-bottom: 15px; }
        .btn table {
            width: auto;
        }
        .btn table td {
            background-color: #ffffff;
            border-radius: 5px;
            text-align: center;
        }
        .btn a {
            background-color: #ffffff;
            border: solid 1px #3498db;
            border-radius: 5px;
            box-sizing: border-box;
            color: #3498db;
            cursor: pointer;
            display: block;
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            padding: 12px 25px;
            text-decoration: none;
            text-transform: capitalize;
            text-align: center;
        }

        .btn-primary table td {
            background-color: #3498db;
        }

        .btn-primary a {
            background-color: #3498db;
            border-color: #3498db;
            color: #ffffff;
        }

        /* -------------------------------------
            OTHER STYLES THAT MIGHT BE USEFUL
        ------------------------------------- */
        .last {
            margin-bottom: 0;
        }

        .first {
            margin-top: 0;
        }

        .align-center {
            text-align: center;
        }

        .align-right {
            text-align: right;
        }

        .align-left {
            text-align: left;
        }

        .clear {
            clear: both;
        }

        .mt0 {
            margin-top: 0;
        }

        .mb0 {
            margin-bottom: 0;
        }

        .bold {
            font-weight: 600;
        }

        .border {
            border: 1px solid gray;
        }

        hr {
            border: 0;
            border-bottom: 1px solid gray;
            margin: 10px 0;
        }

        /* -------------------------------------
            RESPONSIVE AND MOBILE FRIENDLY STYLES
        ------------------------------------- */
        @media only screen and (max-width: 620px) {
            table[class=body] h1 {
                font-size: 28px !important;
                margin-bottom: 10px !important;
            }
            table[class=body] p,
            table[class=body] ul,
            table[class=body] ol,
            table[class=body] td,
            table[class=body] span,
            table[class=body] a {
                font-size: 16px !important;
            }
            table[class=body] .wrapper,
            table[class=body] .article {
                padding: 10px !important;
            }
            table[class=body] .content {
                padding: 0 !important;
            }
            table[class=body] .container {
                padding: 0 !important;
                width: 100% !important;
            }
            table[class=body] .main {
                border-left-width: 0 !important;
                border-radius: 0 !important;
                border-right-width: 0 !important;
            }
            table[class=body] .btn table {
                width: 100% !important;
            }
            table[class=body] .btn a {
                width: 100% !important;
            }
            table[class=body] .img-responsive {
                height: auto !important;
                max-width: 100% !important;
                width: auto !important;
            }
        }
        </style>
    </head>
    <body class="">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
        <tr>
            <td>&nbsp;</td>
            <td class="container">
            <div class="content">
                <!-- START CENTERED WHITE CONTAINER -->
                <table role="presentation" class="main">
                <!-- START MAIN CONTENT AREA -->
                <tr>
                    <td class="wrapper">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                        <td>
                            <table width="300" border="0" align="center" cellpadding="0" cellspacing="0" ">
                                <tr>
                                    <td align="center" valign="top">
                                        <img src="https://beta-api.izidok.id/api/v1/image?path=logo/Logo-izidok-blue.png" alt="logo-izidok" width="200" height=""/></a>
                                    </td>
                                </tr>
                            </table>
                            <br>
                            <h2>Hai {{@$name}},</h2>
                            <p>Mohon klik link berikut untuk mereset password Anda!</p>
                            <br>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="btn btn-primary" style="box-sizing: border-box; width: 100%; background-color: #3498db; border-radius: 5px; text-align: center;">
                                        <a style="background-color: #3498db; border: solid 1px #3498db; border-radius: 5px; box-sizing: border-box; color: #ffffff; cursor: pointer; display: block; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-decoration: none; text-transform: capitalize; text-align: center;" href="{{@$activation_url}}" target="_blank">Reset Password</a>
                                    </td>
                                </tr>
                            </table>
                            <hr><br>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="word-break:break-all">Jika Anda kesulitan mengklik tombol 'Reset Password', copy dan paste URL <br>di bawah ke dalam browser Anda:</td>
                                </tr>
                                <tr>
                                    <td style="word-break:break-all">{{@$activation_url}}</td>
                                </tr>
                            </table>
                            <br><br>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>Terima kasih,</td>
                                </tr>
                                <tr>
                                    <td>Tim izidok</td>
                                </tr>
                            </table>
                        </td>
                        </tr>
                    </table>
                    </td>
                </tr>
                <!-- END MAIN CONTENT AREA -->
                </table>
                <!-- END CENTERED WHITE CONTAINER -->
            </div>
            </td>
            <td>&nbsp;</td>
        </tr>
        </table>
    </body>
</html>
