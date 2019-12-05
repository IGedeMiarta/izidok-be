<?php

namespace App;

class Constant
{
	#Role
	const INTERNAL_ADMIN = 1; #super admin (medlink)
	const KLINIK_ADMIN = 2; #admin klinik
	const KLINIK_OPERATOR = 3; #operator
	const KLINIK_OWNER = 4; #dokter praktek
	const DOKTER = 5; #dokter

	#Tipe Klinik
	const TIPE_KLINIK = 1;
	const DOKTER_PRAKTIK = 2;

	#Status Transaksi Klinik
	const QUEUED = 'QUEUED';
	const REKAM_MEDIS = 'REKAM_MEDIS';
	const CANCELLED = 'CANCELLED';
	const COMPLETED = 'COMPLETED';

	#redirection
	const REDIRECTION = 'url_redirection';
	const FORGOT_VALID = 'forgot_valid';
	const FORGOT_INVALID = 'forgot_invalid';

	const ACT_OPT_VALID = 'act_opt_valid';
	const ACT_OPT_INVALID = 'act_opt_invalid';

	const ACTIVATION_SUCCESS = 'activation_success';
	const ACTIVATION_FAILED = 'activation_failed';
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

	#template email
	const ACTIVATION_EMAIL_TEMPLATE = 'email-activation';
	const OPERATOR_EMAIL_TEMPLATE = 'operator-activation';
	const FORGOT_EMAIL_TEMPLATE = 'forgot-password';
}
