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
    public function non_string_xuid_throws_error()
    {
        $this->expectException('\Exception');
        $this->igad = new IGAD('fe005b9297bd8d4a2ae71a15b0a7ba5a9dfc5303', 2584878536129841);
    }

    /** @test */
    public function get_account_xuid()
    {
        $id = '2584878536129841';
        $response = $this->igad->getXuid('Major Nelson');

        $this->assertEquals($id, $response);
    }

    /** @test */
    public function get_account_gamertag()
    {
        $id = 'Major Nelson';
        $response = $this->igad->getGamertag('2584878536129841');

        $this->assertEquals($id, $response);
    }

    /** @test */
    public function get_xboxone_achievements_8char()
    {
        //Major Nelson's achievements for Oxenfree
        $response = $this->igad->getAchievements(35717019, '2584878536129841');

        $this->assertNotNull($response);

        $this->assertEquals(13, count($response));

        $ach = $response[0];

        $this->assertNotNull($ach['gamerscore']);
        $this->assertNotNull($ach['achievement_description']);
        $this->assertNotNull($ach['name']);
        $this->assertEquals(1, count($ach['requirements']));
    }

    /** @test */
    public function get_xboxone_achievements_9char()
    {
        //Major Nelson's achievements for Rare Replay
        $response = $this->igad->getAchievements(515050978, '2533274912189019');

        $this->assertNotNull($response);

        $this->assertEquals(200, count($response));

        $ach = $response[6];

        $this->assertNotNull($ach['gamerscore']);
        $this->assertNotNull($ach['achievement_description']);
        $this->assertNotNull($ach['name']);
        $this->assertEquals(5, count($ach['requirements']));
    }

    /** @test */
    public function get_xbox360_achievements()
    {
        //Major Nelson's achievements for Halo 4
        $response = $this->igad->getAchievements(1297287449, '2584878536129841');

        $this->assertNotNull($response);

        $this->assertEquals(86, count($response));

        $ach = $response[0];

        $this->assertNotNull($ach['gamerscore']);
        $this->assertNotNull($ach['achievement_description']);
        $this->assertNotNull($ach['name']);
        $this->assertEquals(0, count($ach['requirements']));
    }
}