<?php
/**
 * @package modx
 * @subpackage processors.element
 */
class modElementGetInsertProperties extends modProcessor {
    /** @var modElement $element */
    public $element;
    
    public function checkPermissions() {
        return $this->modx->hasPermission('element_tree');
    }
    public function getLanguageTopics() {
        return array('element','propertyset');
    }

    public function initialize() {
        $this->setDefaultProperties(array(
            'classKey' => 'modSnippet',
            'pk' => false,
        ));

        $this->element = $this->modx->getObject($this->getProperty('classKey'),$this->getProperty('pk'));
        if (empty($this->element)) return $this->modx->lexicon('element_err_nf');
        return true;
    }

    public function process() {
        $properties = $this->getElementProperties();
        $list = array();
        if (!empty($properties) && is_array($properties)) {
            foreach ($properties as $key => $property) {
                $propertyArray = $this->prepareProperty($key,$property);
                if (!empty($propertyArray)) {
                    $list[] = $propertyArray;
                }
            }
        }

        return $this->toJSON($list);
    }

    /**
     * Get the properties for the element
     * @return array
     */
    public function getElementProperties() {
        $properties = $this->element->get('properties');
        $propertySet = $this->getProperty('propertySet');
        
        if (!empty($propertySet)) {
            /** @var modPropertySet $set */
            $set = $this->modx->getObject('modPropertySet',$propertySet);
            if ($set) {
                $setProperties = $set->get('properties');
                if (is_array($setProperties) && !empty($setProperties)) {
                    $properties = array_merge($properties,$setProperties);
                }
            }
        }
        return $properties;
    }

    /**
     * Prepare the property array for property insertion
     * 
     * @param string $key
     * @param array $property
     * @return array
     */
    public function prepareProperty($key,array $property) {
        $xtype = 'textfield';
        $desc = $property['desc_trans'];
        if (!empty($property['lexicon'])) {
            $this->modx->lexicon->load($property['lexicon']);
        }

        if (is_array($property)) {
            $v = $property['value'];
            $xtype = $property['type'];
        } else { $v = $property; }

        $propertyArray = array();
        $listener = array(
            'fn' => 'function() { Ext.getCmp(\'modx-window-insert-element\').changeProp(\''.$key.'\'); }',
        );
        switch ($xtype) {
            case 'list':
            case 'combo':
                $data = array();
                foreach ($property['options'] as $option) {
                    if (empty($property['text']) && !empty($property['name'])) $property['text'] = $property['name'];
                    $text = !empty($property['lexicon']) ? $this->modx->lexicon($option['text']) : $option['text'];
                    $data[] = array($option['value'],$text);
                }
                $propertyArray = array(
                    'xtype' => 'combo',
                    'fieldLabel' => $key,
                    'description' => $desc,
                    'name' => $key,
                    'value' => $v,
                    'id' => 'modx-iprop-'.$key,
                    'listeners' => array('select' => $listener),
                    'hiddenName' => $key,
                    'displayField' => 'd',
                    'valueField' => 'v',
                    'mode' => 'local',
                    'editable' => false,
                    'forceSelection' => true,
                    'typeAhead' => false,
                    'triggerAction' => 'all',
                    'store' => $data,
                );
                break;
            case 'boolean':
            case 'modx-combo-boolean':
            case 'combo-boolean':
                $propertyArray = array(
                    'xtype' => 'modx-combo-boolean',
                    'fieldLabel' => $key,
                    'description' => $desc,
                    'name' => $key,
                    'value' => $v,
                    'id' => 'modx-iprop-'.$key,
                    'listeners' => array('select' => $listener),
                );
                break;
            case 'date':
            case 'datefield':
                $propertyArray = array(
                    'xtype' => 'datefield',
                    'fieldLabel' => $key,
                    'description' => $desc,
                    'name' => $key,
                    'value' => $v,
                    'width' => 175,
                    'id' => 'modx-iprop-'.$key,
                    'listeners' => array('change' => $listener),
                );
                break;
            case 'textarea':
                $propertyArray = array(
                    'xtype' => 'textarea',
                    'fieldLabel' => $key,
                    'description' => $desc,
                    'name' => $key,
                    'value' => $v,
                    'width' => 300,
                    'grow' => true,
                    'id' => 'modx-iprop-'.$key,
                    'listeners' => array('change' => $listener),
                );
                break;
            default:
                $propertyArray = array(
                    'xtype' => 'textfield',
                    'fieldLabel' => $key,
                    'description' => $desc,
                    'name' => $key,
                    'value' => $v,
                    'width' => 300,
                    'id' => 'modx-iprop-'.$key,
                    'listeners' => array('change' => $listener),
                );
                break;
        }
        return $propertyArray;
    }
    
}
return 'modElementGetInsertProperties';