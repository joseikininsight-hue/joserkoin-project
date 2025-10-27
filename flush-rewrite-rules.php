#!/usr/bin/env php
<?php
/**
 * Flush WordPress Rewrite Rules
 * 
 * Run this script after adding new rewrite rules to WordPress
 */

// Load WordPress
require_once __DIR__ . '/wp-load.php';

// Check if WordPress is loaded
if (!function_exists('flush_rewrite_rules')) {
    echo "ERROR: WordPress not loaded properly\n";
    exit(1);
}

echo "Flushing WordPress rewrite rules...\n";

// Flush rewrite rules
flush_rewrite_rules(false);

echo "✓ Rewrite rules flushed successfully!\n";
echo "\nPurpose page URLs are now active:\n";

$purposes = [
    'equipment' => '設備を導入したい',
    'training' => '人材育成したい',
    'sales' => '営業強化したい',
    'startup' => '事業を始めたい',
    'digital' => 'IT化を進めたい',
    'funding' => '資金調達したい',
    'environment' => '環境対策したい',
    'succession' => '事業を引き継ぎたい',
    'global' => '海外展開したい',
    'rnd' => '研究開発したい',
    'workstyle' => '働き方改善したい',
    'regional' => '地域活性化したい',
];

foreach ($purposes as $slug => $title) {
    $url = home_url("/purpose/{$slug}/");
    echo "  • {$title}: {$url}\n";
}

echo "\nDone!\n";
