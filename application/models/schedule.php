<?php
class Schedule extends CI_Model
{
    var $con;

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        //Seleccion de DB
        $db = $this->session->userdata('dblocation');
        if($db)
            $this->con = $this->load->database($db, true);
        else
            $this->con = $this->db;
    }

    function save($employee_schedule, $employee_id){
        //Borro permisos actuales
        $this->con->delete('schedules', array('person_id'=>$employee_id) );

        //Agrega permisos actuales
        foreach ($employee_schedule as $key => $value) {
            $data = array(
            'day'=>$value['day'],
            'in'=>$value['in'],
            'out'=>$value['out'],
            'person_id'=>$employee_id
            );

            $this->con->insert('schedules', $data);
        }

        return true;
    }

    function workable_day($day, $person_id){
        $this->con->from('schedules');
        $this->con->where( array('day'=>$day, 'person_id'=>$person_id) );
        $query = $this->con->get();

        if( $query->num_rows == 1) return true;

        return false;
    }

    function workable_day_hour($day, $hour, $person_id){
        $this->con->select($hour);
        $this->con->from('schedules');
        $this->con->where( array('day'=>$day, 'person_id'=>$person_id) );
        $query = $this->con->get();

        if( $query->num_rows == 1){
            return substr($query->row()->$hour, 0, 2);
        }

        return false;
    }

    function get_schedule($employee_id, $specific_day=false){
        $this->con->from('schedules');
        $this->con->where( array('person_id'=>$employee_id) );
        $query = $this->con->get();

        if($query->num_rows() > 0)
        {
            return $query->row();
        }

        return false;
    }

}
?>
