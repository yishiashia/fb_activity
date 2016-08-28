<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('fb_time_transform')) {
	function fb_time_transform($ctime) {
		if($ctime<60) {
			$ctime = "Just now";
		} else if($ctime<3600) {
			$ctime = floor($ctime/60);
			if($ctime==1) {
				$ctime = "1 min ago";
			} else {
				$ctime = $ctime . " mins ago";
			}
		} else if($ctime<86400) {
			$ctime = floor($ctime/60/60);
			if($ctime==1) {
				$ctime = "1 hour ago";
			} else {
				$ctime = $ctime . " hours ago";
			}
		} else {
			$ctime = floor($ctime/86400);
			if($ctime==1) {
				$ctime = "1 day ago";
			} else {
				$ctime = $ctime . " days ago";
			}
		}
		return $ctime;
	}
}

