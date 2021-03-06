<?php

namespace Redjik\WikiBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'pages' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.src.Redjik.WikiBundle.Model.map
 */
class PagesTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Redjik.WikiBundle.Model.map.PagesTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('pages');
        $this->setPhpName('Pages');
        $this->setClassname('Redjik\\WikiBundle\\Model\\Pages');
        $this->setPackage('src.Redjik.WikiBundle.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('title', 'Title', 'VARCHAR', true, 255, null);
        $this->addColumn('text', 'Text', 'LONGVARCHAR', true, null, null);
        $this->addColumn('alias', 'Alias', 'VARCHAR', true, 255, null);
        $this->addColumn('fullpath', 'Fullpath', 'VARCHAR', true, 255, null);
        $this->addForeignKey('parent', 'Parent', 'INTEGER', 'pages', 'id', false, 10, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('PagesRelatedByParent', 'Redjik\\WikiBundle\\Model\\Pages', RelationMap::MANY_TO_ONE, array('parent' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('PagesRelatedById', 'Redjik\\WikiBundle\\Model\\Pages', RelationMap::ONE_TO_MANY, array('id' => 'parent', ), 'CASCADE', 'CASCADE', 'PagessRelatedById');
    } // buildRelations()

} // PagesTableMap
