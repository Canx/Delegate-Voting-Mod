class ucp_delegate_info
{
    function module()
    {
        return array(
            'filename' => 'ucp_delegate',
            'title'    => 'UCP_DELEGATE',
            'version'  => '0.1',
            'modes'    => array('index' => array('title' => 'UCP_DELEGATE_INDEX_TITLE', 'auth' => 'ucl_a_delegate_auth', 'cat' => array('')), ),
        );
    }
 
    function install()
    {
    }
 
    function uninstall()
    {
    }
}
