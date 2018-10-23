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
        $sql = trim(preg_replace('/^explain/i', '', $sql));
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

class SoarHtml {
  
  private $r;
  
  private $config;
  
  private $columns = [
    'Item', 'Level', 'Summary', 'Content', 'Case'
  ];

  public function __construct($arr) {
    $this->r = $arr;
    $this->parseResult();
  }
  
  private function parseResult(){
    $total = 100;
    $explainItem = [];
    $analysis = [];
    foreach ($this->r as $key => $val) {
      $num = intval(str_replace(['L', 'l'], '', $val['Severity']));
      $total -= $num * 5;
      if (strpos($key, 'EXP') !== false) {
        $explainItem = $val;
      } else {
        $val['Level'] = $num;
        $analysis[] = $val;
      }
    }
    usort($analysis, function ($a, $b){
      return $b['Level'] - $a['Level'];
    });
    $this->config['num'] = $total < 0 ? 0 : $total;
    $this->config['explain'] = $explainItem;
    $this->config['analysis'] = $analysis;
  }

  public function asNumHtml() {
    return "<h3 style=\"margin:20px 0px 0px 0px;\">评分：{$this->config['num']}分</h3>";
  }

  public function asExplainHtml() {
    if ($this->config['explain']) {
      $html = $this->config['explain']['Case'];
      $html = preg_replace('/####(.+?)\n/', '<h4 style="margin:5px 20px;">$1</h4>', $html);
      $html = preg_replace('/###(.+?)\n/', '<h3 style="margin:10px 0px;">$1：</h3>', $html);
      $html = preg_replace('/\* (.+?)\n/', '<ul style="margin:0px;">$1</ul>', $html);
      $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
      $html = "<div style=\"margin-bottom:20px;\">{$html}</div>";
      return $html;
    }
    return '';
  }

  public function asItemHtml() {
    $html = ''; 
    if ($this->config['analysis']) {
      $html .= '<h3 style="margin:10px 0px;">SQL建议与优化：</h3>';
      $html .= '<table class="table_results ajax pma_table" data-uniqueid="18066"><thead><tr>';
      foreach ($this->columns as $column) {
        $html .= '<th class="draggable"><span>'.$column.'</span></th>';
      }
      $html .= '<td class="print_ignore"><span></span></td></tr></thead><tbody>';
      foreach ($this->config['analysis'] as $index => $item) {
        $class = ($index % 2) ? 'even' : 'odd';
        $html .= '<tr class="'.$class.'">';
        foreach ($this->columns as $column) {
          $html .= "<td data-decimals=\"0\" data-type=\"string\" class=\"data text\"><span>{$item[$column]}</span></td>";
        }
        $html .= '</tr>';
      }
    }
    $html .= '</tbody></table>';
    return $html;
  }
}

if (defined('PMA_VERSION')) {
    $db = $GLOBALS['db'];
    global $cfg;
    $host = $cfg['Server']['host'];
    $user = $cfg['Server']['user'];
    $pwd = $cfg['Server']['password'];
    $port = $cfg['Server']['port'] ?: '3306';
    $dsn = "{$user}:{$pwd}@{$host}:{$port}/$db";
    $soar = new Soar(['test-dsn' => $dsn]);
    $GLOBALS['soar'] = $soar;
    
    function get_analyzed_sql($sqlParser){
      $sql = '';
      foreach ($sqlParser->tokens as $val) {
        if (!is_null($val)) $sql .= $val->token;
      }
      return $sql;
    }
}
