<?php
class Bug43395Test extends Sugar_PHPUnit_Framework_OutputTestCase
{
	private static $quickSearch;
	private static $contact;
	
	static function setUpBeforeClass() 
    {
    	global $app_strings, $app_list_strings;
        $app_strings = return_application_language($GLOBALS['current_language']);
        $app_list_strings = return_app_list_strings_language($GLOBALS['current_language']);    	
    	$user = new User();
    	$user->retrieve('1');
        $GLOBALS['current_user'] = $user;
        self::$contact = SugarTestContactUtilities::createContact();
        self::$contact->first_name = 'Bug43395';
        self::$contact->last_name = 'Test';
        self::$contact->salutation = 'Mr.'; 
        self::$contact->save();    	
    }
    
    public static function tearDownAfterClass() 
    {
        unset($_REQUEST['data']);
        unset($_REQUEST['query']);
        SugarTestContactUtilities::removeAllCreatedContacts();
    }
    
    public function testFormatResults()
    {	
    	$_REQUEST = array();
    	$_REQUEST['data'] = '{"form":"search_form","method":"query","modules":["Contacts"],"group":"or","field_list":["name","id"],"populate_list":["contact_c_basic","contact_id_c_basic"],"required_list":["parent_id"],"conditions":[{"name":"name","op":"like_custom","end":"%","value":""}],"order":"name","limit":"30","no_match_text":"No Match"}';
        $_REQUEST['query'] = self::$contact->first_name;
        require_once 'modules/Home/quicksearchQuery.php';
        
        $json = getJSONobj();
		$data = $json->decode(html_entity_decode($_REQUEST['data']));
		if(isset($_REQUEST['query']) && !empty($_REQUEST['query'])){
    		foreach($data['conditions'] as $k=>$v){
    			if(empty($data['conditions'][$k]['value'])){
       				$data['conditions'][$k]['value']=$_REQUEST['query'];
    			}
    		}
		}
 		self::$quickSearch = new quicksearchQuery();
		$result = self::$quickSearch->query($data);
		$resultBean = $json->decodeReal($result);
	    $this->assertEquals($resultBean['fields'][0]['name'], self::$contact->first_name . ' ' . self::$contact->last_name, 'Assert that the quicksearch returns a contact name without salutation');
    }
    
    public function testPersonLocaleNameFormattting()
    {
        $GLOBALS['current_user']->setPreference('default_locale_name_format', 's f l');

    	self::$contact->createLocaleFormattedName = true;
    	self::$contact->_create_proper_name_field();
    	$this->assertEquals(self::$contact->name, 'Mr. Bug43395 Test', 'Assert that _create_proper_name_field with createLocaleFormattedName set to true returns salutation');

    	self::$contact->createLocaleFormattedName = false;
    	self::$contact->_create_proper_name_field();
    	$this->assertEquals(self::$contact->name, 'Bug43395 Test', 'Assert that _create_proper_name_field with createLocaleFormattedName set to false does not return salutation');
    }
    
}
?>