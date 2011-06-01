<?php
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2011 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/

 
class ConvertLeadTests extends Sugar_PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $GLOBALS['app_list_strings'] = return_app_list_strings_language($GLOBALS['current_language']);
    }
    
    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['app_list_strings']);
        unset($GLOBALS['current_user']);
    }
    
	/**
	 * @group bug39787
	 */
    public function testOpportunityNameValueFilled(){
        $lead = SugarTestLeadUtilities::createLead();
        $lead->opportunity_name = 'SBizzle Dollar Store';
        $lead->save();
        
        $_REQUEST['module'] = 'Leads';
        $_REQUEST['action'] = 'ConvertLead';
        $_REQUEST['record'] = $lead->id;
        
        // Check that the opportunity name doesn't get populated when it's not in the Leads editview layout
        require_once('include/MVC/Controller/ControllerFactory.php');
        require_once('include/MVC/View/ViewFactory.php');
        $GLOBALS['app']->controller = ControllerFactory::getController($_REQUEST['module']);
        ob_start();
        $GLOBALS['app']->controller->execute();
        $output = ob_get_clean();
        
        $matches_one = array();
        $pattern = '/SBizzle Dollar Store/';
        preg_match($pattern, $output, $matches_one);
        $this->assertTrue(count($matches_one) == 0, "Opportunity name got carried over to the Convert Leads page when it shouldn't have.");

        // Add the opportunity_name to the Leads EditView
        SugarTestStudioUtilities::addFieldToLayout('Leads', 'editview', 'opportunity_name');
        
        // Check that the opportunity name now DOES get populated now that it's in the Leads editview layout
        ob_start();
        $GLOBALS['app']->controller = ControllerFactory::getController($_REQUEST['module']);
        $GLOBALS['app']->controller->execute();
        $output = ob_get_clean();
        $matches_two = array();
        $pattern = '/SBizzle Dollar Store/';
        preg_match($pattern, $output, $matches_two);
        $this->assertTrue(count($matches_two) > 0, "Opportunity name did not carry over to the Convert Leads page when it should have.");
        
        SugarTestStudioUtilities::removeAllCreatedFields();
        unset($GLOBALS['app']->controller);
        unset($_REQUEST['module']);
        unset($_REQUEST['action']);
        unset($_REQUEST['record']);
        SugarTestLeadUtilities::removeAllCreatedLeads();
    }
}