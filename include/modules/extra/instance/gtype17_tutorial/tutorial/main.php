<?php

namespace tutorial
{
	function init() {}
	
	function get_tutorial(){//�̳�ר�õ���ʾ�����Ը�skill1000�ҹ���
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','skill1000','tutorial'));
		$step = \skillbase\skill_getvalue(1000,'step',$pa);
		$tsetting = $tutorialsetting[$step];
		if(!empty($tsetting['pulse'])) {
			$chasschg[$tsetting['pulse']] = $tsetting['pulseclass'];
		}
		$r = Array(
			$tsetting['tips'],
			$tsetting['allowed']
		);
		return $r;
	}
	
//	function act()	
//	{
//		if (eval(__MAGIC__)) return $___RET_VALUE;
//		
//		eval(import_module('sys','player','input'));
//		$chprocess();
//		if ($gametype == 17) {
//			$mode = 'tutorial';
//		}
//		return;
//	}
	
//	function post_act()
//	{
//		if (eval(__MAGIC__)) return $___RET_VALUE;
//		eval(import_module('sys','player','input'));
//		if ($gametype == 17) {
//			$mode = 'tutorial';
//		}
//		return;
//	}
}

?>