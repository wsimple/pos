<?php
require_once ("secure_area.php");
class Share_inventories extends Secure_area
{
    
    private $status = array(
        'Canceled'   => 0,
        'Pending'    => 1,
        'Incomplete' => 2,
        'Received'   => 3
    );

    function __construct()
    {
        parent::__construct('share_inventories');
        $this->load->library('receiving_lib');
    }

    public function index(){
        $this->load->model('Share_inventory');
        $model = $this->Share_inventory;
        $tabular_data = array();
        $report_data = $model->getData();

        foreach($report_data as $row)
        {
            $tabular_data[] = array($row['item_id'], character_limiter($row['name'], 16), $row['quantity']);
        }

        $data = array(
            "title"           => $this->lang->line('share_items'),
            "subtitle"        => 'Send Items to others Locations',
            "headers"         => $model->getDataColumns(),
            "data"            => $tabular_data,
            'controller_name' => strtolower(get_class()),
            'session'=>$this->session->userdata('items_shipping')
        );

        $this->load->view("items/share_inventory",$data);
    }

    public function suggest(){
        $this->load->model('Share_inventory');
        $model = $this->Share_inventory;
        $all_items = $this->session->userdata('items_shipping');
        $suggestions = $model->get_search_suggestion($this->input->post('q'), $all_items);
        echo implode("\n",$suggestions);
    }

    public function delete_suggest(){
        if(isset($_GET['id'])){
            $data = $this->session->userdata('items_shipping');
            $i = array_search($_GET['id'], $data);
            unset($data[$i]);
            $this->session->set_userdata('items_shipping', $data);
        }else{
            $this->session->unset_userdata('items_shipping');
        }
    }

    function search()
    {
        $this->load->model('Share_inventory');
        $all_items = $this->session->userdata('items_shipping');
        $search = substr( $this->input->get('search'), 1, strpos($this->input->get('search'),']')-1 );
        $data_rows = $this->Share_inventory->search($search);
        $table_rows = '';

        foreach ($data_rows->result() as $value) {
            $all_items[] = $value->item_id;
            $table_rows .= '<tr>';
            $table_rows .= '<td>'.$value->item_id.'</td>';
            $table_rows .= '<td>'.$value->name.'</td>';
            $table_rows .= '<td>'.$value->quantity.'</td>';
            // $table_rows .= '<td><input type="text" name="amount[]" value="1" id="amount'.$value->item_id.'"></td>';
            $table_rows .= '<td><select name="amount[]" id="amount"'.$value->item_id.'">';
            for ($i=0; $i < $value->quantity-2; $i++) {
                $j = $i+1; 
                $table_rows .= '<option value="'.$j.'">'.$j.'</option>';
            }
            $table_rows .= '</select></td>';
            $table_rows .= '<td><input id="del'.$value->item_id.'" type="checkbox" name="check[]" value="'.$value->item_id.'" class="cb" checked><label for="del'.$value->item_id.'"></label></td>';
            $table_rows .= '</tr>';
        }
        $this->session->set_userdata('items_shipping', $all_items);

        echo $table_rows;
    }

    public function save_dispatch(){
        $this->load->model('Share_inventory');
        $response = array('success'=>-1, 'msg'=>'Location Invalid, Try Again!');
        //Comprueba si hay otra bd presente para tranferencia de items entre bds
        $db = ( $this->input->get('dbselected') != '...' ) ? $this->input->get('dbselected') : null ;
        $transfer = array(
            'sender'   => $_SESSION['dblocation'],
            'receiver' => $db,
            'date'     => date('Y-m-d H:i:s'),
            'status'   => $this->status['Pending']
        );

        if ($db != '...' && $db != null){
            if( count($_GET['check']) == count($_GET['amount']) ){
                $items = array(); //Array paralelo de datos POST

                //Preparo arreglo de items a trasladar
                for ($i=0; $i < count($_GET['check']); $i++) { 
                    $items[$i] = array('item_id'=>$_GET['check'][$i], 'quantity'=>$_GET['amount'][$i]);
                }

                asort($items);
                if ($id = $this->Share_inventory->save_dispatchs($transfer, $items) ){
                    //Resto cantidad de BD local
                    $this->load->model('Item');
                    $items_info = $this->Item->get_multiple_info( $_GET['check'] )->result();
                    foreach ($items_info as $i => $item){
                        $item_data = array(
                            'quantity' =>  $_GET['amount'][$i],
                            'name' => $item->name
                        );

                        $item_data['quantity'] = $item->quantity + ($_GET['amount'][$i] * -1);
                        $this->Item->save($item_data,$items[$i]['item_id']);
                    }

                    //Retorno Json
                    $response['success'] = 1;
                    $response['msg'] = 'Items transfer successful!';
                    $response['id'] = $id;
                }
            }else{
                $response['success'] = 0;
                $response['msg'] = 'There was an error with the selected items!';
            }
        }

        echo json_encode($response);
    }

    public function save_reception(){
        //Comprueba si hay otra bd presente para tranferencia de items entre bds
        $db = ( $this->input->post('dbselected') != '...' ) ? $this->input->post('dbselected') : null ;

        if ($db != '...' && $db != null){
            if( count($_POST['check']) == count($_POST['amount']) ){
                $arr = array('item_id'=>0, 'amount'=>0); //Array paralelo de datos POST
                $data = array(
                    'title'=>'Report Transfer',
                    'subtitle'=>'All items transferred to them:'+$db
                ); //Array para vista

                //Preparo arreglo de items a trasladar
                for ($i=0; $i < count($_POST['check']); $i++) { 
                    $arr[$i]['item_id'] = $_POST['check'][$i];
                    $arr[$i]['amount'] = $_POST['amount'][$i];
                }
                asort($arr); //Ordeno arreglos para hacerlo paralelos con el resultado del modelo

                //Resultado de items
                $items_info = $this->Item->get_multiple_info( $_POST['check'] )->result();
                foreach ($items_info as $i => $item) {
                    if ($item->quantity > $arr[$i]['amount']) {
                        $item_data = array(
                            'quantity' =>  $arr[$i]['amount'],
                            'name' => $item->name
                        );

                        // Guardo en la otra bd
                        if( $this->Item->save_in_other_inventory($item_data, $arr[$i]['item_id'], $db)){
                            //Resto cantidad traspasada y guardo en la bd local
                            $item_data['quantity'] = $item->quantity + ($arr[$i]['amount'] * -1);
                            $this->Item->save($item_data,$arr[$i]['item_id']);
                        } 
                    }
                }
            }

            $data['items_info'] = $items_info;
            $data['form'] = $arr;
            $this->load->view("items/test",$data);
        }
    }

    public function dispatch_details($dispatch_id){
        $this->load->model('Share_inventory');
        $details = $this->Share_inventory->get_reception_detail($dispatch_id)->result();
        $db = $this->Share_inventory->get_reception_locations($dispatch_id)->result()[0];
        $arr = array('item_id'=>0, 'amount'=>0);
        $items = array();

        //Cabecera completa
        $employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
        $emp_info=$this->Employee->get_info($employee_id);

        foreach ($details as $i => $detail) {
            $arr[$i]['item_id'] = $detail->item_id;
            $arr[$i]['amount'] = $detail->quantity;
            $items[$i] = $detail->item_id;
        }

        //Resultado de items
        $items_info = $this->Item->get_multiple_info( $items )->result();
        foreach ($items_info as $i => $item) {
            $item_data = array(
                'quantity' => $arr[$i]['amount'],
                'name'     => $item->name
            );
        }

        $data = array(
            'title'       => 'Dispatch Order',
            'subtitle'    => 'From: '.ucwords($db->sender).' to: '.ucwords($db->receiver),
            'items_info'  => $items_info,
            'barcode'     => $dispatch_id.'tranfer',
            'barcodetext' => 'Transfer Code',
            'form'        => $arr,
            'employee'    => $emp_info->first_name.' '.$emp_info->last_name
        ); //Array para vista
        $this->load->view("items/test",$data);
    }
}
?>
