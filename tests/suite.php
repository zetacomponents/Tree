<?php
/**
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Tree
 * @subpackage Tests
 */

/**
 * Require the tests
 */
require_once 'tree.php';
require_once 'tree_node.php';
require_once 'tree_node_list.php';
require_once 'tree_node_list_iterator.php';
require_once 'memory_store.php';
require_once 'visitor.php';
require_once 'memory_tree.php';
require_once 'xml_tree.php';
require_once 'db_materialized_path_tree.php';
require_once 'db_nested_set_tree.php';
require_once 'db_parent_child_tree.php';

/**
 * @package Tree
 * @subpackage Tests
 */
class ezcTreeSuite extends PHPUnit_Framework_TestSuite
{
    public function __construct()
    {
        parent::__construct();
        $this->setName("Tree");

        $this->addTest( ezcTreeNodeTest::suite() );
        $this->addTest( ezcTreeNodeListTest::suite() );
        $this->addTest( ezcTreeNodeListIteratorTest::suite() );
        $this->addTest( ezcTreeMemoryStoreTest::suite() );
        $this->addTest( ezcTreeVisitorTest::suite() );
        $this->addTest( ezcTreeMemoryTest::suite() );
        $this->addTest( ezcTreeXmlTest::suite() );
        $this->addTest( ezcTreeDbMaterializedPathTest::suite() );
        $this->addTest( ezcTreeDbNestedSetTest::suite() );
        $this->addTest( ezcTreeDbParentChildTest::suite() );
    }

    public static function suite()
    {
        return new ezcTreeSuite();
    }
}

?>
