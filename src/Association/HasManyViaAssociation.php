<?php

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseStructure\AssociationInterface;
use ActiveCollab\DatabaseStructure\StructureInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use Doctrine\Common\Inflector\Inflector;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Association
 */
class HasManyViaAssociation extends HasManyAssociation implements AssociationInterface
{
    /**
     * @var string
     */
    private $intermediary_type_name;

    /**
     * @param string $name
     * @param string $intermediary_type_name
     * @param string $target_type_name
     */
    public function __construct($name, $intermediary_type_name, $target_type_name = null)
    {
        parent::__construct($name, $target_type_name);

        if (empty($intermediary_type_name)) {
            throw new InvalidArgumentException("Value '$intermediary_type_name' is not a valid type name");
        }

        $this->intermediary_type_name = $intermediary_type_name;
    }

    /**
     * @param StructureInterface $structure
     * @param TypeInterface      $source_type
     * @param TypeInterface      $target_type
     * @param string             $namespace
     * @param array              $result
     */
    protected function buildGetFinderMethod(StructureInterface $structure, TypeInterface $source_type, TypeInterface $target_type, $namespace, array &$result)
    {
        $intermediary_type = $structure->getType($this->intermediary_type_name);

        $order_by = $this->getOrderBy() ? '->orderBy(' . var_export($this->getOrderBy(), true) . ')' : '';

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * @var  \\ActiveCollab\\DatabaseObject\\Finder';
        $result[] = '     */';
        $result[] = '     private $' . $this->getFinderPropertyName() . ';';
        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Return ' . Inflector::singularize($source_type->getName()) . ' ' . $this->getName() . ' finder instance';
        $result[] = '     *';
        $result[] = '     * @return \\ActiveCollab\\DatabaseObject\\Finder';
        $result[] = '     */';
        $result[] = '    private function ' . $this->getFinderMethodName() . '()';
        $result[] = '    {';
        $result[] = '        if (empty($this->' . $this->getFinderPropertyName() . ')) {';
        $result[] = '            $this->' . $this->getFinderPropertyName() . ' = $this->pool->find(' . var_export($this->getInstanceClassFrom($namespace, $target_type), true) . ')->join(' . var_export($this->getInstanceClassFrom($namespace, $intermediary_type), true) . ')->where("`' . $intermediary_type->getTableName() . '`.`' . $this->getFkFieldNameFrom($source_type) . '` = ?", $this->getId())' . $order_by . ';';
        $result[] = '        }';
        $result[] = '        return $this->' . $this->getFinderPropertyName() . ';';
        $result[] = '    }';
    }

    /**
     * @param StructureInterface $structure
     * @param TypeInterface      $source_type
     * @param TypeInterface      $target_type
     * @param string             $namespace
     * @param array              $result
     */
    public function buildAddRelatedObjectMethod(StructureInterface $structure, TypeInterface $source_type, TypeInterface $target_type, $namespace, array &$result)
    {
        $intermediary_type = $structure->getType($this->intermediary_type_name);
        $target_instance_class = $this->getInstanceClassFrom($namespace, $target_type);
        $intermediary_instance_class = $this->getInstanceClassFrom($namespace, $intermediary_type);
        
        $longest_class_name = max(strlen($target_instance_class), 'array|null', $intermediary_instance_class);

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * @param  ' . str_pad($target_instance_class, $longest_class_name, ' ', STR_PAD_RIGHT) . ' $object_to_add';
        $result[] = '     * @param  ' . str_pad('array|null', $longest_class_name, ' ', STR_PAD_RIGHT) . ' $attributes';
        $result[] = '     * @return ' . $intermediary_instance_class;
        $result[] = '     */';
        $result[] = '    public function &add' . $this->getClassifiedSingleAssociationName() . '(' . $this->getInstanceClassFrom($namespace, $target_type) . ' $object_to_add, array $attributes = null)';
        $result[] = '    {';
        $result[] = '        if ($this->isNew()) {';
        $result[] = '            throw new \RuntimeException("' . ucfirst(Inflector::singularize($source_type->getName())) . ' needs to be saved first");';
        $result[] = '        }';
        $result[] = '        ';
        $result[] = '        if ($object_to_add->isNew()) {';
        $result[] = '            throw new \RuntimeException("' . ucfirst(Inflector::singularize($target_type->getName())) . ' needs to be saved first");';
        $result[] = '        }';
        $result[] = '        ';
        $result[] = '        $produce_attributes = [';
        $result[] = '            "' . $this->getFkFieldNameFrom($source_type) . '" => $this->getId(),';
        $result[] = '            "' . $this->getFkFieldNameFrom($target_type) . '" => $object_to_add->getId(),';
        $result[] = '        ];';
        $result[] = '        ';
        $result[] = '        if (!empty($attributes)) {';
        $result[] = '            $produce_attributes = array_merge($produce_attributes, $attributes);';
        $result[] = '        }';
        $result[] = '        ';
        $result[] = '        $this->pool->produce(' . $intermediary_instance_class . '::class, $produce_attributes);';
        $result[] = '        ';
        $result[] = '        return $this;';
        $result[] = '    }';
    }

    /**
     * @param StructureInterface $structure
     * @param TypeInterface      $source_type
     * @param TypeInterface      $target_type
     * @param string             $namespace
     * @param array              $result
     */
    public function buildRemoveRelatedObjectMethod(StructureInterface $structure, TypeInterface $source_type, TypeInterface $target_type, $namespace, array &$result)
    {

    }
}
