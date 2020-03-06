<style type="text/css">
	html, body{
		margin: 0;
		padding: 0;
	}
	*{
		font-family: 'Courier New', Courier, monospace;
		font-size: 10px;
		line-height: 6px;
	}
    p{
		height: 3px;
		padding: 0px 10px 0px 10px;
	}
    span{
        float: right;
    }
    hr{
        width: 95%
    }
	table{
		border-collapse: collapse;
        width: 100%;
	}
	th, td{
        height: 20px;
		padding: 5px 10px 5px 10px;
	}
	.main{
		width: 90mm;
		height: auto;
		border: 1px solid #eee;
        margin-left: 50px;
        margin-right: auto;
	}
	.medlinx{
        display: block;
        margin-left: auto;
        margin-right: auto;
		max-width: 140px;
		max-height: 70px;
	}
    .header{
        font-weight: bold;
        text-align: center;
        margin-top: 20px;
    }
    .signature{
        text-align: right;
        margin: 30px 0px 0px 0px;
    }
    .footer{
        text-align: center;
        margin: 50px 0px 15px 0px;
    }
    .footer2{
        text-align: center;
        margin-bottom: 30px;
    }
</style>
<html>
  <head>
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Simple Transactional Email</title>
    <style>
    /* -------------------------------------
        INLINED WITH htmlemail.io/inline
    ------------------------------------- */
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

    /* -------------------------------------
        PRESERVE THESE STYLES IN THE HEAD
    ------------------------------------- */
    @media all {
      .ExternalClass {
        width: 100%;
      }
      .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
        line-height: 100%;
      }
      .apple-link a {
        color: inherit !important;
        font-family: inherit !important;
        font-size: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
        text-decoration: none !important;
      }
      #MessageViewBody a {
        color: inherit;
        text-decoration: none;
        font-size: inherit;
        font-family: inherit;
        font-weight: inherit;
        line-height: inherit;
      }
      .btn-primary table td:hover {
        background-color: #34495e !important;
      }
      .btn-primary a:hover {
        background-color: #34495e !important;
        border-color: #34495e !important;
      }
    }
    </style>
  </head>
<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background-color: #f6f6f6;" border="0" cellspacing="0" cellpadding="0">
    <tbody>
    <tr>
    <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
    <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; margin: 0 auto; max-width: 580px; padding: 10px; width: 580px;">
    <div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"><!-- START CENTERED WHITE CONTAINER --> <span class="preheader" style="color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;">This is preheader text. Some clients will show this text as a preview.</span>
    <table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background: #ffffff; border-radius: 3px;"><!-- START MAIN CONTENT AREA -->
    <tbody>
    <tr>
    <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;">
    <p>Hai, {{@$nama_pasien}},</p>
    <br>
    <p>Berikut kami laporkan struk pembayaran transaksi Anda pada tanggal {{@$tanggal}} pukul {{@$jam}} di Tempat Praktek dr. {{@$nama_dokter}},</p>
    <div class="block-grid">
        <br>
        @foreach ($pembayaran as $p)
            <p class="header">Praktek dr. {{$p->nama_dokter}} </p>
            <img class="medlinx" src="https://pngimage.net/wp-content/uploads/2018/06/logo-rumah-sakit-png-2.png" alt="logo-izidok"/>
            <p style="font-weight: bold" align="center">SIP. {{$p->no_sip}}</p>
            <p style="font-weight: bold" align="center">Telp. {{$p->nomor_telp}}</p>
        <br>
            <p style="font-weight: bold">RAWAT JALAN</p>
            <p style="font-weight: bold">PEMBAYARAN</p>
        <br>
            <p>NO. RM : {{$p->nomor_rekam_medis}}</p>
            <p>NO. STRUK : {{$p->no_invoice}}</p>
            <p>{{strtoupper($p->nama_pasien)}}</p>
            <p>PASIEN {{$p->jaminan}}</p>
            <p>Dokter : dr.{{$p->nama_dokter}}</p>
            <p>Created by : {{$p->createdBy->nama}}</p>
        <br>
            <p align="right">CREATED TIME : {{date("d M,Y H:i:s", strtotime($p->created_time))}}</p>
            <p align="right">ADMISSION TIME : {{date("d M,Y H:i:s", strtotime($p->admission_time))}}</p>
            <p align="right">DISCHARGE TIME : {{date("d M,Y H:i:s", strtotime($p->discharge_time))}}</p>
        <hr>
            <p>Nama Layanan</p>
            <p>Qty x Harga Layanan <span>Subtotal Layanan</span></p>
        <hr>
        <table>
            <?php $i = 1; ?>
            @foreach ($detail_pembayaran as $dp)
                <tr>
                    <td><?php echo $i++ ?>. {{$dp->nama_layanan}} - {{$dp->kode_layanan}}</td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;{{$dp->quantity}} x {{number_format($dp->tarif)}}</td>
                    <td align="right">{{number_format($dp->subtotal_tarif)}}</td>
                </tr>
            @endforeach
        </table>
        <hr>
        <table>
            <tr>
                <td>TOTAL LAYANAN</td>
                <td align="right">{{number_format($p->total)}}</td>
            </tr>
            <tr>
                <td>POTONGAN</td>
                <td align="right">{{number_format($p->potongan)}} %</td>
            </tr>
            <tr>
                <td style="font-weight: bold">TOTAL NETT</td>
                <td style="font-weight: bold" align="right">{{number_format($p->total_net)}}</td>
            </tr>
        </table>
        <br>
            <p align="right">{{date("d F Y", strtotime($p->created_time))}} </td>
            <p class="signature">{{$p->createdBy->nama}}</td>
            @endforeach
        <p class="footer">**********</p>
        <p class="footer2">Semoga Lekas Sembuh dan Sehat Selalu</p>
    </div>
    <p align="right">Terima kasih,</p>
    <br>
    <p align="right">Praktek dr. {{$nama_dokter}}</p>

     </td>
    </tr>
    <!-- END MAIN CONTENT AREA --></tbody>
    </table>
    <!-- START FOOTER -->
    <div class="footer" style="clear: both; margin-top: 10px; text-align: center; width: 100%;">&nbsp;</div>
    <!-- END FOOTER --> <!-- END CENTERED WHITE CONTAINER --></div>
    </td>
    <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
    </tr>
    </tbody>
    </table>
