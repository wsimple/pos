<?php
require_once ("secure_area.php");
class Inventories_compare extends Secure_area
{

    function __construct()
    {
        parent::__construct('invetories_compare');
    }

    public function index(){
        if ($this->Employee->isAdmin()){
            $this->load->model('reports/Inventory_compare');
            $model = $this->Inventory_compare;
            if (!$model->exist_inventory()){
                $tabular_data = array();
                $report_data = $model->getData();

                foreach($report_data as $row)
                {
                    $tabular_data[] = array($row['item_id'], character_limiter($row['name'], 16), $row['quantity']);
                }

                $data = array(
                    "title" => $this->lang->line('reports_items_summary_report'),
                    "subtitle" => 'Compare items stock '.date("m/d/Y"),
                    "headers" => $model->getDataColumns(),
                    "data" => $tabular_data
                    // "summary_data" => $model->getSummaryData( array('sale_type' => '0') )
                );

                $this->load->view("reports/compare_stock",$data);
            }else{ redirect('home'); }
        }else{ redirect('home'); }
    }

    function send_mail_to_admin(){
        $response = array('status'=>0, 'msg'=>'Error sending to administrator.');
        $obs=$this->input->post('obs');
        $this->load->model('reports/Inventory_compare');
        $model = $this->Inventory_compare;
        $cometarios=$this->cleaned_comments($obs);
        //$response['insert']=$cometarios;
        //$response['insert']=$model->save_inventory($this->cleaned_comments($obs)['like']);
        $response['insert']=$model->save_inventory($obs);
        if ($response['insert']){
            
            if ($cometarios['like']!=''){
                
                $body=$this->html_email($cometarios);
                $this->load->library('email');
                $this->email->initialize();
                $email = $this->Appconfig->get('email');
                $this->email->from($email, 'Fast I Repair');
                $this->email->to($email);

                $this->email->subject('Report Inventory Stock');
                $this->email->message($body);
                // $response['email']=$body;
                if(preg_match('/^(localhost|127\.\d\.\d\.\d|192\.168(\.\d{1,3}){2})/',$_SERVER['SERVER_NAME'])){
                    $response['status'] = 1;
                    $response['msg'] = 'Email successfully sent al administrator!';
                }else{
                    if ($this->email->send()){
                        $response['status'] = 1;
                        $response['msg'] = 'Email successfully sent al administrator!';
                    }
                }
            }else{
                $response['status'] = 1;
                $response['msg'] = 'Email successfully sent al administrator!';
            }
     
            // $email = 'skat0@hotmail.com';
            // $headers = 'From:pos@om-parts.com';
            // $subject = 'A message from Om Parts Inc. Corporate Web';

            // if (mail($email,$subject,$head.$body.$footer, $headers)) {
            //     $response['status'] = 1;
            //     $response['msg'] = 'Email successfully sent al administrator!';
            // }    
        }        
        // $this->load->library('email');
        // $config['protocol'] = 'mail';
        // $config['mailpath'] = '/usr/sbin/sendmail';
        // $config['charset'] = 'iso-8859-1';
        // $config['wordwrap'] = true;
        // $config['mailtype'] = 'html';
        // $config['priority'] = 5;
        // $config['charset'] = 'utf-8';
        // $this->email->initialize($config);
        // echo $this->email->print_debugger();

        die( json_encode($response) );
    }

    function cleaned_comments($string){
        $arra=explode("<li>", $string);
        $arra2 =array();$string='';
        foreach ($arra as $key) {
            if (!trim($key)) continue;
            $key1=explode("-|-", str_replace('</li>','',$key));
            $arra2[trim($key1[0])]=$key1[1];
            $string.=($string!=''?',':'').trim($key1[0]);
        }
        $string=$string!=''?'IN ('.$string.')':'';
        return array('ids'=>$arra2,'like'=>$string);
    }
    function html_email($cometarios){
               // $head = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head><body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0"><center>';
                // $body = '<p>Hola que hace</p>';
        $this->load->model('reports/Inventory_compare');
        $model = $this->Inventory_compare;
        $report_data = $model->getData($cometarios['like']);
        $body='<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head><body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0"><center>
                <div id="table_holder" style="margin-bottom: 0px;">
                <table class="tablesorter report compare-stock" id="sortable_table"><thead>
                    <tr style="text-align: center;">';
        $headers=$model->getDataColumns();
        array_pop($headers);
        foreach ($headers as $header) { 
            switch ($header) {
                case 'Id': $style = ' style=" border-top-left-radius:5px;-webkit-border-top-left-radius:5px; width:5%; padding: 5px" '; 
                break;  
                case 'Item': $style = ' style=" width:55%; padding: 5px" '; 
                break;
                case 'In Stock': $style = ' style=" width:10%; padding: 5px" '; 
                break;
                case 'Comment': $style = ' style=" width:30%; padding: 5px border-top-right-radius:5px;-webkit-border-top-right-radius:5px;" '; 
                break;
            }
            $body.='<th '.$style.' >'. $header.'</th>';
        } 
        $body.='</tr></thead><tbody>';
        foreach ($report_data as $row){ 
            $body.='<tr>
            <td>'.$row['item_id'].'</td>
            <td>'.character_limiter($row['name'], 16).'</td>
            <td>'.$row['quantity'].'</td>
            <td >'.$cometarios['ids'][$row['item_id']].'</td>
            </tr>';
        } 
        $body.='</tbody></table></div></center></body></html>';
        return $body;
    }
}
?>
