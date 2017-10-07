<?php
namespace Andach\Tests;
require_once __DIR__ . '/../vendor/autoload.php';
use Andach\IGAD\IGAD;

class IGADTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var igad
     */
    public $igad;
    public function setUp()
    {
        $this->igad = new IGAD('fe005b9297bd8d4a2ae71a15b0a7ba5a9dfc5303');
    }
    public function tearDown()
    {
        $this->igad = null;
    }

    /** @test @expectException */
    public function invalid_api_key_throws_error ()
    {
        $this->expectException('\Exception');
        $this->igad = new IGAD();
        $this->igad->getAccountXuid();
    }

    /** @test */
    public function get_company_info ()
    {
        $id = 7041;
        $response = $this->igad->getAccountXuid();
    }
}