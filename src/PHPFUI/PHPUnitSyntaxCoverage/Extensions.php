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
	private $classes = [];

	private $currentNamespace = '';

	public function enterNode(\PhpParser\Node $node) : void
		{
		if ($node instanceof \PhpParser\Node\Stmt\Namespace_)
			{
			$this->currentNamespace = \implode('\\', $node->name->parts);
			}
		elseif ($node instanceof \PhpParser\Node\Stmt\Class_ && $node->name)
			{
			$this->classes[] = $this->currentNamespace ? $this->currentNamespace . '\\' . $node->name->name : $node->name->name;
			}
		}

	public function getNamespace() : string
		{
		return $this->currentNamespace;
		}

	public function getClasses() : array
		{
		return $this->classes;
		}
	}

class Extensions extends \PHPUnit\Framework\TestCase implements \PHPUnit\Runner\Hook
	{
	private static $parser = null;

	private $skipDirectories = [];

	private $skipNamespaces = [];

	private $skipNamespaceTest = false;

	private $traverser;

	private $classFinder;

	public static function setUpBeforeClass() : void
		{
		$factory = new \PhpParser\ParserFactory();
		self::$parser = $factory->create($_ENV[__CLASS__ . '_parser_type'] ?? \PhpParser\ParserFactory::PREFER_PHP7);
		}

  protected function setUp() : void
		{
		$this->traverser = new \PhpParser\NodeTraverser();
		$this->classFinder = new ClassFinder();
		}

	protected function tearDown() : void
		{
		$this->traverser = null;
		$this->classFinder = null;
		}

	/**
	 * Assert a string containing valid PHP will parse.
	 *
	 * Important: Any classes defined in this code will not be seen by the autoloader, as it only exists in this string.
	 */
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

		$this->traverser->addVisitor($this->classFinder);

		$this->traverser->traverse($ast);

		foreach ($this->classFinder->getClasses() as $class)
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
	 * Exclude any file with this $directory string in the path.
	 *
	 * Only a simple stripos is used to match anything in the file name.
	 *
	 * You can add multiple skips.
	 */
	public function addSkipDirectory(string $directory) : self
		{
		$this->skipDirectories[] = $directory;

		return $this;
		}

	/**
	 * Skip namespace testing
	 */
	public function skipNamespaceTesting() : self
		{
		$this->skipNamespaceTest = true;

		return $this;
		}

	/**
	 * Exclude namespace from namespace testing
	 *
	 * You can add multiple namespaces to skip.
	 */
	public function addSkipNamespace(string $namespace) : self
		{
		$this->skipNamespaces[] = $namespace;

		return $this;
		}

	/**
	 * Validate all files in a directory.  Recursive and only looks at .php files by default.
	 */
	public function assertValidPHPDirectory(string $directory, string $message = '', bool $recurseSubdirectories = true, array $extensions = ['.php']) : void
		{
		if ($recurseSubdirectories)
			{
			$iterator = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
				\RecursiveIteratorIterator::SELF_FIRST
			);
			}
		else
			{
			$iterator = new \DirectoryIterator($directory);
			}
		$exts = \array_flip($extensions);

		foreach ($iterator as $item)
			{
			$type = $item->getType();

			if ('file' == $type)
				{
				$file = $item->getPathname();
				$ext = \strrchr($file, '.');

				if ($ext && isset($exts[$ext]))
					{
					$skip = false;

					foreach ($this->skipDirectories as $directory)
						{
						if (false !== \stripos($file, $directory))
							{
							$skip = true;

							break;
							}
						}

					if (! $skip)
						{
						$this->setup();
						$this->assertValidPHPFile($file, $message . "\nFile: " . $file);
						$this->tearDown();
						}
					}
				}
			}
		}

	/**
	 * Test a specific file
	 */
	public function assertValidPHPFile(string $fileName, string $message = '') : void
		{
		$this->assertFileExists($fileName, $message);

		$code = \file_get_contents($fileName);

		$this->assertValidPHP($code, $message);

		if (! $this->skipNamespaceTest)
			{
			$namespace = $this->classFinder->getNamespace();

			if (! \in_array($namespace, $this->skipNamespaces))
				{
				// assert namespace is correct
				$fileName = \str_replace('/', '\\', $fileName);
				$this->assertStringContainsString($namespace . '\\', $fileName, "Namespace {$namespace} not found in file path {$fileName}");
				}
			}
		}
	}
