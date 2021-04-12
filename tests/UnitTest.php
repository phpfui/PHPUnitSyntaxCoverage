<?php

/**
 * This file is part of the PHPFUI package
 *
 * (c) Bruce Wells
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source
 * code
 */
class UnitTest extends \PHPFUI\PHPUnitSyntaxCoverage\Extensions
	{
	public function testDirectory() : void
		{
		$this->assertValidPHPDirectory(__DIR__, 'Test directory is not valid', false);
		$this->assertValidPHPDirectory(__DIR__ . '/../src', 'src directory is not valid');

		$this->expectException(\PHPFUI\PHPUnitSyntaxCoverage\Exception::class);
		$this->assertValidPHPDirectory(__DIR__ . '/badPHP', 'badPHP directory is valid (should not be)');
		}

	public function testInvalidExtendedClassFile() : void
		{
		$file = __DIR__ . '/badPHP/BaseClass.php';
		$this->assertValidPHPFile($file, $file . ' is invalid');

		$file = __DIR__ . '/badPHP/BadClass.php';
		$this->expectException(\PHPFUI\PHPUnitSyntaxCoverage\Exception::class);
		$this->assertValidPHPFile($file, $file . ' is valid (should not be)');
		}

	public function testValidPHP() : void
		{
		$this->assertValidPHP('<?php namespace Testing; echo "hello world";', 'Provided PHP string is not valid PHP');

		$this->expectException(\PHPFUI\PHPUnitSyntaxCoverage\Exception::class);
		$this->assertValidPHP('<?php namespace Testing; class TestClass { public function __construct(){}}', 'Provided PHP string with class is not valid PHP due to namespace lookup');
		}

	public function testValidPHPFile() : void
		{
		$this->assertValidPHPFile(__FILE__, 'Test file is not valid');
		}

	public function testVendorDirectory() : void
		{
		$this->skipNamespaceTesting();
		// Sloppy coding from various packages causes us to have to skip directories.  If only they used PHPUnitSyntaxCoverage they would have detected these issues!
		$this->addSkipDirectory('package-versions-deprecated');	// phpunit
		$this->addSkipDirectory('php-cs-fixer');
		$this->addSkipDirectory('DependencyInjection'); // Symfony\Component\DependencyInjection
		$this->addSkipDirectory('path-util');	// Webmozart\PathUtil
		$this->assertValidPHPDirectory(__DIR__ . '/../vendor', 'Vendor directory is not valid');
		}
	}
