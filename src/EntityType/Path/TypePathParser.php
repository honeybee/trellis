<?php

namespace Trellis\EntityType\Path;

use JMS\Parser\AbstractParser;
use JMS\Parser\SimpleLexer;
use Trellis\Error\InvalidTypePath;

final class TypePathParser extends AbstractParser
{
    private const T_UNKNOWN = 0;

    private const T_TYPE = 1;

    private const T_COMPONENT_SEP = 2;

    private const T_PART_SEP = 3;

    private const TOKEN_REGEX = <<<REGEX
/
    # type identifier which refers to either an attribute or entity-type
    ([a-z_]+)

    # path-part-component separator, the two components of a type-path-part being attribute and entity-type.
    |(\.)

    # path-part separator
    |(\-)
/x
REGEX;

    private const TOKEN_MAP = [
        0 => "T_UNKNOWN",
        1 => "T_TYPE",
        2 => "T_COMPONENT_SEP",
        3 => "T_PART_SEP"
    ];

    /**
     * @return TypePathParser
     */
    public static function create(): TypePathParser
    {
        $mapToken = function (string $token): array {
            switch ($token) {
                case ".":
                    return [ self::T_COMPONENT_SEP, $token ];
                case "-":
                    return [ self::T_PART_SEP, $token ];
                default:
                    return preg_match("/[a-z_]+/", $token)
                        ? [ self::T_TYPE, $token ]
                        : [ self::T_UNKNOWN, $token ];
            }
        };
        $lexer = new SimpleLexer(self::TOKEN_REGEX,  self::TOKEN_MAP, $mapToken);
        return new TypePathParser($lexer);
    }

    /**
     * @param string $path
     * @param string $context
     *
     * @return TypePath
     */
    public function parse($path, $context = null): TypePath
    {
        return parent::parse($path, $context);
    }

    /**
     * @return TypePath
     */
    public function parseInternal(): TypePath
    {
        $type_path_parts = [];
        while ($type_path_part = $this->consumePathPart()) {
            $type_path_parts[] = $type_path_part;
        }
        return new TypePath($type_path_parts);
    }

    /**
     * @return null|TypePathPart
     */
    private function consumePathPart(): ?TypePathPart
    {
        if ($this->lexer->isNext(self::T_PART_SEP)) {
            $this->match(self::T_PART_SEP);
        }
        if (!$this->lexer->isNext(self::T_TYPE)) {
            if ($this->lexer->next !== null) {
                throw new InvalidTypePath("Expecting T_TYPE at the beginning of a new path-part.");
            }
            return null;
        }
        $attribute = $this->match(self::T_TYPE);
        $type = "";
        if ($this->lexer->isNext(self::T_COMPONENT_SEP)) {
            $this->match(self::T_COMPONENT_SEP);
            $type = $this->match(self::T_TYPE);
            if ($this->lexer->next === null) {
                throw new InvalidTypePath(
                    "Unexpected T_TYPE at the end of type-path. Type-paths must end pointing towards an attribute."
                );
            }
        }
        return new TypePathPart($attribute, $type);
    }
}
