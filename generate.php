#!/usr/bin/env php -q
<?php
/**
 * VERY simple Slovak names generator script
 *
 * Use at your own risk.
 * Creative Commons Zero v1.0 Universal
 * Data files are scraped from public sources (Wikipedia and calendar)
 *
 * @author Miroslav Hudak <mhudak@dev.enscope.com>
 */

/**
 * Checks if string ends with given suffix.
 *
 * @param string $string String to check
 * @param string $ending Suffix
 *
 * @return bool
 */
function ends_with($string, $ending)
{
    return (substr($string, -strlen($ending)) == $ending);
}

/**
 * Loads data file to array.
 *
 * @param array $array
 * @param string $infile
 */
function load_names(&$array, $infile)
{
    foreach (file($infile) as $line)
    {
        $name = trim($line);
        if (!empty($name))
        {
            $array[] = $name;
        }
    }
}

/** Default Slovak female surname suffix */
define('FEMALE_SUFFIX', 'ová');

/**
 * Slovak female surnames generator with built-in rules.
 * This might not be complete list of rules, but I hope it is complete enough.
 *
 * @param string $surname
 *
 * @return string
 */
function female_surname($surname)
{
    if (ends_with($surname, 'ec'))
    {
        // adamec -> adamCOVA
        return (substr($surname, 0, -2) . 'c' . FEMALE_SUFFIX);
    }
    elseif (ends_with($surname, 'a'))
    {
        // babka -> babkOVA
        return (substr($surname, 0, -1) . FEMALE_SUFFIX);
    }
    elseif (ends_with($surname, 'o'))
    {
        // bahno -> bahnOVA
        return (substr($surname, 0, -1) . FEMALE_SUFFIX);
    }
    elseif (ends_with($surname, 'ý'))
    {
        // brunovsky -> brunovskA
        return (substr($surname, 0, -2) . 'á'); // -2 as multibyte
    }

    return ($surname . FEMALE_SUFFIX);
}

function show_usage()
{
    global $argv, $count, $gender, $delimiter;

    echo "VERY Simple Slovak Names Generator\n";
    echo "- by Miro Hudak <mhudak@dev.enscope.com>\n\n";

    echo "Usage:\n";
    echo "  {$argv[0]} count <gender:male|female|both> <delimiter>\n\n";

    echo "Defaults:\n";
    echo "- count:     $count\n";
    echo "- gender:    $gender\n";
    echo "- delimiter: '$delimiter'\n\n";
}

$names = [];
load_names($names['male'], 'input/names.male.txt');
load_names($names['female'], 'input/names.female.txt');
$surnames = [];
load_names($surnames['male'], 'input/surnames.male.txt');
load_names($surnames['female'], 'input/surnames.female.txt');

$count = 100;
$gender = 'both';
$delimiter = "\t";
switch ($argc)
{
    case 1:
        // use defaults
        break;
    case 2:
        if (in_array($argv[1], ['?', 'help', '-h', '--help']))
        {
            show_usage();
            exit;
        }
        $count = $argv[1];
        break;
    case 3:
        $count = $argv[1];
        $gender = $argv[2];
        break;
    case 4:
        $count = $argv[1];
        $gender = $argv[2];
        $delimiter = $argv[3];
        break;
    default:
        show_usage();
        exit;
}

for ($i = 0; $i < $count; $i++)
{
    $cgender = ($gender == 'both')
        ? ((rand(0, 100) % 2) ? 'male' : 'female')
        : $gender;

    echo $names[$cgender][array_rand($names[$cgender])] . $delimiter .
        $surnames[$cgender][array_rand($surnames[$cgender])] . "\n";
}
