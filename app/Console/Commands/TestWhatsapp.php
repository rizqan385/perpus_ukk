<?php

namespace App\Console\Commands;

use App\Services\FonnteService;
use Illuminate\Console\Command;

class TestWhatsapp extends Command
{
    protected $signature = 'test:wa {nomor} {pesan}';
    protected $description = 'Test kirim WhatsApp via Fonnte';

    public function handle(FonnteService $fonnte)
    {
        $hasil = $fonnte->send($this->argument('nomor'), $this->argument('pesan'));
        $this->info($hasil ? '✅ Berhasil!' : '❌ Gagal, cek log.');
    }
}