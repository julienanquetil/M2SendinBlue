<?php
/**
 * *
 * M2SendinBlue
 *
 * @author      Julien Anquetil (https://www.julien-anquetil.com/)
 * @copyright   Copyright 2018 Julien ANQUETIL (https://www.julien-anquetil.com/)
 * @license     http://opensource.org/licenses/MIT MIT
 *
 *
 */

namespace JulienAnquetil\M2SendinBlue\Test\Unit\Helper;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class DataTest extends \PHPUnit\Framework\TestCase
{
    protected $_scopeMock;
    protected $_helper;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->_scopeMock = $this->getMockBuilder('Magento\Framework\App\Config\ScopeConfigInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock = $this->getMockBuilder('Magento\Framework\App\Helper\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock->expects($this->any())
            ->method('getScopeConfig')
            ->willReturn($this->_scopeMock);
        $this->_helper = $objectManager->getObject('JulienAnquetil\M2SendinBlue\Helper\Data', ['context'=>$contextMock]);
    }

    /**
     * @covers JulienAnquetil\M2SendinBlue\Helper\Data::getApiKey
     */
    public function testGetApiKey()
    {
        $this->_scopeMock->expects($this->once())
            ->method('getValue')
            ->willReturn('wll30bi80h');
        $this->assertEquals($this->_helper->getApiKey(), 'wll30bi80h');
    }

    /**
     * @covers JulienAnquetil\M2SendinBlue\Helper\Data::GetListId
     */
    public function testGetListId()
    {
        $this->_scopeMock->expects($this->once())
            ->method('getValue')
            ->willReturn('5');
        $this->assertEquals($this->_helper->getListId(), '5');
    }
}