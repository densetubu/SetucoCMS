<?php

class Admin_Model_Media_Test extends Setuco_Test_PHPUnit_ModelTestCase
{

    public function test_createNewMediaIDがDaoのinsert結果をそのまま返すこと()
    {

        $expected = sha1(rand());

        $mediaDao = $this->getMock('Common_Model_Dbtable_Media', array('insert'));
        $mediaDao->expects($this->once())
                 ->method('insert')
                 ->will($this->returnValue($expected));

        $target = $this->getMock('Admin_Model_Media', array('getMediaDao'));
        $target->expects($this->once())
               ->method('getMediaDao')
               ->will($this->returnValue($mediaDao));

        $result = $target->createNewMediaID();

        $this->assertSame($expected, $result);

    }


}
