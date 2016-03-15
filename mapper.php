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



//create a new file parser
$parser = new FileParser();

$parser->load('./Michael_Jackson_-_Beat_It.mid');

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
              $event = new NoteOnEvent($params[0], --$params[1], $params[2], $event->isContinuation());
          }

          if ($event instanceof NoteOffEvent) {
              $params = $event->getData();
              $event = new NoteOffEvent($params[0], --$params[1], $params[2], $event->isContinuation());
          }

          $track->appendEvent($event, $deltas[$unique][$key]);

     }
     $newFile->addTrack($track);
}

$newFile->save('./newFile.mid');

