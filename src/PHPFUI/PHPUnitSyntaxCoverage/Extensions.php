<?php

/**
 * This file is part of the PHPFUI/HTMLUnitTester package
 *
 * (c) Bruce Wells
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source
 * code
 */

namespace PHPFUI\PHPUnitSyntaxCoverage;

class ClassFinder extends \PhpParser\NodeVisitorAbstract
	{

	private array $classes = [];
	private string $currentNamespace = '';

	public function enterNode(\PhpParser\Node $node) : void
		{
		if ($node instanceof \PhpParser\Node\Stmt\Namespace_)
			{
			$this->currentNamespace = implode('\\', $node->name->parts);
			}
		elseif ($node instanceof \PhpParser\Node\Stmt\Class_)
			{
			$this->classes[] = $this->currentNamespace ? $this->currentNamespace . '\\' . $node->name->name : $node->name->name;
			}
		}

	public function getClasses() : array
		{
		return $this->classes;
		}

	}

class Extensions extends \PHPUnit\Framework\TestCase implements \PHPUnit\Runner\Hook
	{

	private static \PhpParser\Parser $parser;

	public static function setUpBeforeClass() : void
		{
		$factory = new \PhpParser\ParserFactory();
		self::$parser = $factory->create($_ENV['parser_type'] ?? \PhpParser\ParserFactory::PREFER_PHP7);
		}

	public function assertValidPHP(string $code, string $message = '') : void
		{
		$this->assertNotEmpty($code, 'Empty PHP file. ' . $message);

		try
			{
			$ast = self::$parser->parse($code);
			}
		catch (\Throwable $e)
			{
			throw new Exception($message . "\n" . $e->getMessage());
			}

		$this->assertNotEmpty($ast, 'Empty Abstract Syntax tree. ' . $message);

		$traverser = new \PhpParser\NodeTraverser();
		$classFinder = new ClassFinder();
		$traverser->addVisitor($classFinder);

		$traverser->traverse($ast);

		foreach ($classFinder->getClasses() as $class)
			{
			try
				{
				$reflection = new \ReflectionClass($class);
				}
			catch (\Throwable $e)
				{
				throw new Exception($message . "\n" . $e->getMessage());
				}
			}
		}

	/**
	 * Validate all files in a directory.
	 */
	public function assertValidPHPDirectory(string $directory, string $message = '', bool $recurseSubdirectories = true, array $extensions = ['.php']) : void
		{
		if ($recurseSubdirectories)
			{
			$iterator = new \RecursiveIteratorIterator(
					new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
					\RecursiveIteratorIterator::SELF_FIRST);
			}
		else
			{
			$iterator = new \DirectoryIterator($directory);
			}
		$exts = array_flip($extensions);

		foreach ($iterator as $item)
			{
			if ('file' == $item->getType())
				{
				$file = $item->getPathname();
				$ext = strrchr($file, '.');

				if ($ext && isset($exts[$ext]))
					{
					$this->assertValidPHPFile($file, $message . "\nFile: " . $file);
					}
				}
			}
		}

	public function assertValidPHPFile(string $fileName, string $message = '') : void
		{
		$this->assertFileExists($fileName, $message);

		$code = file_get_contents($fileName);

		$this->assertValidPHP($code, $message);
		}

	}
