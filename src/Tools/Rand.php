<?php

namespace Peach\Tools;

use Peach\Support\Arr;

class Rand
{
    protected $probabilities = [
        'frequently' => [2, 3],
        'sometimes'  => [1, 4],
        'rarely'     => [1, 10],
        'veryRarely' => [1, 25],
    ];


    public function setProbability($name, $numerator, $denominator)
    {
        if ($numerator == $denominator) {
            throw new \InvalidArgumentException("Numerator and denominator can't be the same");
        }

        $numerator = min($numerator, $denominator);
        $denominator = max($numerator, $denominator);
        $this->probabilities[$name] = [$numerator, $denominator];
    }

    public function __call($name, $cb)
    {
        $probs = Arr::safe($this->probabilities, $name);
        if (!$probs) {
            throw new \BadMethodCallException("no method called '$name' on Rand obj");
        }

        $this->exec($probs, $cb);

    }

    protected function exec($probs, $cb)
    {
        list($min, $max) = $probs;
        if (rand(1, $max) <= $min) {
            call_user_func($cb);
        }
    }

}