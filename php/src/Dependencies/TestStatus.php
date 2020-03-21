<?php


namespace UntangledConditionals\Dependencies;


final class TestStatus
{
    private const NO_TESTS = 0;
    private const PASSING_TESTS = 1;
    private const FAILING_TESTS = 2;

    /**
     * @var int
     */
    private $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public static function noTests(): self
    {
        return new self(self::NO_TESTS);
    }

    public static function passingTests(): self
    {
        return new self(self::PASSING_TESTS);
    }

    public static function failingTests(): self
    {
        return new self(self::FAILING_TESTS);
    }
}