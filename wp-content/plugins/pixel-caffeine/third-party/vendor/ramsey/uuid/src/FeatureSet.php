<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link https://benramsey.com/projects/ramsey-uuid/ Documentation
 * @link https://packagist.org/packages/ramsey/uuid Packagist
 * @link https://github.com/ramsey/uuid GitHub
 */
namespace PixelCaffeine\Dependencies\Ramsey\Uuid;

use PixelCaffeine\Dependencies\Ramsey\Uuid\Converter\TimeConverterInterface;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Generator\PeclUuidTimeGenerator;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Provider\Node\FallbackNodeProvider;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Provider\Node\RandomNodeProvider;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Provider\Node\SystemNodeProvider;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Converter\NumberConverterInterface;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Converter\Number\BigNumberConverter;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Converter\Number\DegradedNumberConverter;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Converter\Time\BigNumberTimeConverter;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Converter\Time\DegradedTimeConverter;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Converter\Time\PhpTimeConverter;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Provider\Time\SystemTimeProvider;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Builder\UuidBuilderInterface;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Builder\DefaultUuidBuilder;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Codec\CodecInterface;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Codec\StringCodec;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Codec\GuidStringCodec;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Builder\DegradedUuidBuilder;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Generator\RandomGeneratorFactory;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Generator\RandomGeneratorInterface;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Generator\TimeGeneratorFactory;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Generator\TimeGeneratorInterface;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Provider\TimeProviderInterface;
use PixelCaffeine\Dependencies\Ramsey\Uuid\Provider\NodeProviderInterface;
/**
 * FeatureSet detects and exposes available features in the current environment
 * (32- or 64-bit, available dependencies, etc.)
 */
class FeatureSet
{
    /**
     * @var bool
     */
    private $disableBigNumber = \false;
    /**
     * @var bool
     */
    private $disable64Bit = \false;
    /**
     * @var bool
     */
    private $ignoreSystemNode = \false;
    /**
     * @var bool
     */
    private $enablePecl = \false;
    /**
     * @var UuidBuilderInterface
     */
    private $builder;
    /**
     * @var CodecInterface
     */
    private $codec;
    /**
     * @var NodeProviderInterface
     */
    private $nodeProvider;
    /**
     * @var NumberConverterInterface
     */
    private $numberConverter;
    /**
     * @var RandomGeneratorInterface
     */
    private $randomGenerator;
    /**
     * @var TimeGeneratorInterface
     */
    private $timeGenerator;
    /**
     * Constructs a `FeatureSet` for use by a `UuidFactory` to determine or set
     * features available to the environment
     *
     * @param bool $useGuids Whether to build UUIDs using the `GuidStringCodec`
     * @param bool $force32Bit Whether to force the use of 32-bit functionality
     *     (primarily for testing purposes)
     * @param bool $forceNoBigNumber Whether to disable the use of moontoast/math
     *     `BigNumber` (primarily for testing purposes)
     * @param bool $ignoreSystemNode Whether to disable attempts to check for
     *     the system host ID (primarily for testing purposes)
     * @param bool $enablePecl Whether to enable the use of the `PeclUuidTimeGenerator`
     *     to generate version 1 UUIDs
     */
    public function __construct($useGuids = \false, $force32Bit = \false, $forceNoBigNumber = \false, $ignoreSystemNode = \false, $enablePecl = \false)
    {
        $this->disableBigNumber = $forceNoBigNumber;
        $this->disable64Bit = $force32Bit;
        $this->ignoreSystemNode = $ignoreSystemNode;
        $this->enablePecl = $enablePecl;
        $this->numberConverter = $this->buildNumberConverter();
        $this->builder = $this->buildUuidBuilder();
        $this->codec = $this->buildCodec($useGuids);
        $this->nodeProvider = $this->buildNodeProvider();
        $this->randomGenerator = $this->buildRandomGenerator();
        $this->setTimeProvider(new \PixelCaffeine\Dependencies\Ramsey\Uuid\Provider\Time\SystemTimeProvider());
    }
    /**
     * Returns the builder configured for this environment
     *
     * @return UuidBuilderInterface
     */
    public function getBuilder()
    {
        return $this->builder;
    }
    /**
     * Returns the UUID UUID coder-decoder configured for this environment
     *
     * @return CodecInterface
     */
    public function getCodec()
    {
        return $this->codec;
    }
    /**
     * Returns the system node ID provider configured for this environment
     *
     * @return NodeProviderInterface
     */
    public function getNodeProvider()
    {
        return $this->nodeProvider;
    }
    /**
     * Returns the number converter configured for this environment
     *
     * @return NumberConverterInterface
     */
    public function getNumberConverter()
    {
        return $this->numberConverter;
    }
    /**
     * Returns the random UUID generator configured for this environment
     *
     * @return RandomGeneratorInterface
     */
    public function getRandomGenerator()
    {
        return $this->randomGenerator;
    }
    /**
     * Returns the time-based UUID generator configured for this environment
     *
     * @return TimeGeneratorInterface
     */
    public function getTimeGenerator()
    {
        return $this->timeGenerator;
    }
    /**
     * Sets the time provider for use in this environment
     *
     * @param TimeProviderInterface $timeProvider
     */
    public function setTimeProvider(\PixelCaffeine\Dependencies\Ramsey\Uuid\Provider\TimeProviderInterface $timeProvider)
    {
        $this->timeGenerator = $this->buildTimeGenerator($timeProvider);
    }
    /**
     * Determines which UUID coder-decoder to use and returns the configured
     * codec for this environment
     *
     * @param bool $useGuids Whether to build UUIDs using the `GuidStringCodec`
     * @return CodecInterface
     */
    protected function buildCodec($useGuids = \false)
    {
        if ($useGuids) {
            return new \PixelCaffeine\Dependencies\Ramsey\Uuid\Codec\GuidStringCodec($this->builder);
        }
        return new \PixelCaffeine\Dependencies\Ramsey\Uuid\Codec\StringCodec($this->builder);
    }
    /**
     * Determines which system node ID provider to use and returns the configured
     * system node ID provider for this environment
     *
     * @return NodeProviderInterface
     */
    protected function buildNodeProvider()
    {
        if ($this->ignoreSystemNode) {
            return new \PixelCaffeine\Dependencies\Ramsey\Uuid\Provider\Node\RandomNodeProvider();
        }
        return new \PixelCaffeine\Dependencies\Ramsey\Uuid\Provider\Node\FallbackNodeProvider([new \PixelCaffeine\Dependencies\Ramsey\Uuid\Provider\Node\SystemNodeProvider(), new \PixelCaffeine\Dependencies\Ramsey\Uuid\Provider\Node\RandomNodeProvider()]);
    }
    /**
     * Determines which number converter to use and returns the configured
     * number converter for this environment
     *
     * @return NumberConverterInterface
     */
    protected function buildNumberConverter()
    {
        if ($this->hasBigNumber()) {
            return new \PixelCaffeine\Dependencies\Ramsey\Uuid\Converter\Number\BigNumberConverter();
        }
        return new \PixelCaffeine\Dependencies\Ramsey\Uuid\Converter\Number\DegradedNumberConverter();
    }
    /**
     * Determines which random UUID generator to use and returns the configured
     * random UUID generator for this environment
     *
     * @return RandomGeneratorInterface
     */
    protected function buildRandomGenerator()
    {
        return (new \PixelCaffeine\Dependencies\Ramsey\Uuid\Generator\RandomGeneratorFactory())->getGenerator();
    }
    /**
     * Determines which time-based UUID generator to use and returns the configured
     * time-based UUID generator for this environment
     *
     * @param TimeProviderInterface $timeProvider
     * @return TimeGeneratorInterface
     */
    protected function buildTimeGenerator(\PixelCaffeine\Dependencies\Ramsey\Uuid\Provider\TimeProviderInterface $timeProvider)
    {
        if ($this->enablePecl) {
            return new \PixelCaffeine\Dependencies\Ramsey\Uuid\Generator\PeclUuidTimeGenerator();
        }
        return (new \PixelCaffeine\Dependencies\Ramsey\Uuid\Generator\TimeGeneratorFactory($this->nodeProvider, $this->buildTimeConverter(), $timeProvider))->getGenerator();
    }
    /**
     * Determines which time converter to use and returns the configured
     * time converter for this environment
     *
     * @return TimeConverterInterface
     */
    protected function buildTimeConverter()
    {
        if ($this->is64BitSystem()) {
            return new \PixelCaffeine\Dependencies\Ramsey\Uuid\Converter\Time\PhpTimeConverter();
        }
        if ($this->hasBigNumber()) {
            return new \PixelCaffeine\Dependencies\Ramsey\Uuid\Converter\Time\BigNumberTimeConverter();
        }
        return new \PixelCaffeine\Dependencies\Ramsey\Uuid\Converter\Time\DegradedTimeConverter();
    }
    /**
     * Determines which UUID builder to use and returns the configured UUID
     * builder for this environment
     *
     * @return UuidBuilderInterface
     */
    protected function buildUuidBuilder()
    {
        if ($this->is64BitSystem()) {
            return new \PixelCaffeine\Dependencies\Ramsey\Uuid\Builder\DefaultUuidBuilder($this->numberConverter);
        }
        return new \PixelCaffeine\Dependencies\Ramsey\Uuid\Builder\DegradedUuidBuilder($this->numberConverter);
    }
    /**
     * Returns true if the system has `Moontoast\Math\BigNumber`
     *
     * @return bool
     */
    protected function hasBigNumber()
    {
        return \class_exists('PixelCaffeine\\Dependencies\\Moontoast\\Math\\BigNumber') && !$this->disableBigNumber;
    }
    /**
     * Returns true if the system is 64-bit, false otherwise
     *
     * @return bool
     */
    protected function is64BitSystem()
    {
        return \PHP_INT_SIZE == 8 && !$this->disable64Bit;
    }
}
