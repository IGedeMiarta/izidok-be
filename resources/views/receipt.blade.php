<style type="text/css">
	html, body{
		margin: 0;
		padding: 0;
	}
	*{
		font-family: 'Courier New', Courier, monospace;
		font-size: 12px;
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

<div class="main">
	<br>
    @foreach ($pembayaran as $p)
        <p class="header">Praktek {{$p->nama_dokter}} </p>
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
        <p>Dokter : {{$p->nama_dokter}}</p>
        <p>Created by : {{ Auth::user()->nama }}</p>
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
            <td>DISKON</td>
            <td align="right">{{number_format($p->potongan)}} %</td>
        </tr>
        <tr>
            <td style="font-weight: bold">TOTAL NETT</td>
            <td style="font-weight: bold" align="right">{{number_format($p->total_net)}}</td>
        </tr>
    </table>
    <br>
        <p align="right">{{App\DateFormat::ConvertDate(strftime("%A, %d %b %Y", strtotime($p->created_time)))}}</td>
        <p class="signature">{{Auth::user()->nama}}</td>
        @endforeach
    <p class="footer">**********</p>
    <p class="footer2">Semoga Lekas Sembuh dan Sehat Selalu</p>
</div>
