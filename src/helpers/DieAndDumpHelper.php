<?php

namespace src\helpers;

use Yii;

class DieAndDumpHelper
{
    /**
     * @param mixed ...$values
     */
    static function dd(...$values)
    {
        if (!is_null($values)) {
            foreach ($values as $value) {
                echo '<pre>';
                var_dump($value);
                echo '</pre>';
            }
        }

        $debugBacktrace = debug_backtrace();

        echo '<pre>';
        echo("<span style=\"color:#6495ED\">die call: " . $debugBacktrace[1]['function'] . "() in line: " . $debugBacktrace[0]['line'] . "</span>");
        echo '</pre>';

        die();
    }

    /**
     * @param mixed ...$values
     */
    static function printing(...$values)
    {
        if (!is_null($values)) {
            foreach ($values as $value) {
                echo '<pre>';
                var_dump($value);
                echo '</pre>';
            }
        }

        $debugBacktrace = debug_backtrace();

        echo '<pre>';
        echo("<span style=\"color:#6495ED\">print call: (" . $debugBacktrace[1]['function'] . ") in line: " . $debugBacktrace[0]['line'] . "</span>");
        echo '</pre>';

    }

    /**
     * @param string|null $filename
     *
     * @param mixed ...$values
     */
    static function ddToFile($filename, ...$values)
    {
        if (!is_null($values)) {
            foreach ($values as $value) {
                static::saveToFile($value, $filename);
            }
        }

        $debugBacktrace = debug_backtrace();

        $str = "die call: (" . $debugBacktrace[1]['function'] . ") in line: " . $debugBacktrace[0]['line'];
        static::saveToFile($str, $filename);

        die();
    }

    /**
     * @param string|null $filename
     *
     * @param mixed ...$values
     */
    static function printingToFile($filename, ...$values)
    {
        if (!is_null($values)) {
            foreach ($values as $value) {
                static::saveToFile($value, $filename);
            }
        }
    }

    static function saveToFile($value, $filename = null)
    {
        if (is_null($filename)) {
            $filename = 'test.log';
        }
        $path_parts = pathinfo($filename) ?? '';

        try {
            $alias = Yii::getAlias('@testlog/');
        } catch (\Exception $exception) {
            $alias = '';
        }

        if ($path_parts['dirname'] === ".") {
            $filePath = $alias . $filename;
        } else {
            $filePath = $filename;
        }

        ob_start();
        var_dump($value);
        $c = ob_get_contents();
        ob_end_clean();
        file_put_contents($filePath, date('Y-M-d H:i:s') . " --- " . $c . "\n", FILE_APPEND);
    }
}