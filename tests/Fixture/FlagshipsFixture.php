<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FlagshipsFixture
 */
class FlagshipsFixture extends TestFixture
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
                'tbl_name' => 'Lorem ipsum dolor sit amet',
                'fld_name' => 'Lorem ipsum dolor sit amet',
                'order_index' => 1,
                'title' => 'Lorem ipsum dolor sit amet',
                'searchable' => 1,
                'reference' => 1,
                'exportable' => 1,
                'viewable' => 1,
                'format' => 'Lorem ipsum dolor sit amet',
                'boolean_yes' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'boolean_no' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'lists' => 'Lorem ipsum dolor sit amet',
                'width' => 1,
                'contains' => 'Lorem ipsum dolor sit amet',
                'contains_field' => 'Lorem ipsum dolor sit amet',
            ],
        ];
        parent::init();
    }
}
