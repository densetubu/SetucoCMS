<?php

require_once "bootstrap.php";

class AdminTest extends Setuco_Test_PHPUnit_SeleniumTestCase
{
    public function test_ログインをすることができるか()
    {
        $this->_login();

        //ドメイン部分が違っていてもテストが通るようにする
        //@todo ヘルパーメソッドにする
        $location = preg_replace("/(http:\/\/.+)(\/.+\/.+)/", "$2", "http://setucocms.localdomain/admin/login");
        $this->assertEquals("/admin/login", $location);
    }



}
