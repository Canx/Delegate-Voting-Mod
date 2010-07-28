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
      	  
	  $sql = 'SELECT delegated_user, is_delegate
	          FROM ' . POLL_DELEGATE_TABLE . '
	          WHERE user_id = ' . $user->data['user_id'];   
	  $result = $db->sql_query($sql);
	  $fila = $db->sql_fetchrow($result);
	  $db->sql_freeresult($result);
		 
	  if (!$fila) {
	    // TODO: Si no hay resultados se crea la fila con los valores por defecto: no es delegado y no tiene delegados.
		$sql = 'INSERT INTO ' . POLL_DELEGATE_TABLE . '
		        SET delegated_user = null, is_delegate = 0, user_id = ' . $user->data['user_id'];
		$db->sql_query($sql);
			
		$delegado_id = 0;
		$es_delegado = 0;
     }
	     
     else {
		 $delegado_id = $fila['delegated_user'];
		 $es_delegado = $fila['is_delegate'];
	 }
	 
	 if ($delegado_id) {
		 $sql = 'SELECT username FROM ' . USERS_TABLE . '
		         WHERE user_id = "' . $delegado_id . '"';
		 $result = $db->sql_query($sql);
	     $fila = $db->sql_fetchrow($result);
	     $db->sql_freeresult($result); 
	      
	     if ($fila) {
			 $delegado = $fila['username'];
		 }
		 else {
			 $delegado = '';
		 }

	 }
		 	  
     // TODO: Guardar datos nuevos
     if ($submit) {
		// TODO: Si no han cambiado los valores no hacer acceso a datos
        $delegado = request_var('delegado','');
        $es_delegado = request_var('es_delegado', 0);
         
        if ($delegado) {
	    	// TODO: Comprobar que el usuario tenga delegación activada.
			$sql = 'SELECT user_id FROM ' . USERS_TABLE . '
					WHERE username = "' . $delegado . '"';
			$result = $db->sql_query($sql);
			$fila = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			 
			// TODO: Controlar error si no existe el usuario.
			if ($fila) {
			$sql = 'UPDATE  ' . POLL_DELEGATE_TABLE . '
					SET delegated_user = ' . $fila['user_id'] . ',
					    is_delegate = ' . $es_delegado . ',
					    user_id = ' . $user->data['user_id'];
			$db->sql_query($sql);	
			}	   
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
