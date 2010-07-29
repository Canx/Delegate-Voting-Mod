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
      	 
      // Cargamos la información de delegación y si no existe la creamos con valores vacíos.
	  $sql = 'SELECT delegated_user, is_delegate
	          FROM ' . POLL_DELEGATE_TABLE . '
	          WHERE user_id = ' . $user->data['user_id'];   
	  $result = $db->sql_query($sql);
	  $fila = $db->sql_fetchrow($result);
	  $db->sql_freeresult($result);
		 
	  if (!$fila) {
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
	 
	 // Cargamos el nombre de usuario a partir del user_id
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
		 	  
     // Al hacer submit guardamos los nuevos valores del formulario.
     if ($submit) {
		// TODO: Si no han cambiado los valores no guardar nada.
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
			// TODO: Utilizar $db->sql_build_array
			$sql = 'UPDATE  ' . POLL_DELEGATE_TABLE . '
					SET delegated_user = ' . $fila['user_id'] . ',
					    is_delegate = ' . $es_delegado . ',
					    user_id = ' . $user->data['user_id'];
			$db->sql_query($sql);	
			}
			
			// TODO: Actualizar el total de votos delegados 
		 }
	  }
	  
      $template->assign_vars(array(
									'DELEGADO' => $delegado,
									'ES_DELEGADO' => $es_delegado
								  )
							);
      $this->tpl_name = 'ucp_delegate_' . $mode;
	  $this->page_title = 'UCP_DELEGATE_' . strtoupper($mode);
 
   }
}

?>
