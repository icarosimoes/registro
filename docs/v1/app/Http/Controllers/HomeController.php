<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
class HomeController extends Controller
{
    var $errorPermission = false;
    
    public function inactive(){
        $this->errorPermission = true;
        $totalOccurrence = $this->service->totalOccurrence();
        $totalOccurrenceOpen = $this->service->totalOccurrenceOpen();
        $totalOccurrenceClosed = $this->service->totalOccurrenceClosed();
        $totalUsers = $this->service->totalUsers();
        return view('home')->with([
            'totalOccurrence' => $totalOccurrence,
            'totalOccurrenceOpen' => $totalOccurrenceOpen,
            'totalOccurrenceClosed' => $totalOccurrenceClosed,
            'totalUsers' => $totalUsers,
            'errorPermission' => $this->errorPermission
        ]);
    }

    public function __construct()
    {
        parent::__construct();
        //$this->middleware('auth');
        //  $this->middleware('can:checkPermission');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $totalOccurrence = $this->service->totalOccurrence();
        $totalOccurrenceOpen = $this->service->totalOccurrenceOpen();
        $totalOccurrenceClosed = $this->service->totalOccurrenceClosed();
        $totalUsers = $this->service->totalUsers();
        return view('home')->with([
            'totalOccurrence' => $totalOccurrence,
            'totalOccurrenceOpen' => $totalOccurrenceOpen,
            'totalOccurrenceClosed' => $totalOccurrenceClosed,
            'totalUsers' => $totalUsers
        ]);
    }

    public function getNotification(Request $request){
        
        $notification = Notification::where('user_id',Auth::id())
        ->where('checked','not')
        ->get();
        
        
        return response()->json($notification);
    }
    public function indexNotification(Request $request){
        
        $notifications = Notification::where('user_id', Auth::id());
        if($request->checked == 'all'){
            
        }elseif($request->checked == 'yes'){
            $notifications->where('checked','yes');
        }else{
            $notifications->where('checked','not');   
        }

        $notifications = $notifications->get();
        
        
        return view('notification',compact('notifications'));
    }
}
