<!doctype html>
<html>
    <head>
        <meta name="viewport" content="width=device-width" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Pembatalan Berlangganan</title>
        <style>
        /* -------------------------------------
            GLOBAL RESETS
        ------------------------------------- */
        /*All the styling goes here*/
        img {
            border: none;
            -ms-interpolation-mode: bicubic;
            margin-bottom: 5px;
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
        }

        a {
            color: #3498db;
            text-decoration: underline;
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
                                            <img src="https://api.izidok.id/api/v1/image?path=logo/Logo-izidok-blue.png" alt="logo-izidok" width="100" height="35" style="height: auto;">
                                            <h2>Pembayaran melalui {{$data['pg']->nama}} dibatalkan</h2>
                                            <?php //setlocale(LC_TIME, 'id_ID'); ?>
                                            <p class="bold">Pembayaran Anda telah dibatalkan</p>
                                            <p class="bold" style="color:red">Mohon untuk tidak membayar pembelian ini</p>
                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td class="bold">Total Pembayaran</td>
                                                    <td class="bold">Batas Waktu Pembayaran</td>
                                                </tr>
                                                <tr>
                                                    <td>Rp. {{number_format($data['pl']->transactionAmount,0,',','.')}},-</td>
                                                    <td>{{App\DateFormat::ConvertDate(strftime("%a, %d %b %Y, %H:%M:%S", strtotime($data['pl']->transactionExpire)))}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Metode Pembayaran</td>
                                                    <td class="bold">Kode Pembayaran/No. Virtual Account</td>
                                                </tr>
                                                <tr>
                                                    <td>{{$data['pg']->nama}}</td>
                                                    <td>{{$data['pl']->customerAccount}}</td>
                                                </tr>
                                            </table>
                                            <br>
                                            <p class="bold">Keterangan</p>
                                            <p>Pembayaran dibatalkan oleh {{$data['user']}} pada tanggal {{App\DateFormat::ConvertDate(strftime("%a, %d %b %Y, %H:%M:%S", strtotime($data['now'])))}}</p>
                                            <hr>
                                            <p class="bold">Detail Pembelian :</p>
                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td>No. Tagihan : <span style="color:blue">{{$data['pl']->transactionNo}}</span></td>
                                                </tr>
                                                <tr>
                                                    <td>Paket {{$data['paket']->nama.' ('.$data['bill']->paket_bln.' bulan)'}}</td>
                                                    <td class="align-right">Rp. {{number_format($data['bill']->amount_real,0,'','.')}},-</td>
                                                </tr>
                                                @if(!is_null($data['addson']))
                                                <tr>
                                                    <td>Paket {{$data['addson']->nama}}</td>
                                                    <td class="align-right">Rp. {{number_format($data['addson']->harga,0,'','.')}},-</td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <td>Potongan</td>
                                                    <td class="align-right">
                                                        @if (!is_null($data['promo']))
                                                            @if ($data['promo']->satuan === 'rupiah')
                                                                {{'Rp. '.number_format($data['promo']->value,0,'','.').',-'}}
                                                            @elseif ($data['promo']->satuan === 'percent')
                                                                {{$data['promo']->value.'%'}}
                                                            @endif
                                                        @else
                                                            {{'-'}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Biaya Admin</td>
                                                    <td class="align-right">Rp. {{number_format($data['pg']->biaya_admin,0,'','.')}},-</td>
                                                </tr>
                                            </table>
                                            <hr>
                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td>Total Pembayaran</td>
                                                <td class="align-right">Rp. {{number_format($data['pl']->transactionAmount,0,'','.')}},-</td>
                                            </tr>
                                            </table>
                                            <hr>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <!-- END MAIN CONTENT AREA -->
                    </table>
                    <!-- END CENTERED WHITE CONTAINER -->
                    <!-- START FOOTER -->
                    <div class="footer">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="content-block">
                                <span>Email dibuat secara otomatis. Mohon untuk tidak mengirimkan balasan ke email ini.</span>
                                <br>customercare@izidok.id, contact center : 021-723-7982
                            </td>
                        </tr>
                    </table>
                    </div>
                    <!-- END FOOTER -->
                </div>
            </td>
            <td>&nbsp;</td>
        </tr>
        </table>
    </body>
</html>
