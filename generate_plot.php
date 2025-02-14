<?php
// Load the CSV data
$dataFile = "file.csv";
$rows = array_map('str_getcsv', file($dataFile));
array_shift($rows);

// Create image
$margin = 50;
$width = 800;
$height = 800;
$image = imagecreatetruecolor($width, $height);
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);

// Fill the background
imagefilledrectangle($image, 0, 0, $width, $height, $white);

// Find min and max for scaling
$xMin = $yMin = PHP_INT_MAX;
$xMax = $yMax = PHP_INT_MIN;

foreach ($rows as $row) {
    $x = (int)$row[0];
    $y = (int)$row[1];
    $xMin = min($xMin, $x);
    $xMax = max($xMax, $x);
    $yMin = min($yMin, $y);
    $yMax = max($yMax, $y);
}

// Adjust for negative values
$xRange = $xMax - $xMin;
$yRange = $yMax - $yMin;

// Calculate axis origins
$originX = $margin - ($xMin / $xRange) * ($width - 2 * $margin);
$originY = $height - $margin + ($yMin / $yRange) * ($height - 2 * $margin);

// Ensure axes are within bounds
$originX = max($margin, min($width - $margin, $originX));
$originY = max($margin, min($height - $margin, $originY));

// Draw X-axis
imageline($image, $margin, (int)$originY, $width - $margin, (int)$originY, $black);

// Draw Y-axis
imageline($image, (int)$originX, $margin, (int)$originX, $height - $margin, $black);

// Plot each point
foreach ($rows as $row) {
    $x = (int)$row[0];
    $y = (int)$row[1];
    $plotX = $margin + (($x - $xMin) / $xRange) * ($width - 2 * $margin);
    $plotY = $height - $margin - (($y - $yMin) / $yRange) * ($height - 2 * $margin); // Flip Y for image coordinates
    imagesetpixel($image, (int)round($plotX), (int)round($plotY), $black);
}

// Save the image
imagepng($image, 'scatter_plot.png');
imagedestroy($image);
