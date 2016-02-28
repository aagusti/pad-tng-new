<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//author: irul
if ( ! function_exists('msg_block'))
{
    function msg_block()
    {
		$CI =& get_instance();
		
		if($CI->session->flashdata('msg_error')) {
			$ret = '<div id="msg_helper" class="alert alert-error">';
			$ret.= '<button type="button" class="close" data-dismiss="alert">&times;</button>';
			$ret.= $CI->session->flashdata('msg_error');
			$ret.= '</div>';
			
			return $ret;
		}
		
		if($CI->session->flashdata('msg_warning')) {
			$ret = '<div id="msg_helper" class="alert alert-error">';
			$ret.= '<button type="button" class="close" data-dismiss="alert">&times;</button>';
			$ret.= $CI->session->flashdata('msg_warning');
			$ret.= '</div>';
			
			return $ret;
		}
		
		if($CI->session->flashdata('msg_info')) {
			$ret = '<div id="msg_helper" class="alert alert-info">';
			$ret.= '<button type="button" class="close" data-dismiss="alert">&times;</button>';
			$ret.= $CI->session->flashdata('msg_info');
			$ret.= '</div>';
			
			return $ret;
		}
				
		if($CI->session->flashdata('msg_success')) {
			$ret = '<div id="msg_helper" class="alert alert-success">';
			$ret.= '<button type="button" class="close" data-dismiss="alert">&times;</button>';
			$ret.= $CI->session->flashdata('msg_success');
			$ret.= '</div>';
			
			return $ret;
		}

		if($CI->session->flashdata('msg_block')) {
			$ret = '<div id="msg_helper" class="alert alert-block">';
			$ret.= '<button type="button" class="close" data-dismiss="alert">&times;</button>';
			$ret.= $CI->session->flashdata('msg_block');
			$ret.= '</div>';
			
			return $ret;
		}
    }   
}