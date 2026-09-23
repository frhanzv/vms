<?php

namespace Tests\Unit;

use App\Models\ClientFeatureModel;
use CodeIgniter\Test\CIUnitTestCase;

class ClientFeatureDefaultsTest extends CIUnitTestCase
{
    public function testNightlyAutoCheckoutIsAvailableAndOptIn(): void
    {
        $this->assertArrayHasKey('nightly_auto_checkout', ClientFeatureModel::allFeatures());
        $this->assertFalse(ClientFeatureModel::defaultEnabled('nightly_auto_checkout'));
    }
}
