<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

define('IN_GAME', TRUE);
define('GAME_ROOT', '');
$server_config =  './include/modules/core/sys/config/server.config.php';
include $server_config;
include './include/global.func.php';
check_authority();
$db = init_dbstuff();
//$db->query("ALTER TABLE {$gtablepre}rooms ADD `roomtype` tinyint unsigned NOT NULL DEFAULT 0");

//����bra.install.sql����ǰ׺��Ϊalter_�����½���
$install_db = file_get_contents('./install/bra.install.sql');
$install_db = str_replace('bra_', 'alter_', $install_db);
runquery($install_db);

//��ȡ�������ݱ���
$alter_tables = array();
$result = $db->query("SHOW TABLES LIKE 'alter_%';");
while($rarr = $db->fetch_array($result)){
	$table = str_replace('alter_','',current($rarr));
	$alter_tables[] = $table;
	//winners���и�����Ҫ����
	if($table == 'winners') $alter_tables[] = 'swinners';
}

foreach($alter_tables as $at){
	//��ȡ���б�ļ�¼
	//$db->select_db('acdts0');
	$result = $db->query("SELECT * FROM {$gtablepre}{$at} WHERE 1");
	$data = array();
	while($rarr = $db->fetch_array($result)){
		$data[] = $rarr;
	}
	//$db->select_db('acdts');
	if(!empty($data)){
		//���汸�ã����������ɾ������Ȼ��й¶��Ϣ�Ŀ�����
		$dir = './gamedata/tmp/backup';
		if(!file_exists($dir)) mymkdir($dir);
		$file = $dir.'/'.$at.'.bak';
		writeover($file,gencode($data));
		//ɾ��ԭ����alter��ĸ�ʽ�����±�
		$data_a = col_filter("alter_{$at}", $data);
		$db->query("DROP TABLE IF EXISTS {$gtablepre}{$at}");
		if($at == 'swinners'){//winners���и�����Ҫ����
			$db->query("CREATE TABLE {$gtablepre}{$at} LIKE alter_winners");
		}else{
			$db->query("CREATE TABLE {$gtablepre}{$at} LIKE alter_{$at}");
		}
		//��ͷϷ������insert����Ϊ��query��䳬�����Բ���ƴ�ӳ�һ��insert
		$i = 0;
		foreach($data_a as $v){
			$db->array_insert("{$gtablepre}{$at}", $v);
		}
	}	
}

//��������������
foreach($alter_tables as $at){
	$db->query("DROP TABLE IF EXISTS alter_{$at}");
}
//$result = $db->query("DESCRIBE {$gtablepre}players");
echo 'done';

function col_filter($objtable, $data){
	global $db;
	if(strpos($objtable,'swinners')!==false) $objtable = str_replace('swinners','winners',$objtable);
	$result = $db->query("DESCRIBE $objtable");
	$fields = array();
	while ($rarr = $db->fetch_array($result))
	{
		$fields[] = $rarr['Field'];
	}
	$del_fields = array();
	foreach(array_keys($data[0]) as $k0){
		if(!in_array($k0,$fields)) $del_fields[] = $k0;
	}
	foreach($data as &$v){
		foreach($del_fields as $dv){
			if(isset($v[$dv])) unset($v[$dv]);
		}
	}
	return $data;
}

function runquery($sql) {
	global $lang, $dbcharset, $gtablepre, $db;

	$sql = str_replace("\r", "\n", str_replace('bra_', $gtablepre, $sql));
	$ret = array();
	$num = 0;
	foreach(explode(";\n", trim($sql)) as $query) {
		$queries = explode("\n", trim($query));
		foreach($queries as $query) {
			$ret[$num] .= $query[0] == '#' || $query[0].$query[1] == '--' ? '' : $query;
		}
		$num++;
	}
	unset($sql);

	foreach($ret as $query) {
		$query = trim($query);
		if($query) {
			if(substr($query, 0, 12) == 'CREATE TABLE') {
				//$name = preg_replace("/CREATE TABLE `*([a-z0-9_]+)`*\s*\(.*/is", "\\1", $query);
				//echo $lang['create_table'].' '.$name.' ... <font color="#0000EE">'.$lang['succeed'].'</font><br>';
				$db->query(createtable($query, $dbcharset));
			} else {
				$db->query($query);
			}
		}
	}
}

function createtable($sql, $dbcharset) {
	$type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
	$type = in_array($type, array('MYISAM', 'HEAP')) ? $type : 'MYISAM';
	return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql)." ENGINE=$type DEFAULT CHARSET=$dbcharset";
}