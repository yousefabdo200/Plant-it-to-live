<?php

namespace App\Http\Controllers;

use App\Exports\PlantsSuggesionExport;
use App\Models\Plant;
use App\Models\Suggested_plant;
use App\Models\User;
use App\Models\Admin;
use Firebase\JWT\JWK;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\Console\Completion\Suggestion;
use Tymon\JWTAuth\JWTGuard;
use Illuminate\Http\Request;
use App\Mail\AdminChangePassword;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Exports\PlantsExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use function Laravel\Prompts\select;

class AdminController extends Controller
{
    //
    use ApiResponse;
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['login','forgetpassword','resetpassword','download']]);

    }
    public function home()
    {
        $data=Admin::find(Auth()->user()->id);
        return $this->SuccessResponse($data);
    }
    public function login(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
           // return $this->response($validator->errors(), 'Validation errors', 406);
           return $this->validationerrors($validator->errors());
        }
        // Attempt authentication
        if (! $token = auth('admin')->attempt($validator->validated())) {
           // return response('',404);
         return $this->failed("your login falid try aging with correct email and pasword");
            //return response()->json(['error' => 'Unauthorized'], 401);
        }
       // return response(['token'=>$token],200);
       return $this->SuccessResponse(['token'=>$token]);
    }
    //get all  users
    public function users()//get all active users ;
    {
        $users=User::paginate(50);//get only 50 users
        $totalUsersCount = User::count();
        return $this->SuccessResponse([
            'users count'=>$totalUsersCount,
            'users'=>$users
        ]);
    }
    //edit users
    public function edit(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'access_Key'=>'required|exists:admins,access_Key',
            'name'=>'required',
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return $this->validationerrors($validator->errors());
         }
        $admin=Admin::find(auth()->user()->id);
        $admin->name=$validator->validated()['name'];
        $admin->email=$validator->validated()['email'];
        if($admin->save())
            return $this->SuccessResponse(null,"data is updated");
        return $this->failed("can't edit this user now try agin");
        }
        public function forgetpassword(Request $request)//get email from the forget password
    {
        $validator=Validator::make($request->all(),
        [
            'email' => 'required|email|exists:admins,email'
        ]);
        if($validator->fails())
        {
            return $this->validationerrors($validator->errors());
        }
        $user=Admin::where('email',$request->email)->first();
        if(!$user)
        {
            return $this->failed('Email not found');
        }
        //token
        $token = Str::random(64);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );
        Mail::to($user->email)->send(new AdminChangePassword($name=$user->name,$token));
        return $this->SuccessResponse("Password reset email sent successfully.");
    }
    public function resetpassword(Request $request)
    {
        $validator=Validator::make($request->all(),
        [
            'token' => 'required|exists:password_reset_tokens,token',
            'access_Key'=>'required|exists:admins,access_Key',
            'password' => 'required|min:6',
            'confirm_password'=>'required|required|min:6|same:password',
        ]);
        if($validator->fails())
        {
            return $this->validationerrors($validator->errors());
        }
        $data=DB::table('password_reset_tokens')->where('token',$request->token)->first();
        if(!$data)
        {
            return $this->failed('Not found');
        }
        $user=Admin::where('email',$data->email)->first();
        if(!$user)
        {
            return $this->failed('Email Not found');
        }
        $user->password = bcrypt($request->password);
        $user->save();
       DB::table('password_reset_tokens')->where('email',$user->email)->delete();
       $token= JWTAuth::fromUser($user);
       return $this->SuccessResponse(['token'=>$token],"Password Saved successfully.");
    }
    public function changepassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'access_Key'=>'required|exists:admins,access_Key',
            'oldpassword'=>'required',
            'password' => 'required|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            // return $this->response($validator->errors(), 'Validation errors', 406);
            return $this->validationerrors($validator->errors());
         }
         if($validator->validated()['oldpassword']===$validator->validated()['password'])
            return $this->failed("The new password must not match the old password");
         $user=Admin::find(Auth()->user()->id);
         if(Hash::check($validator->validated()['oldpassword'], $user->password))
           {
                $user->password=bcrypt($validator->validated()['password']);
                return  ($user->save())?  $this->SuccessResponse(null,"password is updated"):$this->failed("Try agin later");
           }
        else
        {
            return $this->failed("please check the old password");
        }
    }
    public function addplant(Request $request)
    {
        //validation
        $validator=Validator::make($request->all(),[
            'common_name'=>'required|string',
            'scientific_name'=>'required|string',
            'watering'=>'required|string',
            'fertilizer'=>'required|string',
            'sunlight'=>'required|string',
            'pruning'=>'required|string',
            'img'=>'required|image|mimes:jpeg,png,jpg,gif,svg',
            'water_amount'=>'required|string',
            'fertilizer_amount'=>'required|string',
            'sun_per_day'=>'required|string',
            'soil_salinty'=>'required|string',
            'appropriate_season'=>'required|string',
        ]);
        if($validator->fails())
        {
            return $this->validationerrors($validator->errors());
        }
        $plant=new Plant();
        $plant->common_name=$request->common_name;
        $plant->scientific_name=$request->scientific_name;
        $plant->watering=$request->watering;
        $plant->fertilizer=$request->fertilizer;
        $plant->sunlight=$request->sunlight;
        $plant->pruning=$request->pruning;
        $plant->water_amount=$request->water_amount;
        $plant->fertilizer_amount=$request->fertilizer_amount;
        $plant->sun_per_day=$request->sun_per_day;
        $plant->soil_salinty=$request->soil_salinty;
        $plant->appropriate_season=$request->appropriate_season;
        $img=$request->file('img');
        $filename=time().'.'.$img->getClientOriginalExtension();
        //$img->move(public_path('plantImges'),$filename);
        //$filepath = 'C:/xampp/htdocs/plant-it-to-live/frontend/src/assets/images/' . $filename;
        $finalPath = 'C:/xampp/htdocs/plant-it-to-live/frontend/src/assets/images';
        $img->move($finalPath, $filename);
        $plant->img = $filename;
        $plant->admin_id=Auth()->user()->id;
        $plant->save();
        return $this->SuccessResponse();
    }
    public function plants()//get all admin plants
    {
        $plants=Plant::where('admin_id',Auth()->user()->id)->paginate(50);
        $plants->makeHidden(['admin_id','updated_at']);
        return $this->SuccessResponse($plants);
    }
    //single plant
    public function plant(Request $request)
    {

        $plant=Plant::find($request->id);
        if(!$plant)
        {
            return $this->failed("Plant not found");
        }
        $plant->makeHidden(['admin_id','updated_at','created_at']);
        return $this->SuccessResponse($plant);
    }
    //edit plant
    public function editplant(Request $request)
    {
        //validation
        $validator=Validator::make($request->all(),[
            'id'=>'required|exists:plants,id',
            'common_name'=>'required|string',
            'scientific_name'=>'required|string',
            'watering'=>'required|string',
            'fertilizer'=>'required|string',
            'sunlight'=>'required|string',
            'pruning'=>'required|string',
            'img'=>'image|mimes:jpeg,png,jpg,gif,svg',
            'water_amount'=>'required|string',
            'fertilizer_amount'=>'required|string',
            'sun_per_day'=>'required|string',
            'soil_salinty'=>'required|string',
            'appropriate_season'=>'required|string',
        ]);
        if($validator->fails())
        {
            return $this->validationerrors($validator->errors());
        }
        $plant=Plant::find($request->id);

        if(isset($request->img))
        {
            $filePath = $plant->img; // Assuming $plant->img contains the relative path
            if($filePath!=null)
                unlink('C:/xampp/htdocs/plant-it-to-live/frontend/src/assets/images/'.$plant->img);
            $img=$request->file('img');
            $filename=time().'.'.$img->getClientOriginalExtension();
            $finalPath = 'C:/xampp/htdocs/plant-it-to-live/frontend/src/assets/images';
            $img->move($finalPath, $filename);
            $plant->img = $filename;
        }
        $plant->common_name=$request->common_name;
        $plant->scientific_name=$request->scientific_name;
        $plant->watering=$request->watering;
        $plant->fertilizer=$request->fertilizer;
        $plant->sunlight=$request->sunlight;
        $plant->pruning=$request->pruning;
        $plant->water_amount=$request->water_amount;
        $plant->fertilizer_amount=$request->fertilizer_amount;
        $plant->sun_per_day=$request->sun_per_day;
        $plant->soil_salinty=$request->soil_salinty;
        $plant->appropriate_season=$request->appropriate_season;
        $plant->save();
        return $this->SuccessResponse();
    }
    public function deleteplant(Request $request)//delete the plant
    {
        //validation
        $validator=Validator::make($request->all(),[
            'id'=>'required|exists:plants,id'
        ]);
        if($validator->fails())
        {
            return $this->validationerrors($validator->errors());
        }
        $plant=Plant::find($request->id);
        $filePath = $plant->img; // Assuming $plant->img contains the relative path
        $suggested=Suggested_plant::where('plant_id',$plant->id)->first();
        if ($suggested)
        {
            $suggested->approved=0;
            $suggested->plant_id=null;
            $suggested->save();
        }
        $plant->users()->detach();
        if(!$plant->delete())
        {
            return $this->failed("try again");
        }

        if($filePath!=null)
            unlink('C:/xampp/htdocs/plant-it-to-live/frontend/src/assets/images/'.$plant->img);
        return $this->SuccessResponse();
    }
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully loged out']);
    }
    public function export(Request $request)
    {
        $fileName = 'plants.xlsx'; // You can generate a dynamic file name if needed
        $filePath = storage_path('app/' . $fileName);

        // Delete the old file if it exists
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        // Export the file
        Excel::store(new PlantsExport(), $fileName);
        // Check if file exists
        if (!file_exists($filePath)) {
            abort(404);
        }
        // Return the file as a response
        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
    public function download($fileName)
    {
        $filePath = storage_path('app/' . $fileName);

        // Check if file exists
        if (!file_exists($filePath)) {
            abort(404);
        }

        // Return the file as a response
        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
    public function delete_user(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'id'=>'required|exists:users,id'
        ]);
        if($validator->fails())
        {
            return $this->validationerrors($validator->errors());
        }
        $user= User::find($request->id);
        if($user->picture!=null&&$user->google_id==null)
        {
            unlink('C:/xampp/htdocs/plant-it-to-live/frontend/src/assets/images/'.$user->picture);
        }
        $user->plants()->detach();
        //$user->suggestions()->delete();
        $plants=$user->suggestions;
       if($plants)
       {
           foreach($plants as $plant)
           {
               if($plant->img!=null)
               {
                   unlink('C:/xampp/htdocs/plant-it-to-live/frontend/src/assets/images/'.$plant->img);
                   $plant->delete();
               }
           }
       }
        if( $user->delete())
            return $this->SuccessResponse();
       return $this->failed();
    }
    public function allsuggestions()
    {
        $suggestions = Suggested_plant::with(['user:id,name'])->paginate(50);
        if($suggestions)
        {
            $suggestions->makeHidden(['admin_id','user_id']);
            return $this->SuccessResponse($suggestions);
        }
        return $this->failed();
    }
    public function suggestion(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'id'=>'required|exists:Suggested_plants,id'
        ]);
        if($validator->fails())
        {
            return $this->validationerrors($validator->errors());
        }
        $plant=Suggested_plant::with(['user'=>function($q)
        {
            $q->select('id', 'name', 'email');
        }])->find($request->id);
        if($plant)
        {
            return $this->SuccessResponse($plant);
        }
        return $this->failed();
    }
    public function acceptsuggestion(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'id'=>'required|exists:Suggested_plants,id'
        ]);
        if($validator->fails())
        {
            return $this->validationerrors($validator->errors());
        }
        $suggestedplant=Suggested_plant::find($request->id);
        if($suggestedplant->plant_id!=null)
        {
            return $this->validationerrors("this plant is already accepted");
        }
        if($suggestedplant)
        {
            $plant=new Plant();
            $imgoldpath="C:/xampp/htdocs/plant-it-to-live/frontend/src/assets/images/".$suggestedplant->img;
            $filename=time().'.'.pathinfo($imgoldpath, PATHINFO_EXTENSION);
            $filepath = "C:/xampp/htdocs/plant-it-to-live/frontend/src/assets/images/".$filename;
            if (file_exists($imgoldpath)) {
                copy($imgoldpath, $filepath);
            }
            $suggestedplant->admin_id=Auth()->user()->id;
            $suggestedplant->approved=1;
            $suggestedplant->plant_id=$plant->id;
            $suggestedplant->save();
            $plant->fill($suggestedplant->only(['common_name','scientific_name','watering','fertilizer','sunlight','pruning','water_amount','fertilizer_amount','sun_per_day','soil_salinty','appropriate_season','admin_id']));
            $plant->img=$filename;
            $plant->save();
            $suggestedplant->admin_id=Auth()->user()->id;
            $suggestedplant->approved=1;
            $suggestedplant->plant_id=$plant->id;
            $suggestedplant->save();

            return $this->SuccessResponse();
        }
        return $this->failed();
    }
    public function editsuggestion(Request $request)
    {
        //validation
        $validator=Validator::make($request->all(),[
            'id'=>'required|exists:Suggested_plants,id',
            'common_name'=>'required|string',
            'scientific_name'=>'required|string',
            'watering'=>'required|string',
            'fertilizer'=>'required|string',
            'sunlight'=>'required|string',
            'pruning'=>'required|string',
            'img'=>'image|mimes:jpeg,png,jpg,gif,svg',
            'water_amount'=>'required|string',
            'fertilizer_amount'=>'required|string',
            'sun_per_day'=>'required|string',
            'soil_salinty'=>'required|string',
            'appropriate_season'=>'required|string',
        ]);
        if($validator->fails())
        {
            return $this->validationerrors($validator->errors());
        }
        $plant=Suggested_plant::find($request->id);
        if(isset($request->img))
        {
            $filePath = $plant->img; // Assuming $plant->img contains the relative path
            if($filePath!=null)
                unlink('C:/xampp/htdocs/plant-it-to-live/frontend/src/assets/images/'.$plant->img);
            $img=$request->file('img');
            $filename=time().'.'.$img->getClientOriginalExtension();
            $finalPath = 'C:/xampp/htdocs/plant-it-to-live/frontend/src/assets/images';
            $img->move($finalPath, $filename);
            $plant->img = $filename;
        }
        $plant->common_name=$request->common_name;
        $plant->scientific_name=$request->scientific_name;
        $plant->watering=$request->watering;
        $plant->fertilizer=$request->fertilizer;
        $plant->sunlight=$request->sunlight;
        $plant->pruning=$request->pruning;
        $plant->water_amount=$request->water_amount;
        $plant->fertilizer_amount=$request->fertilizer_amount;
        $plant->sun_per_day=$request->sun_per_day;
        $plant->soil_salinty=$request->soil_salinty;
        $plant->appropriate_season=$request->appropriate_season;
        $plant->admin_id=Auth()->user()->id;
        $plant->save();
        return $this->SuccessResponse();
    }
    public function deletesuggestion(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'id'=>'required|exists:Suggested_plants,id'
        ]);
        if($validator->fails())
        {
            return $this->validationerrors($validator->errors());
        }
        $plant=Suggested_plant::find($request->id);
        $filePath = $plant->img;
        if($filePath!=null)
            unlink('C:/xampp/htdocs/plant-it-to-live/frontend/src/assets/images/'.$plant->img);
        if($plant)
        {
            $plant->delete();
            return $this->SuccessResponse();
        }
        return $this->failed();
    }
    public function exportsuggest(Request $request)
    {
        $fileName = 'suggestions.xlsx'; // You can generate a dynamic file name if needed
        $filePath = storage_path('app/' . $fileName);
        // Delete the old file if it exists
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        // Export the file
        Excel::store(new PlantsSuggesionExport(), $fileName);
        // Check if file exists
        if (!file_exists($filePath)) {
            abort(404);
        }
        // Return the file as a response
        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
