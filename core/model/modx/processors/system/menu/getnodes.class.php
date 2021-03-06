<?php
/**
 * Get the menu items, in node format
 *
 * @param string $id The parent ID
 * @param integer $start (optional) The record to start at. Defaults to 0.
 * @param integer $limit (optional) The number of records to limit to. Defaults
 * to 10.
 *
 * @package modx
 * @subpackage processors.system.menu
 */

class modMenuGetNodesProcessor extends modObjectGetListProcessor {
    public $classKey = 'modMenu';
    public $objectType = 'menu';
    public $primaryKeyField = 'text';
    public $languageTopics = array('action','menu','topmenu');
    public $permission = 'menus';
    public $defaultSortField = 'menuindex';

    /**
     * {@inheritDoc}
     * @return mixed
     */
    public function initialize() {
        $this->setDefaultProperties(array(
            'limit' => 10,
            'id' => '',
        ));
        $id = $this->getProperty('id');
        $this->setProperty('id', str_replace('n_','',$id));

        return parent::initialize();
    }

    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $c->leftJoin($this->classKey,'Children');
        $c->where(array(
            $this->classKey.'.parent' => $this->getProperty('id'),
        ));
        $c->groupby($this->classKey.'.text');

        return parent::prepareQueryBeforeCount($c);
    }

    public function prepareQueryAfterCount(xPDOQuery $c) {
        $c->select($this->modx->getSelectColumns('modMenu','modMenu'));
        $c->select(array(
            'COUNT(Children.text) AS childrenCount'
        ));

        return $c;
    }

    public function prepareRow(xPDOObject $object) {
        $controller = $object->get('action');
        $namespace = $object->get('namespace');
        if (!in_array($namespace, array('core', '', null))) {
            $this->modx->lexicon->load($namespace . ':default');
        }
        $text = $this->modx->lexicon($object->get('text'));

        $objectArray = array(
            'text' => $text.($controller != '' ? ' <i>('.$namespace.':'.$controller.')</i>' : ''),
            'id' => 'n_'.$object->get('text'),
            'cls' => 'icon-menu',
            'iconCls' => 'icon icon-' . ( $object->get('childrenCount') > 0 ? ( $object->get('parent') === '' ? 'navicon' : 'folder' ) : 'terminal' ),
            'type' => 'menu',
            'pk' => $object->get('text'),
            'leaf' => $object->get('childrenCount') > 0 ? false : true,
            'data' => $object->toArray(),
        );

        return $objectArray;
    }

    public function outputArray(array $array,$count = false) {
        return $this->modx->toJSON($array);
    }

}
return 'modMenuGetNodesProcessor';