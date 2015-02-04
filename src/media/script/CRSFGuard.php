<?php
namespace media\script ;

class CRSFGuard {
	public static function store_in_session($key,$value)
	{
		if (isset($_SESSION))
		{
			$_SESSION[$key]=$value;
		}
	}
	public static function unset_session($key)
	{
		$_SESSION[$key]=' ';
		unset($_SESSION[$key]);
	}
	public static function get_from_session($key)
	{
		if (isset($_SESSION[$key]))
		{
			return $_SESSION[$key];
		}
		else {  return false; }
	}
	public static function csrfguard_generate_token($unique_form_name)
	{
		if (function_exists("hash_algos") and in_array("sha512",hash_algos()))
		{
			$token=hash("sha512",mt_rand(0,mt_getrandmax()));
		}
		else
		{
			$token=' ';
			for ($i=0;$i<128;++$i)
			{
				$r=mt_rand(0,35);
				if ($r<26)
				{
					$c=chr(ord('a')+$r);
				}
				else
				{ 
					$c=chr(ord('0')+$r-26);
				} 
				$token.=$c;
			}
		}
		self::store_in_session($unique_form_name,$token);
		return $token;
	}

	public static function csrfguard_validate_token($unique_form_name,$token_value)
	{
		$token=self::get_from_session($unique_form_name);
		if ($token===false)
		{
			return false;
		}
		elseif ($token===$token_value)
		{
			$result=true;
		}
		else
		{ 
			$result=false;
		} 
		self::unset_session($unique_form_name);
		return $result;
	}

	public static function csrfguard_replace_forms($form_data_html)
	{
		$count=preg_match_all("/<form(.*?)>(.*?)<\\/form>/is",$form_data_html,$matches,PREG_SET_ORDER);
		if (is_array($matches))
		{
			foreach ($matches as $m)
			{
				if (strpos($m[1],"nocsrf")!==false) { continue; }
				$name="CSRFGuard_".mt_rand(0,mt_getrandmax());
				$token=self::csrfguard_generate_token($name);
				$form_data_html=str_replace($m[0],
					"<form{$m[1]}>
					<input type='hidden' name='CSRFName' value='{$name}' />
					<input type='hidden' name='CSRFToken' value='{$token}' />{$m[2]}</form>",$form_data_html);
			}
		}
		return $form_data_html;
	}

}