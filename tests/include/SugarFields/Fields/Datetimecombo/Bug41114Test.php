<?php 
require_once('include/SugarFields/Fields/Relate/SugarFieldRelate.php');

class Bug41114Test extends Sugar_PHPUnit_Framework_TestCase
{
	public function setUp()
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
	}

    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
    }
    
    public function _providerEmailTemplateFormat()
    {
        return array(
            array('2010-10-10 13:00:00','2010/10/10 01:00PM', 'Y/m/d', 'h:iA' ),
            array('2010-10-11 13:00:00','2010/10/11 13:00', 'Y/m/d', 'H:i' ),
            
            array('2011-03-25 01:05:22','25.03.2011 01:05AM', 'd.m.Y', 'h:iA'),
            array('2011-04-21 01:05:22','21.04.2011 01:05', 'd.m.Y', 'H:i'),
            
            array('','', 'Y-m-d', 'h:iA'),
            array('','', 'Y-m-d', 'H:i'),
            
        );   
    }
     /**
     * @dataProvider _providerEmailTemplateFormat
     */
	public function testEmailTemplateFormat($unformattedValue, $expectedValue, $dateFormat, $timeFormat)
	{
	    $GLOBALS['sugar_config']['default_date_format'] = $dateFormat;
		$GLOBALS['sugar_config']['default_time_format'] = $timeFormat;
		
        require_once('include/SugarFields/SugarFieldHandler.php');
   		$sfr = SugarFieldHandler::getSugarField('datetimecombo');
    	$formattedValue = $sfr->getEmailTemplateValue($unformattedValue,array());
    	
   	 	$this->assertEquals($expectedValue, $formattedValue);
    }
}