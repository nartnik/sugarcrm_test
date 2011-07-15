<?php
require_once 'include/SugarObjects/SugarConfig.php';
require_once 'include/SugarObjects/VardefManager.php';

/**
 * @group bug32797
 */
class Bug32797Test extends Sugar_PHPUnit_Framework_TestCase
{
    private $_old_sugar_config = null;

    public function setUp()
    {
        $this->_old_sugar_config = $GLOBALS['sugar_config'];
        $GLOBALS['sugar_config'] = array('require_accounts' => false);
    }

    public function tearDown()
    {
        $config = SugarConfig::getInstance();
        $config->clearCache();
        $GLOBALS['sugar_config'] = $this->_old_sugar_config;
    }

    public function vardefProvider()
    {
        return array(
            array(
                array('fields' => array('account_name' => array('required' => true))),
                array('fields' => array('account_name' => array('required' => false)))
            ),
            array(
                array('fields' => array('account_name' => array('required' => false))),
                array('fields' => array('account_name' => array('required' => false)))
            ),
            array(
                array('fields' => array('account_name' => array('required' => null))),
                array('fields' => array('account_name' => array('required' => false)))
            ),
            array(
                array('fields' => array('account_name' => array())),
                array('fields' => array('account_name' => array()))
            ),
            array(
                array('fields' => array()),
                array('fields' => array())
            )
        );
    }

    /**
     * @dataProvider vardefProvider
     */
    public function testApplyGlobalAccountRequirements($vardef, $vardefToCompare)
    {
        $this->assertEquals($vardefToCompare, VardefManager::applyGlobalAccountRequirements($vardef));
    }

    public function vardefProvider1()
    {
        return array(
            array(
                array('fields' => array('account_name' => array('required' => true))),
                array('fields' => array('account_name' => array('required' => true)))
            ),
            array(
                array('fields' => array('account_name' => array('required' => false))),
                array('fields' => array('account_name' => array('required' => true)))
            )
        );
    }

    /**
     * @dataProvider vardefProvider1
     */
    public function testApplyGlobalAccountRequirements1($vardef, $vardefToCompare)
    {
        $GLOBALS['sugar_config']['require_accounts'] = true;
        $this->assertEquals($vardefToCompare, VardefManager::applyGlobalAccountRequirements($vardef));
    }

    public function vardefProvider2()
    {
        return array(
            array(
                array('fields' => array('account_name' => array('required' => true))),
                array('fields' => array('account_name' => array('required' => true)))
            ),
            array(
                array('fields' => array('account_name' => array('required' => false))),
                array('fields' => array('account_name' => array('required' => false)))
            )
        );
    }

    /**
     * @dataProvider vardefProvider2
     */
    public function testApplyGlobalAccountRequirements2($vardef, $vardefToCompare)
    {
        unset($GLOBALS['sugar_config']['require_accounts']);
        $this->assertEquals($vardefToCompare, VardefManager::applyGlobalAccountRequirements($vardef));
    }
}