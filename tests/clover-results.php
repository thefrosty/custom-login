<?php

declare(strict_types=1);

/**
 * Fail when line coverage in a Clover report is below a minimum percentage.
 * Usage: php tests/clover-results.php tests/clover.xml 90
 */

$inputFile = $argv[1] ?? '';
$minimum = min(100, max(0, (float)($argv[2] ?? 0)));

if (!is_file($inputFile)) {
    fwrite(STDERR, sprintf('Clover report not found: "%s"', $inputFile) . PHP_EOL);
    exit(1);
}

if ($minimum <= 0) {
    fwrite(STDERR, 'A minimum coverage percentage must be given as the second parameter.' . PHP_EOL);
    exit(1);
}

$xml = new SimpleXMLElement((string)file_get_contents($inputFile));
// Only the project totals: file and class level <metrics> would count every element more than once.
$metrics = $xml->xpath('/coverage/project/metrics')[0] ?? null;

if ($metrics === null) {
    fwrite(STDERR, 'No project metrics found in the Clover report.' . PHP_EOL);
    exit(1);
}

$percent = static fn(int $covered, int $total): float => $total === 0 ? 0.0 : $covered / $total * 100;
$lines = $percent((int)$metrics['coveredstatements'], (int)$metrics['statements']);
$methods = $percent((int)$metrics['coveredmethods'], (int)$metrics['methods']);

printf(
    'Lines: %.2f%% (%d/%d), Methods: %.2f%% (%d/%d)' . PHP_EOL,
    $lines,
    (int)$metrics['coveredstatements'],
    (int)$metrics['statements'],
    $methods,
    (int)$metrics['coveredmethods'],
    (int)$metrics['methods']
);

if ($lines < $minimum) {
    printf('Line coverage %.2f%% is below the required %.2f%%.' . PHP_EOL, $lines, $minimum);
    exit(1);
}

printf('Line coverage meets the required %.2f%%.' . PHP_EOL, $minimum);
