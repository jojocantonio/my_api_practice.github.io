<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Models\Event;
use Carbon\Carbon;


class StoreEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */  
    public function rules()
    {
        return [
            'eventName' => 'required',
            'frequency' => 'required|in:Once-Off,Weekly,Monthly',
            'startDateTime' => 'required|date|date_format:Y-m-d H:i|before_or_equal:endDateTime',
            'endDateTime' => 'nullable|date|date_format:Y-m-d H:i|after:startDateTime',
            'duration' => [
                'integer', 'min:1', 
                function ($attribute, $value, $fail) {
                    if ($this->frequency === 'Once-Off') {
                        if (!is_null($this->endDateTime)) {
                            $fail('For Once-Off events, endDateTime should be null.');
                        }
                    }

                    //For recurring events (weekly & monthly)
                    if (in_array($this->frequency, ['Weekly', 'Monthly'])) {
                        $startDateTime = Carbon::parse($this->startDateTime);
                        $recurrence = $this->frequency === 'Weekly' ? 'week' : 'month';
                        $endDateTime = $this->endDateTime ? Carbon::parse($this->endDateTime) : null;
                        
                        $nextEventStart = $startDateTime->copy();
                        $nextEventEnd = $startDateTime->copy()->addMinutes($value);

                        while (!$endDateTime || $nextEventStart <= $endDateTime) {
                            if ($this->checkForEventOverlaps($nextEventStart, $nextEventEnd)) {
                                $fail('The recurring event duration causes overlap.');
                            }

                            $nextEventStart->add(1, $recurrence); // Add a week or a month
                            $nextEventEnd = $nextEventStart->copy()->addMinutes($value);
                        }
                    }
                }
            ],
            'invitees' => 'required|array|min:1',
            'invitees.*' => 'distinct|exists:users,id', // user IDs should exist in the 'users' table
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Custom logic for Weekly frequency validation
            if ($this->frequency === 'Weekly') {
                if (is_null($this->duration)) {
                    $validator->errors()->add('duration', 'For Weekly events, duration is required.');
                }
            }

            // Custom logic for Monthly frequency validation
            if ($this->frequency === 'Monthly') {
                $startDate = Carbon::parse($this->startDateTime)->format('Y-m-d H:i');
                $dayOfMonth = Carbon::parse($startDate)->format('d');
                $monthValidLastDay = Carbon::parse($startDate)->endOfMonth()->format('d');
                
                if ($dayOfMonth > $monthValidLastDay) {
                    $validator->errors()->add('startDateTime', 'Invalid day of month for Monthly event, adjusted to the last day of the month.');
                }
            }
        });
    }

    /**
     * Query the database to check for overlapping events.
     * 
     */
    public function checkForEventOverlaps($newStartDateTime, $newEndDateTime)
    {
        $newStart = Carbon::parse($newStartDateTime);
        $newEnd = Carbon::parse($newEndDateTime);

        // Query the events table for overlapping events
        $overlapFlag = Event::where(function ($query) use ($newStart, $newEnd) {
            $query->where(function ($query) use ($newStart) {
                $query->where('startDateTime', '<=', $newStart)
                      ->where('endDateTime', '>=', $newStart);
            })
            ->orWhere(function ($query) use ($newEnd) {
                $query->where('startDateTime', '<=', $newEnd)
                      ->where('endDateTime', '>=', $newEnd);
            })
            ->orWhere(function ($query) use ($newStart, $newEnd) {
                $query->where('startDateTime', '>=', $newStart)
                      ->where('endDateTime', '<=', $newEnd);
            });
        })->exists();

        return $overlapFlag;
    }

    public function messages()
    {
        return [
            'startDateTime.before_or_equal' => 'The start date must be before or equal to the end date.',
            'endDateTime.after' => 'The end date must be after the start date.',
            'startDateTime.date' => 'The start date and time must be a valid date.',
            'endDateTime.date' => 'The end date and time must be a valid date.',
            'duration.integer' => 'The duration must be an integer value.',
            'duration.min' => 'The duration must be at least 1 minute.',
            'invitees.*.distinct' => 'Invitee must be unique.',
            'invitees.*.exists' => 'One or more invitees do not exist in our records.',
        ];
    }
    

    public function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data' => $validator->errors()
        ]));

    }
}
