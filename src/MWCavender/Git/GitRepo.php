<?php
namespace MWCavender\Git;

use MWCavender\Common\Executer;

class GitRepo extends Executer
{
    public function __call(name, arguments)
    {
        return call_user_func_array(array($this, $name), $arguments);
    }

    public static function __callStatic(name, arguments)
    {
        $instance = new self;

        return call_user_func_array(array($instance, $name), $arguments);
    }

    public function __construct($repo_path = null)
    {
        $this->setApplication('git');

        if ($repo_path !== null) {
            $this->setWorkingDirectory($repo_path);
        }
    }

    protected function checkout($branch)
    {
        $this->addArgument('checkout')->addArgument($branch);

        return $this->execute();
    }

    protected function clone($repo, $target = null)
    {
        $this->addArgument('clone')->addArgument($repo);

        if ($target !== null) {
            $target = realpath($target);
            $this->addArgument($target);
        }

        return $this->execute();
    }

    protected function getBranches()
    {
        $output = $this->addArgument('branch')->execute()->getOutput();

        return explode(PHP_EOL, $output);
    }

    protected function getCurrentBranch()
    {
        $branches = $this->getBranches();

        foreach ($branches as $branch) {
            if (strlen($branch) > 0 && substr($branch, 0, 1) === '*') {
                return substr($branch, 2);
            }
        }

        return '';
    }

    protected function pull($remote = null, $branch = null)
    {
        $this->addArgument('pull');

        if ($remote !== null) {
            $this->addArgument($remote);
        }

        if ($branch !== null) {
            $this->addArgument($branch);
        }

        return $this->execute();
    }
}