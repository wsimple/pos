<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Loader extends CI_Loader{

    public function layout($template_name, $vars = array(), $jsLibraries = array())
    {    
        //_imprimir($vars);
        
        //Settings
        $ci = get_instance();
        $vars['config'] = $ci->config->item('websarrollo');
        
        //language
        // $ci->session->set_userdata('ws-language', get_language());
        // $ci->lang->load('general', $ci->session->userdata('ws-language'));
        // $vars['language'] = $ci->lang; 

        //Body
        $content = $this->view($template_name, $vars);

    }
}
?>