<?php
/**
*
* @package Delegate-Voting-Mod
* @version 0.1 
* @copyright (c) 2010 Ruben Cancho
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

// VARIABLES NECESARIAS
// $db, $topic_id, $user_data['user_id'], $user->data['is_registered'] 
// $voted_id  -> la opción seleccionada en el formulario
// $cur_voted_id -> la opción que se había seleccionado anteriormente guardada en base de datos.

// TABLAS NECESARIAS
// POLL_VOTES_TABLE, POLL_DELEGATE_TABLE, POLL_OPTIONS_TABLE

function calc_delegated_votes($db, $topic_id, $user_id) {
	// Miro cuantos votos delegados tiene el usuario actual
	$sql = 'SELECT delegated_votes, delegated_user, is_delegate 
			FROM ' . POLL_DELEGATE_TABLE . '
			WHERE user_id = ' . (int) $user_id;
	$result = $db->sql_query($sql);
	$fila = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	$delegated_votes = (int) $fila['delegated_votes'];
	$delegated_user = (int) $fila['delegated_user'];
	$is_delegate = (int) $fila['is_delegate'];
			
	// Si NO SOY DELEGADO no he de tener votos delegados
	if (!$is_delegate) {
		$delegated_votes = 0;
	}
			
	// Si SOY DELEGADO cuento los votantes que han delegado en mí que ya han votado y los resto de mi total.
	else {	
		$sql = 'SELECT COUNT(user_id) AS direct_vote
				FROM ' . POLL_DELEGATE_TABLE . ' AS delegate
				INNER JOIN ' . POLL_VOTES_TABLE . ' AS vote
				ON (delegate.user_id = vote.vote_user_id)
				WHERE vote.topic_id = ' . (int) $topic_id . '
				AND delegate.delegated_user = ' . (int) $user_id;
		$result = $db->sql_query($sql);
		$fila = $db->sql_fetchrow($result);
		$delegated_votes -= $fila['direct_vote'];
	}	
	
	// Calculo si he votado anteriormente.
	$sql = 'SELECT poll_option_id
			FROM ' . POLL_VOTES_TABLE . '
			WHERE topic_id = ' . (int) $topic_id . '
			AND vote_user_id = ' . (int) $user_id;
	$result = $db->sql_query($sql);
	$direct_vote_row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	// Si mi delegado ha votado y no he votado ya directamente antes le resto mi voto personal antes de votar.
	
	if (!$direct_vote_row) {
		$sql = 'SELECT poll_option_id
				FROM ' . POLL_VOTES_TABLE . ' 
				WHERE topic_id = ' . (int) $topic_id . '
				AND vote_user_id = ' . (int) $delegated_user;
		$result = $db->sql_query($sql);
		$delegated_vote_row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if ( $delegated_vote_row ) {
			$delegated_user_vote = $delegated_vote_row['poll_option_id'];
			$sql = 'UPDATE ' . POLL_OPTIONS_TABLE . '
					SET poll_option_total = poll_option_total - 1 - ' . $delegated_votes . '
					WHERE topic_id = ' . (int) $topic_id . '
					AND poll_option_id = ' . (int) $delegated_user_vote;
			$db->sql_query($sql);
		}
	}
	
	return $delegated_votes;
}
