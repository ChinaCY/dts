::��2��������Ҫ�Լ���
set php="D:\phpStudy\php\php-7.0.12-nts\php.exe"
set wget="D:\Program Files (x86)\GnuWin32\bin\wget.exe"
::��������ַ
%php% -r "error_reporting(0); include './include/modules/core/sys/config/server.config.php'; echo $server_address;" > daemon.temp
set /p sv=<daemon.temp
::��ȡ��������
%php% -r "error_reporting(0); include './include/modulemng.config.php'; echo $___MOD_CONN_PASSWD;" > daemon.temp
set /p pw=<daemon.temp
pause
exit

:luanch_new_daemon
::Ҫ��һ��������Ϊ1��ʾ��root daemon
if "%1" == "1" (
	del /q .\gamedata\tmp\server\request_new_root_server
	set extr="&is_root=1"
) else (
	del /q .\gamedata\tmp\server\request_new_server
	set extr=""
)
GOTO :EOF