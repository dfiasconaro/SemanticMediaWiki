<?php

namespace SMW\Tests;

use SMW\NamespaceManager;
use SMW\Tests\TestEnvironment;
use SMW\ExtraneousLanguage;

/**
 * @covers \SMW\NamespaceManager
 * @group semantic-mediawiki
 *
 * @license GNU GPL v2+
 * @since 1.9
 *
 * @author mwjames
 */
class NamespaceManagerTest extends \PHPUnit_Framework_TestCase {

	private $testEnvironment;
	private $extraneousLanguage;
	private $default;

	protected function setUp() {
		$this->testEnvironment = new TestEnvironment();

		$this->extraneousLanguage = $this->getMockBuilder( '\SMW\ExtraneousLanguage' )
			->disableOriginalConstructor()
			->getMock();

		$this->extraneousLanguage->expects( $this->any() )
			->method( 'fetchByLanguageCode' )
			->will( $this->returnSelf() );

		$this->extraneousLanguage->expects( $this->any() )
			->method( 'getNamespaces' )
			->will( $this->returnValue( array() ) );

		$this->extraneousLanguage->expects( $this->any() )
			->method( 'getNamespaceAliases' )
			->will( $this->returnValue( array() ) );

		$this->default = array(
			'smwgNamespacesWithSemanticLinks' => array(),
			'wgNamespacesWithSubpages' => array(),
			'wgExtraNamespaces'  => array(),
			'wgNamespaceAliases' => array(),
			'wgLanguageCode'     => 'en'
		);
	}

	protected function tearDown() {
		$this->testEnvironment->tearDown();
	}

	public function testCanConstruct() {

		$this->assertInstanceOf(
			'\SMW\NamespaceManager',
			new NamespaceManager( $test, $this->extraneousLanguage )
		);
	}


	public function testExecutionWithIncompleteConfiguration() {

		$test = $this->default + array(
				'wgExtraNamespaces'  => '',
				'wgNamespaceAliases' => ''
			);

		$instance = new NamespaceManager( $test, $this->extraneousLanguage );
		$instance->init();

		$this->assertNotEmpty(
			$test
		);
	}

	public function testGetCanonicalNames() {

		$this->testEnvironment->addConfiguration(
			'smwgHistoricTypeNamespace',
			false
		);

		$result = NamespaceManager::getCanonicalNames();

		$this->assertInternalType(
			'array',
			$result
		);

		$this->assertCount(
			6,
			$result
		);
	}

	public function testGetCanonicalNamesWithTypeNamespace() {

		$this->testEnvironment->addConfiguration(
			'smwgHistoricTypeNamespace',
			true
		);

		$result = NamespaceManager::getCanonicalNames();

		$this->assertInternalType(
			'array',
			$result
		);

		$this->assertCount(
			6,
			$result
		);
	}

	public function testBuildNamespaceIndex() {
		$this->assertInternalType(
			'array',
			NamespaceManager::buildNamespaceIndex( 100 )
		);
	}

	public function testInitCustomNamespace() {

		$test = array(
			'wgLanguageCode' => 'en'
		);

		NamespaceManager::initCustomNamespace( $test );

		$this->assertNotEmpty( $test );
		$this->assertEquals(
			100,
			$test['smwgNamespaceIndex']
		);
	}

}
