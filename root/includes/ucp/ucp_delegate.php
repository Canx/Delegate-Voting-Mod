<?php

class ucp_delegate
{
   var $u_action;
   var $new_config;
   function main($id, $mode)
   {
      global $db, $user, $auth, $template;
      global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;
      
      $username	= request_var('username', '', true);
      $submit = (isset($_POST['submit'])) ? true : false;
      
      
      
      // TODO: Guardar datos nuevos
      if ($submit) {
         $delegado = request_var('delegado','');
         $es_delegado = request_var('es_delegado', 0);
         
         // TODO: Guardar formulario en la base de datos.
         //$sql = 'UPDATE 
	  }
	  
	  else {
		 $sql = 'SELECT delegated_user, is_delegate
		         FROM ' . POLL_DELEGATE_TABLE . '
		         WHERE user_id = ' . $user->data['user_id'];   
		 $result = $db->sql_query($sql);
		 $fila_delegacion = $db->sql_fetchrow($result);
		 $db->sql_freeresult($result);
		 
		 if (!$fila_delegacion) {
			// TODO: Si no hay resultados se crea la fila con los valores por defecto: no es delegado y no tiene delegados.
			$sql = 'INSERT INTO ' . POLL_DELEGATE_TABLE . '
			        SET delegated_user = null, is_delegate = 0, user_id = ' . $user->data['user_id'];
			$db->sql_query($sql);
			
			$delegado = "";
			$es_delegado = 0;
	     }
	     
	     else {
			 $delegado = $fila_delegacion['delegated_user'];
			 $es_delegado = $fila_delegacion['is_delegate'];
		 }
		 	  
	  }
      
      $template->assign_vars(array(
									'DELEGADO' => $delegado,
									'ES_DELEGADO' => $es_delegado
								  )
							);
      
      //
      
      $this->tpl_name = 'ucp_delegate_' . $mode;
	  $this->page_title = 'UCP_DELEGATE_' . strtoupper($mode);
 
   }
}

?>
