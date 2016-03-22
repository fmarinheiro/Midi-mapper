<?php
require_once 'vendor/autoload.php';

use Tmont\Midi\Parsing\FileParser;
use Tmont\Midi\Parsing\ParseState;
use Tmont\Midi\Emit\File;
use Tmont\Midi\Emit\Track;
use Tmont\Midi\Delta;
use Tmont\Midi\Event;
use Tmont\Midi\Event\NoteOnEvent;
use Tmont\Midi\Event\NoteOffEvent;

$sourceFile = './input.mid';
$destinationFile = './output.mid';
$mapCsvFile = './map.csv';

//create a new file parser
$parser = new FileParser();
echo 'Loading map file '.$mapCsvFile."...\n\n";
$fileHandler = fopen($mapCsvFile, 'r');
$map = array();

while (($data = fgetcsv($fileHandler)) !== false) {
    $map[$data[0]] = $data[1];
}
var_dump($map);
fclose($fileHandler);

echo 'Loading source file '.$sourceFile."...\n\n";
$parser->load($sourceFile);

$state = $parser->getState();
$trackHeader = array();
$eventsAndDeltas = array();

 while ($parser->getState() != ParseState::EOF) {

     if ($parser->getState() == ParseState::FILE_HEADER) {
         $header = $parser->parse();
     }

     if ($parser->getState() == ParseState::TRACK_HEADER) {
         $unique = uniqid();
          $trackHeader[$unique] = $parser->parse();
      } else {
          $chunk =  $parser->parse();

         if ($chunk instanceof Delta)
         {
              $deltas[$unique][] = $chunk;
         }

         if ($chunk instanceof Event){
             $events[$unique][] = $chunk;
        }


   }
}

$headerData = $header->getData();
$newFile = new File($headerData[2], $headerData[0]);

foreach ($trackHeader as $unique => $theader) {

    $track = new Track();

     foreach ($events[$unique] as $key => $event) {

          if ($event instanceof NoteOnEvent) {
              $params = $event->getData();

              if (array_key_exists($params[1], $map)) {
                echo "Mapping ".$params[1]." to ".$map[$params[1]]."\n\n\n";
                $event = new NoteOnEvent($params[0], $map[$params[1]], $params[2], $event->isContinuation());
              }  else {
                  echo 'Note '.$params[1]." wasn't mapped...\n\n";
              }
          }

          if ($event instanceof NoteOffEvent) {
              $params = $event->getData();

              if (array_key_exists($params[1], $map)) {
                echo "Mapping ".$params[1]." to ".$map[$params[1]]."\n\n\n";

                $event = new NoteOffEvent($params[0], $map[$params[1]], $params[2], $event->isContinuation());
              } else {
                  echo 'Note '.$params[1]." wasn't mapped...\n\n";
              }
          }

          $track->appendEvent($event, $deltas[$unique][$key]);

     }
     $newFile->addTrack($track);
}

echo 'Saving map file '.$destinationFile."...\n\n";
$newFile->save($destinationFile);

echo "#########################Done#######################\n\n";

