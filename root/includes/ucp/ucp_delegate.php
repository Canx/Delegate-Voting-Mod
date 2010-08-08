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
      $delegado = '';
      
	  // @todo 1 Refactorizar en functions_voting.php
      	 
      // Cargamos la información de delegación y si no existe la creamos con valores vacíos.
	  $sql = 'SELECT delegated_user, is_delegate
	          FROM ' . POLL_DELEGATE_TABLE . '
	          WHERE user_id = ' . $user->data['user_id'];   
	  $result = $db->sql_query($sql);
	  $fila = $db->sql_fetchrow($result);
	  $db->sql_freeresult($result);
		 
	  if (!$fila) {
		$sql = 'INSERT INTO ' . POLL_DELEGATE_TABLE . '
		        SET delegated_user = 0, is_delegate = 0, delegated_votes = 0, user_id = ' . $user->data['user_id'];
		$db->sql_query($sql);
			
		$delegado_id = 0;
		$es_delegado = 0;
     }    
     else {
		 $delegado_id = $fila['delegated_user'];
		 $es_delegado = $fila['is_delegate'];
	 }
		 	  
     // Al hacer submit guardamos los nuevos valores del formulario.
     if ($submit) {
        $nuevo_delegado_id = request_var('delegate_select',0);
        $nuevo_es_delegado = request_var('es_delegado', 0);
        
        if ($nuevo_es_delegado != $es_delegado) {
			// @todo 2 Si ya no es delegado borrar la delegación de los votantes.
			
			$sql = 'UPDATE ' . POLL_DELEGATE_TABLE . '
					SET is_delegate = ' . $nuevo_es_delegado . '
					WHERE user_id = ' . $user->data['user_id'];
			$db->sql_query($sql);
			$es_delegado = $nuevo_es_delegado;
		}
		
		// delegado_id ha cambiado?
        if ($nuevo_delegado_id != $delegado_id) {
			// @todo 2 Comprobar que el usuario tenga delegación activada.
			// @todo 1 Controlar error si no existe el usuario.
			// @todo 3 Utilizar $db->sql_build_array
			
			
			// @todo 1 Si cambio de delegado he de cambiar los votos de las votaciones activas en las que no he votado a las que haya dado mi voto a mi antiguo delegado!
			$sql = 'SELECT polls.topic_id AS topic_id,
						   polls.poll_option_id AS voto_del1,
						   delegado2.poll_option_id AS voto_del2
					FROM 
						(SELECT polls.topic_id, delegado1.poll_option_id
						 FROM
							(SELECT topic_id FROM phpbb_topics AS topics
							 WHERE poll_start > 0
							 AND ( (poll_length + poll_start > unix_timestamp(now()))
							 OR poll_length = 0 )
							 AND topic_id NOT IN
								(SELECT topic_id 
								 FROM phpbb_poll_votes AS VOTES 
								 WHERE vote_user_id = ' . $user->data['user_id'] . ')) AS polls
						LEFT JOIN (
							SELECT topic_id, poll_option_id, vote_user_id
							FROM phpbb_poll_votes 
							WHERE vote_user_id = ' . (int) $delegado_id . ') AS delegado1 
						ON (polls.topic_id = delegado1.topic_id)) AS polls
					LEFT JOIN (
						SELECT topic_id, poll_option_id, vote_user_id
						FROM phpbb_poll_votes
						WHERE vote_user_id = ' . (int) $nuevo_delegado_id . ' ) AS delegado2 
					ON (polls.topic_id = delegado2.topic_id)';

			$result = $db->sql_query($sql);
			
			while ($row = $db->sql_fetchrow($result))
			{

				// Si el delegado anterior había votado le restamos un voto de su opción.
				if ($row['voto_del1']) {
					$sql = 'UPDATE phpbb_poll_options
							SET poll_option_total = poll_option_total - 1
							WHERE topic_id = ' . (int)$row['topic_id'] . '
							AND poll_option_id = ' . (int)$row['voto_del1'];
					$db->sql_query($sql);					
				}
				// Si el delegado actual ha votado le sumamos el voto.
				if ($row['voto_del2']) {
					$sql = 'UPDATE phpbb_poll_options
							SET poll_option_total = poll_option_total + 1
							WHERE topic_id = ' . (int)$row['topic_id'] . '
							AND poll_option_id = ' . (int) $row['voto_del2'];
					$db->sql_query($sql);	
				}
			}
			
			$sql = 'UPDATE  ' . POLL_DELEGATE_TABLE . '
					SET delegated_user = ' . $nuevo_delegado_id . '
					WHERE user_id = ' . $user->data['user_id'];
			$db->sql_query($sql);
			
			if ($delegado_id) {
				$sql = 'UPDATE ' . POLL_DELEGATE_TABLE . '
						SET delegated_votes = delegated_votes - 1
						WHERE user_id = ' . $delegado_id;
				$db->sql_query($sql);
			}
			
			if ($nuevo_delegado_id) {
				$sql = 'UPDATE ' . POLL_DELEGATE_TABLE . '
						SET delegated_votes = delegated_votes + 1
						WHERE user_id = ' . $nuevo_delegado_id;
				$db->sql_query($sql);
			}
			
			$delegado_id = $nuevo_delegado_id;
		}
	  }
	 
	 $sql = 'SELECT users.username, users.user_id, polldel.delegated_votes 
	         FROM ' . USERS_TABLE . ' AS users,' . POLL_DELEGATE_TABLE . ' AS polldel
	         WHERE users.user_id = polldel.user_id
	         AND is_delegate = 1
	         ORDER BY delegated_votes DESC';
	 $result = $db->sql_query($sql);
	 
	 // @todo 2 Si no hay resultados damos error de que no hay delegados.
	 while ( $row = $db->sql_fetchrow($result) ) {
		
		if ($row['user_id'] == $delegado_id) {
			$selected = 'selected';
			$delegado = $row['username'];
		}
		else {
			$selected = '';
		}
		$template->assign_block_vars('delegate', array('DELEGATE' => $row['username'],
												       'DELEGATE_ID' => $row['user_id'],
												       'DELEGATED_VOTES' => $row['delegated_votes'],
												       'SELECTED' => $selected)
									);
														
	 }
	 $db->sql_freeresult($result);
	 
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
