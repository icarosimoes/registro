<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
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
        
        $notification = Notification::where('user_id',$request->user_id)
        ->where('checked','not')
        ->get();
        
        
        return response()->json($notification);
    }
    public function indexNotification(Request $request){
        
        $notifications = Notification::where('user_id',2)
        ->where('checked','not')
        ->get();
        
        
        return view('notification',compact('notifications'));
    }
}
