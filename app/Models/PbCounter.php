<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PbCounter extends Model
{
    use HasFactory;

    protected $fillable = ['counter_date', 'counter_value'];

    /**
     * Generate next PB number for given date with concurrency handling
     */
    public static function getNextNumber()
    {
        return DB::transaction(function () {
            // Lock untuk menghindari race condition
            $counter = self::lockForUpdate()->first();

            if (!$counter) {
                // Buat counter baru jika belum ada
                // Cek nomor PB terakhir untuk starting point yang tepat
                $lastPb = \App\Models\Pbs::orderBy('nomor_pb', 'desc')->first();
                $startValue = $lastPb ? $lastPb->nomor_pb + 1 : 1;
                $counter = self::create(['counter_value' => $startValue]);
                return $startValue;
            }

            // Selalu sinkronkan dengan nomor PB terakhir
            $lastPb = \App\Models\Pbs::orderBy('nomor_pb', 'desc')->first();
            if ($lastPb) {
                // Set counter ke nomor terakhir + 1
                $nextNumber = $lastPb->nomor_pb + 1;
                $counter->counter_value = $nextNumber;
            } else {
                // Jika tidak ada PB sama sekali, mulai dari 1
                $counter->counter_value = 1;
            }

            $counter->save();

            return $counter->counter_value;
        });
    }

    /**
     * Reset counter for new day (optional)
     */
    public static function resetDaily()
    {
        $today = Carbon::today()->format('Y-m-d');
        $yesterday = Carbon::yesterday()->format('Y-m-d');

        // Archive old counters (optional)
        self::where('counter_date', '<', $yesterday)->delete();

        return true;
    }

    /**
     * Reset counter to specific value for today
     */
    public static function resetToValue($value = 4589)
    {
        $today = Carbon::today()->format('Y-m-d');

        return DB::transaction(function () use ($today, $value) {
            $counter = self::where('counter_date', $today)->first();

            if ($counter) {
                $counter->update(['counter_value' => $value]);
            } else {
                self::create([
                    'counter_date' => $today,
                    'counter_value' => $value
                ]);
            }

            return $value + 1; // Return next number that will be generated
        });
    }
}
