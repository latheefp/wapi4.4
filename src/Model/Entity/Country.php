<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Country Entity
 *
 * @property int $id
 * @property string $iso
 * @property string $name
 * @property string $nicename
 * @property string|null $iso3
 * @property int|null $numcode
 * @property int $phonecode
 */
class Country extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'iso' => true,
        'name' => true,
        'nicename' => true,
        'iso3' => true,
        'numcode' => true,
        'phonecode' => true,
    ];
    
    protected function _getPhonecountry()
    {
        return $this->name . '  ' . $this->phonecode;
    }
    
     protected $_virtual = ['name'. ' ' . 'phonecode'];
}
