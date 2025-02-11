<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
// Models
use App\Models\Admin;
use App\Models\VccCredential;
use App\Models\Club;
use App\Models\Course;

use Illuminate\Support\Facades\Log;


class AdminController extends Controller
{
    /************************************************************************/ 
    /******************** * Authontication Functions * **********************/ 
    /************************************************************************/

    //
    public function loginAdmin(Request $request)
    {
    	$validator = Validator::make($request->all(), [
    		'email' => 'required|email|string|max:255',
    		'password' => 'required|string|max:255|min:8',
    	]);

        if ($validator->fails()) {
    		return response()->json(["status" => 400, "message" => "Required param not passed."], 200);
    	}
        
        $admin = Admin::where('email', $request->email)->first();
        
        if (!($admin && Hash::check($request->password, $admin->password))) {
        	return response()->json(["status" => 400, "message" => "Invalid credentials."], 200);
        }

        return response()->json(["status" => 200, "data" => $admin, "message" => "Admin has logged in successfully."], 200);
    }

    /***************************************************************************/ 
    /************************ * Credential Functions * *************************/ 
    /***************************************************************************/ 

    //
    public function addClub(Request $request)
    {
        $validator = Validator::make($request->all(), [
    		'domain_type' => 'required',
    		'username' => 'required|string|max:255',
    		'password' => 'required|string|max:255',
    		'is_active' => 'required',
    	]);

    	if ($validator->fails()) {
    		return response()->json(["status" => 400, "message" => "Required param not passed."], 200);
    	}
        if(!($request->domain_type == 'visagenet' || $request->domain_type == 'visagegolf' || $request->domain_type == 'betavisagenet')) {
            return response()->json(["status" => 400, "message" => "Unsupported domain type."], 200);
        }
        $club_exist = Club::where('domain_type', $request->domain_type)
                        ->where('username', $request->username)
                        ->where('password', $request->password)
                        ->first();
        if($club_exist) {
            return response()->json(["status" => 400, "message" => "Duplicated credential."], 200);
        }
        //
        $loginApi = '';
        $domainEndPoint = "";
        if($request->domain_type == 'betavisagenet') {
            $domainEndPoint = "https://beta.visagenet.com";
        } else if($request->domain_type == 'visagenet') {
            $domainEndPoint = "https://vcc.visagenet.com";
        } else {
            $domainEndPoint = "https://vcc.visage.golf";
        }
        $loginApi = $domainEndPoint . "/api/Account/JsonLogin";
        $credentials = [
            'UserName' => $request->username,
            'Password' => $request->password
        ];
        //
        $response = Http::post($loginApi, $credentials);
        if($response["success"]) {
            $headers = $response->headers();
            $cookie = $headers["Set-Cookie"][0];
            // get
            $getSiteLocatonsApi = $domainEndPoint . "/api/VisageSpa/GetSiteLocations";
            $clubRes = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => '*/*',
                'Cookie' => $cookie
            ])->get($getSiteLocatonsApi, []);
            $clubData = $clubRes->json();
            // Get Site Locatoins Data
            foreach ($clubData as $clubRow) {
                $club = Club::create([
                    'domain_type' => $request->domain_type,
                    'username' => $request->username,
                    'password' => $request->password,
                    'is_active' => ($request->is_active == "true" ? true : false),
                    'site_id' => $clubRow["Id"],
                    'site_name' => $clubRow["Name"],
                    'is_internal' => $clubRow["IsInternal"],
                    'is_eu_site' => $clubRow["IsEUSite"],
                    'is_chinese_site' => $clubRow["IsChineseSite"],
                    'center_point_latitude' => $clubRow["CenterPointLatitude"],
                    'center_point_longitude' => $clubRow["CenterPointLongitude"],
                ]);
                // Get Courses Data
                $getCoursesApi = $domainEndPoint . "/api/VisageSpa/GetSiteSettings";
                $courseRes = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => '*/*',
                    'Cookie' => $cookie
                ])->get($getCoursesApi, ['theSiteId' => $club->site_id]);
                $courseDataObj = $courseRes->json();
                $courseData = $courseDataObj['Courses'];
                foreach ($courseData as $courseRow) {
                    $course = Course::create([
                        'domain_type' => $request->domain_type,
                        'club_id' => $club->id,
                        'site_id' => $club->site_id,
                        'site_name' => $club->site_name,
                        'course_id' => $courseRow["Id"],
                        'course_name' => $courseRow["Name"],
                        'course_number' => $courseRow["Number"],
                        'first_hole' => $courseRow["FirstHole"],
                        'hole_count' => $courseRow["HoleCount"],
                        'site_settings' => json_encode($courseRow["SiteSettings"]),
                    ]);
                }
            }
    
            return response()->json(["status" => 200, "data" => "", "message" => "Successfully added."], 200);
        } else {

            return response()->json(["status" => 400, "data" => null, "message" => "Invalid Credential."], 200);
        }
    }

    //
    public function getCourses(Request $request)
    {
        $validator = Validator::make($request->all(), [
    		'order_column' => 'required|max:255',
    		'order_by' => 'required|max:255',
    	]);

    	if ($validator->fails()) {
    		return response()->json(["status" => 400, "message" => "Required param not passed."], 200);
    	}

        $coursesQuery = Course::with('club');
        if (strpos($request->order_column, 'id') !== false) {
            $coursesQuery->orderByRaw('LENGTH('.$request->order_column.') '.$request->order_by);
        }
        $coursesQuery->orderBy($request->order_column, $request->order_by);
        $courses = $coursesQuery->get();

        return response()->json(["status" => 200, "data" => $courses, "message" => ""], 200);
    }

    //
    public function removeClub(Request $request)
    {
        $validator = Validator::make($request->all(), [
    		'id' => 'required|max:255',
    	]);

    	if ($validator->fails()) {
    		return response()->json(["status" => 400, "message" => "Required param not passed."], 200);
    	}

        $courses = Course::where('club_id', $request->id)->get();
        if($courses) {
            foreach ($courses as $course) {
                Course::find($course->id)->delete();
            }
            $club = Club::find($request->id);
            if($club) {
                $club->delete();
            }
            return response()->json(["status" => 200, "data" => null, "message" => "Successfully removed."], 200);
        } else {
            return response()->json(["status" => 200, "data" => null, "message" => "There is no matched data."], 200);
        }

    }

    //
    public function resetTable(Request $request)
    {
        $validator = Validator::make($request->all(), [
    		'table' => 'required|max:55',
    	]);

    	if ($validator->fails()) {
    		return response()->json(["status" => 400, "message" => "Required param not passed."], 200);
    	}

        $table = $request->table;
        $res = [];
        switch($table) {
            case "CourseActivityArchiveProd" :
                $res = $this->resetCourseActivityArchiveProdTable();
                break;
            case "CourseActivityProd" :
                $res = $this->resetCourseActivityProdTable();
                break;
            case "PinRotationsProd" :
                $res = $this->resetPinRotationsProdTable();
                break;
            case "PinCoordinatesProd" :
                $res = $this->resetPinCoordinatesProdTable();
                break;
            // default : break;
        }
        
        return response()->json($res);
    }

    private function resetCourseActivityArchiveProdTable() {
        $clubs = Club::with('courses')->get();
        foreach ($clubs as $club) {
            $param = []; //
            $param["parent_course_name"] = $club->site_name; //
            $courses = $club->courses;
            $login = [];
            if($club->domain_type == "visagenet") {
                $login = loginVccVisageNet($club->site_name);
            } else {
                $login = loginVccVisageGolf($club->site_name);
            }
            if($login && $login["success"]) {
                //
                $param["child_course_names"] = [];
                $cookie = $login["cookie"];
                foreach ($courses as $course) {
                    //
                    $param["child_course_names"][$course->course_name] = [];
                    $param["child_course_names"][$course->course_name]["course_activity"] = [];
                    //
                    $GreensWithPinPlacements = [];
                    if($club->domain_type == "visagenet") {
                        $GreensWithPinPlacements = getPinPlacementsAtVisageNet($cookie, $club->site_id, $course->course_number);
                    } else {
                        $GreensWithPinPlacements = getPinPlacementsAtVisageGolf($cookie, $club->site_id, $course->course_number);
                    }
                    $RotationSets = $GreensWithPinPlacements["RotationSets"];
                    $ApproachViews = $GreensWithPinPlacements["ApproachViews"];
                    foreach ($RotationSets as $RotationSet) {
                        $param["child_course_names"][$course->course_name]["course_activity"][$RotationSet["RotationSetType"]] = [];
                        //
                        $Rotations = $RotationSet["Rotations"];
                        foreach ($Rotations as $Rotation) {
                            $param["child_course_names"][$course->course_name]["course_activity"][$RotationSet["RotationSetType"]][$Rotation["RotationNumber"]] = [];
                            //
                            foreach ($ApproachViews as $ApproachView) {
                                $param["child_course_names"][$course->course_name]["course_activity"][$RotationSet["RotationSetType"]][$Rotation["RotationNumber"]][$ApproachView["Hole"]] = [];
                                //
                                // $PinPlacements = $ApproachView["PinPlacements"];
                                // foreach ($PinPlacements as $PinPlacement) {
                                //     if($Rotation["RotationNumber"] == $PinPlacement["Name"]) {
                                //         $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]][$Rotation["RotationNumber"]][$ApproachView["Hole"]]["lat"] = (float)number_format((float)$PinPlacement["Latitude"], 6);
                                //         $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]][$Rotation["RotationNumber"]][$ApproachView["Hole"]]["long"] = (float)number_format((float)$PinPlacement["Longitude"], 6);
                                //     }
                                // }
                            }
                        }
                    }

                }
                //
                $lambdaApi = "https://5yjcrgi025.execute-api.us-east-1.amazonaws.com/accupin_dev/resetCourseTables";
                $params = [
                    "table" => "CourseActivityArchiveProd",
                    "data" => json_encode($param),
                ];
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => '*/*'
                ])->post($lambdaApi, $params);
                // dd('***********', $response->json());
                $res = $response->json();
                //
                // dd('CourseActivityArchiveProd>>>', $param["parent_course_name"], $res);
            }
        }
        //
        return true;
    }

    private function resetCourseActivityProdTable() {
        $clubs = Club::with('courses')->get();
        foreach ($clubs as $club) {
            $param = []; //
            $param["parent_course_name"] = $club->site_name; //
            $courses = $club->courses;
            $login = [];
            if($club->domain_type == "visagenet") {
                $login = loginVccVisageNet($club->site_name);
            } else {
                $login = loginVccVisageGolf($club->site_name);
            }
            if($login && $login["success"]) {
                //
                $param["child_course_names"] = [];
                $cookie = $login["cookie"];
                foreach ($courses as $course) {
                    //
                    $param["child_course_names"][$course->course_name] = [];
                    $param["child_course_names"][$course->course_name]["course_activity"] = [];
                    //
                    $GreensWithPinPlacements = [];
                    if($club->domain_type == "visagenet") {
                        $GreensWithPinPlacements = getPinPlacementsAtVisageNet($cookie, $club->site_id, $course->course_number);
                    } else {
                        $GreensWithPinPlacements = getPinPlacementsAtVisageGolf($cookie, $club->site_id, $course->course_number);
                    }
                    $RotationSets = $GreensWithPinPlacements["RotationSets"];
                    $ApproachViews = $GreensWithPinPlacements["ApproachViews"];
                    //
                    foreach ($ApproachViews as $ApproachView) {
                        //
                        $param["child_course_names"][$course->course_name]["course_activity"][$ApproachView["Hole"]] = [];
                        //
                        $param["child_course_names"][$course->course_name]["course_activity"][$ApproachView["Hole"]]["distance"] = "";
                        $param["child_course_names"][$course->course_name]["course_activity"][$ApproachView["Hole"]]["down"] = "";
                        $param["child_course_names"][$course->course_name]["course_activity"][$ApproachView["Hole"]]["up"] = "";
                        $param["child_course_names"][$course->course_name]["course_activity"][$ApproachView["Hole"]]["left"] = "";
                        $param["child_course_names"][$course->course_name]["course_activity"][$ApproachView["Hole"]]["right"] = "";
                        $param["child_course_names"][$course->course_name]["course_activity"][$ApproachView["Hole"]]["rotation"] = "";
                        $param["child_course_names"][$course->course_name]["course_activity"][$ApproachView["Hole"]]["last_update"] = "";
                    }
                }
                //
                $lambdaApi = "https://5yjcrgi025.execute-api.us-east-1.amazonaws.com/accupin_dev/resetCourseTables";
                $params = [
                    "table" => "CourseActivityProd",
                    "data" => json_encode($param),
                ];
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => '*/*'
                ])->post($lambdaApi, $params);
                // dd('***********', $response->json());
                $res = $response->json();
                //
                // dd('CourseActivityProd>>>', $param["parent_course_name"], $res);
            }
        }
        //
        return true;
    }

    private function resetPinRotationsProdTable() {
        $clubs = Club::with('courses')->get();
        foreach ($clubs as $club) {
            $param = []; //
            $param["parent_course_name"] = $club->site_name; //
            // dd('=1=', $param["parent_course_name"]);
            $courses = $club->courses;
            $login = [];
            if($club->domain_type == "visagenet") {
                $login = loginVccVisageNet($club->site_name);
            } else {
                $login = loginVccVisageGolf($club->site_name);
            }
            if($login && $login["success"]) {
                //
                $param["child_course_names"] = [];
                $cookie = $login["cookie"];
                foreach ($courses as $course) {
                    //
                    $param["child_course_names"][$course->course_name] = [];
                    $param["child_course_names"][$course->course_name]["rotations"] = [];
                    //
                    $GreensWithPinPlacements = [];
                    if($club->domain_type == "visagenet") {
                        $GreensWithPinPlacements = getPinPlacementsAtVisageNet($cookie, $club->site_id, $course->course_number);
                    } else {
                        $GreensWithPinPlacements = getPinPlacementsAtVisageGolf($cookie, $club->site_id, $course->course_number);
                    }
                    $RotationSets = $GreensWithPinPlacements["RotationSets"];
                    $ApproachViews = $GreensWithPinPlacements["ApproachViews"];
                    foreach ($RotationSets as $RotationSet) {
                        $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]] = [];
                        //
                        $Rotations = $RotationSet["Rotations"];
                        foreach ($Rotations as $Rotation) {
                            $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]][$Rotation["RotationNumber"]] = [];
                            $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]][$Rotation["RotationNumber"]]["pin_coordinates"] = [];
                            //
                            foreach ($ApproachViews as $ApproachView) {
                                $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]][$Rotation["RotationNumber"]]["pin_coordinates"][$ApproachView["Hole"]] = [];
                                //
                                $PinPlacements = $ApproachView["PinPlacements"];
                                //
                                foreach ($PinPlacements as $PinPlacement) {
                                    if($Rotation["RotationNumber"] == $PinPlacement["Name"]) {
                                        $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]][$Rotation["RotationNumber"]]["pin_coordinates"][$ApproachView["Hole"]]["lat"] = number_format((float)$PinPlacement["Latitude"], 6);
                                        $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]][$Rotation["RotationNumber"]]["pin_coordinates"][$ApproachView["Hole"]]["long"] = number_format((float)$PinPlacement["Longitude"], 6);
                                    }
                                }
                            }
                        }
                    }

                }
                //
                $lambdaApi = "https://5yjcrgi025.execute-api.us-east-1.amazonaws.com/accupin_dev/resetCourseTables";
                $params = [
                    "table" => "PinRotationsProd",
                    "data" => json_encode($param),
                ];
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => '*/*'
                ])->post($lambdaApi, $params);
                // dd('***********', $response->json());
                $res = $response->json();
                //
                // dd('PinRotationsProd>>>', $param["parent_course_name"], $res);
            }
        }
        //
        return true;
    }

    private function resetPinCoordinatesProdTable() {
        $clubs = Club::with('courses')->get();
        foreach ($clubs as $club) {
            $param = []; //
            $param["parent_course_name"] = $club->site_name; //
            $param["multiple_child_courses"] = count($club->courses) > 1 ? true : false; //
            $courses = $club->courses;
            $login = [];
            if($club->domain_type == "visagenet") {
                $login = loginVccVisageNet($club->site_name);
            } else {
                $login = loginVccVisageGolf($club->site_name);
            }
            if($login && $login["success"]) {
                //
                $param["child_course_names"] = [];
                $cookie = $login["cookie"];
                foreach ($courses as $course) {
                    //
                    $param["child_course_names"][$course->course_name] = [];
                    $param["child_course_names"][$course->course_name]["images_available"] = true;
                    $param["child_course_names"][$course->course_name]["long_factor"] = "";
                    $param["child_course_names"][$course->course_name]["time_zone"] = "America/Los_Angeles";
                    $param["child_course_names"][$course->course_name]["visage_update"] = true;
                    $param["child_course_names"][$course->course_name]["pin_coordinates"] = [];
                    $param["child_course_names"][$course->course_name]["rotations"] = [];
                    //
                    $GreensWithPinPlacements = [];
                    if($club->domain_type == "visagenet") {
                        $GreensWithPinPlacements = getPinPlacementsAtVisageNet($cookie, $club->site_id, $course->course_number);
                    } else {
                        $GreensWithPinPlacements = getPinPlacementsAtVisageGolf($cookie, $club->site_id, $course->course_number);
                    }
                    $RotationSets = $GreensWithPinPlacements["RotationSets"];
                    $ApproachViews = $GreensWithPinPlacements["ApproachViews"];
                    // pin_coordinates
                    foreach ($ApproachViews as $ApproachView) {
                        $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]] = [];
                        $PoiData = $ApproachView["PoiData"];
                        foreach ($PoiData as $PoiDataItem) {
                            if($PoiDataItem["Name"] == "CenterOfGreen") {
                                $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["cog_lat"] = number_format((float)$PoiDataItem["Latitude"], 6);
                                $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["cog_long"] = number_format((float)$PoiDataItem["Longitude"], 6);
                                $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["image_height_pix"] = "";
                                $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["image_height_yards"] = "";
                                $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["image_width_pix"] = "";
                                $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["image_width_yards"] = "";
                                $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["orientation"] = "";
                                
                            }
                        }
                        $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["ul_lat"] = number_format((float)(explode(",", $ApproachView["GreenUpperLeft"])[1]), 6);
                        $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["ul_long"] = number_format((float)(explode(",", $ApproachView["GreenUpperLeft"])[0]), 6);
                        $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["worldToImageTransform"] = $ApproachView["WorldToImageTransform"];
                        $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["upper_left_lat"] = number_format((float)(explode(",", $ApproachView["GreenUpperLeft"])[1]), 6);
                        $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["upper_left_long"] = number_format((float)(explode(",", $ApproachView["GreenUpperLeft"])[0]), 6);
                        $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["lower_right_lat"] = number_format((float)(explode(",", $ApproachView["GreenLowerRight"])[1]), 6);
                        $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["lower_right_long"] = number_format((float)(explode(",", $ApproachView["GreenLowerRight"])[0]), 6);
                    }
                    // rotations
                    foreach ($RotationSets as $RotationSet) {
                        $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]] = [];
                        //
                        $Rotations = $RotationSet["Rotations"];
                        foreach ($Rotations as $Rotation) {
                            $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]][$Rotation["RotationNumber"]] = $Rotation["Marker"];
                        }
                    }

                }
                //
                $lambdaApi = "https://5yjcrgi025.execute-api.us-east-1.amazonaws.com/accupin_dev/resetCourseTables";
                $params = [
                    "table" => "PinCoordinatesProd",
                    "data" => json_encode($param),
                ];
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => '*/*'
                ])->post($lambdaApi, $params);
                // dd('***********', $response->json());
                $res = $response->json();
                //
                // dd('PinCoordinatesProd>>>', $param["parent_course_name"], $res);
            }
        }
        //
        return true;
    }

    public function syncClubTables(Request $request)
    {
        $validator = Validator::make($request->all(), [
    		'club_id' => 'required|max:55',
    	]);

    	if ($validator->fails()) {
    		return response()->json(["status" => 400, "message" => "Required param not passed."], 200);
    	}

        $club_id = $request->club_id;
        $res = [];
        $res["CourseActivityArchiveProd"] = $this->syncCourseActivityArchiveProdTable($club_id);
        $res["CourseActivityProd"] = $this->syncCourseActivityProdTable($club_id);
        $res["PinRotationsProd"] = $this->syncPinRotationsProdTable($club_id);
        $res["PinCoordinatesProd"] = $this->syncPinCoordinatesProdTable($club_id);
        $res["status"] = 200;
        $res["message"] = "Successfully Synced.";
        
        return response()->json($res);
    }

    private function syncCourseActivityArchiveProdTable($club_id) {
        $club = Club::with('courses')->find($club_id);
        $param = []; //
        $param["parent_course_name"] = $club->site_name; //
        $courses = $club->courses;
        $login = [];
        if($club->domain_type == "betavisagenet") {
            $login = loginBetaVisageNet($club->site_name);
        } else if($club->domain_type == "visagenet") {
            $login = loginVccVisageNet($club->site_name);
        } else {
            $login = loginVccVisageGolf($club->site_name);
        }
        if($login && $login["success"]) {
            //
            $param["child_course_names"] = [];
            $cookie = $login["cookie"];
            foreach ($courses as $course) {
                //
                $param["child_course_names"][$course->course_name] = [];
                $param["child_course_names"][$course->course_name]["course_activity"] = [];
                //
                $GreensWithPinPlacements = [];
                if($club->domain_type == "betavisagenet") {
                    $GreensWithPinPlacements = getPinPlacementsAtBetaVisageNet($cookie, $club->site_id, $course->course_number);
                } else if($club->domain_type == "visagenet") {
                    $GreensWithPinPlacements = getPinPlacementsAtVisageNet($cookie, $club->site_id, $course->course_number);
                } else {
                    $GreensWithPinPlacements = getPinPlacementsAtVisageGolf($cookie, $club->site_id, $course->course_number);
                }
                $RotationSets = $GreensWithPinPlacements["RotationSets"];
                $ApproachViews = $GreensWithPinPlacements["ApproachViews"];
                $RotationSetType2Index = 1;
                $RotationSetType2Arr = [];
                foreach ($RotationSets as $RotationSet) {
                    $param["child_course_names"][$course->course_name]["course_activity"][$RotationSet["RotationSetType"]] = [];
                    //
                    $Rotations = $RotationSet["Rotations"];
                    // RotationSetType === 1
                    if($RotationSet["RotationSetType"] === 1) {
                        foreach ($Rotations as $Rotation) {
                            $param["child_course_names"][$course->course_name]["course_activity"][$RotationSet["RotationSetType"]][$Rotation["RotationNumber"]] = (object)[];
                        }
                        //
                        // foreach ($ApproachViews as $ApproachView) {
                        //     $param["child_course_names"][$course->course_name]["course_activity"][$RotationSet["RotationSetType"]][$Rotation["RotationNumber"]][$ApproachView["Hole"]] = (object)[];
                        //     //
                        //     // $PinPlacements = $ApproachView["PinPlacements"];
                        //     // foreach ($PinPlacements as $PinPlacement) {
                        //     //     if($Rotation["RotationNumber"] == $PinPlacement["Name"]) {
                        //     //         $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]][$Rotation["RotationNumber"]][$ApproachView["Hole"]]["lat"] = (float)number_format((float)$PinPlacement["Latitude"], 6);
                        //     //         $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]][$Rotation["RotationNumber"]][$ApproachView["Hole"]]["long"] = (float)number_format((float)$PinPlacement["Longitude"], 6);
                        //     //     }
                        //     // }
                        // }
                    }
                    // RotationSetType === 2
                    if($RotationSet["RotationSetType"] === 2) {
                        $RotationSetType2Arr[$RotationSetType2Index] = (object)[];
                        // $param["child_course_names"][$course->course_name]["course_activity"][$RotationSet["RotationSetType"]][$RotationSetType2Index] = (object)[];
                        $RotationSetType2Index++;
                    }
                }
                // 
                $param["child_course_names"][$course->course_name]["course_activity"]["2"] = $RotationSetType2Arr;

            }
            //
            $lambdaApi = "https://5yjcrgi025.execute-api.us-east-1.amazonaws.com/accupin_dev/resetCourseTables";
            $params = [
                "table" => "CourseActivityArchiveProd",
                "data" => json_encode($param),
            ];
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => '*/*'
            ])->post($lambdaApi, $params);
            // dd('***********', $response->json());
            $res = $response->json();
            //
            // dd('CourseActivityArchiveProd>>>', $param["parent_course_name"], $res);
            return $res;
        }
        //
        return false;
    }

    private function syncCourseActivityProdTable($club_id) {
        $club = Club::with('courses')->find($club_id);
        $param = []; //
        $param["parent_course_name"] = $club->site_name; //
        $courses = $club->courses;
        $login = [];
        if($club->domain_type == "betavisagenet") {
            $login = loginBetaVisageNet($club->site_name);
        } else if($club->domain_type == "visagenet") {
            $login = loginVccVisageNet($club->site_name);
        } else {
            $login = loginVccVisageGolf($club->site_name);
        }
        if($login && $login["success"]) {
            //
            $param["child_course_names"] = [];
            $cookie = $login["cookie"];
            foreach ($courses as $course) {
                //
                $param["child_course_names"][$course->course_name] = [];
                $param["child_course_names"][$course->course_name]["course_activity"] = [];
                //
                $GreensWithPinPlacements = [];
                if($club->domain_type == "betavisagenet") {
                    $GreensWithPinPlacements = getPinPlacementsAtBetaVisageNet($cookie, $club->site_id, $course->course_number);
                } else if($club->domain_type == "visagenet") {
                    $GreensWithPinPlacements = getPinPlacementsAtVisageNet($cookie, $club->site_id, $course->course_number);
                } else {
                    $GreensWithPinPlacements = getPinPlacementsAtVisageGolf($cookie, $club->site_id, $course->course_number);
                }
                $RotationSets = $GreensWithPinPlacements["RotationSets"];
                $ApproachViews = $GreensWithPinPlacements["ApproachViews"];
                //
                foreach ($ApproachViews as $ApproachView) {
                    //
                    $param["child_course_names"][$course->course_name]["course_activity"][$ApproachView["Hole"]] = [];
                    //
                    $param["child_course_names"][$course->course_name]["course_activity"][$ApproachView["Hole"]]["distance"] = "";
                    $param["child_course_names"][$course->course_name]["course_activity"][$ApproachView["Hole"]]["down"] = "";
                    $param["child_course_names"][$course->course_name]["course_activity"][$ApproachView["Hole"]]["up"] = "";
                    $param["child_course_names"][$course->course_name]["course_activity"][$ApproachView["Hole"]]["left"] = "";
                    $param["child_course_names"][$course->course_name]["course_activity"][$ApproachView["Hole"]]["right"] = "";
                    $param["child_course_names"][$course->course_name]["course_activity"][$ApproachView["Hole"]]["rotation"] = "";
                    $param["child_course_names"][$course->course_name]["course_activity"][$ApproachView["Hole"]]["last_update"] = date("Y-m-d H:m:s");
                }
            }
            //
            $lambdaApi = "https://5yjcrgi025.execute-api.us-east-1.amazonaws.com/accupin_dev/resetCourseTables";
            $params = [
                "table" => "CourseActivityProd",
                "data" => json_encode($param),
            ];
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => '*/*'
            ])->post($lambdaApi, $params);
            // dd('***********', $response->json());
            $res = $response->json();
            //
            // dd('CourseActivityProd>>>', $param["parent_course_name"], $res);
            return $res;
        }
        //
        return false;
    }

    private function syncPinRotationsProdTable($club_id) {
        $club = Club::with('courses')->find($club_id);
        $param = []; //
        $param["parent_course_name"] = $club->site_name; //
        // dd('=1=', $param["parent_course_name"]);
        $courses = $club->courses;
        $login = [];
        if($club->domain_type == "betavisagenet") {
            $login = loginBetaVisageNet($club->site_name);
        } else if($club->domain_type == "visagenet") {
            $login = loginVccVisageNet($club->site_name);
        } else {
            $login = loginVccVisageGolf($club->site_name);
        }
        if($login && $login["success"]) {
            //
            $param["child_course_names"] = [];
            $cookie = $login["cookie"];
            foreach ($courses as $course) {
                //
                $param["child_course_names"][$course->course_name] = [];
                $param["child_course_names"][$course->course_name]["rotations"] = [];
                //
                $GreensWithPinPlacements = [];
                if($club->domain_type == "betavisagenet") {
                    $GreensWithPinPlacements = getPinPlacementsAtBetaVisageNet($cookie, $club->site_id, $course->course_number);
                } else if($club->domain_type == "visagenet") {
                    $GreensWithPinPlacements = getPinPlacementsAtVisageNet($cookie, $club->site_id, $course->course_number);
                } else {
                    $GreensWithPinPlacements = getPinPlacementsAtVisageGolf($cookie, $club->site_id, $course->course_number);
                }
                $RotationSets = $GreensWithPinPlacements["RotationSets"];
                $ApproachViews = $GreensWithPinPlacements["ApproachViews"];
                foreach ($RotationSets as $RotationSet) {
                    $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]] = [];
                    //
                    $Rotations = $RotationSet["Rotations"];
                    foreach ($Rotations as $Rotation) {
                        $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]][$Rotation["RotationNumber"]] = [];
                        $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]][$Rotation["RotationNumber"]]["pin_coordinates"] = [];
                        //
                        foreach ($ApproachViews as $ApproachView) {
                            $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]][$Rotation["RotationNumber"]]["pin_coordinates"][$ApproachView["Hole"]] = [];
                            //
                            $PinPlacements = $ApproachView["PinPlacements"];
                            //
                            foreach ($PinPlacements as $PinPlacement) {
                                if($Rotation["RotationNumber"] == $PinPlacement["Name"]) {
                                    $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]][$Rotation["RotationNumber"]]["pin_coordinates"][$ApproachView["Hole"]]["lat"] = number_format((float)$PinPlacement["Latitude"], 6);
                                    $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]][$Rotation["RotationNumber"]]["pin_coordinates"][$ApproachView["Hole"]]["long"] = number_format((float)$PinPlacement["Longitude"], 6);
                                }
                            }
                        }
                    }
                }

            }
            //
            $lambdaApi = "https://5yjcrgi025.execute-api.us-east-1.amazonaws.com/accupin_dev/resetCourseTables";
            $params = [
                "table" => "PinRotationsProd",
                "data" => json_encode($param),
            ];
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => '*/*'
            ])->post($lambdaApi, $params);
            // dd('***********', $response->json());
            $res = $response->json();
            //
            // dd('PinRotationsProd>>>', $param["parent_course_name"], $res);
            return $res;
        }
        //
        return false;
    }

    private function syncPinCoordinatesProdTable($club_id) {
        $club = Club::with('courses')->find($club_id);
        $param = []; //
        $param["parent_course_name"] = $club->site_name; //
        $param["multiple_child_courses"] = count($club->courses) > 1 ? true : false; //
        $courses = $club->courses;
        $login = [];
        if($club->domain_type == "betavisagenet") {
            $login = loginBetaVisageNet($club->site_name);
        } else if($club->domain_type == "visagenet") {
            $login = loginVccVisageNet($club->site_name);
        } else {
            $login = loginVccVisageGolf($club->site_name);
        }
        if($login && $login["success"]) {
            //
            $param["child_course_names"] = [];
            $cookie = $login["cookie"];
            foreach ($courses as $course) {
                //
                $param["child_course_names"][$course->course_name] = [];
                $param["child_course_names"][$course->course_name]["images_available"] = true;
                $param["child_course_names"][$course->course_name]["long_factor"] = "";
                $param["child_course_names"][$course->course_name]["time_zone"] = "America/Los_Angeles";
                $param["child_course_names"][$course->course_name]["visage_update"] = true;
                $param["child_course_names"][$course->course_name]["pin_coordinates"] = [];
                $param["child_course_names"][$course->course_name]["rotations"] = [];
                //
                $GreensWithPinPlacements = [];
                if($club->domain_type == "betavisagenet") {
                    $GreensWithPinPlacements = getPinPlacementsAtBetaVisageNet($cookie, $club->site_id, $course->course_number);
                } else if($club->domain_type == "visagenet") {
                    $GreensWithPinPlacements = getPinPlacementsAtVisageNet($cookie, $club->site_id, $course->course_number);
                } else {
                    $GreensWithPinPlacements = getPinPlacementsAtVisageGolf($cookie, $club->site_id, $course->course_number);
                }
                $RotationSets = $GreensWithPinPlacements["RotationSets"];
                $ApproachViews = $GreensWithPinPlacements["ApproachViews"];
                // pin_coordinates
                foreach ($ApproachViews as $ApproachView) {
                    $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]] = [];
                    $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["ul_lat"] = number_format((float)(explode(",", $ApproachView["GreenUpperLeft"])[1]), 6);
                    $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["ul_long"] = number_format((float)(explode(",", $ApproachView["GreenUpperLeft"])[0]), 6);
                    $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["worldToImageTransform"] = $ApproachView["WorldToImageTransform"];
                    $upper_left_lat = number_format((float)(explode(",", $ApproachView["GreenUpperLeft"])[1]), 6);
                    $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["upper_left_lat"] = $upper_left_lat;
                    $upper_left_long = number_format((float)(explode(",", $ApproachView["GreenUpperLeft"])[0]), 6);
                    $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["upper_left_long"] = $upper_left_long;
                    $lower_right_lat = number_format((float)(explode(",", $ApproachView["GreenLowerRight"])[1]), 6);
                    $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["lower_right_lat"] = $lower_right_lat;
                    $lower_right_long = number_format((float)(explode(",", $ApproachView["GreenLowerRight"])[0]), 6);
                    $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["lower_right_long"] = $lower_right_long;
                    // 
                    $PoiData = $ApproachView["PoiData"];
                    foreach ($PoiData as $PoiDataItem) {
                        if($PoiDataItem["Name"] == "CenterOfGreen") {
                            $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["cog_lat"] = number_format((float)$PoiDataItem["Latitude"], 6);
                            $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["cog_long"] = number_format((float)$PoiDataItem["Longitude"], 6);
                            $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["image_height_pix"] = 377;
                            $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["image_height_yards"] = number_format(getImageHeightYards($upper_left_lat, $upper_left_long, $lower_right_lat, $lower_right_long), 1);
                            $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["image_width_pix"] = 377;
                            $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["image_width_yards"] = number_format(getImageWidthYards($upper_left_lat, $upper_left_long, $lower_right_lat, $lower_right_long), 1);
                            $param["child_course_names"][$course->course_name]["pin_coordinates"][$ApproachView["Hole"]]["orientation"] = number_format(getOrientation($upper_left_lat, $upper_left_long, $lower_right_lat, $lower_right_long), 1);
                        }
                    }
                    
                }
                // rotations
                foreach ($RotationSets as $RotationSet) {
                    // $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]] = [];
                    //
                    $Rotations = $RotationSet["Rotations"];
                    $rotationLength = count($param["child_course_names"][$course->course_name]["rotations"]);
                    if ($rotationLength === $RotationSet["RotationSetType"]) {
                        // dd('>>>>', $rotationLength, $RotationSet["RotationSetType"]);
                        foreach ($Rotations as $Rotation) {
                            $rSetTypeLength = count($param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]]);
                            $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]][($rSetTypeLength + 1)] = $Rotation["Marker"];
                        }
                    } else {
                        // dd('000000000', $rotationLength, $RotationSet["RotationSetType"]);
                        $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]] = [];
                        foreach ($Rotations as $Rotation) {
                            $param["child_course_names"][$course->course_name]["rotations"][$RotationSet["RotationSetType"]][$Rotation["RotationNumber"]] = $Rotation["Marker"];
                        }
                    }
                }

            }
            // dd('========', $param["child_course_names"]["Watermark"]["rotations"]);
            //
            $lambdaApi = "https://5yjcrgi025.execute-api.us-east-1.amazonaws.com/accupin_dev/resetCourseTables";
            $params = [
                "table" => "PinCoordinatesProd",
                "data" => json_encode($param),
            ];
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => '*/*'
            ])->post($lambdaApi, $params);
            // dd('***********', $response->json());
            $res = $response->json();
            //
            // dd('PinCoordinatesProd>>>', $param["parent_course_name"], $res);
            return $res;
        }
        //
        return false;
    }

    // 
    public function syncPCPTablesOfClub(Request $request)
    {
        $validator = Validator::make($request->all(), [
    		'club_id' => 'required|max:55',
    	]);

    	if ($validator->fails()) {
    		return response()->json(["status" => 400, "message" => "Required param not passed."], 200);
    	}

        $club_id = $request->club_id;
        $res = [];
        $res["PinCoordinatesProd"] = $this->syncPinCoordinatesProdTable($club_id);
        $res["status"] = 200;
        $res["message"] = "Successfully Synced.";
        
        return response()->json($res);
    }
}
