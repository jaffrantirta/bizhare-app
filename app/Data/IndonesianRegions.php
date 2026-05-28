<?php

namespace App\Data;

class IndonesianRegions
{
    public static function provinces(): array
    {
        return [
            'aceh'                => 'Aceh',
            'sumatera_utara'      => 'Sumatera Utara',
            'sumatera_barat'      => 'Sumatera Barat',
            'riau'                => 'Riau',
            'jambi'               => 'Jambi',
            'sumatera_selatan'    => 'Sumatera Selatan',
            'bengkulu'            => 'Bengkulu',
            'lampung'             => 'Lampung',
            'bangka_belitung'     => 'Kepulauan Bangka Belitung',
            'kepulauan_riau'      => 'Kepulauan Riau',
            'dki_jakarta'         => 'DKI Jakarta',
            'jawa_barat'          => 'Jawa Barat',
            'jawa_tengah'         => 'Jawa Tengah',
            'di_yogyakarta'       => 'DI Yogyakarta',
            'jawa_timur'          => 'Jawa Timur',
            'banten'              => 'Banten',
            'bali'                => 'Bali',
            'nusa_tenggara_barat' => 'Nusa Tenggara Barat',
            'nusa_tenggara_timur' => 'Nusa Tenggara Timur',
            'kalimantan_barat'    => 'Kalimantan Barat',
            'kalimantan_tengah'   => 'Kalimantan Tengah',
            'kalimantan_selatan'  => 'Kalimantan Selatan',
            'kalimantan_timur'    => 'Kalimantan Timur',
            'kalimantan_utara'    => 'Kalimantan Utara',
            'sulawesi_utara'      => 'Sulawesi Utara',
            'sulawesi_tengah'     => 'Sulawesi Tengah',
            'sulawesi_selatan'    => 'Sulawesi Selatan',
            'sulawesi_tenggara'   => 'Sulawesi Tenggara',
            'gorontalo'           => 'Gorontalo',
            'sulawesi_barat'      => 'Sulawesi Barat',
            'maluku'              => 'Maluku',
            'maluku_utara'        => 'Maluku Utara',
            'papua_barat'         => 'Papua Barat',
            'papua_barat_daya'    => 'Papua Barat Daya',
            'papua'               => 'Papua',
            'papua_selatan'       => 'Papua Selatan',
            'papua_tengah'        => 'Papua Tengah',
            'papua_pegunungan'    => 'Papua Pegunungan',
        ];
    }

    public static function kabupatens(?string $province): array
    {
        if (!$province) {
            return [];
        }

        $list = self::allKabupatens()[$province] ?? [];

        return array_combine($list, $list);
    }

    private static function allKabupatens(): array
    {
        return [
            'aceh' => [
                'Kab. Aceh Barat', 'Kab. Aceh Barat Daya', 'Kab. Aceh Besar',
                'Kab. Aceh Jaya', 'Kab. Aceh Selatan', 'Kab. Aceh Singkil',
                'Kab. Aceh Tamiang', 'Kab. Aceh Tengah', 'Kab. Aceh Tenggara',
                'Kab. Aceh Timur', 'Kab. Aceh Utara', 'Kab. Bener Meriah',
                'Kab. Bireuen', 'Kab. Gayo Lues', 'Kab. Nagan Raya',
                'Kab. Pidie', 'Kab. Pidie Jaya', 'Kab. Simeulue',
                'Kota Banda Aceh', 'Kota Langsa', 'Kota Lhokseumawe',
                'Kota Sabang', 'Kota Subulussalam',
            ],
            'sumatera_utara' => [
                'Kab. Asahan', 'Kab. Batubara', 'Kab. Dairi', 'Kab. Deli Serdang',
                'Kab. Humbang Hasundutan', 'Kab. Karo', 'Kab. Labuhanbatu',
                'Kab. Labuhanbatu Selatan', 'Kab. Labuhanbatu Utara', 'Kab. Langkat',
                'Kab. Mandailing Natal', 'Kab. Nias', 'Kab. Nias Barat',
                'Kab. Nias Selatan', 'Kab. Nias Utara', 'Kab. Padang Lawas',
                'Kab. Padang Lawas Utara', 'Kab. Pakpak Bharat', 'Kab. Samosir',
                'Kab. Serdang Bedagai', 'Kab. Simalungun', 'Kab. Tapanuli Selatan',
                'Kab. Tapanuli Tengah', 'Kab. Tapanuli Utara', 'Kab. Toba',
                'Kota Binjai', 'Kota Gunungsitoli', 'Kota Medan',
                'Kota Padangsidimpuan', 'Kota Pematangsiantar', 'Kota Sibolga',
                'Kota Tanjungbalai', 'Kota Tebing Tinggi',
            ],
            'sumatera_barat' => [
                'Kab. Agam', 'Kab. Dharmasraya', 'Kab. Kepulauan Mentawai',
                'Kab. Lima Puluh Kota', 'Kab. Padang Pariaman', 'Kab. Pasaman',
                'Kab. Pasaman Barat', 'Kab. Pesisir Selatan', 'Kab. Sijunjung',
                'Kab. Solok', 'Kab. Solok Selatan', 'Kab. Tanah Datar',
                'Kota Bukittinggi', 'Kota Padang', 'Kota Padangpanjang',
                'Kota Pariaman', 'Kota Payakumbuh', 'Kota Sawahlunto', 'Kota Solok',
            ],
            'riau' => [
                'Kab. Bengkalis', 'Kab. Indragiri Hilir', 'Kab. Indragiri Hulu',
                'Kab. Kampar', 'Kab. Kepulauan Meranti', 'Kab. Kuantan Singingi',
                'Kab. Pelalawan', 'Kab. Rokan Hilir', 'Kab. Rokan Hulu', 'Kab. Siak',
                'Kota Dumai', 'Kota Pekanbaru',
            ],
            'jambi' => [
                'Kab. Batanghari', 'Kab. Bungo', 'Kab. Kerinci', 'Kab. Merangin',
                'Kab. Muaro Jambi', 'Kab. Sarolangun', 'Kab. Tanjung Jabung Barat',
                'Kab. Tanjung Jabung Timur', 'Kab. Tebo',
                'Kota Jambi', 'Kota Sungai Penuh',
            ],
            'sumatera_selatan' => [
                'Kab. Banyuasin', 'Kab. Empat Lawang', 'Kab. Lahat', 'Kab. Muara Enim',
                'Kab. Musi Banyuasin', 'Kab. Musi Rawas', 'Kab. Musi Rawas Utara',
                'Kab. Ogan Ilir', 'Kab. Ogan Komering Ilir', 'Kab. Ogan Komering Ulu',
                'Kab. Ogan Komering Ulu Selatan', 'Kab. Ogan Komering Ulu Timur',
                'Kab. Penukal Abab Lematang Ilir',
                'Kota Lubuklinggau', 'Kota Pagar Alam', 'Kota Palembang', 'Kota Prabumulih',
            ],
            'bengkulu' => [
                'Kab. Bengkulu Selatan', 'Kab. Bengkulu Tengah', 'Kab. Bengkulu Utara',
                'Kab. Kaur', 'Kab. Kepahiang', 'Kab. Lebong', 'Kab. Mukomuko',
                'Kab. Rejang Lebong', 'Kab. Seluma',
                'Kota Bengkulu',
            ],
            'lampung' => [
                'Kab. Lampung Barat', 'Kab. Lampung Selatan', 'Kab. Lampung Tengah',
                'Kab. Lampung Timur', 'Kab. Lampung Utara', 'Kab. Mesuji',
                'Kab. Pesawaran', 'Kab. Pesisir Barat', 'Kab. Pringsewu',
                'Kab. Tanggamus', 'Kab. Tulang Bawang', 'Kab. Tulang Bawang Barat',
                'Kab. Way Kanan',
                'Kota Bandar Lampung', 'Kota Metro',
            ],
            'bangka_belitung' => [
                'Kab. Bangka', 'Kab. Bangka Barat', 'Kab. Bangka Selatan',
                'Kab. Bangka Tengah', 'Kab. Belitung', 'Kab. Belitung Timur',
                'Kota Pangkal Pinang',
            ],
            'kepulauan_riau' => [
                'Kab. Bintan', 'Kab. Karimun', 'Kab. Kepulauan Anambas',
                'Kab. Lingga', 'Kab. Natuna',
                'Kota Batam', 'Kota Tanjungpinang',
            ],
            'dki_jakarta' => [
                'Kab. Kepulauan Seribu',
                'Kota Jakarta Barat', 'Kota Jakarta Pusat', 'Kota Jakarta Selatan',
                'Kota Jakarta Timur', 'Kota Jakarta Utara',
            ],
            'jawa_barat' => [
                'Kab. Bandung', 'Kab. Bandung Barat', 'Kab. Bekasi', 'Kab. Bogor',
                'Kab. Ciamis', 'Kab. Cianjur', 'Kab. Cirebon', 'Kab. Garut',
                'Kab. Indramayu', 'Kab. Karawang', 'Kab. Kuningan', 'Kab. Majalengka',
                'Kab. Pangandaran', 'Kab. Purwakarta', 'Kab. Subang', 'Kab. Sukabumi',
                'Kab. Sumedang', 'Kab. Tasikmalaya',
                'Kota Bandung', 'Kota Banjar', 'Kota Bekasi', 'Kota Bogor',
                'Kota Cimahi', 'Kota Cirebon', 'Kota Depok', 'Kota Sukabumi',
                'Kota Tasikmalaya',
            ],
            'jawa_tengah' => [
                'Kab. Banjarnegara', 'Kab. Banyumas', 'Kab. Batang', 'Kab. Blora',
                'Kab. Boyolali', 'Kab. Brebes', 'Kab. Cilacap', 'Kab. Demak',
                'Kab. Grobogan', 'Kab. Jepara', 'Kab. Karanganyar', 'Kab. Kebumen',
                'Kab. Kendal', 'Kab. Klaten', 'Kab. Kudus', 'Kab. Magelang',
                'Kab. Pati', 'Kab. Pekalongan', 'Kab. Pemalang', 'Kab. Purbalingga',
                'Kab. Purworejo', 'Kab. Rembang', 'Kab. Semarang', 'Kab. Sragen',
                'Kab. Sukoharjo', 'Kab. Tegal', 'Kab. Temanggung', 'Kab. Wonogiri',
                'Kab. Wonosobo',
                'Kota Magelang', 'Kota Pekalongan', 'Kota Salatiga',
                'Kota Semarang', 'Kota Surakarta', 'Kota Tegal',
            ],
            'di_yogyakarta' => [
                'Kab. Bantul', 'Kab. Gunungkidul', 'Kab. Kulon Progo', 'Kab. Sleman',
                'Kota Yogyakarta',
            ],
            'jawa_timur' => [
                'Kab. Bangkalan', 'Kab. Banyuwangi', 'Kab. Blitar', 'Kab. Bojonegoro',
                'Kab. Bondowoso', 'Kab. Gresik', 'Kab. Jember', 'Kab. Jombang',
                'Kab. Kediri', 'Kab. Lamongan', 'Kab. Lumajang', 'Kab. Madiun',
                'Kab. Magetan', 'Kab. Malang', 'Kab. Mojokerto', 'Kab. Nganjuk',
                'Kab. Ngawi', 'Kab. Pacitan', 'Kab. Pamekasan', 'Kab. Pasuruan',
                'Kab. Ponorogo', 'Kab. Probolinggo', 'Kab. Sampang', 'Kab. Sidoarjo',
                'Kab. Situbondo', 'Kab. Sumenep', 'Kab. Trenggalek', 'Kab. Tuban',
                'Kab. Tulungagung',
                'Kota Batu', 'Kota Blitar', 'Kota Kediri', 'Kota Madiun',
                'Kota Malang', 'Kota Mojokerto', 'Kota Pasuruan',
                'Kota Probolinggo', 'Kota Surabaya',
            ],
            'banten' => [
                'Kab. Lebak', 'Kab. Pandeglang', 'Kab. Serang', 'Kab. Tangerang',
                'Kota Cilegon', 'Kota Serang', 'Kota Tangerang', 'Kota Tangerang Selatan',
            ],
            'bali' => [
                'Kab. Badung', 'Kab. Bangli', 'Kab. Buleleng', 'Kab. Gianyar',
                'Kab. Jembrana', 'Kab. Karangasem', 'Kab. Klungkung', 'Kab. Tabanan',
                'Kota Denpasar',
            ],
            'nusa_tenggara_barat' => [
                'Kab. Bima', 'Kab. Dompu', 'Kab. Lombok Barat', 'Kab. Lombok Tengah',
                'Kab. Lombok Timur', 'Kab. Lombok Utara', 'Kab. Sumbawa',
                'Kab. Sumbawa Barat',
                'Kota Bima', 'Kota Mataram',
            ],
            'nusa_tenggara_timur' => [
                'Kab. Alor', 'Kab. Belu', 'Kab. Ende', 'Kab. Flores Timur',
                'Kab. Kupang', 'Kab. Lembata', 'Kab. Malaka', 'Kab. Manggarai',
                'Kab. Manggarai Barat', 'Kab. Manggarai Timur', 'Kab. Nagekeo',
                'Kab. Ngada', 'Kab. Rote Ndao', 'Kab. Sabu Raijua', 'Kab. Sikka',
                'Kab. Sumba Barat', 'Kab. Sumba Barat Daya', 'Kab. Sumba Tengah',
                'Kab. Sumba Timur', 'Kab. Timor Tengah Selatan',
                'Kab. Timor Tengah Utara',
                'Kota Kupang',
            ],
            'kalimantan_barat' => [
                'Kab. Bengkayang', 'Kab. Kapuas Hulu', 'Kab. Kayong Utara',
                'Kab. Ketapang', 'Kab. Kubu Raya', 'Kab. Landak', 'Kab. Melawi',
                'Kab. Mempawah', 'Kab. Sambas', 'Kab. Sanggau', 'Kab. Sekadau',
                'Kab. Sintang',
                'Kota Pontianak', 'Kota Singkawang',
            ],
            'kalimantan_tengah' => [
                'Kab. Barito Selatan', 'Kab. Barito Timur', 'Kab. Barito Utara',
                'Kab. Gunung Mas', 'Kab. Kapuas', 'Kab. Katingan',
                'Kab. Kotawaringin Barat', 'Kab. Kotawaringin Timur',
                'Kab. Lamandau', 'Kab. Murung Raya', 'Kab. Pulang Pisau',
                'Kab. Seruyan', 'Kab. Sukamara',
                'Kota Palangka Raya',
            ],
            'kalimantan_selatan' => [
                'Kab. Balangan', 'Kab. Banjar', 'Kab. Barito Kuala',
                'Kab. Hulu Sungai Selatan', 'Kab. Hulu Sungai Tengah',
                'Kab. Hulu Sungai Utara', 'Kab. Kotabaru', 'Kab. Tabalong',
                'Kab. Tanah Bumbu', 'Kab. Tanah Laut', 'Kab. Tapin',
                'Kota Banjarbaru', 'Kota Banjarmasin',
            ],
            'kalimantan_timur' => [
                'Kab. Berau', 'Kab. Kutai Barat', 'Kab. Kutai Kartanegara',
                'Kab. Kutai Timur', 'Kab. Mahakam Ulu', 'Kab. Paser',
                'Kab. Penajam Paser Utara',
                'Kota Balikpapan', 'Kota Bontang', 'Kota Samarinda',
            ],
            'kalimantan_utara' => [
                'Kab. Bulungan', 'Kab. Malinau', 'Kab. Nunukan', 'Kab. Tana Tidung',
                'Kota Tarakan',
            ],
            'sulawesi_utara' => [
                'Kab. Bolaang Mongondow', 'Kab. Bolaang Mongondow Selatan',
                'Kab. Bolaang Mongondow Timur', 'Kab. Bolaang Mongondow Utara',
                'Kab. Kepulauan Sangihe', 'Kab. Kepulauan Siau Tagulandang Biaro',
                'Kab. Kepulauan Talaud', 'Kab. Minahasa', 'Kab. Minahasa Selatan',
                'Kab. Minahasa Tenggara', 'Kab. Minahasa Utara',
                'Kota Bitung', 'Kota Kotamobagu', 'Kota Manado', 'Kota Tomohon',
            ],
            'sulawesi_tengah' => [
                'Kab. Banggai', 'Kab. Banggai Kepulauan', 'Kab. Banggai Laut',
                'Kab. Buol', 'Kab. Donggala', 'Kab. Morowali', 'Kab. Morowali Utara',
                'Kab. Parigi Moutong', 'Kab. Poso', 'Kab. Sigi',
                'Kab. Tojo Una-Una', 'Kab. Tolitoli',
                'Kota Palu',
            ],
            'sulawesi_selatan' => [
                'Kab. Bantaeng', 'Kab. Barru', 'Kab. Bone', 'Kab. Bulukumba',
                'Kab. Enrekang', 'Kab. Gowa', 'Kab. Jeneponto',
                'Kab. Kepulauan Selayar', 'Kab. Luwu', 'Kab. Luwu Timur',
                'Kab. Luwu Utara', 'Kab. Maros', 'Kab. Pangkajene dan Kepulauan',
                'Kab. Pinrang', 'Kab. Sidenreng Rappang', 'Kab. Sinjai',
                'Kab. Soppeng', 'Kab. Takalar', 'Kab. Tana Toraja',
                'Kab. Toraja Utara', 'Kab. Wajo',
                'Kota Makassar', 'Kota Palopo', 'Kota Parepare',
            ],
            'sulawesi_tenggara' => [
                'Kab. Bombana', 'Kab. Buton', 'Kab. Buton Selatan',
                'Kab. Buton Tengah', 'Kab. Buton Utara', 'Kab. Kolaka',
                'Kab. Kolaka Timur', 'Kab. Kolaka Utara', 'Kab. Konawe',
                'Kab. Konawe Kepulauan', 'Kab. Konawe Selatan', 'Kab. Konawe Utara',
                'Kab. Muna', 'Kab. Muna Barat', 'Kab. Wakatobi',
                'Kota Bau-Bau', 'Kota Kendari',
            ],
            'gorontalo' => [
                'Kab. Boalemo', 'Kab. Bone Bolango', 'Kab. Gorontalo',
                'Kab. Gorontalo Utara', 'Kab. Pohuwato',
                'Kota Gorontalo',
            ],
            'sulawesi_barat' => [
                'Kab. Majene', 'Kab. Mamasa', 'Kab. Mamuju', 'Kab. Mamuju Tengah',
                'Kab. Pasangkayu', 'Kab. Polewali Mandar',
            ],
            'maluku' => [
                'Kab. Buru', 'Kab. Buru Selatan', 'Kab. Kepulauan Aru',
                'Kab. Maluku Barat Daya', 'Kab. Maluku Tengah', 'Kab. Maluku Tenggara',
                'Kab. Maluku Tenggara Barat', 'Kab. Seram Bagian Barat',
                'Kab. Seram Bagian Timur',
                'Kota Ambon', 'Kota Tual',
            ],
            'maluku_utara' => [
                'Kab. Halmahera Barat', 'Kab. Halmahera Selatan',
                'Kab. Halmahera Tengah', 'Kab. Halmahera Timur',
                'Kab. Halmahera Utara', 'Kab. Kepulauan Sula',
                'Kab. Pulau Morotai', 'Kab. Pulau Taliabu',
                'Kota Ternate', 'Kota Tidore Kepulauan',
            ],
            'papua_barat' => [
                'Kab. Fakfak', 'Kab. Kaimana', 'Kab. Manokwari',
                'Kab. Manokwari Selatan', 'Kab. Pegunungan Arfak',
                'Kab. Teluk Bintuni', 'Kab. Teluk Wondama',
            ],
            'papua_barat_daya' => [
                'Kab. Maybrat', 'Kab. Raja Ampat', 'Kab. Sorong',
                'Kab. Sorong Selatan', 'Kab. Tambrauw',
                'Kota Sorong',
            ],
            'papua' => [
                'Kab. Biak Numfor', 'Kab. Jayapura', 'Kab. Keerom',
                'Kab. Kepulauan Yapen', 'Kab. Mamberamo Raya', 'Kab. Sarmi',
                'Kab. Supiori', 'Kab. Waropen',
                'Kota Jayapura',
            ],
            'papua_selatan' => [
                'Kab. Asmat', 'Kab. Boven Digoel', 'Kab. Mappi', 'Kab. Merauke',
            ],
            'papua_tengah' => [
                'Kab. Deiyai', 'Kab. Dogiyai', 'Kab. Intan Jaya', 'Kab. Mimika',
                'Kab. Nabire', 'Kab. Paniai', 'Kab. Puncak', 'Kab. Puncak Jaya',
            ],
            'papua_pegunungan' => [
                'Kab. Jayawijaya', 'Kab. Lanny Jaya', 'Kab. Mamberamo Tengah',
                'Kab. Nduga', 'Kab. Pegunungan Bintang', 'Kab. Tolikara',
                'Kab. Yahukimo', 'Kab. Yalimo',
            ],
        ];
    }
}
