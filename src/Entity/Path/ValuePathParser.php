<?php

namespace Trellis\Entity\Path;

use JMS\Parser\AbstractParser;
use JMS\Parser\SimpleLexer;
use Trellis\Exception;

class ValuePathParser extends AbstractParser
{
    const T_UNKNOWN = 0;

    const T_TYPE = 1;

    const T_POSITION = 2;

    const T_COMPONENT_SEP = 3;

    const T_PART_SEP = 4;

    public function parseInternal()
    {
        $value_path_parts = [];
        while ($value_path_part = $this->consumeValuePathPart()) {
            $value_path_parts[] = $value_path_part;
        }

        return new ValuePath($value_path_parts);
    }

    protected function consumeValuePathPart()
    {
        if ($this->lexer->isNext(self::T_PART_SEP)) {
            $this->match(self::T_PART_SEP);
        }

        if (!$this->lexer->isNext(self::T_TYPE)) {
            if ($this->lexer->next !== null) {
                throw new Exception('Expecting T_TYPE at the begining of a new path-part.');
            }
            return false;
        }

        $attribute = $this->match(self::T_TYPE);
        $type = null;
        $position = null;
        if ($this->lexer->isNext(self::T_COMPONENT_SEP)) {
            $this->match(self::T_COMPONENT_SEP);
            $position = $this->match(self::T_POSITION);
        }

        return new ValuePathPart($attribute, $position, $type);
    }

    public static function create()
    {
        return new static(self::createLexer());
    }

    protected static function createLexer()
    {
        $regex = <<<REGEX
/
    # type identifier which refers to an attribute
    ([a-z_]+)

    # value position
    |(\d+)

    # value-path-component separator. the two components of a value-path-part being attribute and position.
    |(\.)

    # value-path sepatator
    |(\-)
/x
REGEX;

        $token_map = [ 0 => 'T_UNKNOWN', 1 => 'T_TYPE', 2 => 'T_POSITION', 3 => 'T_COMPONENT_SEP', 3 => 'T_PART_SEP' ];
        $value_mapper = function ($value) {
            if ('.' === $value) {
                return [ self::T_COMPONENT_SEP, $value ];
            }
            if ('-' === $value) {
                return [ self::T_PART_SEP, $value ];
            }
            if (is_numeric($value)) {
                return [ self::T_POSITION, (int)$value ];
            }
            return [ self::T_TYPE, $value ];
        };

        return new SimpleLexer($regex, $token_map, $value_mapper);
    }
}
