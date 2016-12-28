<?php

namespace Trellis\Entity\Path;

use JMS\Parser\AbstractParser;
use JMS\Parser\SimpleLexer;
use Trellis\Error\InvalidValuePath;

final class ValuePathParser extends AbstractParser
{
    private const T_ATTRIBUTE = 1;

    private const T_POSITION = 2;

    private const T_COMPONENT_SEP = 3;

    private const T_PART_SEP = 4;

    /**
     * @var string $tokens_regex
     */
    private const TOKEN_REGEX = <<<REGEX
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
    private const TOKEN_MAP = [
        0 => "T_UNKNOWN",
        1 => "T_ATTRIBUTE",
        2 => "T_POSITION",
        3 => "T_PART_SEP"
    ];

    /**
     * @return ValuePathParser
     */
    public static function create(): ValuePathParser
    {
        $mapToken = function (string $token): array {
            switch ($token) {
                case ".":
                    return [ self::T_COMPONENT_SEP, $token ];
                case "-":
                    return [ self::T_PART_SEP, $token ];
                default:
                    return is_numeric($token)
                        ? [ self::T_POSITION, (int)$token ]
                        : [ self::T_ATTRIBUTE, $token ];
            }
        };
        $lexer = new SimpleLexer(self::TOKEN_REGEX, self::TOKEN_MAP, $mapToken);
        return new ValuePathParser($lexer);
    }

    /**
     * @param string $path
     * @param string $context
     *
     * @return ValuePath
     */
    public function parse($path, $context = null): ValuePath
    {
        return parent::parse($path, $context);
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
        if (!$this->lexer->isNext(self::T_ATTRIBUTE)) {
            if ($this->lexer->next !== null) {
                throw new InvalidValuePath("Expecting T_TYPE at the beginning of a new path-part.");
            }
            return null;
        }
        $attribute = $this->match(self::T_ATTRIBUTE);
        $position = -1;
        if ($this->lexer->isNext(self::T_COMPONENT_SEP)) {
            $this->match(self::T_COMPONENT_SEP);
            $position = $this->match(self::T_POSITION);
        }
        return new ValuePathPart($attribute, $position);
    }
}
