<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Log;
// Model
use App\Models\VccCredential;
use App\Models\PinRotationMarker;

class SyncRotationSets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:rotationsets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Rotation Sets';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //
        Log::info('=============== SyncRotationSets Schedule Run Start =================');
        $clubs = VccCredential::all();
        foreach ($clubs as $club) {
            $this->syncPinRotationsByClub($club->site_name);
        }
        Log::info('=============== SyncRotationSets Schedule Run End =================');
        //
        return 0;
    }

    private function syncPinRotationsByClub($courseName)
    {
        if(!$courseName) {
            return;
        }
        $res = loginVcc($courseName); // helper function
        if($res["success"]) {
            $cred = $res["cred"];
            $cookie = $res["cookie"];
            // get PinPlacements data
            $orgData = getPinPlacements($cred, $cookie);
            // dd('********', $orgData);
            // make the data template for dynamodb
            $RotationSets = $orgData["RotationSets"];
            $ApproachViews = $orgData["ApproachViews"];
            $rotationSetTypeKeys = getRotationSetTypeKeys($RotationSets); // array
            $rotationSetTypeMarkerKeys = getRotationMarkersBySetType($RotationSets, $rotationSetTypeKeys); // array
            // dd('********', $rotationSetTypeMarkerKeys);
            if(updateSetTypeAndMarker($courseName, $rotationSetTypeMarkerKeys))
            {
                // step 1.
                $_rotationSets = [];
                foreach($rotationSetTypeMarkerKeys as $rotationSetTypeMarkerKey)
                {
                    $_rotationSets[$rotationSetTypeMarkerKey["RotationSetType"]] = getRotationsBySetType($ApproachViews, $rotationSetTypeMarkerKey);
                }
                //
                $rotationData = array(
                    "rotations" => $_rotationSets,
                    "time_zone" => "America/New York"
                );
                $lambdaApi = "https://5yjcrgi025.execute-api.us-east-1.amazonaws.com/accupin_dev/syncPinRotations";
                $params = [
                    "course_name" => $courseName,
                    "data" => json_encode($rotationData),
                ];
                // dd("=== generated params === ", $orgData, $params);
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => '*/*'
                ])->post($lambdaApi, $params);
                // dd('***********', $response->json());
                $res = $response->json();
                if($res && isset($res["statusCode"]) && $res["statusCode"] == 200) {
                    Log::info('**************** ' . $courseName . ' AWS Table Updating Successed ******************');
                } else {
                    Log::info('**************** ' . $courseName . ' AWS Table Updating Failed ******************');
                }
            } else {
                Log::info('**************** ' . $courseName . ' Local Table Updating Failed ******************');
            }
        } else {
            Log::info('**************** ' . $courseName . ' Failed Authorization ******************');
        }
    }
}
