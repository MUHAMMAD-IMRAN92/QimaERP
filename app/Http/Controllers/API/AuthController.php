<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Support;
use App\FileSystem;
use App\Transaction;
use App\CoffeeSession;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use AWS\CRT\HTTP\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class AuthController extends Controller
{

    private $app_lang;

    public function __construct(Request $request)
    {
        set_time_limit(0);

        $this->app_lang = $request->header('x-app-lang') ?? 'en';
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            // 'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            // throw ValidationException::withMessages([
            //     'email' => ['The provided credentials are incorrect.'],
            // ]);

            return sendError(Config("statuscodes." . $this->app_lang . ".error_messages.INVALID_USER"), 400);
        }

        if ($user->hasRole(['super admin', 'admin'])) {
            return sendError(Config("statuscodes." . $this->app_lang . ".error_messages.BLOCKED"), 400);
        }

        $user->load(['roles', 'center_user']);

        $user->center = null;

        if (isset($user->center_user) && isset($user->center_user->center_id)) {
            $user->center_id = $user->center_user->center_id;
            $user->center = $user->center_user->center;
        }

        $user->makeHidden('center_user');

        // $user->session_no = 1;

        // $latestTransaction = Transaction::where('created_by', $user->id)->orderBy('local_session_no', 'desc')->first();

        // if ($latestTransaction) {
        //     $user->session_no = ($latestTransaction->local_session_no + 1);
        // }

        $session_no = CoffeeSession::max('server_session_id') ?? 0;

        $user->session_no = $session_no + 1;

        $user->token = $user->createToken($request->email)->plainTextToken;

        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.LOGIN"), $user);
    }
    function support(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
        ]);
        $support = new Support();
        $support->title = $request->title;
        $support->description = $request->description;
        if ($request->image) {
            $destinationPath =  'images/';
            $idfile = base64_decode($request->image);
            $imagename = time()  . getFileExtensionForBase64($idfile);
            Storage::disk('s3')->put($destinationPath . $imagename, $idfile);

            $supportImage = FileSystem::create([
                'user_file_name' => $imagename,
            ]);
            $supportImageId = $supportImage->file_id;
            $support->file_id = $supportImageId;
        }
        $support->user_id = $request->user()->user_id;
        $support->save();
        return Response()->json(['status' => 'Success', 'Message' => ' Your Query Submitted Successfully']);
    }
}
