<?php
namespace Tests\UnitTests;

class KeyTest extends \PHPUnit_Framework_TestCase
{
    public function test_Key_constructor_initialize_data_if_passed_data_is_complete()
    {
        $data = array(
            'title' => 'some title',
            'value' => 'some value',
            'user_id' => '1'
        );
        $Key = new \Gitrepos\Entities\Key($data);
        $this->assertEquals('some title', $Key->getTitle());
        $this->assertEquals('some value', $Key->getValue());
        $this->assertEquals('1', $Key->getUserId());
    }

    public function test_Key_constructor_throws_exception_if_title_is_missing()
    {
        $this->setExpectedException('\Gitrepos\Exceptions\InvalidKey', 'Missing key title');
        $data = array(
            'value' => 'some value'
        );
        new \Gitrepos\Entities\Key($data);
    }

    public function test_Key_constructor_throws_exception_if_title_is_empty()
    {
        $this->setExpectedException('\Gitrepos\Exceptions\InvalidKey', 'Empty key title');
        $data = array(
            'title' => '',
            'value' => 'some value'
        );
        new \Gitrepos\Entities\Key($data);
    }

    public function test_Key_constructor_throws_exception_if_title_is_too_long()
    {
        $this->setExpectedException('\Gitrepos\Exceptions\InvalidKey', 'Key title too long');
        $long_title = '';
        for ($i = 0; $i < 129; $i++) {
            $long_title .= '.';
        }
        $data = array(
            'title' => $long_title,
            'value' => 'some value'
        );
        new \Gitrepos\Entities\Key($data);
    }

    public function test_Key_constructor_throws_exception_if_value_is_missing()
    {
        $this->setExpectedException('\Gitrepos\Exceptions\InvalidKey', 'Missing key value');
        $data = array(
            'title' => 'some title'
        );
        new \Gitrepos\Entities\Key($data);
    }

    public function test_Key_constructor_throws_exception_if_value_is_empty()
    {
        $this->setExpectedException('\Gitrepos\Exceptions\InvalidKey', 'Empty key value');
        $data = array(
            'title' => 'some title',
            'value' => ''
        );
        new \Gitrepos\Entities\Key($data);
    }

    public function test_Key_constructor_throws_exception_if_value_is_too_long()
    {
        $this->setExpectedException('\Gitrepos\Exceptions\InvalidKey', 'Key value too long');
        $long_value = '';
        for ($i = 0; $i < 513; $i++) {
            $long_value .= '.';
        }
        $data = array(
            'title' => 'some title',
            'value' => $long_value
        );
        new \Gitrepos\Entities\Key($data);
    }

    public function test_Key_constructor_throws_exception_if_user_id_is_missing()
    {
        $this->setExpectedException('\Gitrepos\Exceptions\InvalidKey', 'Missing key\'s user id');
        $data = array(
            'title' => 'some title',
            'value' => 'some value'
        );
        new \Gitrepos\Entities\Key($data);
    }

    public function test_Key_constructor_throws_exception_if_user_id_is_empty()
    {
        $this->setExpectedException('\Gitrepos\Exceptions\InvalidKey', 'Empty key\'s user id');
        $data = array(
            'title' => 'some title',
            'value' => 'some value',
            'user_id' => 0
        );
        new \Gitrepos\Entities\Key($data);
    }
}
