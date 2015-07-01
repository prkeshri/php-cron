<?php
/**
	Schedule and remove CRON entries from the local System
**/
class Cron
{
	/**
		Set Timezone from the system!
	**/
	public static function SetTimeZone()
	{
		$timezone=file_get_contents('/etc/timezone');
		$timezone=explode(PHP_EOL, $timezone)[0];
		if($timezone) date_default_timezone_set($timezone);
	}
	/**
		Set a cron job to run After 3 minutes from now and daily at that time.
	**/
	public static function SetNowDaily($sline,$min=NULL)
	{
		static::SetTimeZone();
		if($min===NULL) $min=3;###
		$t=time(NULL)+($min*60);# AFTER 3 minutes
		$m=date('i',$t);
		$m=intval($m);
		$h=intval(date('G',$t));
		$cline="$m $h * * * $sline"; 
		return static::Set($cline);
	}
	/**
		Set a cron job.
	**/
	public static function Set($cline)
	{
		$c=shell_exec('crontab -l');
		$c.=$cline;
		$c.=PHP_EOL;
		file_put_contents('/tmp/crontab.txt', $c);
		exec('crontab /tmp/crontab.txt');
	}
	/**
		Remove a cron job.
	**/
	public static function Remove($search)
	{
		$c=(shell_exec('crontab -l'));
		$ls=explode(PHP_EOL, $c);
		$o='';
		foreach ($ls as $l) {
			if(strpos($l, $search)===FALSE) $o.=$l.PHP_EOL;
		}
		file_put_contents('/tmp/crontab.txt', $o);
		exec('crontab /tmp/crontab.txt');
	}
	/**
		Replace a cron job.
	**/
	public static function Replace($search,$cline)
	{
		$c=(shell_exec('crontab -l'));
		$ls=explode(PHP_EOL, $c);
		$o='';
		foreach ($ls as $l) {
			if(strpos($l, $search)===FALSE) $o.=PHP_EOL.$l;
		}
		$o.=$cline;
		$o.=PHP_EOL;
		file_put_contents('/tmp/crontab.txt', $o);
		exec('crontab /tmp/crontab.txt');
	}
}