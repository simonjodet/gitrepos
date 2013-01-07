<?php
namespace Tests\UnitTests;

class KeyTest extends \PHPUnit_Framework_TestCase
{
    public function test_Key_constructor_initialize_data_if_passed_data_is_complete()
    {
        $data = array(
            'title' => 'some title',
            'value' => 'some value'
        );
        $Key = new \Gitrepos\Entities\Key($data);
        $this->assertEquals('some title', $Key->getTitle());
        $this->assertEquals('some value', $Key->getValue());
    }

    public function test_Key_constructor_throws_exception_if_title_is_missing()
    {
        $this->setExpectedException('\Gitrepos\Exceptions\MissingKeyTitle');
        $data = array(
            'value' => 'some value'
        );
        new \Gitrepos\Entities\Key($data);
    }

    public function test_Key_constructor_throws_exception_if_title_is_empty()
    {
        $this->setExpectedException('\Gitrepos\Exceptions\EmptyKeyTitle');
        $data = array(
            'title' => '',
            'value' => 'some value'
        );
        new \Gitrepos\Entities\Key($data);
    }

    public function test_Key_constructor_throws_exception_if_title_is_too_long()
    {
        $this->setExpectedException('\Gitrepos\Exceptions\KeyTitleTooLong');
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
        $this->setExpectedException('\Gitrepos\Exceptions\MissingKeyValue');
        $data = array(
            'title' => 'some title'
        );
        new \Gitrepos\Entities\Key($data);
    }

    public function test_Key_constructor_throws_exception_if_value_is_empty()
    {
        $this->setExpectedException('\Gitrepos\Exceptions\EmptyKeyValue');
        $data = array(
            'title' => 'some title',
            'value' => ''
        );
        new \Gitrepos\Entities\Key($data);
    }

    public function test_Key_constructor_throws_exception_if_value_is_too_long()
    {
        $this->setExpectedException('\Gitrepos\Exceptions\KeyValueTooLong');
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
}
