<?php

namespace Modules\DataMaster\Livewire\DataMurid;

use App\Helpers\ToastMagic;
use App\Helpers\ValidateMagic;
use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Rombel;
use Livewire\Component;

class Create extends Component
{
    public string $nama = "";
    public string $nipd = "";
    public string $jk = "";
    public string $nisn = "";
    public string $tempat_lahir = "";
    public ?string $tanggal_lahir = null;

    public ?string $agama = null;
    public ?string $alamat = null;
    public ?string $rt = null;
    public ?string $rw = null;
    public ?string $kelurahan = null;
    public ?string $kecamatan = null;
    public ?string $hp = null;
    public ?string $email = null;

    public ?string $nama_ayah = null;
    public ?string $nama_ibu = null;
    public ?string $nama_wali = null;

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
                "nipd" => "required|unique:murid,nipd",
                "jk" => "in:L,P|required",
                "nisn" => "required|unique:murid,nisn",
                "tempat_lahir" => "required",
                "tanggal_lahir" => "date|required",
                "agama" => "nullable",
                "alamat" => "nullable",
                "rt" => "nullable",
                "rw" => "nullable",
                "kelurahan" => "nullable",
                "kecamatan" => "nullable",
                "hp" => "nullable",
                "email" => "nullable|email|unique:murid,email",
                "nama_ayah" => "nullable",
                "nama_ibu" => "nullable",
                "nama_wali" => "nullable",

                "rombel_id" => "nullable|exists:rombel,id",
            ],
            [
                "nama.required" => "Nama wajib diisi.",

                "nipd.unique" => "NIPD sudah digunakan.",
                "nipd.required" => "NIPD wajib diisi.",

                "jk.in" => "Jenis kelamin harus L atau P.",
                "jk.required" => "Jenis kelamin wajib diisi.",

                "nisn.unique" => "NISN sudah digunakan.",
                "nisn.required" => "NISN wajib diisi.",

                "tempat_lahir.required" => "Tempat lahir wajib diisi.",

                "tanggal_lahir.date" =>
                    "Tanggal lahir harus berupa tanggal yang valid.",
                "tanggal_lahir.required" => "Tanggal lahir wajib diisi.",

                "email.unique" => "Email sudah digunakan.",

                "rombel_id.exists" => "Rombel yang dipilih tidak valid.",
            ],
        );

        if (!$validate) {
            return;
        }

        Murid::create([
            "nama" => $this->nama,
            "nipd" => $this->nipd,
            "jk" => $this->jk,
            "nisn" => $this->nisn,
            "tempat_lahir" => $this->tempat_lahir,
            "tanggal_lahir" => $this->tanggal_lahir,
            "agama" => $this->agama,
            "alamat" => $this->alamat,
            "rt" => $this->rt,
            "rw" => $this->rw,
            "kelurahan" => $this->kelurahan,
            "kecamatan" => $this->kecamatan,
            "hp" => $this->hp,
            "email" => $this->email,
            "nama_ayah" => $this->nama_ayah,
            "nama_ibu" => $this->nama_ibu,
            "nama_wali" => $this->nama_wali,
            "rombel_id" => $this->rombel_id,
        ]);

        ToastMagic::success("Menambahkan Murid", "Berhasil menambahkan murid $this->nama.");

        $this->reset([
            "nama",
            "nipd",
            "jk",
            "nisn",
            "tempat_lahir",
            "tanggal_lahir",
            "agama",
            "alamat",
            "rt",
            "rw",
            "kelurahan",
            "kecamatan",
            "hp",
            "email",
            "nama_ayah",
            "nama_ibu",
            "nama_wali",
            "rombel_id",
        ]);
    }

    public function render()
    {
        return view("datamaster::livewire.data-murid.create");
    }
}
