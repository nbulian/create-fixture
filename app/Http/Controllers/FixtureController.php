<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Team;

class FixtureController extends Controller
{
    public function index()
    {
        $teams = Team::select(['id', 'name'])->get();
        $error = $this->validation(count($teams));
        $fixture = $this->createFixture($teams);
        return view('fixture')->with(['fixture' => $fixture, 'error' => $error]);
    }

    private function validation($qTeams)
    {
        $error = null;
        if ( $qTeams < 2 ) {
            $error = __('messages.no_posible', ['q' => $qTeams]);
        } elseif ( $qTeams%2 != 0 ) {
            $error = __('messages.invalid_quantity', ['q' => $qTeams]);
        }
        return $error;
    }

    /**
    * Given a list of teams, it define an schedule of matches between them, divided into phases
    * @author Nahuel Bulian <nbulian@gmail.com>
    * @param array $teams - array with the teams
    * @return array - fixture created
    */
    private function createFixture($teams) 
    {   
        $events = $this->createEvents($teams);
        $qPhases = 2;
        $qMatches = (count($teams) - 1) * 2; // Quantuty of match days
        $qEvents = (count($teams) - 1) * count($teams); // Quantity of events
        $fixture = $matchesInPhase = $eventsInMatch = $excludeInPhase = $excludeInMatch = [];
        $e = $m = 1; // Auxiliary variables to start the loops from a particular point
        $q=0; // Auxiliary variable to control the quantity of events per match day

        for ($p=1; $p <= $qPhases ; $p++) { // Loop phases
            for ($m; $m <= $qMatches; $m++) { // Loop matches
                for ($e; $e <= $qEvents ; $e++) { // Loop the quantity of events for each match day
                    $q++;
                    foreach ($events as $key => $event) { // Loop the events to select the correct ones
                        if ( $this->existInPhaseExcludes($excludeInPhase, $event) ) { // Validate if event is excluded in this phase
                            continue;
                        } elseif ( in_array($event['h'], $excludeInMatch) || 
                            in_array($event['a'], $excludeInMatch) ) { // Validate if event is excluded in this match day
                            continue;
                        } else {
                            array_push($eventsInMatch, $event); // Adding the event to the match day
                            array_push($excludeInPhase, [$event['h'], $event['a']]); // These two teams cannot meet again in this phase
                            array_push($excludeInMatch, $event['h'], $event['a']); // These two teams cannot meet again in this match (round)
                            unset($events[$key]); // Remove the event 
                            break; // Next event
                        }
                    }

                    if ( $q == ($qEvents / $qMatches ) ) { // Events for the match day were completed reset variables
                        $q = 0;
                        $e++;
                        $excludeInMatch = [];
                        break; // Next match day
                    }
                }

                $matchesInPhase[] = ['day' => $m, 'events' => $eventsInMatch];
                $eventsInMatch = [];

                if ( $m == ($qMatches / $qPhases) ) { // Matches for the phase were completed reset variables
                    $m++;
                    $excludeInPhase = [];
                    break; // Next phase
                }
            }
            
            $fixture[] = ['phase' => $p, 'matches' => $matchesInPhase];
            $matchesInPhase = [];
        }

        return $fixture;
    }
    
    /**
    * Given a list of teams, it create the list of all possible events
    * @author Nahuel Bulian <nbulian@gmail.com>
    * @param array $teams - array with the teams
    * @return array - list of events
    */
    private function createEvents($teams) 
    {
        $teams2 = $teams;
        $events = [];
        foreach ($teams as $team1) {
            foreach ($teams2 as $team2) {
                if ( $team1->id != $team2->id ) {
                    $events[] = [
                        'h' => (object)['id'=>$team1->id, 'team'=>$team1->name], 
                        'a' => (object)['id'=>$team2->id, 'team'=>$team2->name]
                    ];
                }
            }
        }
        return $events;
    }

    /**
    * Check if an event has already used in the phase
    * @author Nahuel Bulian <nbulian@gmail.com>
    * @param array $excludeInPhase - array with events already used in the phase
    * @param array $event - array with the teams involved in the event
    * @return bool - success or failure
    */
    private function existInPhaseExcludes($excludeInPhase, $event) 
    {
        foreach ($excludeInPhase as $value) {
            if ( ($event['h'] == $value[0] && $event['a'] == $value[1]) ||
                ($event['a'] == $value[0] && $event['h'] == $value[1]) ) {
                    return true;
                }
        }
        return false;
    }
}