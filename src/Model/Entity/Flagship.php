<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Flagship Entity
 *
 * @property int $id
 * @property string $tbl_name
 * @property string $fld_name
 * @property int $order_index
 * @property string $title
 * @property bool $searchable
 * @property bool $exportable
 * @property bool $viewable
 * @property string|null $format
 * @property string|null $lists
 * @property int $width
 */
class Flagship extends Entity
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
        'tbl_name' => true,
        'fld_name' => true,
        'order_index' => true,
        'title' => true,
        'searchable' => true,
        'exportable' => true,
        'viewable' => true,
        'format' => true,
        'lists' => true,
        'width' => true,
    ];
}
