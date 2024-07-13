<?php



$inputFile = __DIR__ . "/The Dresden Files #05 - Death Masks.m4b";
$outputFile = __DIR__ . "/The Dresden Files #05 - Death Masks.mp3";

// Construct the FFmpeg command
$command = "ffmpeg -i \"$inputFile\" \"$outputFile\"";

// Execute the FFmpeg command
$output = shell_exec($command);

// Check if the conversion was successful
if (file_exists($outputFile)) {
    echo "Conversion successful. The MP3 file is located at: $outputFile";
} else {
    echo "Conversion failed.";
}

