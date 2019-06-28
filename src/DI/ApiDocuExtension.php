<?php

declare(strict_types=1);

/**
 * @copyright   Copyright (c) 2016 ublaboo <ublaboo@paveljanda.com>
 * @author      Pavel Janda <me@paveljanda.com>
 * @package     Ublaboo
 */

namespace Ublaboo\ApiDocu\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Definition;
use Nette\DI\Helpers;
use Nette\PhpGenerator\ClassType as GClassType;
use Ublaboo\ApiDocu\Generator;
use Ublaboo\ApiDocu\Starter;

class ApiDocuExtension extends CompilerExtension
{

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * @var array
	 */
	private $defaults = [
		'apiDir' => '%wwwDir%/api',
		'httpAuth' => [
			'user' => null,
			'password' => null,
		],
	];

    /**
     * @var Definition
     */
    private $definition;

	public function loadConfiguration(): void
	{
		$this->config = $this->_getConfig();
	}


	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;

		$builder->addDefinition($this->prefix('generator'))
			->setClass(Generator::class)
			->setArguments([$config['apiDir'], $config['httpAuth']]);

		$this->definition = $builder->addDefinition($this->prefix('starter'))
			->setClass(Starter::class)
			->setArguments([
				$builder->getDefinition($this->prefix('generator')),
				$builder->getDefinition('router'),
			]);
	}


	protected function _getConfig(): array
	{
		$config = $this->validateConfig($this->defaults, $this->config);

		$config['apiDir'] = Helpers::expand(
			$config['apiDir'],
			$this->getContainerBuilder()->parameters
		);

		return $config;
	}

    public function afterCompile(GClassType $class)
    {
        parent::afterCompile($class);
        $class->getMethod('initialize')->addBody('$this->getService(?);', [$this->definition->getName()]);
    }
}
