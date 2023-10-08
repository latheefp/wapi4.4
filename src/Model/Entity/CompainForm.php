<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CompainForm Entity
 *
 * @property int $id
 * @property int $compaign_id
 * @property string|null $field_type
 * @property string|null $field_name
 * @property string|null $field_value
 * @property string|null $language
 * @property string|null $file_type
 * @property string|null $file_path
 * @property int|null $file_size
 *
 * @property \App\Model\Entity\Compaign $compaign
 */
class CompainForm extends Entity
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
        'compaign_id' => true,
        'field_type' => true,
        'field_name' => true,
        'field_value' => true,
        'language' => true,
        'file_type' => true,
        'file_path' => true,
        'file_size' => true,
        'compaign' => true,
    ];
}
