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
 * Require the test classes file
 */
require_once 'files/test_classes.php';

/**
 * @package Tree
 * @subpackage Tests
 */
class ezcTreeTest extends ezcTestCase
{
    public function testTreeFetchById()
    {
        $tree = $this->setUpTestTree();

        self::assertSame( true, $tree->nodeExists( '1' ) );

        $node8 = $tree->fetchNodeById( 8 ); // returns 8
        self::assertType( 'ezcTreeNode', $node8 );
        self::assertSame( '8', $node8->id );
        self::assertSame( 'Node 8', $node8->data );

        $node3 = $tree->fetchNodeById( '3' ); // returns 3
        self::assertType( 'ezcTreeNode', $node3 );
        self::assertSame( '3', $node3->id );
        self::assertSame( 'Node 3', $node3->data );
    }

    public function testGetUnknownProperty()
    {
        $tree = $this->setUpTestTree();

        try
        {
            $dummy = $tree->unknown;
            self::fail( "Expected exception not thrown" );
        }
        catch ( ezcBasePropertyNotFoundException $e )
        {
            self::assertSame( "No such property name 'unknown'.", $e->getMessage() );
        }
    }

    public function testSetStore()
    {
        $tree = $this->setUpTestTree();
        
        try
        {
            $tree->store = new TestTranslateDataStore;
            self::fail( "Expected exception not thrown" );
        }
        catch ( ezcBasePropertyPermissionException $e )
        {
            self::assertSame( "The property 'store' is read-only.", $e->getMessage() );
        }
    }

    public function testSetPrefetch()
    {
        $tree = $this->setUpTestTree();
        
        $tree->prefetch = true;
        self::assertSame( true, $tree->prefetch );
        
        $tree->prefetch = false;
        self::assertSame( false, $tree->prefetch );
    }

    public function testSetPrefetchWrongValue()
    {
        $tree = $this->setUpTestTree();
        
        try
        {
            $tree->prefetch = 42;
            self::fail( "Expected exception not thrown" );
        }
        catch ( ezcBaseValueException $e )
        {
            self::assertSame( "The value '42' that you were trying to assign to setting 'prefetch' is invalid. Allowed values are: boolean.", $e->getMessage() );
        }
    }

    public function testSetNodeClassName()
    {
        $tree = $this->setUpTestTree();
        
        $tree->nodeClassName = 'TestExtendedTreeNode';
        self::assertSame( 'TestExtendedTreeNode', $tree->nodeClassName );
    }

    public function testSetNodeClassNameWrongValue1()
    {
        $tree = $this->setUpTestTree();
        
        try
        {
            $tree->nodeClassName = 42;
            self::fail( "Expected exception not thrown" );
        }
        catch ( ezcBaseValueException $e )
        {
            self::assertSame( "The value '42' that you were trying to assign to setting 'nodeClassName' is invalid. Allowed values are: string that contains a class name.", $e->getMessage() );
        }
    }

    public function testSetNodeClassNameWrongValue2()
    {
        $tree = $this->setUpTestTree();
        
        try
        {
            $tree->nodeClassName = 'ezcTreeMemoryNode';
            self::fail( "Expected exception not thrown" );
        }
        catch ( ezcBaseInvalidParentClassException $e )
        {
            self::assertSame( "Class 'ezcTreeMemoryNode' does not exist, or does not inherit from the 'ezcTreeNode' class.", $e->getMessage() );
        }
    }

    public function testSetUnknownProperty()
    {
        $tree = $this->setUpTestTree();

        try
        {
            $tree->unknown = 'whatever';
            self::fail( "Expected exception not thrown" );
        }
        catch ( ezcBasePropertyNotFoundException $e )
        {
            self::assertSame( "No such property name 'unknown'.", $e->getMessage() );
        }
    }

    public function testTreeFetchByUnknownId()
    {
        $tree = $this->setUpTestTree();

        try
        {
            $node = $tree->fetchNodeById( 42 );
            self::fail( "Expected exception was not thrown." );
        }
        catch ( ezcTreeInvalidIdException $e )
        {
            self::assertSame( "The node with ID '42' could not be found.", $e->getMessage() );
        }
    }

    public function testTreeIsChildOfOnNode()
    {
        $tree = $this->setUpTestTree();

        self::assertSame( true, $tree->fetchNodeById( 2 )->isChildOf( $tree->fetchNodeById( 1 ) ) );
        self::assertSame( false, $tree->fetchNodeById( 6 )->isChildOf( $tree->fetchNodeById( 2 ) ) );
        self::assertSame( false, $tree->fetchNodeById( 8 )->isChildOf( $tree->fetchNodeById( 2 ) ) );
        self::assertSame( true, $tree->fetchNodeById( 6 )->isChildOf( $tree->fetchNodeById( 4 ) ) );
        self::assertSame( true, $tree->fetchNodeById( 7 )->isChildOf( $tree->fetchNodeById( 6 ) ) );
        self::assertSame( true, $tree->fetchNodeById( 8 )->isChildOf( $tree->fetchNodeById( 6 ) ) );
        self::assertSame( false, $tree->fetchNodeById( 7 )->isChildOf( $tree->fetchNodeById( 7 ) ) );
        self::assertSame( false, $tree->fetchNodeById( 7 )->isChildOf( $tree->fetchNodeById( 8 ) ) );
    }

    public function testTreeIsChildOfOnNodeWithInvalidNodes()
    {
        $tree = $this->setUpTestTree();

        try
        {
            self::assertSame( true, $tree->fetchNodeById( 98 )->isChildOf( $tree->fetchNodeById( 99 ) ) );
        }
        catch ( ezcTreeInvalidIdException $e )
        {
            self::assertSame( "The node with ID '98' could not be found.", $e->getMessage() );
        }
    }

    public function testTreeIsChildOfOnTree()
    {
        $tree = $this->setUpTestTree();

        self::assertSame( true, $tree->isChildOf( 2, 1 ) );
        self::assertSame( false, $tree->isChildOf( 6, 2 ) );
        self::assertSame( false, $tree->isChildOf( 8, 2 ) );
        self::assertSame( true, $tree->isChildOf( 6, 4 ) );
        self::assertSame( true, $tree->isChildOf( 7, 6 ) );
        self::assertSame( true, $tree->isChildOf( 8, 6 ) );
        self::assertSame( false, $tree->isChildOf( 7, 7 ) );
        self::assertSame( false, $tree->isChildOf( 7, 8 ) );
    }

    public function testTreeIsChildOfOnTreeWithInvalidNodes()
    {
        $tree = $this->setUpTestTree();

        try
        {
            self::assertSame( false, $tree->isChildOf( 98, 99 ) );
        }
        catch ( ezcTreeInvalidIdException $e )
        {
            self::assertSame( "The node with ID '98' could not be found.", $e->getMessage() );
        }
    }

    public function testTreeIsDecendantOfOnNode()
    {
        $tree = $this->setUpTestTree();

        self::assertSame( true, $tree->fetchNodeById( 2 )->isDecendantOf( $tree->fetchNodeById( 1 ) ) );
        self::assertSame( false, $tree->fetchNodeById( 6 )->isDecendantOf( $tree->fetchNodeById( 2 ) ) );
        self::assertSame( false, $tree->fetchNodeById( 8 )->isDecendantOf( $tree->fetchNodeById( 2 ) ) );
        self::assertSame( true, $tree->fetchNodeById( 8 )->isDecendantOf( $tree->fetchNodeById( 4 ) ) );
        self::assertSame( true, $tree->fetchNodeById( 6 )->isDecendantOf( $tree->fetchNodeById( 4 ) ) );
        self::assertSame( true, $tree->fetchNodeById( 7 )->isDecendantOf( $tree->fetchNodeById( 6 ) ) );
        self::assertSame( true, $tree->fetchNodeById( 8 )->isDecendantOf( $tree->fetchNodeById( 6 ) ) );
        self::assertSame( false, $tree->fetchNodeById( 7 )->isDecendantOf( $tree->fetchNodeById( 7 ) ) );
        self::assertSame( false, $tree->fetchNodeById( 7 )->isDecendantOf( $tree->fetchNodeById( 8 ) ) );
    }

    public function testTreeIsDecendantOfOnTree()
    {
        $tree = $this->setUpTestTree();

        self::assertSame( true, $tree->isDecendantOf( 2, 1 ) );
        self::assertSame( false, $tree->isDecendantOf( 6, 2 ) );
        self::assertSame( false, $tree->isDecendantOf( 8, 2 ) );
        self::assertSame( true, $tree->isDecendantOf( 6, 4 ) );
        self::assertSame( true, $tree->isDecendantOf( 8, 4 ) );
        self::assertSame( true, $tree->isDecendantOf( 7, 6 ) );
        self::assertSame( true, $tree->isDecendantOf( 8, 6 ) );
        self::assertSame( false, $tree->isDecendantOf( 7, 7 ) );
        self::assertSame( false, $tree->isDecendantOf( 7, 8 ) );
    }

    public function testTreeIsSiblingOfOnNode()
    {
        $tree = $this->setUpTestTree();

        self::assertSame( false, $tree->fetchNodeById( 2 )->isSiblingOf( $tree->fetchNodeById( 1 ) ) );
        self::assertSame( false, $tree->fetchNodeById( 6 )->isSiblingOf( $tree->fetchNodeById( 2 ) ) );
        self::assertSame( false, $tree->fetchNodeById( 8 )->isSiblingOf( $tree->fetchNodeById( 2 ) ) );
        self::assertSame( true, $tree->fetchNodeById( 4 )->isSiblingOf( $tree->fetchNodeById( 3 ) ) );
        self::assertSame( false, $tree->fetchNodeById( 6 )->isSiblingOf( $tree->fetchNodeById( 4 ) ) );
        self::assertSame( false, $tree->fetchNodeById( 7 )->isSiblingOf( $tree->fetchNodeById( 6 ) ) );
        self::assertSame( false, $tree->fetchNodeById( 8 )->isSiblingOf( $tree->fetchNodeById( 6 ) ) );
        self::assertSame( false, $tree->fetchNodeById( 7 )->isSiblingOf( $tree->fetchNodeById( 7 ) ) );
        self::assertSame( true, $tree->fetchNodeById( 7 )->isSiblingOf( $tree->fetchNodeById( 8 ) ) );
        self::assertSame( false, $tree->fetchNodeById( 6 )->isSiblingOf( $tree->fetchNodeById( 9 ) ) );
    }

    public function testTreeIsSiblingOfOnTree()
    {
        $tree = $this->setUpTestTree();

        self::assertSame( false, $tree->isSiblingOf( 2, 1 ) );
        self::assertSame( false, $tree->isSiblingOf( 6, 2 ) );
        self::assertSame( false, $tree->isSiblingOf( 8, 2 ) );
        self::assertSame( true, $tree->isSiblingOf( 4, 3 ) );
        self::assertSame( false, $tree->isSiblingOf( 6, 4 ) );
        self::assertSame( false, $tree->isSiblingOf( 7, 6 ) );
        self::assertSame( false, $tree->isSiblingOf( 8, 6 ) );
        self::assertSame( false, $tree->isSiblingOf( 7, 7 ) );
        self::assertSame( true, $tree->isSiblingOf( 7, 8 ) );
        self::assertSame( false, $tree->isSiblingOf( 6, 9 ) );
    }

    public function testTreeHasChildrenOnNode()
    {
        $tree = $this->setUpTestTree();

        self::assertSame( true, $tree->fetchNodeById( 1 )->hasChildNodes() );
        self::assertSame( false, $tree->fetchNodeById( 3 )->hasChildNodes() );
        self::assertSame( true, $tree->fetchNodeById( 4 )->hasChildNodes() );
        self::assertSame( false, $tree->fetchNodeById( 7 )->hasChildNodes() );
    }

    public function testTreeHasChildrenOnTree()
    {
        $tree = $this->setUpTestTree();

        self::assertSame( true, $tree->hasChildNodes( 1 ) );
        self::assertSame( false, $tree->hasChildNodes( 3 ) );
        self::assertSame( true, $tree->hasChildNodes( 4 ) );
        self::assertSame( false, $tree->hasChildNodes( 7 ) );
    }

    public function testTreeGetChildCountOnNode()
    {
        $tree = $this->setUpTestTree();

        self::assertSame( 4, $tree->fetchNodeById( 1 )->getChildCount() );
        self::assertSame( 0, $tree->fetchNodeById( 3 )->getChildCount() );
        self::assertSame( 1, $tree->fetchNodeById( 4 )->getChildCount() );
        self::assertSame( 0, $tree->fetchNodeById( 7 )->getChildCount() );
    }

    public function testTreeGetChildCountOnTree()
    {
        $tree = $this->setUpTestTree();

        self::assertSame( 4, $tree->getChildCount( 1 ) );
        self::assertSame( 0, $tree->getChildCount( 3 ) );
        self::assertSame( 1, $tree->getChildCount( 4 ) );
        self::assertSame( 0, $tree->getChildCount( 7 ) );
    }

    public function testTreeGetChildCountRecursiveOnNode()
    {
        $tree = $this->setUpTestTree();

        self::assertSame( 8, $tree->fetchNodeById( 1 )->getChildCountRecursive() );
        self::assertSame( 0, $tree->fetchNodeById( 3 )->getChildCountRecursive() );
        self::assertSame( 3, $tree->fetchNodeById( 4 )->getChildCountRecursive() );
        self::assertSame( 0, $tree->fetchNodeById( 7 )->getChildCountRecursive() );
    }

    public function testTreeGetChildCountRecursiveOnTree()
    {
        $tree = $this->setUpTestTree();

        self::assertSame( 8, $tree->getChildCountRecursive( 1 ) );
        self::assertSame( 0, $tree->getChildCountRecursive( 3 ) );
        self::assertSame( 3, $tree->getChildCountRecursive( 4 ) );
        self::assertSame( 0, $tree->getChildCountRecursive( 7 ) );
    }

    public function testTreeGetPathLengthOnNode()
    {
        $tree = $this->setUpTestTree();

        self::assertSame( 0, $tree->fetchNodeById( 1 )->getPathLength() );
        self::assertSame( 1, $tree->fetchNodeById( 2 )->getPathLength() );
        self::assertSame( 1, $tree->fetchNodeById( 4 )->getPathLength() );
        self::assertSame( 2, $tree->fetchNodeById( 6 )->getPathLength() );
        self::assertSame( 3, $tree->fetchNodeById( 7 )->getPathLength() );
    }

    public function testTreeGetPathLengthOnTree()
    {
        $tree = $this->setUpTestTree();

        self::assertSame( 0, $tree->getPathLength( 1 ) );
        self::assertSame( 1, $tree->getPathLength( 2 ) );
        self::assertSame( 1, $tree->getPathLength( 4 ) );
        self::assertSame( 2, $tree->getPathLength( 6 ) );
        self::assertSame( 3, $tree->getPathLength( 7 ) );
    }

    public function testTreeFetchSubtreeOnNode()
    {
        $tree = $this->setUpTestTree();

        $nodeList = $tree->fetchNodeById( 4 )->fetchSubtree();
        self::assertSame( 4, $nodeList->size );
        self::assertSame( '4', $nodeList[4]->id );
        self::assertSame( '6', $nodeList[6]->id );
        self::assertSame( '7', $nodeList[7]->id );
        self::assertSame( '8', $nodeList[8]->id );
    }

    public function testTreeFetchSubtreeOnTree()
    {
        $tree = $this->setUpTestTree();

        $nodeList = $tree->fetchSubtree( 4 );
        self::assertSame( 4, $nodeList->size );
        self::assertSame( '4', $nodeList[4]->id );
        self::assertSame( '6', $nodeList[6]->id );
        self::assertSame( '7', $nodeList[7]->id );
        self::assertSame( '8', $nodeList[8]->id );
    }

    public function testTreeFetchChildrenOnNode()
    {
        $tree = $this->setUpTestTree();

        $nodeList = $tree->fetchNodeById( 3 )->fetchChildren();
        self::assertSame( 0, $nodeList->size );

        $nodeList = $tree->fetchNodeById( 4 )->fetchChildren();
        self::assertSame( 1, $nodeList->size );
        self::assertSame( '6', $nodeList['6']->id );

        $nodeList = $tree->fetchNodeById( '6' )->fetchChildren();
        self::assertSame( 2, $nodeList->size );
        self::assertSame( '7', $nodeList['7']->id );
        self::assertSame( '8', $nodeList['8']->id );
    }

    public function testTreeFetchChildrenOnTree()
    {
        $tree = $this->setUpTestTree();

        $nodeList = $tree->fetchChildren( 3 );
        self::assertSame( 0, $nodeList->size );

        $nodeList = $tree->fetchChildren( 4 );
        self::assertSame( 1, $nodeList->size );
        self::assertSame( '6', $nodeList['6']->id );

        $nodeList = $tree->fetchChildren( '6' );
        self::assertSame( 2, $nodeList->size );
        self::assertSame( '7', $nodeList['7']->id );
        self::assertSame( '8', $nodeList['8']->id );
    }

    public function testTreeFetchPathOnNode()
    {
        $tree = $this->setUpTestTree();

        $nodeList = $tree->fetchNodeById( '1' )->fetchPath();
        self::assertSame( 1, $nodeList->size );
        self::assertSame( '1', $nodeList['1']->id );

        $nodeList = $tree->fetchNodeById( 8 )->fetchPath();
        self::assertSame( 4, $nodeList->size );
        self::assertSame( '1', $nodeList['1']->id );
        self::assertSame( '4', $nodeList['4']->id );
        self::assertSame( '6', $nodeList['6']->id );
        self::assertSame( '8', $nodeList['8']->id );
    }

    public function testTreeFetchPathOnTree()
    {
        $tree = $this->setUpTestTree();

        $nodeList = $tree->fetchPath( '1' );
        self::assertSame( 1, $nodeList->size );
        self::assertSame( '1', $nodeList['1']->id );

        $nodeList = $tree->fetchPath( 8 );
        self::assertSame( 4, $nodeList->size );
        self::assertSame( '1', $nodeList['1']->id );
        self::assertSame( '4', $nodeList['4']->id );
        self::assertSame( '6', $nodeList['6']->id );
        self::assertSame( '8', $nodeList['8']->id );
    }

    public function testTreeMoveNode()
    {
        $tree = $this->setUpTestTree();

        self::assertSame( false, $tree->fetchNodeById( 4 )->isChildOf( $tree->fetchNodeById( 5 ) ) );

        $tree->move( '4', '5' ); // makes node 4 a child of node 5

        self::assertSame( true, $tree->isChildOf( 4, 5 ) );

        $nodeList = $tree->fetchNodeById( 8 )->fetchPath();
        self::assertSame( 5, $nodeList->size );
        self::assertSame( '1', $nodeList[1]->id );
        self::assertSame( '5', $nodeList[5]->id );
        self::assertSame( '4', $nodeList[4]->id );
        self::assertSame( '6', $nodeList[6]->id );
        self::assertSame( '8', $nodeList[8]->id );
        self::assertSame( true, $tree->fetchNodeById( 4 )->isSiblingOf( $tree->fetchNodeById( 9 ) ) );
    }

    public function testTreeMoveNodeWithTransaction()
    {
        $tree = $this->setUpTestTree();

        $tree->beginTransaction();
        $tree->move( '4', '5' ); // makes node 4 a child of node 5

        self::assertSame( false, $tree->fetchNodeById( 4 )->isChildOf( $tree->fetchNodeById( 5 ) ) );
        self::assertSame( false, $tree->fetchNodeById( 4 )->isSiblingOf( $tree->fetchNodeById( 9 ) ) );

        $tree->commit();

        self::assertSame( true, $tree->fetchNodeById( 4 )->isChildOf( $tree->fetchNodeById( 5 ) ) );
        self::assertSame( true, $tree->fetchNodeById( 4 )->isSiblingOf( $tree->fetchNodeById( 9 ) ) );
    }

    public function testTreeDeleteNode()
    {
        $tree = $this->setUpTestTree();

        self::assertSame( true, $tree->nodeExists( 5 ) );
        $tree->delete( '5' );
        self::assertSame( false, $tree->nodeExists( 5 ) );
        self::assertSame( 3, $tree->fetchNodeById( 1 )->getChildCount() );

        self::assertSame( true, $tree->nodeExists( '4' ) );
        self::assertSame( true, $tree->nodeExists( '6' ) );
        self::assertSame( true, $tree->nodeExists( '8' ) );
        $tree->delete( '4' );
        self::assertSame( false, $tree->nodeExists( '4' ) );
        self::assertSame( false, $tree->nodeExists( '6' ) );
        self::assertSame( false, $tree->nodeExists( '7' ) );
        self::assertSame( false, $tree->nodeExists( '8' ) );
        self::assertSame( 2, $tree->fetchNodeById( '1' )->getChildCount() );
    }

    public function testTreeDeleteNodeWithTransaction()
    {
        $tree = $this->setUpTestTree();

        self::assertSame( true, $tree->nodeExists( 5 ) );

        $tree->beginTransaction();
        $tree->delete( '5' );
        $tree->delete( '4' );

        self::assertSame( true, $tree->nodeExists( '4' ) );
        self::assertSame( true, $tree->nodeExists( '6' ) );
        self::assertSame( true, $tree->nodeExists( '8' ) );
        self::assertSame( 4, $tree->getChildCount( '1' ) );

        $tree->commit();

        self::assertSame( false, $tree->nodeExists( '4' ) );
        self::assertSame( false, $tree->nodeExists( '5' ) );
        self::assertSame( false, $tree->nodeExists( '6' ) );
        self::assertSame( false, $tree->nodeExists( '7' ) );
        self::assertSame( false, $tree->nodeExists( '8' ) );
        self::assertSame( 2, $tree->getChildCount( '1' ) );
    }

    public function testTreeNodeListIterator()
    {
        $tree = $this->setUpTestTree();

        $nodeList = $tree->fetchNodeById( 4 )->fetchSubtree();
        self::assertSame( 4, $nodeList->size );

        foreach ( new ezcTreeNodeListIterator( $tree, $nodeList ) as $id => $data )
        {
            self::assertSame( "Node $id", $data );
        }
    }
}

?>
