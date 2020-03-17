<style type="text/css">
	*{
		font-family: 'Cabin', Arial, 'Helvetica Neue', Helvetica, sans-serif;
        font-size: 12px;
		line-height: 6px;
	}
    p{
		height: 3px;
		padding: 0px 10px 0px 10px;
	}
    hr{
        width: 95%
    }
    table.one {
        position:relative;
        float:left;
        margin-bottom: 20px;
    }
    table.two {
        position:relative;
        float:right;
        margin-bottom: 20px;
    }
    table.three{
        width: 100%;
        border: 1px solid black;
        margin-bottom: 20px;
	}
    table.four{
        width: 25%;
        border: 1px solid black;
        float: right;
	}
    td{
        height: 10px;
        padding: 5px 10px 10px 10px;
    }
	.content{
        width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
</style>

<div class="content">
    <table class="one">
        <tr>
            <td>{{$data['dokter']->nama}}</td>
        </tr>
        <tr>
            <td>No. Telepon : {{$data['dokter']->nomor_telp}}</td>
        </tr>
        <tr>
            <td>{{$data['dokter']->email}}</td>
        </tr>
    </table>
    <table class="two">
        <tr>
            <td>No. Invoice : {{$data['detail']->transactionNo}}</td>
        </tr>
        <tr>
            <td>Tanggal Pembelian : {{$data['detail']->transactionDate}}</td>
        </tr>
        <tr>
            <td>Tanggal Maksimal Pembayaran : {{$data['detail']->transactionExpire}}</td>
        </tr>
        <tr>
            <td>Metode Pembayaran : {{$data['paygate']->nama}}</td>
        </tr>
        <tr>
            <td>Status Pembayaran : {{$data['detail']->status_billing}}</td>
        </tr>
    </table>
    <table class="three">
        <thead>
            <tr>
                <th>Nama Paket</th>
                <th>Qty</th>
                <th>Harga Unit</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Paket {{$data['detail']->paket}}</td>
                <td>1</td>
                <td>{{$data['detail']->harga_paket}}</td>
                <td>{{$data['detail']->harga_paket}}</td>
            </tr>
            @if(!is_null($data['detail']->addson))
            <tr>
                <td>1</td>
                <td>Paket {{$data['detail']->addson}}</td>
                <td>Rp{{$data['detail']->harga_addson}}</td>
                <td>Rp{{$data['detail']->harga_addson}}</td>
            </tr>
            @endif
        </tbody>
    </table>
    <table class="four">
        <tr>
            <td>{{$data['detail']->amount_real}}</td>
        </tr>
        <tr>
            <td>{{$data['detail']->diskon ? null : '-'}}</td>
        </tr>
        <tr>
            <td>{{$data['detail']->amount_disc}}</td>
        </tr>
    </table>
</div>

