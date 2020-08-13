# PHPUnitSyntaxCoverage [![Build Status](https://travis-ci.org/phpfui/PHPUnitSyntaxCoverage.png?branch=master)](https://travis-ci.org/phpfui/PHPUnitSyntaxCoverage) [![Latest Packagist release](https://img.shields.io/packagist/v/phpfui/PHPUnitSyntaxCoverage.svg)](https://packagist.org/packages/phpfui/PHPUnitSyntaxCoverage)

## PHPUnit Extension for complete PHP Syntax Code Coverage

This package will checks for easy to miss syntax errors in all your PHP code. It will also check all your classes to see if they are loadable, but without actually instantiating the class.

Often we accidently check in code with easily detectable syntax errors, but unless the file or class is actually loaded by PHP, we might not see the error. Often the file or class is syntaxically correct, but a method signature may not match an updated vendor library. Normally this would only be detectable at run time, but with PHPUnitSyntaxCoverage, you can make sure all files and classes are checked.

PHPUnitSyntaxCoverage uses [PhpParser](https://github.com/nikic/PHP-Parser) to check for basic syntax errors. It then uses [ReflectionClass](https://www.php.net/manual/en/class.reflectionclass.php) to load any classes that are found in the source without instantiating them.  This will find additional errors (such as missing or changed base classes from a package update).

# Requirements
- PHP 7.1 or higher
- PHPUnit 7 or higher

## Installation
```
composer require phpfui/phpunit-syntax-coverage
```

## Usage
Extend your unit tests from \PHPFUI\PHPUnitSyntaxCoverage\Extensions
```php
class UnitTest extends \PHPFUI\PHPUnitSyntaxCoverage\Extensions
	{
	public function testValidPHP()
		{
		$this->assertValidPHPFile(__FILE__, 'Unit Test file not valid');
		$this->assertValidPHP('<?php echo "hi";');
		}
	}
```
You can use any of the following asserts:
- assertValidPHP(string $code, string $message = '')
- assertValidPHPDirectory(string $directory, string $message = '', bool $recurseSubdirectories = true, array $extensions = ['.php'])
- assertValidPHPFile(string $fileName, string $message = '')

## Directory Testing
Instead of file by file testing, use **assertValidPHPDirectory** to test an entire directory. Any files added to the directory will be automatically tested.
```php
	$this->assertValidPHPDirectory(__DIR__ . '/../App', 'App directory error');
```
The error message will include the offending file name and line number.

## Examples
See [examples](https://github.com/phpfui/PHPUnitSyntaxCoverage/blob/master/tests/UnitTest.php)

## Documentation

Full documentation at [PHPFUI\PHPUnitSyntaxCoverage](http://phpfui.com/?p=d&n=PHPFUI%5CPHPUnitSyntaxCoverage)
