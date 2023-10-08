<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Template Entity
 *
 * @property int $id
 * @property string $name
 * @property string $language
 * @property string $status
 * @property string|null $template_details
 * @property string $category
 * @property \Cake\I18n\FrozenTime $created
 *
 * @property \App\Model\Entity\Campaign[] $campaigns
 */
class Template extends Entity
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
        'name' => true,
        'language' => true,
        'status' => true,
        'template_details' => true,
        'category' => true,
        'created' => true,
        'campaigns' => true,
    ];
}
