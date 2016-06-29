<?php

namespace Trellis\Path;

use JMS\Parser\AbstractParser;
use JMS\Parser\SimpleLexer;
use Trellis\Path\ValuePathPart;
use Trellis\Exception;
use Trellis\Path\AttributePathPart;

class TrellisPathParser extends AbstractParser
{
    const T_UNKNOWN = 0;

    const T_TYPE = 1;

    const T_POSITION = 2;

    const T_SEP = 3;

    public function parseInternal()
    {
        $value_path_parts = [];
        while ($value_path_part = $this->consumePathPart()) {
            $value_path_parts[] = $value_path_part;
        }

        return new TrellisPath($value_path_parts);
    }

    protected function consumePathPart()
    {
        if ($this->lexer->isNext(self::T_SEP)) {
            $this->match(self::T_SEP);
            if (!$this->lexer->isNext(self::T_TYPE)) {
                throw new Exception('Expecting T_TYPE after given T_SEP.');
            }
        }
        if (!$this->lexer->isNext(self::T_TYPE)) {
            return false;
        }

        $type = null;
        $attribute = $this->match(self::T_TYPE);

        if ($this->lexer->isNext(self::T_SEP)) {
            $this->match(self::T_SEP);
            if ($this->lexer->isNext(self::T_POSITION)) {
                $position = $this->match(self::T_POSITION);
                return new ValuePathPart($attribute, $position);
            }
            $type = $this->match(self::T_TYPE);
        }

        return new AttributePathPart($attribute, $type);
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

    # value position
    |(\d+|\*)

    # attribute-path sepatator
    |(\.)
/x
REGEX;

        $token_map = [ 0 => 'T_UNKNOWN', 1 => 'T_TYPE', 2 => 'T_POSITION', 3 => 'T_SEP' ];
        $value_mapper = function ($value) {
            if (('.' === $value)) {
                return [ self::T_SEP, $value ];
            }
            if (is_numeric($value)) {
                return [ self::T_POSITION, (int)$value ];
            }
            if ($value === '*') {
                return [ self::T_POSITION, $value ];
            }
            return [ self::T_TYPE, $value ];
        };

        return new SimpleLexer($regex, $token_map, $value_mapper);
    }
}
