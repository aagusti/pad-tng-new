<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//author: irul 
/*
pad_pemda_nama
pad_pemda_type
pad_pemda_kepala
pad_pemda_jabatan
pad_pemda_ibukota
pad_pemda_unitid

pad_reklame_id
pad_air_tanah_id
pad_dok_self_id
pad_dok_office_id

pad_spt_date
pad_spt_due_date

pad_spt_denda
pad_bunga

pad_tahun_anggaran ?
pad_format_npwpd
pad_to_decimal

wp_login
wp_id
wp_nm

hit_denda
*/

if (!function_exists('hit_denda')) {
    // by irfani.firdausy.com
    function hit_denda($tagihan, $pdenda, $jthtmpotgl, $prosestgl)
    {
        $jthtmpotgl = strtotime($jthtmpotgl);
        $prosestgl  = strtotime($prosestgl);
        $x   = (date('Y', $prosestgl) - date('Y', $jthtmpotgl)) * 12;
        $y   = (date('m', $prosestgl) - date('m', $jthtmpotgl));
        $z   = $x + $y + 1;
        
        if (date('d', $prosestgl) <= date('d', $jthtmpotgl))
            $z = $z - 1;
        if ($z < 1)
            $z = 0;
        if ($z > 24)
            $z = 24;
        
        $denda  = round($z * $pdenda / 100 * $tagihan);
        
        $ret = new stdClass;
        $ret->bulan = $z;
        $ret->denda = $denda;
        return $ret;
    }
}

// wp_login
if ( ! function_exists('wp_login'))
{
    function wp_login()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('wp_login');
		return  $ret;
    }
}

// wp_id
if ( ! function_exists('wp_id'))
{
    function wp_id()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('wp_id');
		return  $ret;
    }
}

// wp_nm
if ( ! function_exists('wp_nm'))
{
    function wp_nm()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('wp_nm');
		return  $ret;
    }
}

// pad_to_decimal
if ( ! function_exists('pad_to_decimal'))
{
    function pad_to_decimal($str_val, $ret_val = NULL)
    {
        $val = $str_val;
        $val = str_replace(".", "", $val);
        $val = str_replace(",", ".", $val);
        return $val != '' ? $val : (!empty($ret_val) ? $ret_val : 0);
    }
}
	
// pad_format_npwpd
if ( ! function_exists('pad_format_npwpd'))
{
	function pad_format_npwpd($npwpd) {
		$npwpd_out = substr($npwpd, 0, 1).'.'.substr($npwpd, 1, 1).'.'.substr($npwpd, 2, 7).'.'.substr($npwpd, 9, 2).'.'.substr($npwpd, 11, 2);
		return $npwpd_out;
	}
}
	
// pad_tahun_anggaran
if ( ! function_exists('pad_tahun_anggaran'))
{
    function pad_tahun_anggaran()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_tahun_anggaran');
		return  $ret;
    }
}

// pad_pemda_daerah
if ( ! function_exists('pad_pemda_daerah'))
{
    function pad_pemda_daerah()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_pemda_daerah');
		return  $ret;
    }
}

// pad_pemda_alamat
if ( ! function_exists('pad_pemda_alamat'))
{
    function pad_pemda_alamat()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_pemda_alamat');
		return  $ret;
    }
}

// pad_pemda_alamat_lengkap
if ( ! function_exists('pad_pemda_alamat_lengkap'))
{
    function pad_pemda_alamat_lengkap()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_pemda_alamat_lengkap');
		return  $ret;
    }
}

// pad_pemda_telp
if ( ! function_exists('pad_pemda_telp'))
{
    function pad_pemda_telp()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_pemda_telp');
		return  $ret;
    }
}

// pad_pemda_fax
if ( ! function_exists('pad_pemda_fax'))
{
    function pad_pemda_fax()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_pemda_fax');
		return  $ret;
    }
}

// pad_pemda_website
if ( ! function_exists('pad_pemda_website'))
{
    function pad_pemda_website()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_pemda_website');
		return  $ret;
    }
}

// pad_pemda_email
if ( ! function_exists('pad_pemda_email'))
{
    function pad_pemda_email()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_pemda_email');
		return  $ret;
    }
}

// pad_pemda_nama
if ( ! function_exists('pad_pemda_nama'))
{
    function pad_pemda_nama()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_pemda_nama');
		return  $ret;
    }
}

// pad_pemda_singkatan
if ( ! function_exists('pad_pemda_singkatan'))
{
    function pad_pemda_singkatan()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_pemda_singkatan');
		return  $ret;
    }
}

// pad_pemda_type
if ( ! function_exists('pad_pemda_type'))
{
    function pad_pemda_type()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_pemda_type');
		return  $ret;
    }
}

// pad_pemda_kepala
if ( ! function_exists('pad_pemda_kepala'))
{
    function pad_pemda_kepala()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_pemda_kepala');
		return  $ret;
    }
}

// pad_pemda_jabatan
if ( ! function_exists('pad_pemda_jabatan'))
{
    function pad_pemda_jabatan()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_pemda_jabatan');
		return  $ret;
    }
}

// pad_pemda_ibukota
if ( ! function_exists('pad_pemda_ibukota'))
{
    function pad_pemda_ibukota()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_pemda_ibukota');
		return  $ret;
    }
}

// pad_pemda_unitid
if ( ! function_exists('pad_pemda_unitid'))
{
    function pad_pemda_unitid()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_pemda_unitid');
		return  $ret;
    }
}

// pad_reklame_id
if ( ! function_exists('pad_reklame_id'))
{
    function pad_reklame_id()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_reklame_id');
		return  $ret;
    }
}

// pad_air_tanah_id
if ( ! function_exists('pad_air_tanah_id'))
{
    function pad_air_tanah_id()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_air_tanah_id');
		return  $ret;
    }
}

// pad_dok_self_id
if ( ! function_exists('pad_dok_self_id'))
{
    function pad_dok_self_id()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_dok_self_id');
		return  $ret;
    }
}

// pad_dok_office_id
if ( ! function_exists('pad_dok_office_id'))
{
    function pad_dok_office_id()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_dok_office_id');
		return  $ret;
    }
}

// pad_spt_date
if ( ! function_exists('pad_spt_date'))
{
    function pad_spt_date()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_spt_date');
		return  $ret;
    }
}

// pad_spt_due_date
if ( ! function_exists('pad_spt_due_date'))
{
    function pad_spt_due_date()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_spt_due_date');
		return  $ret;
    }
}

// pad_spt_denda
if ( ! function_exists('pad_spt_denda'))
{
    function pad_spt_denda()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_spt_denda');
		return  $ret;
    }
}

// pad_bunga
if ( ! function_exists('pad_bunga'))
{
    function pad_bunga()
    {
		$CI  =& get_instance();
		$ret =  $CI->session->userdata('pad_bunga');
		return  $ret;
    }
}

if ( ! function_exists('pad_bunga'))
{
    function map_jns_pajak($kode){
        if ($kode==6) $kode=7; //Parkir
        elseif ($kode==7) $kode=8; //Air Bawah Tanah
        return $kode;
    }
}

if ( ! function_exists('pad_bunga'))
{
    function map_jns_skp($kode){
      $kode=(int)$kode;
      if ($kode==1) $kode=2; //SELF->SPTPD     
      elseif ($kode==2 || $kode==3) $kode=3; //SKPD     SKPD
      elseif ($kode==5 || $kode==6) $kode=4; //SKPDKB
      elseif ($kode==7) $kode=9; //SKPDKBT
      elseif ($kode==8) $kode=10; //SKPDLB
      return $kode;
      /*  3 | SKPD J   |     |       0 |             |            |            |
          4 | SKPD N   |     |       0 |             |            |            |
          6 | SKPDKB J |     |       0 |             |            |            |
      */
    }
}

if ( ! function_exists('pad_bunga'))
{
    function get_rekening_dotted($kode)
    {
        $str = substr($kode,0,1).'.'.substr($kode,1,1).'.'.substr($kode,2,1).'.'.substr($kode,3,2).'.'.substr($kode,5,2);
        return $str;
    }
}    