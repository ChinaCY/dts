<?php

namespace skill219
{
	function init() 
	{
		define('MOD_SKILL219_INFO','club;active;hidden;');
	}
	
	function acquire219(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function lost219(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function wpoison(){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','itemmain','logger'));
		if($wepk == 'WN' || !$wepe || !$weps){
			$log .= '<span class="red">你没有装备武器，无法改造！</span><br />';
			$mode = 'command';
			return;
		}
		if (strpos($wepsk,'j')!==false){
			$log.='多重武器不能改造。<br>';
			$mode='command';
			return;
		}
		if(\skillbase\skill_query(219)){
			$position = 0;
			foreach(Array(1,2,3,4,5,6) as $imn){
				global ${'itm'.$imn},${'itmk'.$imn},${'itme'.$imn},${'itms'.$imn},${'itmsk'.$imn};
				if(${'itm'.$imn} == '毒药' && ${'itmk'.$imn} == 'Y' && ${'itme'.$imn} > 0 ){
					$position = $imn;
					break;
				}
			}
			if($position){
				if(strpos($wepsk,'p')!==false){
					$log .= '<span class="red">武器已经带毒，不用改造！</span><br />';
					$mode = 'command';
					return;
				}elseif(strlen($wepsk)>=5){
					$log .= '<span class="red">武器属性数目达到上限，无法改造！</span><br />';
					$mode = 'command';
					return;
				}
				$wepsk .= 'p';
				$log .= "<span class=\"yellow\">用毒药为{$wep}淬毒了，{$wep}增加了带毒属性！</span><br />";
				$wep = '毒性'.$wep;
				${'itms'.$position}-=1;
				$itm = ${'itm'.$position};
				if(${'itms'.$position} == 0){
					$log .= "<span class=\"red\">$itm</span>用光了。<br />";
					${'itm'.$position} = ${'itmk'.$position} = ${'itmsk'.$position} = '';
					${'itme'.$position} =${'itms'.$position} =0;				
				}
				$mode = 'command';
				return;
			}else{
				$log .= '<span class="red">你没有毒药，无法给武器淬毒！</span><br />';
				$mode = 'command';
				return;
			}
		}else{
			$log .= '<span class="red">你没有这个技能！</span><br />';
			$mode = 'command';
			return;
		}
	}
	
	function act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		eval(import_module('sys','player','logger','input'));
	

		if ($mode == 'special' && $command == 'skill219_special' && $subcmd=='wpoison') 
		{
			wpoison();
			return;
		}
			
		$chprocess();
	}
	
}

?>
