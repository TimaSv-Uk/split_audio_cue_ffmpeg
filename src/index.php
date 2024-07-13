<?php

function get_duration(string $path_to_file): float
{
  $time_duration = shell_exec('ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 "' . $path_to_file . '"');
  $time_duration = trim($time_duration);
  return (float)$time_duration;
}

function convert_to_seconds(string $timestamp): float
{
  $parts = explode(":", $timestamp);

  $minutes = (int)$parts[0];
  $seconds = (int)$parts[1];
  $frames = (int)$parts[2];

  $total_seconds = (float)($minutes * 60 + $seconds + $frames / 75.0);
  return $total_seconds;
}

function split_mp3_chunk(string $path_to_mp3_file, string $chapter_title, float $chapter_duration, float $chapter_start_time, string $save_dir): void
{
  // Sanitize file name
  $chapter_title = preg_replace('/[^A-Za-z0-9_\-]/', '_', $chapter_title);

  if (!is_dir($save_dir)) {
    mkdir($save_dir, 0777, true);
  }

  $output_file = $save_dir . DIRECTORY_SEPARATOR . $chapter_title . ".mp3";

  // Format start time and duration to 2 decimal places
  $start_time_formatted = number_format($chapter_start_time, 2, '.', '');
  $duration_formatted = number_format($chapter_duration, 2, '.', '');

  $command = sprintf('ffmpeg -i "%s" -ss %s -t %s -c copy "%s"', $path_to_mp3_file, $start_time_formatted, $duration_formatted, $output_file);
  exec($command);
}

function main()
{
  $cue_file_path = __DIR__ . "/The Dresden Files #05 - Death Masks.cue";
  $mp3_file_path = __DIR__ . "/Death_Masks.mp3";
  $save_dir = __DIR__ . "/Death_Masks_#05/";

  $cue_file = file($cue_file_path);

  $previous_start_time = 0;
  for ($i = 3; $i < count($cue_file); $i += 3) {
    if (strpos($cue_file[$i], 'TRACK') === false) {
      continue;
    }

    $chapter_title = explode("\"", $cue_file[$i + 1])[1];

    $chapter_start_time = convert_to_seconds(trim(explode("INDEX 01 ", $cue_file[$i + 2])[1]));

    // Calculate chapter duration
    if ($i + 3 < count($cue_file)) {
      $next_chapter_start_time = convert_to_seconds(trim(explode("INDEX 01 ", $cue_file[$i + 5])[1]));
      $chapter_duration = $next_chapter_start_time - $chapter_start_time;
    } else {
      $chapter_duration = get_duration($mp3_file_path) - $chapter_start_time;
    }

    split_mp3_chunk($mp3_file_path, $chapter_title, $chapter_duration, $chapter_start_time, $save_dir);
  }
}

main();
