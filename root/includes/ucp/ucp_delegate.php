<?php

class ucp_delegate
{
   var $u_action;
   var $new_config;
   function main($id, $mode)
   {
      global $db, $user, $auth, $template;
      global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;
      
      // Aqui va el código
      
      // Variables del template: {USUARIO}, {ES_DELEGADO}
      
      // TODO: Probar a asignar las variables
      
      $template->assign_vars(array(
									'USUARIO' => 'PRUEBA',
									'ES_DELEGADO' => 1
								  )
							);
      
      //
      
      $this->tpl_name = 'ucp_delegate_' . $mode;
	  $this->page_title = 'UCP_DELEGATE_' . strtoupper($mode);
 
   }
}

?>
