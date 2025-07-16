<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Waktu;
use Carbon\Carbon;

class WaktuSeeder extends Seeder
{
    public function run()
    {

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        foreach ($hariList as $hari) {
            if ($hari === 'Jumat') {
                $this->buatJumat();
            } else {
                $this->buatHariBiasa($hari);
            }
        }
    }

    private function buatHariBiasa($hari)
    {
        $jamMulai = Carbon::createFromTime(7, 0); // 07:00
        for ($jamKe = 1; $jamKe <= 9; $jamKe++) {
            if ($jamKe === 5) {
                // Istirahat setelah jam ke-4
                Waktu::create([
                    'hari' => $hari,
                    'jam_ke' => 4.5,
                    'jam_mulai' => $jamMulai->format('H:i'),
                    'jam_selesai' => $jamMulai->copy()->addMinutes(20)->format('H:i'),
                    'ket' => 'Istirahat'
                ]);
                $jamMulai->addMinutes(20); // Tambah waktu istirahat
            }

            Waktu::create([
                'hari' => $hari,
                'jam_ke' => $jamKe,
                'jam_mulai' => $jamMulai->format('H:i'),
                'jam_selesai' => $jamMulai->copy()->addMinutes(40)->format('H:i'),
                'ket' => 'Pelajaran'
            ]);

            $jamMulai->addMinutes(40); // Tambah 40 menit per pelajaran
        }
    }

    private function buatJumat()
    {
        $jamMulai = Carbon::createFromTime(6, 30); // 06:30
        for ($jamKe = 1; $jamKe <= 5; $jamKe++) {
            if ($jamKe === 4) {
                // Istirahat setelah jam ke-3
                Waktu::create([
                    'hari' => 'Jumat',
                    'jam_ke' => 3.5,
                    'jam_mulai' => $jamMulai->format('H:i'),
                    'jam_selesai' => $jamMulai->copy()->addMinutes(10)->format('H:i'),
                    'ket' => 'Istirahat'
                ]);
                $jamMulai->addMinutes(10);
            }

            Waktu::create([
                'hari' => 'Jumat',
                'jam_ke' => $jamKe,
                'jam_mulai' => $jamMulai->format('H:i'),
                'jam_selesai' => $jamMulai->copy()->addMinutes(40)->format('H:i'),
                'ket' => 'Pelajaran'
            ]);

            $jamMulai->addMinutes(40);
        }
    }
}
