<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AccountsFixture
 */
class AccountsFixture extends TestFixture
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
                'company_name' => 'Lorem ipsum dolor sit amet',
                'Address' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'primary_contact_person' => 'Lorem ipsum dolor ',
                'primary_number' => 'Lorem ipsum ',
                'secondary_number' => 'Lorem ipsum ',
                'created' => '2025-07-21 20:30:57',
                'modified' => '2025-07-21 20:30:57',
                'user_id' => 1,
                'current_balance' => 1,
                'WBAID' => 'Lorem ipsum dolor sit amet',
                'API_VERSION' => 'Lor',
                'ACCESSTOKENVALUE' => 'Lorem ipsum dolor sit amet',
                'phone_numberId' => 'Lorem ipsum dolor sit amet',
                'def_language' => 'Lorem ipsu',
                'test_number' => 'Lorem ipsum ',
                'restricted_start_time' => '20:30:57',
                'restricted_end_time' => '20:30:57',
                'interactive_webhook' => 'Lorem ipsum dolor sit amet',
                'rcv_notification_template' => 'Lorem ipsum dolor sit amet',
                'interactive_api_key' => 'Lorem ipsum dolor sit amet',
                'interactive_menu_function' => 'Lorem ipsum dolor sit amet',
                'interactive_notification_numbers' => 'Lorem ipsum ',
                'def_isd' => 'Lore',
                'welcome_msg' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            ],
        ];
        parent::init();
    }
}
