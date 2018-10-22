<?php
class Soar {
	
	private $config = [
		'report-type' => 'json',
		'allow-online-as-test' => 'true'
	];
	private $cmd = '';
	
	public function __construct($config = []){
        $this->config($config);
		$this->cmd = DIRECTORY_SEPARATOR == '\\' ? __DIR__ . '/bin/soar.windows-amd64.exe' : __DIR__ . '/bin/soar.linux-amd64';
		$this->cmd .= ' ' . $this->buildConfig();
	}
    
    public function config($config){
		$this->config = array_merge($this->config, $config);
    }

    private function buildConfig(){
		$options = [];
		foreach ($this->config as $key=>$val) {
			$options[] = "-{$key}={$val}";
		}
		return implode(' ', $options);
	}
	
	public function analysis($sql){
		$f=proc_open($this->cmd,[
			['pipe', 'r'],['pipe','w'],['pipe','w']
		 ],$pipes);
		fwrite($pipes[0], $sql);
		fclose($pipes[0]);
        
        if (DIRECTORY_SEPARATOR == '\\') fgets($pipes[1]);
		$data = stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		return $this->config['report-type'] == 'json' ? json_decode($data, true) : $data;
	}
}

if (defined('PMA_VERSION')) {
  include_once __DIR__ . '/phpmyadmin/4.6.x/html.php';
  global $cfg;
  $db = $_REQUEST['db'];
  $host = $cfg['Server']['host'];
  $user = $cfg['Server']['user'];
  $pwd = $cfg['Server']['password'];
  $port = $cfg['Server']['port'] ?: '3306';
  $dsn = "{$user}:{$pwd}@{$host}:{$port}/$db";
  $soar = new Soar(['test-dsn' => $dsn]);
  $query = trim($_REQUEST['sql_query']);
  $query = trim(preg_replace('/^explain/i', '', $query));
  $r = $soar->analysis($query);
  return new SoarHtml(array_values($r)[0]);
}
