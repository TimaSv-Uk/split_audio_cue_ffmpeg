<?php

function convert_audio(string $input_file, string  $output_file): void
{
  $command = "ffmpeg -i \"$input_file\" \"$output_file\"";

  exec($command);

  // Check if the conversion was successful
  if (file_exists($output_file)) {
    echo "Conversion successful. The MP3 file is located at: $output_file";
  } else {
    echo "Conversion failed.";
  }
}
