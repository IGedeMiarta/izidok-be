<style type="text/css">
    .clearfix:after {
        content: "";
        display: table;
        clear: both;
    }

    body {
        position: relative;
        width: 21cm;
        height: 29.7cm;
        margin: 0 auto;
        font-family: Arial, sans-serif;
        font-size: 12px;
    }

    img {
        padding: 5px 0 40px 0;
    }

    h2.name {
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 30px;
    }

    #details {
        margin-bottom: 50px;
    }

    #left-top {
        float: left;
        padding: 28px 0 0 6px;
    }

    #right-top {
        float: right;
        padding-right: 86px;
    }

    div.status {
        vertical-align: middle;
    }

    div.lunas {
        display:inline-block;
        width: 200px;
        padding: 5px 0 5px 0;
        margin-top: 10px;
        border: 1px solid green;
        box-sizing: border-box;
        color: green;
        font-size: 14px;
        font-weight: bold;
        text-align: center;
    }

    table {
        width: 90%;
        border:none;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 20px;
    }

    table th {
        background-color: #0080ff;
    }

    table th{
        border:none;
        padding: 10px;
        text-align: right;
    }

    table td {
        border-bottom: 1px solid gray;
        padding: 10px;
        text-align: right;
    }

    table .desc {
        width: 30%;
        text-align: left;
    }

    table .qty {
        width: 20%;
        text-align: center;
    }

    table .unit,
    table .total {
        width: 25%;
    }

    table tfoot td {
        padding: 10px 10px;
    }

    table tfoot tr:first-child td {
        border-top: none;
    }

    table tfoot tr:last-child td {
        border-top: none;
    }

    table tfoot tr td:first-child {
        border: none;
    }

    footer {
        width: 90%;
        height: 10px;
        position: absolute;
        bottom: 0;
        padding-right: 30px;
        text-align: right;
    }
</style>
<!DOCTYPE html>
<html lang="en">
    <body>
    <main>
        <div id="details" class="clearfix">
            <div id="left-top">
                <img src="{{asset('upload/images/logo-izidok.png')}}" alt="logo-izidok" width="15%"/>
                <div>{{$data['dokter']->nama}}</div>
                <div>No. Handphone : {{$data['dokter']->nomor_telp}}</div>
                <div>{{$data['dokter']->email}}</div>
            </div>
            <div id="right-top">
                <?php setlocale(LC_TIME, 'id_ID'); ?>
                <h2 class="name">INVOICE</h2>
                <div>No. Invoice : {{$data['detail']->transactionNo}}</div>
                <div>Tanggal Pembelian : {{strftime("%d %B %Y", strtotime($data['detail']->transactionDate))}}</div>
                <div>Tanggal Maksimal Pembayaran : {{strftime("%d %B %Y %H:%M:%S", strtotime($data['detail']->transactionExpire))}}</div>
                <div>Metode Pembayaran : {{$data['paygate']->nama}}</div>
                <div class="status"> Status Pembayaran : <div class="lunas">{{$data['detail']->status_billing}}</div></div>
            </div>
        </div>
        <table border="0" cellspacing="0" cellpadding="0">
            <thead>
            <tr>
                <th class="desc">Nama Paket</th>
                <th class="qty">QTY</th>
                <th>Harga Unit</th>
                <th>Jumlah (Rp)</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="desc">Paket {{$data['detail']->paket.'-'.$data['detail']->durasi_paket.' ('.$data['detail']->durasi_paket.' bulan)'}}</td>
                <td class="qty">1</td>
                <td class="unit">Rp. {{number_format($data['detail']->harga_paket,0,'','.')}},-</td>
                <td class="total">Rp. {{number_format($data['detail']->amount_real,0,'','.')}},-</td>
            </tr>
            @if(!is_null($data['detail']->addson))
            <tr>
                <td class="desc">Paket {{$data['detail']->addson}}</td>
                <td class="qty">1</td>
                <td class="unit">Rp. {{number_format($data['detail']->harga_addson,0,'','.')}},-</td>
                <td class="total">Rp. {{number_format($data['detail']->harga_addson,0,'','.')}},-</td>
            </tr>
            @endif
            <tfoot>
                <tr>
                    <td colspan="2"></td>
                    <td>Subtotal (Rp)</td>
                    <td>Rp. {{number_format($data['detail']->amount_real,0,'','.')}},-</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Potongan</td>
                    <td>
                        @if ($data['detail']->satuan_promo == 'rupiah')
                            {{'Rp. '.number_format($data['detail']->diskon,0,'','.').',-'}}
                        @elseif ($data['detail']->satuan_promo == 'percent')
                            {{$data['detail']->diskon.'%'}}
                        @else
                            {{'-'}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Biaya Admin</td>
                    <td>Rp. {{number_format($data['paygate']->biaya_admin,0,'','.')}},-</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Total</td>
                    <td>Rp. {{number_format($data['detail']->transactionAmount,0,'','.')}},-</td>
                </tr>
              </tfoot>
        </table>
    </main>
        <footer>
        customercare@medlinx.co.id, contact center : 021-723-7982
        </footer>
    </body>
</html>
