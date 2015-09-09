<?php

namespace Peach\Tools;

use Peach\Support\Arr;

class Rand
{
    protected $probabilities = [
        'notAlways'  => [24, 25],
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

    public function __call($name, $args)
    {
        $probs = Arr::safe($this->probabilities, $name);
        if (!$probs) {
            throw new \BadMethodCallException("no method called '$name' on Rand obj");
        }

        array_unshift($args, $probs);

        call_user_func_array([$this, 'exec'], $args);
    }

    protected function exec($probs, $cb, $times = 1)
    {
        list($min, $max) = $probs;
        $count = 0;
        do {
            if (rand(1, $max) <= $min) {
                call_user_func($cb);
            }
        } while ($count++ < $times);
    }

}
