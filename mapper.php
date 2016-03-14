<?php
require_once 'vendor/autoload.php';

use Tmont\Midi\Parsing\FileParser;
use Tmont\Midi\Reporting\TextFormatter;
use Tmont\Midi\Reporting\Printer;
use Tmont\Midi\Parsing\ParseState;
use Tmont\Midi\Emit\File;
use Tmont\Midi\Emit\Track;
use Tmont\Midi\Delta;
use Tmont\Midi\Event;


//$newFile = new
//create a new file parser
$parser = new FileParser();

//replace this path with the path to an actual MIDI file
$parser->load('./Michael_Jackson_-_Beat_It.mid');

$state = $parser->getState();
$trackHeader = array();
$eventsAndDeltas = array();

 while ($parser->getState() != ParseState::EOF) {

     echo get_class($parser->parse())."\n";
//      if ($parser->getState() == ParseState::FILE_HEADER) {
//          $header = $parser->parse();

//      }

//      if ($parser->getState() == ParseState::TRACK_HEADER) {
//          $unique = uniqid();
//          $trackHeader[$unique] = $parser->parse();
//      } else {
//          $chunk =  $parser->parse();

//          if ($chunk instanceof Delta)
//          {
//               $deltas[$unique][] = $chunk;
//          }

//          if ($chunk instanceof Event){
//              $events[$unique][] = $chunk;
//          }


//      }
}

// echo count(array_keys($eventsAndDeltas))." tracks\n";


// $headerData = $header->getData();
// $newFile = new File($headerData[2], $headerData[0]);

// foreach ($trackHeader as $unique => $theader) {
//      $track = new Track();

//      foreach ($events[$unique] as $chunk) {
//           $newFile->addTrack($chunk);
//      }
// }

// $newFile->save('./newFile.midi');





//create a Printer object
// $printer = new Printer(new TextFormatter(), $parser);

// //output the parse result
// $printer->printAll();
