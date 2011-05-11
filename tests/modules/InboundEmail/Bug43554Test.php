<?php
require_once('modules/InboundEmail/InboundEmail.php');

/**
 * @ticket 43554
 */
class Bug43554Test extends Sugar_PHPUnit_Framework_TestCase
{
	var $ie = null;
    var $_user = null;

	public function setUp()
    {
        $this->_user = SugarTestUserUtilities::createAnonymousUser();
        $GLOBALS['current_user'] = $this->_user;

		$this->ie = new InboundEmail();
	}

    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
    }

    public function getUrls()
    {
        return array(
            array("http://localhost:8888/sugarent/index.php?composeLayoutId=composeLayout1&fromAccount=1&module=Emails&action=EmailUIAjax&emailUIAction=sendEmail&setEditor=1"),
            array("http://localhost:8888/index.php?composeLayoutId=composeLayout1&fromAccount=1&module=Emails&action=EmailUIAjax&emailUIAction=sendEmail&setEditor=1"),
            array(to_html("http://localhost:8888/index.php?composeLayoutId=composeLayout1&fromAccount=1&module=Emails&action=EmailUIAjax&emailUIAction=sendEmail&setEditor=1")),
            array("/index.php?composeLayoutId=composeLayout1&fromAccount=1&module=Emails&action=EmailUIAjax&emailUIAction=sendEmail&setEditor=1"),
            array("index.php?composeLayoutId=composeLayout1&fromAccount=1&module=Emails&action=EmailUIAjax&emailUIAction=sendEmail&setEditor=1"),
            array("/?composeLayoutId=composeLayout1&fromAccount=1&module=Emails&action=EmailUIAjax&emailUIAction=sendEmail&setEditor=1"),
            array("https://localhost/?composeLayoutId=composeLayout1&fromAccount=1&module=Emails&action=EmailUIAjax&emailUIAction=sendEmail&setEditor=1"),
            );
    }

    /**
     * @dataProvider getUrls
     * @param string $url
     */
	function testEmailCleanup($url)
	{
        $data = "Test: <img src=\"$url\">";
        $res = str_replace("<img />", "", $this->ie->cleanContent($data));
        $this->assertNotContains("<img", $res);
	}
}