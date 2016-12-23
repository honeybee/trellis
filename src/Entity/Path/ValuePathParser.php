<?php

namespace Trellis\Entity\Path;

use JMS\Parser\AbstractParser;
use JMS\Parser\SimpleLexer;
use Trellis\Error\InvalidValuePath;

final class ValuePathParser extends AbstractParser
{
    const T_UNKNOWN = 0;

    const T_TYPE = 1;

    const T_POSITION = 2;

    const T_COMPONENT_SEP = 3;

    const T_PART_SEP = 4;

    /**
     * @var string $tokens_regex
     */
    private static $tokens_regex = <<<REGEX
/
    # type identifier which refers to an attribute
    ([a-z_]+)

    # value position
    |(\d+)

    # value-path-component separator. the two components of a value-path-part being attribute and position.
    |(\.)

    # value-path separator
    |(\-)
/x
REGEX;

    /**
     * @var mixed[] $token_map
     */
    private static $token_map = [
        0 => 'T_UNKNOWN',
        1 => 'T_TYPE',
        2 => 'T_POSITION',
        3 => 'T_PART_SEP'
    ];

    /**
     * @return self
     */
    public static function create(): self
    {
        return new self(
            new SimpleLexer(
                self::$tokens_regex,
                self::$token_map,
                function (string $token): array {
                    switch ($token) {
                        case '.':
                            return [ self::T_COMPONENT_SEP, $token ];
                        case '-':
                            return [ self::T_PART_SEP, $token ];
                        default:
                            return is_numeric($token)
                                ? [ self::T_POSITION, (int)$token ]
                                : [ self::T_TYPE, $token ];
                    }
                }
            )
        );
    }

    /**
     * @return ValuePath
     */
    public function parseInternal(): ValuePath
    {
        $value_path_parts = [];
        while ($value_path_part = $this->consumeValuePathPart()) {
            $value_path_parts[] = $value_path_part;
        }
        return new ValuePath($value_path_parts);
    }

    /**
     * @return null|ValuePathPart
     */
    private function consumeValuePathPart(): ?ValuePathPart
    {
        if ($this->lexer->isNext(self::T_PART_SEP)) {
            $this->match(self::T_PART_SEP);
        }
        if (!$this->lexer->isNext(self::T_TYPE)) {
            if ($this->lexer->next !== null) {
                throw new InvalidValuePath('Expecting T_TYPE at the beginning of a new path-part.');
            }
            return null;
        }
        $attribute = $this->match(self::T_TYPE);
        $position = -1;
        if ($this->lexer->isNext(self::T_COMPONENT_SEP)) {
            $this->match(self::T_COMPONENT_SEP);
            $position = $this->match(self::T_POSITION);
        }
        return new ValuePathPart($attribute, $position);
    }
}
