<?php
/**
*
* @package ucp
* @version $Id$
* @copyright (c) 2010 Partido de Internet
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*
* @package module_install
*/
class ucp_delegate_info
{
    function module()
    {
        return array(
            'filename' => 'ucp_delegate',
            'title'    => 'UCP_DELEGATE',
            'version'  => '0.1',
            'modes'    => array('index' => array('title' => 'UCP_DELEGATE_INDEX_TITLE',
                                                 'auth'  => '', 
                                                 'cat'   => array('UCP_DELEGATE')
                                                )
	 	               )
                    );
    }
 
    function install()
    {
    }
 
    function uninstall()
    {
    }
}

?>
