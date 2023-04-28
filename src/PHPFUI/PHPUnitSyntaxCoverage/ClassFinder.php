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
	/**
	 * @var array<string> $classes
	 */
	private array $classes = [];

	private string $currentNamespace = '';

	public function enterNode(int|\PhpParser\Node|null $node)
		{
		if ($node instanceof \PhpParser\Node\Stmt\Namespace_)
			{
			$this->currentNamespace = \implode('\\', $node->name->parts);
			}
		elseif ($node instanceof \PhpParser\Node\Stmt\Class_ && $node->name)
			{
			$this->classes[] = $this->currentNamespace ? $this->currentNamespace . '\\' . $node->name->name : $node->name->name;
			}

		return $node;
		}

	/**
	 * @return array<string>
	 */
	public function getClasses() : array
		{
		return $this->classes;
		}

	public function getNamespace() : string
		{
		return $this->currentNamespace;
		}
	}
