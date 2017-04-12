<?php
/*
 * Ensures compatibility with PHPUnit < 6.x
 */

if (!class_exists('PHPUnit\Framework\Constraint\Constraint') && class_exists('PHPUnit_Framework_Constraint')) {
    namespace PHPUnit\Framework\Constraint {
        abstract class Constraint extends \PHPUnit_Framework_Constraint {}
    }
}

if (!class_exists('PHPUnit\Framework\TestCase') && class_exists('PHPUnit_Framework_TestCase')) {
    namespace PHPUnit\Framework {
        abstract class TestCase extends \PHPUnit_Framework_TestCase {}
    }
}
