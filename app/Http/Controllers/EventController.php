<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
// use App\Http\Requests\UpdateEventRequest;
// use App\Interfaces\EventRepositoryInterface;
// use App\Classes\ResponseClass;
// use App\Http\Resources\EventResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
   
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $data = $this->eventRepositoryInterface->index();
        $events = Event::all();
        return response()->json([
            'status' => true,
            'message' => 'Events retrieved successfully',
            'data' => $events
        ], 200);

        // return ResponseClass::sendResponse(EventResource::collection($data). '',200); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request)
    {
    
        // $validator = Validator::make($request->all(), [
        //     'eventName' => 'required',
        //     'frequency' => 'required',
        //     'startDateTime' => 'required|date_format:Y-m-d H:i',
        //     'frequency' => 'in:Once-Off,Weekly,Monthly',
        //     'duration' => [
        //         'required', 'integer', 'min:1', function ($attribute, $value, $fail) {
        //             if ($this->frequency === 'Once-Off') {
        //                 if (!is_null($this->endDateTime)) {
        //                     $fail('For Once-Off events, endDateTime should be null.');
        //                 }
        //             }
        //         }
        //     ],
        //     'invitees' => 'required|array|min:1',
        //     'invitees.*' => 'distinct|exists:users,id',
        // ]);

        // if($validator->fails()){
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Validation error',
        //         'errors' => $validator->errors()
        //     ], 422);
        // }

        $validated = $request->validated();

        // $request['invitees'] = json_encode($request['invitees']);

        $event = Event::create($request->all());
        
        return response()->json([
            'status' => true,
            'message' => 'Event created successfully',
            'data' => $event
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // $event = $this->eventRepositoryInterface->getById($id);

        // return ResponseClass::sendResponse(new EventResource($event),', 200');
        $event = Event::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Event found Successfully',
            'data' => $event
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        //
    }
}
