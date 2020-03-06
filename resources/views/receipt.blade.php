<style type="text/css">
	html, body {
		margin: 0;
		padding: 0;
	}
	*{
		font-family: 'Courier New', Courier, monospace;
		font-size: 10px;
		font-weight: bold;
		line-height: 6px;
	}
	table{
		border-collapse: collapse;
	}
	.main{
		width: 90mm;
		height: auto;
		margin: 10px;
		border: 1px solid #eee;
	}
	.content{
		margin: 0 0px 0 0px;
	}
	.medlinx{
		max-width: 140px;
		max-height: 76px;
	}
	p{
		margin-bottom: -2px;
	}
	td{
		padding: 2px 0px;
	}
</style>
<div class="main">
	<br>
    @foreach ($pembayaran as $p)
        <p align="center">Prakter dr. {{$p->nama_dokter}} </p>
        <p align="center">SIP. {{$p->no_sip}}</p>
        <p align="center">Telp. {{$p->nomor_telp}}</p>
    <br>
        <p>NO. RM : {{$p->nomor_rekam_medis}}</p>
        <p>NO. STRUK : </p>
        <p>{{$p->nama_pasien}}</p>
        <p>PASIEN {{$p->jaminan}}</p>
        <p>Dokter : dr.{{$p->nama_dokter}}</p>
        <p>Created by : {{$p->createdBy->nama}}</p>
    <br>
        <p align="right">CREATED TIME : {{$p->created_time}}</p>
        <p align="right">ADMISSION TIME : </p>
        <p align="right">DISCHARGE TIME : </p>

    <table width="100%">
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
    <table width="100%">
        <tr>
            <td>TOTAL LAYANAN</td>
            <td align="right">{{number_format($p->total)}}</td>
        </tr>
        <tr>
            <td>POTONGAN</td>
            <td align="right">{{number_format($p->potongan)}} %</td>
        </tr>
        <tr>
            <td style="font-weight:bold">TOTAL NETT</td>
            <td align="right">{{number_format($p->total_net)}}</td>
        </tr>
        @endforeach
    </table>
