<?php
class SoarHtml {
  
  private $r;
  
  private $config;
  
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
        $val['level'] = $num;
        $analysis[] = $val;
      }
    }
    usort($analysis, function ($a, $b){
      return $b['level'] - $a['level'];
    });
    $this->config['num'] = $total < 0 ? 0 : $total;
    $this->config['explain'] = $explainItem;
    $this->config['analysis'] = $analysis;
  }

  public function asNumHtml() {
    return "<h3>评分：{$this->config['num']}分</h3>";
  }

  public function asExplainHtml() {;
    if ($this->config['explain']) {
      $html = "<p>{$this->config['explain']['Case']}</p>";
      $html = preg_replace('/####(.+?)\n/', '<h4 style="margin:10px;">$1</h4>', $html);
      $html = preg_replace('/###(.+?)\n/', '<h3 style="margin:5px;">$1</h3>', $html);
      $html = preg_replace('/\* (.+?)\n/', '<ul style="margin:0px;">$1</ul>', $html);
      $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
      return $html;
    }
    return '';
  }

  public function asItemHtml() {
    $html = ''; 
    if ($this->config['analysis']) {
      $html .= <<< EOF
<table class="table_results ajax pma_table" data-uniqueid="18066"><thead>
  <tr>
<th class="draggable"><span>Item</span></th>
<th class="draggable" data-column=""><span>Summary</span></th>
<th class="draggable" data-column=""><span>Content</span></th>
<th class="draggable" data-column=""><span>Case</span></th>
<td class="print_ignore"><span></span></td>
</tr></thead><tbody>
EOF;
      foreach ($this->config['analysis'] as $index => $item) {
        $class = ($index % 2) ? 'even' : 'odd';
        $html .= '<tr class="'.$class.'">';
        $html .= "<td data-decimals=\"0\" data-type=\"string\" class=\"data text\"><span>{$item['Item']}</span></td>";
        $html .= "<td data-decimals=\"0\" data-type=\"string\" class=\"data text\"><span>{$item['Summary']}</span></td>";
        $html .= "<td data-decimals=\"0\" data-type=\"string\" class=\"data text\"><span>{$item['Content']}</span></td>";
        $html .= "<td data-decimals=\"0\" data-type=\"string\" class=\"data text\"><span>{$item['Case']}</span></td>";
        $html .= '</tr>';
      }
    }
    $html .= '</tbody></table>';
    return $html;
  }
}

