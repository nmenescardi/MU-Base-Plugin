<?php

namespace MUBase;


require_once 'Autoloader.php';
Autoloader::register();

class Base
{

  use Pluggable;
}


new Base;
