# PHPUnitSyntaxCoverage [![Tests](https://github.com/phpfui/PHPUnitSyntaxCoverage/actions/workflows/tests.yml/badge.svg)](https://github.com/phpfui/PHPUnitSyntaxCoverage/actions?query=workflow%3Atests) [![Latest Packagist release](https://img.shields.io/packagist/v/phpfui/phpunit-syntax-coverage.svg)](https://packagist.org/packages/phpfui/phpunit-syntax-coverage)

## PHPUnit Extension for complete PHP Syntax Code Coverage

This package will checks for easy to miss syntax errors in all your PHP code. It will also check all your classes to see if they are loadable, but without actually instantiating the class.

Often we accidently check in code with easily detectable syntax errors, but unless the file or class is actually loaded by PHP, we might not see the error. Often the file or class is syntaxically correct, but a method signature may not match an updated class or vendor library. Normally this would only be detectable at run time, but with **PHPUnitSyntaxCoverage**, you can make sure all files and classes are checked.

PHPUnitSyntaxCoverage uses [PhpParser](https://github.com/nikic/PHP-Parser) to check for basic syntax errors. It then uses [ReflectionClass](https://www.php.net/manual/en/class.reflectionclass.php) to load any classes that are found in the source without instantiating them.  This will find additional errors (such as missing or changed base classes from a package update).

# Requirements
- Modern versions of PHP and PHPUnit
- Correctly configured autoloading

## Installation
```
composer require phpfui/phpunit-syntax-coverage
```

## Usage
Extend your unit tests from \PHPFUI\PHPUnitSyntaxCoverage\Extensions
```php
class UnitTest extends \PHPFUI\PHPUnitSyntaxCoverage\Extensions
	{
	public function testProjectSyntax()
		{
		$this->addSkipDirectory(__DIR__ . '/../App/Examples');
		$this->assertValidPHPDirectory(__DIR__ . '/../App', 'App directory has an error');
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

Use **addSkipDirectory** to add simple case insensitive file matching to skip specific directories / files.

## Autoloading
You must make sure autoloading is correctly configured for all classes.  This means you can't pass references to classes that will not resolve correctly in your source. Use **addSkipDirectory** if you have test code that may not validate correctly.

## Namespace Testing
The **assertValidPHPFile** and **assertValidPHPDirectory** asserts will test for the proper namespace in the file path (for PSR-0 autoloading and fully pathed PSR-4 autoloading), but you can turn off namespace testing with **skipNamespaceTesting** or exclude a specific namespace tests with **addSkipNamespace**.

## PHP Version
While this library only supports PHP 7.4 and higher, you can create a project and point it to PHP 5.2 or higher. The default is to prefer PHP 7 code, but to prefer or only parse PHP 5, configure phpunit.xml(.dist) with

~~~xml
	<php>
		<env name="PHPFUI\PHPUnitSyntaxCoverage\Extensions_parser_type" value="X"/>
	</php>
~~~
Where X is one of the following **numbers**:
1. Prefer PHP 7
2. Prefer PHP 5
3. Only PHP 7
4. Only PHP 5

## Examples
See [examples](https://github.com/phpfui/PHPUnitSyntaxCoverage/blob/master/tests/UnitTest.php)

## Full Class Documentation
[PHPFUI/InstaDoc](http://phpfui.com/?n=PHPFUI\PHPUnitSyntaxCoverage)

## License
PHPFUI is distributed under the MIT License.

