<?php

namespace UranoCompiler\Ast;

class FunctionDecl
{
  public $name;
  public $by_ref;
  public $body;
  public $parameters;

  public function __construct($name)
  {
    $this->name = $name;
  }

  public function byRef($by_ref)
  {
    $this->by_ref = $by_ref;
    return $this;
  }

  public function body($body)
  {
    $this->body = $body;
    return $this;
  }

  public function parameters($parameters)
  {
    $this->parameters = $parameters;
    return $this;
  }
}
