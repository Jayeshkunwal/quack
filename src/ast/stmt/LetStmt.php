<?php
/**
 * Quack Compiler and toolkit
 * Copyright (C) 2016 Marcelo Camargo <marcelocamargo@linuxmail.org> and
 * CONTRIBUTORS.
 *
 * This file is part of Quack.
 *
 * Quack is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Quack is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Quack.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace QuackCompiler\Ast\Stmt;

use \QuackCompiler\Intl\Localization;
use \QuackCompiler\Parser\Parser;
use \QuackCompiler\Types\NativeQuackType;
use \QuackCompiler\Types\TypeError;
use \QuackCompiler\Scope\Meta;

class LetStmt extends Stmt
{
    public $name;
    public $type;
    public $value;
    private $scoperef;

    public function __construct($name, $type, $value)
    {
        $this->name = $name;
        $this->type = $type;
        $this->value = $value;
    }

    public function format(Parser $parser)
    {
        $source = 'let ' . $this->name;

        if (!is_null($this->type)) {
            $source .= ' :: ' . $this->type;
        }

        if (!is_null($this->value)) {
            $source .= ' :- ' . $this->value->format($parser);
        }

        $source .= PHP_EOL;
        return $source;
    }

    public function injectScope(&$parent_scope)
    {
        $this->scoperef = $parent_scope;
        if (!is_null($this->value)) {
            $this->value->injectScope($parent_scope);
        }
    }

    public function runTypeChecker()
    {
        $type = null;
        // No type, no value. Free variable
        if (is_null($this->type) && is_null($this->value)) {
            throw new TypeError(Localization::message('TYP290', [$this->name]));
        }

        // type ^ value | type & value
        if (is_null($this->value)) {
            $type = $this->type;
        } else if (is_null($this->type)) {
            $type = $this->value->getType();
        } else {
            $value_type = $this->value->getType();
            if (!$this->type->check($value_type)) {
                throw new TypeError(Localization::message('TYP300', [$this->name, $this->type, $value_type]));
            }
            $type = $this->type;
        }

        $this->scoperef->setMeta(Meta::M_TYPE, $this->name, $type);
    }
}
