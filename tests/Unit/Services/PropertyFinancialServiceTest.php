<?php

namespace Tests\Unit\Services;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\PropertyFinancialService;

/**
 * Tests unitaires pour PropertyFinancialService
 */
class PropertyFinancialServiceTest extends CIUnitTestCase
{
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PropertyFinancialService();
    }

    public function testCalculateGrossYield()
    {
        $marketPrice = 200000;
        $monthlyRent = 1000;
        
        $yield = $this->service->calculateGrossYield($marketPrice, $monthlyRent);
        
        // (1000 * 12) / 200000 * 100 = 6%
        $this->assertEquals(6.0, $yield);
    }

    public function testCalculateGrossYieldWithZeroPrice()
    {
        $marketPrice = 0;
        $monthlyRent = 1000;
        
        $yield = $this->service->calculateGrossYield($marketPrice, $monthlyRent);
        
        $this->assertEquals(0, $yield);
    }

    public function testCalculatePricePerSqm()
    {
        $price = 150000;
        $surface = 100;
        
        $pricePerSqm = $this->service->calculatePricePerSqm($price, $surface);
        
        $this->assertEquals(1500, $pricePerSqm);
    }

    public function testCalculatePricePerSqmWithZeroSurface()
    {
        $price = 150000;
        $surface = 0;
        
        $pricePerSqm = $this->service->calculatePricePerSqm($price, $surface);
        
        $this->assertEquals(0, $pricePerSqm);
    }

    public function testCalculatePaybackPeriod()
    {
        $marketPrice = 200000;
        $annualNetIncome = 10000;
        
        $period = $this->service->calculatePaybackPeriod($marketPrice, $annualNetIncome);
        
        // 200000 / 10000 = 20 ans
        $this->assertEquals(20.0, $period);
    }

    public function testCalculateFutureValue()
    {
        $currentValue = 100000;
        $appreciationRate = 3; // 3% par an
        $years = 5;
        
        $futureValue = $this->service->calculateFutureValue($currentValue, $appreciationRate, $years);
        
        // 100000 * (1 + 0.03)^5 = 115927.41
        $this->assertEqualsWithDelta(115927.41, $futureValue, 0.01);
    }
}
