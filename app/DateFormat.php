<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DateFormat extends Model
{
    public static function convertDate($date)
    {
        $data = explode(',', $date);
        $hari = $data[0];

        switch($hari) {
            case 'Sun':
                $day = "Minggu";
            break;

            case 'Mon':
                $day = "Senin";
            break;

            case 'Tue':
                $day = "Selasa";
            break;

            case 'Wed':
                $day = "Rabu";
            break;

            case 'Thu':
                $day = "Kamis";
            break;

            case 'Fri':
                $day = "Jumat";
            break;

            case 'Sat':
                $day = "Sabtu";
            break;

            default:
                $day = "Tidak di ketahui";
            break;
        }

        $tgl = trim(date('d-m-Y', strtotime($data[1])));
        $bulan = array (
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );
        $pecahkan = explode('-', $tgl);

        // variabel pecahkan 0 = tanggal
        // variabel pecahkan 1 = bulan
        // variabel pecahkan 2 = tahun

        if ($day === "Tidak di ketahui") {
            return $pecahkan[0].' '. $bulan[ (int)$pecahkan[1] ].' '.$pecahkan[2];
        } else {
            return $day.', '.$pecahkan[0].' '. $bulan[ (int)$pecahkan[1] ].' '.$pecahkan[2];
        }
    }
}
