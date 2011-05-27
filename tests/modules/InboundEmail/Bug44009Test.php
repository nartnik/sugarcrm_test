<?php
require_once('modules/InboundEmail/InboundEmail.php');

/**
 * @ticket 44009
 */
class Bug44009Test extends Sugar_PHPUnit_Framework_TestCase
{

	protected $ie = null;

	public function setUp()
    {
		$this->ie = new InboundEmail();
	}

    public function getData()
    {
        return array(
            array("test<b>test</b>", "test<b>test</b>"),
            array("<html>test<b>test</b></html>", "test<b>test</b>"),
            array("<html><head></head><body>test<b>test</b></body></html>", "test<b>test</b>"),
            array("<html><head><style>test</style></head><body>test<b>test</b></body></html>", "test<b>test</b>"),
            array("<html><head></head><body><script language=\"javascript\">alert('test!');</script>test<b>test</b></body></html>", "test<b>test</b>"),
            array("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"><html xmlns=\"http://www.w3.org/1999/xhtml\"><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\" /><title>test 12345</title></head><body><p>test<b>test</b></body></html>", "<p>test<b>test</b></p>"),
            );
    }

    /**
     * @dataProvider getData
     * @param string $url
     */
	function testEmailCleanup($data, $res)
	{
        $this->assertEquals($res,$this->ie->cleanContent($data));
	}
}