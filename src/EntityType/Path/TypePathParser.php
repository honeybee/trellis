<?php

namespace Trellis\EntityType\Path;

use JMS\Parser\AbstractParser;
use JMS\Parser\SimpleLexer;
use Trellis\Exception;

class TypePathParser extends AbstractParser
{
    const T_UNKNOWN = 0;

    const T_TYPE = 1;

    const T_COMPONENT_SEP = 2;

    const T_PART_SEP = 3;

    public function parseInternal()
    {
        $type_path_parts = [];
        while ($type_path_part = $this->consumePathPart()) {
            $type_path_parts[] = $type_path_part;
        }

        return new TypePath($type_path_parts);
    }

    protected function consumePathPart()
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
        if ($this->lexer->isNext(self::T_COMPONENT_SEP)) {
            $this->match(self::T_COMPONENT_SEP);
            $type = $this->match(self::T_TYPE);
            if ($this->lexer->next === null) {
                throw new Exception(
                    'Unexpected T_TYPE at the end of type-path. Type-paths must end pointing towards an attribute.'
                );
            }
        }

        return new TypePathPart($attribute, $type);
    }

    public static function create()
    {
        return new static(self::createLexer());
    }

    protected static function createLexer()
    {
        $regex = <<<REGEX
/
    # type identifier which refers to either an attribute or entity-type
    ([a-z_]+)

    # path-part-component sepatator, the two components of a type-path-part being attribute and entity-type.
    |(\.)

    # path-part separator
    |(\-)
/x
REGEX;

        $token_map = [ 0 => 'T_UNKNOWN', 1 => 'T_TYPE', 2 => 'T_COMPONENT_SEP', 3 => 'T_PART_SEP' ];
        $value_mapper = function ($value) {
            if ('.' === $value) {
                return [ self::T_COMPONENT_SEP, $value ];
            }
            if ('-' === $value) {
                return [ self::T_PART_SEP, $value ];
            }
            if (preg_match('/[a-z_]+/', $value)) {
                return [ self::T_TYPE, $value ];
            }
            return [ self::T_UNKNOWN, $value ];
        };

        return new SimpleLexer($regex, $token_map, $value_mapper);
    }
}
