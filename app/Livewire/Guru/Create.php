<?php

namespace App\Livewire\Guru;

use App\Helpers\ValidateMagic;
use App\Helpers\ToastMagic;
use App\Models\Guru\Guru;
use App\Models\Guru\Rombel\GuruRombel;
use App\Models\Murid\Rombel\Rombel;
use Livewire\Component;

class Create extends Component
{
    public string $nama = "";
    public ?string $nuptk = null;
    public string $jk = "";

    public ?string $tempat_lahir = null;
    public ?string $tanggal_lahir = null;

    public ?string $nip = null;

    public ?string $status_kepegawaian = null;
    public ?string $jenis_ptk = null;
    public ?string $agama = null;

    public ?string $alamat_jalan = null;
    public ?string $rt = null;
    public ?string $rw = null;
    public ?string $desa_kelurahan = null;
    public ?string $kecamatan = null;
    public ?string $kode_pos = null;

    public ?string $telepon = null;
    public ?string $hp = null;
    public ?string $email = null;

    public ?int $rombel_id = null;
    public $rombel = [];

    public function mount(): void
    {
        $this->rombel = Rombel::query()
            ->with(["tingkat", "jurusan", "indeks"])
            ->get()
            ->sortBy(fn($r) => $r->nama)
            ->values();
    }

    public function store()
    {
        $validate = ValidateMagic::run(
            [
                "nama" => "required",

                "nuptk" => "nullable|unique:guru,nuptk",
                "jk" => "in:L,P|required",

                "tempat_lahir" => "nullable",
                "tanggal_lahir" => "nullable|date",

                "nip" => "nullable|unique:guru,nip",

                "status_kepegawaian" => "nullable",
                "jenis_ptk" => "nullable",
                "agama" => "nullable",

                "alamat_jalan" => "nullable",
                "rt" => "nullable",
                "rw" => "nullable",
                "desa_kelurahan" => "nullable",
                "kecamatan" => "nullable",
                "kode_pos" => "nullable",

                "telepon" => "nullable",
                "hp" => "nullable",
                "email" => "nullable|email|unique:guru,email",

                // "rombel_id" => "required|exists:rombel,id",
            ],
            [
                "nama.required" => "Nama wajib diisi.",

                "nuptk.unique" => "NUPTK sudah digunakan.",

                "jk.in" => "Jenis kelamin harus L atau P.",
                "jk.required" => "Jenis kelamin wajib diisi.",

                "tanggal_lahir.date" =>
                    "Tanggal lahir harus berupa tanggal yang valid.",

                "nip.unique" => "NIP sudah digunakan.",
                "email.unique" => "Email sudah digunakan.",

                // "rombel_id.exists" => "Rombel yang dipilih tidak valid.",
            ],
        );

        if (!$validate) {
            return;
        }

        $guru = Guru::create([
            "nama" => $this->nama,
            "nuptk" => $this->nuptk,
            "jk" => $this->jk,

            "tempat_lahir" => $this->tempat_lahir,
            "tanggal_lahir" => $this->tanggal_lahir,

            "nip" => $this->nip,

            "status_kepegawaian" => $this->status_kepegawaian,
            "jenis_ptk" => $this->jenis_ptk,
            "agama" => $this->agama,

            "alamat_jalan" => $this->alamat_jalan,
            "rt" => $this->rt,
            "rw" => $this->rw,
            "desa_kelurahan" => $this->desa_kelurahan,
            "kecamatan" => $this->kecamatan,
            "kode_pos" => $this->kode_pos,

            "telepon" => $this->telepon,
            "hp" => $this->hp,
            "email" => $this->email,
        ]);

        // GuruRombel::create([
        //     "guru_id" => $guru->id,
        //     "rombel_id" => $this->rombel_id,
        // ]);

        ToastMagic::success("Menambahkan Guru", "Berhasil menambahkan guru $this->nama.");

        $this->reset([
            "nama",
            "nuptk",
            "jk",
            "tempat_lahir",
            "tanggal_lahir",
            "nip",
            "status_kepegawaian",
            "jenis_ptk",
            "agama",
            "alamat_jalan",
            "rt",
            "rw",
            "desa_kelurahan",
            "kecamatan",
            "kode_pos",
            "telepon",
            "hp",
            "email",
            // "rombel_id",
        ]);
    }

    public function render()
    {
        return view("livewire.guru.tambah-guru");
    }
}
