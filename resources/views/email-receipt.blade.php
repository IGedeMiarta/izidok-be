<meta name="viewport" content="width=device-width, initial-scale=1">

<div class="content" style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; width: 600px; margin-left: auto; margin-right: auto;">
	<p class="normal" style="font-family: 'Courier New', Courier, monospace; height: 3px; padding: 0px 10px 0px 10px; font-size: 14px; line-height: normal;">Hai, {{@$nama_pasien}}</p>
	<br style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px;">
	<p class="normal" style="font-family: 'Courier New', Courier, monospace; height: 3px; padding: 0px 10px 0px 10px; font-size: 14px; line-height: normal;">Berikut kami lampirkan struk pembayaran transaksi Anda pada tanggal {{@$tanggal2}} pukul {{@$jam}} di Tempat Praktek dr. {{@$nama_dokter}},</p>
	<div class="main" style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; width: 90mm; height: auto; border: 1px solid #eee; margin-top: 50px; margin-right: auto; margin-left: auto;">
		<br style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px;">
		@foreach ($pembayaran as $p)
			<p class="header" style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 3px; padding: 0px 10px 0px 10px; font-weight: bold; text-align: center; margin-top: 20px;">Praktek dr. {{$p->nama_dokter}} </p>
			<img class="medlinx" src="https://pngimage.net/wp-content/uploads/2018/06/logo-rumah-sakit-png-2.png" alt="logo-izidok" style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; display: block; margin-left: auto; margin-right: auto; max-width: 140px; max-height: 70px;">
			<p style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 3px; padding: 0px 10px 0px 10px; font-weight: bold;" align="center">SIP. {{$p->no_sip}}</p>
			<p style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 3px; padding: 0px 10px 0px 10px; font-weight: bold;" align="center">Telp. {{$p->nomor_telp}}</p>
		<br style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px;">
			<p style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 3px; padding: 0px 10px 0px 10px; font-weight: bold;">RAWAT JALAN</p>
			<p style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 3px; padding: 0px 10px 0px 10px; font-weight: bold;">PEMBAYARAN</p>
		<br style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px;">
			<p style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 3px; padding: 0px 10px 0px 10px;">NO. RM : {{$p->nomor_rekam_medis}}</p>
			<p style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 3px; padding: 0px 10px 0px 10px;">NO. STRUK : {{$p->no_invoice}}</p>
			<p style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 3px; padding: 0px 10px 0px 10px;">{{strtoupper($p->nama_pasien)}}</p>
			<p style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 3px; padding: 0px 10px 0px 10px;">PASIEN {{$p->jaminan}}</p>
			<p style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 3px; padding: 0px 10px 0px 10px;">Dokter : dr.{{$p->nama_dokter}}</p>
			<p style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 3px; padding: 0px 10px 0px 10px;">Created by : {{Auth::user()->nama}}</p>
		<br style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px;">
			<p align="right" style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 3px; padding: 0px 10px 0px 10px;">CREATED TIME : {{date("d M,Y H:i:s", strtotime($p->created_time))}}</p>
			<p align="right" style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 3px; padding: 0px 10px 0px 10px;">ADMISSION TIME : {{date("d M,Y H:i:s", strtotime($p->admission_time))}}</p>
			<p align="right" style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 3px; padding: 0px 10px 0px 10px;">DISCHARGE TIME : {{date("d M,Y H:i:s", strtotime($p->discharge_time))}}</p>
		<hr style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; width: 95%;">
			<p style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 3px; padding: 0px 10px 0px 10px;">Nama Layanan</p>
			<p style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 3px; padding: 0px 10px 0px 10px;">Qty x Harga Layanan <span style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; float: right;">Subtotal Layanan</span></p>
		<hr style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; width: 95%;">
		<table style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; border-collapse: collapse; width: 100%;" width="100%">
			<?php $i = 1; ?>
			@foreach ($detail_pembayaran as $dp)
				<tr style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px;">
					<td style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 10px; padding: 5px 10px 5px 10px;" height="10"><?php echo $i++ ?>. {{$dp->nama_layanan}} - {{$dp->kode_layanan}}</td>
				</tr>
				<tr style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px;">
					<td style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 10px; padding: 5px 10px 5px 10px;" height="10">&nbsp;&nbsp;&nbsp;{{$dp->quantity}} x {{number_format($dp->tarif)}}</td>
					<td align="right" style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 10px; padding: 5px 10px 5px 10px;" height="10">{{number_format($dp->subtotal_tarif)}}</td>
				</tr>
			@endforeach
		</table>
		<hr style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; width: 95%;">
		<table style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; border-collapse: collapse; width: 100%;" width="100%">
			<tr style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px;">
				<td style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 10px; padding: 5px 10px 5px 10px;" height="10">TOTAL LAYANAN</td>
				<td align="right" style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 10px; padding: 5px 10px 5px 10px;" height="10">{{number_format($p->total)}}</td>
			</tr>
			<tr style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px;">
				<td style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 10px; padding: 5px 10px 5px 10px;" height="10">POTONGAN</td>
				<td align="right" style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 10px; padding: 5px 10px 5px 10px;" height="10">{{number_format($p->potongan)}} %</td>
			</tr>
			<tr style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px;">
				<td style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 10px; padding: 5px 10px 5px 10px; font-weight: bold;" height="10">TOTAL NETT</td>
				<td style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 10px; padding: 5px 10px 5px 10px; font-weight: bold;" align="right" height="10">{{number_format($p->total_net)}}</td>
			</tr>
		</table>
		<br style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px;">
			<p align="right" style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 3px; padding: 0px 10px 0px 10px;">{{date("d F Y", strtotime($p->created_time))}}
			</p><p class="signature" style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 3px; padding: 0px 10px 0px 10px; text-align: right; margin: 30px 0px 0px 0px;">{{Auth::user()->nama}}
			@endforeach
		</p><p class="footer1" style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 3px; padding: 0px 10px 0px 10px; text-align: center; margin: 50px 0px 15px 0px;">**********</p>
		<p class="footer2" style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px; height: 3px; padding: 0px 10px 0px 10px; text-align: center; margin-bottom: 30px;">Semoga Lekas Sembuh dan Sehat Selalu</p>
	</div>
	<p class="normal" align="right" style="font-family: 'Courier New', Courier, monospace; height: 3px; padding: 0px 10px 0px 10px; font-size: 14px; line-height: normal;">Terima kasih,</p>
	<br style="font-family: 'Courier New', Courier, monospace; font-size: 10px; line-height: 6px;">
	<p class="normal" align="right" style="font-family: 'Courier New', Courier, monospace; height: 3px; padding: 0px 10px 0px 10px; font-size: 14px; line-height: normal;">Praktek dr. {{$nama_dokter}}</p>
</div>
