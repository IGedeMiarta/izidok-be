<?php

namespace App;

class Constant
{
	#Role
	const INTERNAL_ADMIN = 1;
	const INTERNAL_NONADMIN = 2;
	const KLINIK_ADMIN = 3;
	const KLINIK_OPERATOR = 4;
	const DOKTER = 5;

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
}
