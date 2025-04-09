<?php

namespace App\Livewire\Schedules;

use App\Models\MealHours;
use App\Models\Schedule;
use App\Models\ScheduleException;
use App\Models\User;
use App\Models\UserSchedule;
use App\Models\Vacation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public $calendarStart;

    public $calendarEnd;

    public $userId;

    public $users;

    public $schedules;

    public $userSchedules = [];

    public $userMeals = [];

    public $userExceptions = [];

    public $vacations = [];

    public bool $changed = false;

    public function mount()
    {
        $this->userId = Auth::user()->id;
        $this->fetchSchedules();
        $this->calendarStart = Carbon::now()->startOfWeek()->format('Y-m-d');
        $this->calendarEnd = Carbon::now()->addMonth()->endOfWeek()->format('Y-m-d');
        $this->fetchuserSchedule();
        $this->fetchuserMeals();
        $this->fetchuserExceptions();
        $this->fetchVacations();
    }

    public function fetchSchedules()
    {
        $this->schedules = [
            [
                'title' => 'Custom',
                'color' => '#FF0000',
                'textColor' => '#ffffff',
                'data_event' => json_encode(
                    [
                        'title' => 'Custom',
                        'extendedProps' => [
                            'exception' => true,
                            'new' => true,
                        ],
                        'className' => ['bg-work-exception', 'border-none'],
                        'duration' => '01:00',
                    ]
                ),
            ],
            ...Schedule::all()->map(function ($schedule) {
                return [
                    'title' => $schedule->name,
                    'color' => $schedule->color,
                    'textColor' => $schedule->text_color,
                    'data_event' => json_encode([
                        'title' => $schedule->name,
                        'duration' => $schedule->duration,
                        'extendedProps' => [
                            'start' => $schedule->start,
                            'schedule_id' => $schedule->id,
                            'new' => true,
                            'work_hour' => true,
                        ],
                        'color' => $schedule->color,
                        'textColor' => $schedule->text_color,
                    ]),
                ];
            })->toArray(),
        ];
    }

    public function loadEvents($start, $end)
    {
        preg_match_all('/(\d{4}-\d{2}-\d{2}).(\d{2}:\d{2})/', $start, $matches);
        $start = Carbon::parse($matches[1][0] . ' ' . $matches[2][0]);
        preg_match_all('/(\d{4}-\d{2}-\d{2}).(\d{2}:\d{2})/', $end, $matches);
        $end = Carbon::parse($matches[1][0] . ' ' . $matches[2][0]);

        $scheduleEvents = collect($this->userSchedules)
            ->map(function ($events) {
                return collect($events)->map(function ($event) {
                    $event['extendedProps'] = [
                        'start' => $event['start'],
                        'id' => $event['id'],
                        'new' => false,
                        'schedule_id' => $event['schedule_id'],
                    ];
                    $event['title'] = $event['title'];

                    return $event;
                });
            });

        $mealEvents = collect($this->userMeals)
            ->flatten(1)
            ->map(function ($event) {
                $event['extendedProps'] = [
                    'id' => $event['id'],
                    'new' => false,
                    'meal' => true,
                ];
                $event['className'] = ['bg-meal', 'border-none'];

                return $event;
            });
        $exceptionEvents = collect($this->userExceptions)
            ->flatten(1)
            ->map(function ($event) {
                $event['extendedProps'] = [
                    'id' => $event['id'],
                    'new' => false,
                    'exception' => true,
                ];
                $event['className'] = ['bg-work-exception', 'border-none'];
                if (! isset($event['end'])) {
                    [$hours, $minutes] = explode(':', $event['duration']);
                    $event['end'] = Carbon::parse($event['start'])->addHours((int) $hours)->addMinutes((int) $minutes)->format('Y-m-d H:i');
                }

                return $event;
            })
            ->filter(function ($event) use ($start, $end) {
                // solo mostrar excepciones que esten dentro del rango de fechas
                // o que no esten dentro del rango de unas vacaciones
                return Carbon::parse($event['start'])->between($start, $end)
                    || Carbon::parse($event['end'])->between($start, $end);
            })->filter(function ($event) {
                // si la excepcion esta dentro de un rango de vacaciones, no mostrar
                return ! collect($this->vacations)->contains(function ($vacation) use ($event) {
                    $vacationStart = Carbon::parse($vacation['start']);
                    $vacationEnd = Carbon::parse($vacation['end']);
                    $exceptionStart = Carbon::parse($event['start']);
                    $exceptionEnd = Carbon::parse($event['end']);

                    return $exceptionStart->between($vacationStart, $vacationEnd) || $exceptionEnd->between($vacationStart, $vacationEnd)
                        || $vacationStart->between($exceptionStart, $exceptionEnd) || $vacationEnd->between($exceptionStart, $exceptionEnd);
                });
            });

        $currentDate = $start->copy();
        $events = [];
        while ($currentDate->lt($end)) {
            $day = $currentDate->format('l');
            $mealEvents = $mealEvents->map(function ($event) use ($currentDate, $day) {
                if ($event['day'] == $day) {
                    $event['start'] = $currentDate->format('Y-m-d') . ' ' . $event['start'];
                    $event['end'] = Carbon::parse($event['start'])->addHour()->format('Y-m-d H:i');
                }

                return $event;
            });
            $isVacation = collect($this->vacations)->contains(function ($vacation) use ($currentDate) {
                return Carbon::parse($vacation['start'])->lte($currentDate) && Carbon::parse($vacation['end'])->gte($currentDate);
            });
            if (! isset($scheduleEvents[$day]) || $isVacation) {
                $currentDate->addDay();

                continue;
            }
            $scheduleEvents[$day]->each(function ($event) use ($currentDate, $exceptionEvents, &$events) {
                $event['start'] = $currentDate->format('Y-m-d') . ' ' . $event['start'];
                [$hours, $minutes] = explode(':', $event['duration']);
                $event['end'] = Carbon::parse($event['start'])->addHours((int) $hours)->addMinutes((int) $minutes)->format('Y-m-d H:i');

                // checar si hay un overlap con una excepcion, si hay, no mostrar
                $overlap = $exceptionEvents->filter(function ($exception) use ($event) {
                    $exceptionStart = Carbon::parse($exception['start']);
                    $exceptionEnd = Carbon::parse($exception['end']);
                    $eventStart = Carbon::parse($event['start']);
                    $eventEnd = Carbon::parse($event['end']);

                    return $eventStart->between($exceptionStart, $exceptionEnd) || $eventEnd->between($exceptionStart, $exceptionEnd)
                        || $exceptionStart->between($eventStart, $eventEnd) || $exceptionEnd->between($eventStart, $eventEnd);
                });
                if ($overlap->isEmpty()) {
                    $events[] = $event;
                } else {
                    $event['display'] = 'background';
                    $events[] = $event;
                }
            });
            $currentDate->addDay();
        }
        $events = collect($events)->merge($exceptionEvents);
        $meals = $mealEvents->filter(function ($meal) use ($events) {
            return $events->contains(function ($event) use ($meal) {
                if (isset($event['display'])) {
                    return;
                }

                return Carbon::parse($meal['start'])->between(Carbon::parse($event['start']), Carbon::parse($event['end']));
            });
        });
        $events = $events->merge($meals)->merge($this->vacations);

        return $events->toArray();
    }

    public function fetchuserSchedule()
    {
        $this->userSchedules = UserSchedule::with('schedule')->where('user_id', $this->userId)
            ->get()
            ->groupBy('day')
            ->mapWithKeys(function ($schedules, $day) {
                return [
                    $day => $schedules->mapWithKeys(function ($schedule) {
                        return [
                            $schedule->id => [
                                'new' => false,
                                'id' => $schedule->id,
                                'day' => $schedule->day,
                                'start' => $schedule->schedule->start,
                                'duration' => $schedule->schedule->duration,
                                'schedule_id' => $schedule->schedule_id,
                                'title' => $schedule->schedule->name,
                                'color' => $schedule->schedule->color,
                                'textColor' => $schedule->schedule->text_color,
                            ],
                        ];
                    }),
                ];
            })->toArray();
    }

    public function fetchuserMeals()
    {
        $this->userMeals = MealHours::where('user_id', $this->userId)
            ->get()
            ->groupBy('day')
            ->mapWithKeys(function ($meals, $day) {
                return [
                    $day => $meals->mapWithKeys(function ($meal) {
                        return [
                            $meal->id => [
                                'new' => false,
                                'id' => $meal->id,
                                'start' => $meal->start,
                                'duration' => '01:00',
                                'day' => $meal->day,
                                'title' => 'Comida',
                            ],
                        ];
                    }),
                ];
            })->toArray();
    }

    public function fetchuserExceptions()
    {
        $this->userExceptions = ScheduleException::where('user_id', $this->userId)
            ->where('date', '>=', $this->calendarStart)
            ->where('date', '<=', $this->calendarEnd)
            ->get()
            ->groupBy(function ($exception) {
                return Carbon::parse($exception->date)->format('l');
            })
            ->mapWithKeys(function ($exceptions, $day) {
                return [
                    $day => $exceptions->mapWithKeys(function ($exception) {
                        return [
                            $exception->id => [
                                'new' => false,
                                'id' => $exception->id,
                                'start' => $exception->date . ' ' . $exception->start,
                                'end' => $exception->end_date,
                                'duration' => $exception->duration,
                                'day' => $exception->day,
                                'title' => 'Custom',
                            ],
                        ];
                    }),
                ];
            })->toArray();
    }

    public function fetchVacations()
    {
        $this->vacations = Vacation::where('user_id', $this->userId)
            ->where('start_date', '>=', $this->calendarStart)
            ->orWhere('end_date', '<=', $this->calendarEnd)
            ->get()
            ->map(function ($vacation) {
                return [
                    'title' => 'Vacaciones',
                    'start' => $vacation->start_date,
                    'end' => $vacation->end_date,
                    'display' => 'background',
                    'className' => ['bg-vacation', 'border-none'],
                    'overlap' => false,
                    'color' => '#BCFDFF',
                ];
            });
    }

    public function adduserSchedule($event, $type)
    {
        $this->changed = true;
        $day = Carbon::parse($event['start'])->format('l');
        // si no hay id, agarrar el maximo y sumarle 1
        // si no hay maximo, asignar 1
        if (! isset($event['extendedProps']['id'])) {
            if (empty($this->$type[$day])) {
                $event['extendedProps']['id'] = 1;
            } else {
                $event['extendedProps']['id'] = collect($this->$type[$day])->flatten(1)->keys()->max() + 1;
            }
        }
        if (! isset($event['end'])) {
            $duration = '01:00';
        } else {
            $duration = Carbon::parse($event['end'])->diff(Carbon::parse($event['start']))->format('%H:%I');
        }
        $start = Carbon::parse($event['start']);
        $start = $type == 'userExceptions' ? $start->format('Y-m-d H:i') : $start->format('H:i');
        $this->$type[$day][$event['extendedProps']['id']] = [
            'new' => $event['extendedProps']['new'] ?? false,
            'id' => $event['extendedProps']['id'],
            'start' => $start,
            'duration' => $duration,
            'schedule_id' => $event['extendedProps']['schedule_id'] ?? null,
            'title' => $event['title'],
            'day' => $day,
        ];
        if ($type == 'userSchedules') {
            $this->$type[$day][$event['extendedProps']['id']]['color'] = $event['backgroundColor'];
            $this->$type[$day][$event['extendedProps']['id']]['textColor'] = $event['textColor'];
        }

        return $event['extendedProps']['id'];
    }

    public function removeuserSchedule($event, $type)
    {
        $this->changed = true;
        $day = Carbon::parse($event['start'])->format('l');
        if (! isset($event['extendedProps']['id'])) {
            return;
        }
        unset($this->$type[$day][$event['extendedProps']['id']]);
    }

    public function updateuserSchedule($oldEvent, $event, $type)
    {
        $this->removeuserSchedule($oldEvent, $type);
        $this->adduserSchedule($event, $type);
    }

    public function resizeuserException($event)
    {
        $this->changed = true;
        // change duration
        $day = Carbon::parse($event['start'])->format('l');
        $this->userExceptions[$day][$event['extendedProps']['id']]['duration'] = Carbon::parse($event['end'])->diff(Carbon::parse($event['start']))->format('%H:%I');
        // change end
        $this->userExceptions[$day][$event['extendedProps']['id']]['end'] = Carbon::parse($event['end'])->format('Y-m-d H:i');
    }

    public function createSchedule($name, $start, $duration, $color = null)
    {
        [$r, $g, $b] = array_map(
            function ($color) {
                return $color / 255;
            },
            array_map('hexdec', str_split(ltrim($color, '#'), 2))
        );
        $text_color = (0.2126 * $r + 0.7152 * $g + 0.0722 * $b) > 0.5 ? '#000000' : '#ffffff';
        Schedule::create([
            'name' => $name,
            'start' => $start,
            'duration' => $duration,
            'color' => $color,
            'text_color' => $text_color,
        ]);

        $this->fetchSchedules();
        // $this->toast('success', 'Horario creado exitosamente');
        $this->js('Toast.fire({
            icon: "success",
            title: "Horario creado exitosamente",
        })');
    }

    public function save()
    {
        DB::beginTransaction();
        $user = User::find($this->userId);
        try {
            $idsToSkip = []; // ids de los horarios que no se deben borrar
            collect($this->userSchedules)->each(function ($schedules, $day) use (&$idsToSkip, $user) {
                foreach ($schedules as $id => $schedule) {
                    if ($schedule['new']) {
                        $newSchedule = UserSchedule::create([
                            'user_id' => $user->id,
                            'schedule_id' => $schedule['schedule_id'],
                            'day' => $day,
                        ]);
                        $idsToSkip[] = $newSchedule->id;
                    } else {
                        $userSchedule = UserSchedule::find($id);
                        $userSchedule->update([
                            'day' => $day,
                        ]);
                        $idsToSkip[] = $userSchedule->id;
                    }
                }
            });
            UserSchedule::where('user_id', $user->id)
                ->whereNotIn('id', $idsToSkip)
                ->delete();

            $idsToSkip = []; // ids de las comidas que no se deben borrar
            collect($this->userMeals)->each(function ($meals, $day) use (&$idsToSkip, $user) {
                foreach ($meals as $id => $meal) {
                    if ($meal['new']) {
                        $newMeal = MealHours::create([
                            'user_id' => $user->id,
                            'day' => $day,
                            'start' => $meal['start'],
                        ]);
                        $idsToSkip[] = $newMeal->id;
                    } else {
                        $mealHour = MealHours::find($id);
                        $mealHour->update([
                            'day' => $day,
                            'start' => $meal['start'],
                        ]);
                        $idsToSkip[] = $mealHour->id;
                    }
                }
            });
            MealHours::where('user_id', $user->id)
                ->whereNotIn('id', $idsToSkip)
                ->delete();

            $idsToSkip = []; // ids de las excepciones que no se deben borrar
            collect($this->userExceptions)->each(function ($exceptions, $day) use (&$idsToSkip, $user) {
                foreach ($exceptions as $id => $exception) {
                    if ($exception['new']) {
                        $newException = ScheduleException::create([
                            'user_id' => $user->id,
                            'day' => $day,
                            'date' => Carbon::parse($exception['start'])->format('Y-m-d'),
                            'start' => Carbon::parse($exception['start'])->format('H:i'),
                            'duration' => $exception['duration'],
                            'title' => $exception['title'],
                        ]);
                        $idsToSkip[] = $newException->id;
                    } else {
                        $scheduleException = ScheduleException::find($id);
                        $scheduleException->update([
                            'day' => $day,
                            'date' => Carbon::parse($exception['start'])->format('Y-m-d'),
                            'start' => Carbon::parse($exception['start'])->format('H:i'),
                            'duration' => $exception['duration'],
                            'title' => $exception['title'],
                        ]);
                        $idsToSkip[] = $scheduleException->id;
                    }
                }
            });
            ScheduleException::where('user_id', $user->id)
                ->whereNotIn('id', $idsToSkip)
                ->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            $this->js('Toast.fire({
                icon: "error",
                title: "Ocurrió un error al guardar los horarios",
            })');

            return;
        }
        $this->js('Toast.fire({
            icon: "success",
            title: "Horarios guardados exitosamente",
        })');
    }

    public function render()
    {
        return view('livewire.schedules.index');
    }
}
