<?php

namespace App;

class Constant
{
	#Role
	// const INTERNAL_ADMIN = 1; #super admin (medlink)
	// const KLINIK_ADMIN = 2; #admin klinik
	// const KLINIK_OPERATOR = 3; #operator
	// const KLINIK_OWNER = 4; #dokter praktek
	// const DOKTER = 5; #dokter

	const SUPER_ADMIN = 'super_admin';
	const ADMIN_KLINIK = 'admin_klinik';
	const OPERATOR = 'operator';
	const DOKTER_PRAKTEK = 'dokter_praktek';
	const DOKTER_KLINIK = 'dokter_klinik';

	#Tipe Klinik
	const TIPE_KLINIK = 1;
	const DOKTER_PRAKTIK = 2;

	#Status Transaksi Klinik
	// const QUEUED = 'QUEUED';
	// const REKAM_MEDIS = 'REKAM_MEDIS';
	// const CANCELLED = 'CANCELLED';
	// const COMPLETED = 'COMPLETED';

	const TRX_MENUNGGU = 'MENUNGGU';
	const TRX_KONSULTASI = 'KONSULTASI';
	const TRX_BATAL = 'BATAL';
	const TRX_SELESAI = 'SELESAI';


	#redirection
	const REDIRECTION = 'url_redirection';
	const FORGOT_VALID = 'forgot_valid';
	const FORGOT_INVALID = 'forgot_invalid';

	const ACT_OPT_VALID = 'act_opt_valid';
	const ACT_OPT_INVALID = 'act_opt_invalid';

	const ACTIVATION_SUCCESS = 'activation_success';
    const ACTIVATION_FAILED = 'activation_failed';
    const ACTIVATION_EXPIRED = 'activation_expired';
	const ALREADY_ACTIVATED = 'already_activated';
	const VERIFY_EMAIL = 'verify_email';

	#path folder draw image
	const FOLDER_PEMERIKSAAN = 'pemeriksaan';
	const FOLDER_DIAGNOSA = 'diagnosa';

	#dashboard
	const MIGGUAN = 'mingguan';
	const BULANAN = 'bulanan';
	const TAHUNAN = 'tahunan';
	const DATE_RANGE = 'date_range';
	const SUM_PASIEN = 'pasien';
	const SUM_RAWAT_JALAN = 'rawat_jalan';
	const ANTREAN = 'antrean';
	const SUM_PENDAPATAN = 'pendapatan';

	#template email
	const ACTIVATION_EMAIL_TEMPLATE = 'email-activation';
	const OPERATOR_EMAIL_TEMPLATE = 'operator-activation';
	const FORGOT_EMAIL_TEMPLATE = 'forgot-password';

	#for nomor rekam medis
	const KATEGORI_UMUM = "10";
	const KATEGORI_GIGI = "20";
	const TIPE_FASKES_DOKTER_PRAKTIK = "10";
	const TIPE_FASKES_KLINIK = "20";

	#status pembayaran
	const DRAFT = 'DRAFT';
	const BELUM_LUNAS = 'BELUM LUNAS';
    const LUNAS = 'LUNAS';

    #pembayaran
    const EMAIL_RECEIPT = 'email-receipt';

	#subscribe
	const PAYMENT_CONFIRMATION = 'payment-confirmation';
	const CANCEL_SUBSCRIBE = 'cancel-subscribe';
}
