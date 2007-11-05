<?php

require_once dirname(__FILE__) . '/../../../config.php';
require_once LIB_PATH . '/Swift/ComponentSpec.php';
require_once LIB_PATH . '/Swift/ComponentFactory.php';

abstract class Swift_ComponentSpecFinder_AbstractSpecFinderTest
  extends UnitTestCase
{
  
  protected $_finder;
  protected $_factory;
  
  public function setUp()
  {
    $this->_finder = $this->getFinder();
    $this->_factory = $this->getFactory();
  }
  
  abstract public function getFactory();
  
  abstract public function getFinder();
  
  public function testBasicSpecFinding()
  {
    $spec = $this->_finder->findSpecFor('empty', $this->_factory);
    
    $this->assertIsA($spec, 'Swift_ComponentSpec');
    $this->assertEqual('EmptyClass', $spec->getClassName());
  }
  
  public function testSingletonSpecFinding()
  {
    $spec = $this->_finder->findSpecFor('singletonComponent', $this->_factory);
    
    $this->assertIsA($spec, 'Swift_ComponentSpec');
    $this->assertEqual('stdClass', $spec->getClassName());
    $this->assertTrue($spec->isSingleton(),
      'Specification should be for a singleton');
  }
  
  public function testSetterBasedInjectionSpecFinding()
  {
    $spec = $this->_finder->findSpecFor('setterBased', $this->_factory);
    
    $this->assertIsA($spec, 'Swift_ComponentSpec');
    $this->assertEqual('SetterInjectionClass', $spec->getClassName());
    $prop1 = $spec->getProperty('prop1');
    $this->assertTrue(is_array($prop1), 'Property prop1 should be a collection');
    $this->assertIsA($prop1[0], 'Swift_ComponentReference');
    $this->assertEqual('empty', $prop1[0]->getComponentName());
    $this->assertIsA($prop1[1], 'Swift_ComponentReference');
    $this->assertEqual('singletonComponent', $prop1[1]->getComponentName());
    $this->assertEqual('test', $spec->getProperty('prop2'));
  }
  
  public function testConstructorBasedInjectionSpecFinding()
  {
    $spec = $this->_finder->findSpecFor('constructorBased', $this->_factory);
    
    $this->assertIsA($spec, 'Swift_ComponentSpec');
    $this->assertEqual('ConstructorInjectionClass', $spec->getClassName());
    $constructorArgs = $spec->getConstructorArgs();
    $this->assertTrue(is_array($constructorArgs),
      'Constructor arguments should be an array');
    $this->assertEqual('foo', $constructorArgs[0]);
    $this->assertTrue(is_array($constructorArgs[1]),
      'Argument 2 in constructor should be a collection');
    $this->assertEqual('bar', $constructorArgs[1][0]);
    $this->assertEqual('test', $constructorArgs[1][1]);
  }
  
  public function testNullIsReturnedOnFailure()
  {
     $this->assertNull($this->_finder->findSpecFor('nothing', $this->_factory));
  }
  
}
