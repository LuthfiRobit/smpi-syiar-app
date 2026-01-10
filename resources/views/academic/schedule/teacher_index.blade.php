@extends('layouts.admin')

@section('content')
    <!-- <div class="container-xxl flex-grow-1 container-p-y"> -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Jadwal Mengajar: {{ $teacher->name }}</h5>
            <span class="badge bg-label-primary">{{ $schedules->count() }} Jadwal</span>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Hari</th>
                            <th>Jam</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Tahun Ajaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $schedule)
                            <tr>
                                <td><span class="badge bg-label-info">{{ $schedule->day }}</span></td>
                                <td>{{ $schedule->timeSlot->start_time }} - {{ $schedule->timeSlot->end_time }}</td>
                                <td><strong>{{ $schedule->classroom->name }}</strong></td>
                                <td>{{ $schedule->subject->name }}</td>
                                <td>{{ $schedule->academicYear->name }} - {{ $schedule->academicYear->semester }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    <i class="bx bx-calendar-x bx-lg mb-2"></i>
                                    <p>Belum ada jadwal mengajar</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Weekly Schedule Matrix (Optional) -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Jadwal Mingguan</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="80">Jam</th>
                            <th>Senin</th>
                            <th>Selasa</th>
                            <th>Rabu</th>
                            <th>Kamis</th>
                            <th>Jumat</th>
                            <th>Sabtu</th>
                            <th>Minggu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $timeSlots = $schedules->pluck('timeSlot')->unique('id')->sortBy('start_time');
                            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

                            $matrix = [];
                            foreach ($schedules as $schedule) {
                                $matrix[$schedule->time_slot_id][$schedule->day] = $schedule;
                            }
                        @endphp

                        @foreach($timeSlots as $timeSlot)
                            <tr>
                                <td class="text-center">
                                    <small>{{ \Carbon\Carbon::parse($timeSlot->start_time)->format('H:i') }}<br>{{ \Carbon\Carbon::parse($timeSlot->end_time)->format('H:i') }}</small>
                                </td>
                                @foreach($days as $day)
                                    <td>
                                        @if(isset($matrix[$timeSlot->id][$day]))
                                            @php $sch = $matrix[$timeSlot->id][$day]; @endphp
                                            <small>
                                                <strong>{{ $sch->subject->name }}</strong><br>
                                                Kelas {{ $sch->classroom->name }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- </div> -->
@endsection