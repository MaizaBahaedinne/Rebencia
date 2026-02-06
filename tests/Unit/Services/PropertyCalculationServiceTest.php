<?php

namespace Tests\Unit\Services;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\PropertyCalculationService;

/**
 * Tests unitaires pour PropertyCalculationService
 */
class PropertyCalculationServiceTest extends CIUnitTestCase
{
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PropertyCalculationService();
    }

    public function testCalculateLocationScore()
    {
        $scores = [
            'proximity_to_schools' => 80,
            'proximity_to_transport' => 90,
            'proximity_to_shopping' => 70,
            'proximity_to_parks' => 60,
            'proximity_to_healthcare' => 75,
            'proximity_to_restaurants' => 65,
            'proximity_to_entertainment' => 55,
            'area_safety_score' => 85,
            'noise_level_score' => 70,
            'area_cleanliness_score' => 80
        ];
        
        $overallScore = $this->service->calculateLocationScore($scores);
        
        // Score devrait être entre 0 et 100
        $this->assertGreaterThanOrEqual(0, $overallScore);
        $this->assertLessThanOrEqual(100, $overallScore);
    }

    public function testGetScoreQuality()
    {
        $this->assertEquals('excellent', $this->service->getScoreQuality(90));
        $this->assertEquals('good', $this->service->getScoreQuality(75));
        $this->assertEquals('average', $this->service->getScoreQuality(60));
        $this->assertEquals('below_average', $this->service->getScoreQuality(45));
        $this->assertEquals('poor', $this->service->getScoreQuality(30));
    }

    public function testMapConditionToScore()
    {
        $this->assertEquals(95, $this->service->mapConditionToScore('excellent'));
        $this->assertEquals(80, $this->service->mapConditionToScore('good'));
        $this->assertEquals(60, $this->service->mapConditionToScore('average'));
        $this->assertEquals(40, $this->service->mapConditionToScore('poor'));
        $this->assertEquals(20, $this->service->mapConditionToScore('very_poor'));
        $this->assertEquals(50, $this->service->mapConditionToScore('unknown'));
    }
}
