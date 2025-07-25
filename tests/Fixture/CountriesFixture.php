<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CountriesFixture
 */
class CountriesFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'iso' => '',
                'name' => 'Lorem ipsum dolor sit amet',
                'nicename' => 'Lorem ipsum dolor sit amet',
                'iso3' => '',
                'numcode' => 1,
                'phonecode' => 1,
            ],
        ];
        parent::init();
    }
}
