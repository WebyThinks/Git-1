<?php
namespace MWCavender\Git;

use MWCavender\Common\Executer;

class GitRepo extends Executer
{
    public function __construct()
    {
        $this->setApplication('git');
    }

    public static function checkout($branch)
    {
        $instance = (new static)->addArgument('checkout')->addArgument($branch);

        return $instance;
    }

    public static function clone($repo)
    {
        $instance = (new static)->addArgument('clone')->addArgument($repo);

        return $instance;
    }

    public function into($path)
    {
        $this->addArgument(realpath($path));

        return $this;
    }
}