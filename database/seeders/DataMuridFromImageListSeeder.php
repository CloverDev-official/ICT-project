<?php

namespace Database\Seeders;

use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Indeks;
use App\Models\Murid\Rombel\Jurusan;
use App\Models\Murid\Rombel\Rombel;
use App\Models\Murid\Rombel\Tingkat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class DataMuridFromImageListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $images = $this->loadImages();

        foreach ($images as $image) {
            $kelas = $this->getKelasFromImagePath($image);
            $nama = $this->getNamaFromImagePath($image);
            $parts = $this->getPartsKelasFromImagePath($kelas);

            $rombelId = $this->createRombelIfNotExists($parts);
            $this->createMuridIfNotExists($rombelId, $nama, $image);
        }
    }

    private function generateUniqueNipd(): int
    {
        do {
            $nipd = random_int(10000, 40000);
        } while (Murid::where('nipd', $nipd)->exists());

        return $nipd;
    }

    private function generateUniqueNisn(): string
    {
        do {
            $nisn = (string) random_int(1000000000, 9999999999);
        } while (Murid::where('nisn', $nisn)->exists());

        return $nisn;
    }

    private function createMuridIfNotExists(int $rombelId, string $name, string $imagePath): void
    {
        if ($rombelId === 0) {
            return;
        }

        Murid::where('rombel_id', $rombelId)
            ->where('nama', $name)
            ->update([
                'image_path' => $imagePath,
            ]);
    }

    private function createRombelIfNotExists(array $parts): int
    {
        if (count($parts) < 2) {
            return 0;
        }

        $tingkat = Tingkat::where('nama', $parts[0])->first();
        $jurusan = Jurusan::where('nama', $parts[1])->first();

        if (!$tingkat || !$jurusan) {
            return 0;
        }

        $query = Rombel::where('tingkat_id', $tingkat->id)
            ->where('jurusan_id', $jurusan->id);

        if (isset($parts[2])) {
            $indeks = Indeks::where('nama', $parts[2])->first();

            if (!$indeks) {
                return 0;
            }

            $query->where('indeks_id', $indeks->id);
        } else {
            $query->whereNull('indeks_id');
        }

        $rombel = $query->first();

        return $rombel?->id ?? 0;
    }

    private function getKelasFromImagePath(string $imagePath): string
    {
        $pathParts = explode('/', $imagePath);
        $kelasIndex = array_search('murid', $pathParts) + 1;

        return $kelasIndex !== false && isset($pathParts[$kelasIndex]) ? $pathParts[$kelasIndex] : 'Unknown';
    }

    private function getNamaFromImagePath(string $imagePath): string
    {
        $filename = pathinfo($imagePath, PATHINFO_FILENAME);
        return str_replace('_', ' ', $filename);
    }

    private function getPartsKelasFromImagePath(string $kelas): array
    {
        return preg_split('/\s+/', strtoupper($kelas));
    }

    private function getOrCreateTingkatId(?string $nama): ?int
    {
        $key = strtolower($nama);
        if (isset($this->tingkatCache[$key])) {
            return $this->tingkatCache[$key];
        }

        $tingkat = Tingkat::firstOrCreate(['nama' => $nama]);
        $this->tingkatCache[$key] = $tingkat->id;

        return $tingkat->id;
    }

    private function getOrCreateJurusanId(?string $nama): ?int
    {
        $key = strtolower($nama);
        if (isset($this->jurusanCache[$key])) {
            return $this->jurusanCache[$key];
        }

        $jurusan = Jurusan::firstOrCreate(['nama' => $nama]);
        $this->jurusanCache[$key] = $jurusan->id;

        return $jurusan->id;
    }

    private function getOrCreateIndeksId(?string $nama): ?int
    {
        $key = strtolower($nama);
        if (isset($this->indeksCache[$key])) {
            return $this->indeksCache[$key];
        }

        $indeks = Indeks::firstOrCreate(['nama' => $nama]);
        $this->indeksCache[$key] = $indeks->id;

        return $indeks->id;
    }

    private function loadImages(): array
    {
        $path = storage_path('app/public/murid');

        if (!is_dir($path)) {
            return ['/storage/murid/default.jpg'];
        }

        $rii = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path)
        );

        $images = [];

        foreach ($rii as $file) {
            if ($file->isDir()) {
                continue;
            }

            if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $file->getFilename())) {

                $fullPath = str_replace('\\', '/', $file->getPathname());
                $basePath = str_replace('\\', '/', storage_path('app/public'));

                $relativePath = Str::after($fullPath, $basePath . '/');

                $images[] = '/storage/' . $relativePath;
            }
        }

        shuffle($images);

        return $images;
    }
}
