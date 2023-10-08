<?php
//https://qiita.com/macchaka/items/e8081358ba27ebaa4e7d
namespace App\View\Helper;
use Cake\View\Helper;
class ScriptwrapHelper extends Helper {
    var $helpers = array('Html');
    protected $_scriptBlockOptions = array();

    public function scriptStart($options = array()) {
        $options += array('safe' => true, 'inline' => true);
        $this->_scriptBlockOptions = $options;
        ob_start();
        return null;
    }
    public function scriptEnd() {
        $buffer = preg_replace('/<script>(.*?)<\/script>/s', '\1', ob_get_clean());
        $options = $this->_scriptBlockOptions;
        $this->_scriptBlockOptions = array();
        return $this->Html->scriptBlock($buffer, $options);
    }
}