<?php
/**
 * Mesin inferensi Forward Chaining.
 * Rule dibaca dari tabel `rule` dan `rule_premis`, jadi kalau admin
 * menambah/mengubah rule lewat panel admin, fungsi ini otomatis
 * ikut menyesuaikan tanpa perlu ubah kode.
 *
 * @param mysqli $koneksi
 * @param array  $gejala_terpilih  contoh: ['G01','G02','G03', ...]
 * @return array ['penyakit' => 'P001'|null, 'working_memory' => [...], 'log' => [...]]
 */
function jalankanForwardChaining($koneksi, $gejala_terpilih) {
    $working_memory = $gejala_terpilih;
    $hasil_penyakit = null;
    $log_iterasi = [];

    // Ambil semua rule + kesimpulannya
    $rules = [];
    $q = mysqli_query($koneksi, "SELECT kode_rule, kesimpulan FROM rule");
    while ($r = mysqli_fetch_assoc($q)) {
        $rules[$r['kode_rule']] = [
            'kesimpulan' => $r['kesimpulan'],
            'premis'     => []
        ];
    }

    // Ambil semua premis tiap rule
    $q2 = mysqli_query($koneksi, "SELECT kode_rule, kode_item FROM rule_premis");
    while ($p = mysqli_fetch_assoc($q2)) {
        if (isset($rules[$p['kode_rule']])) {
            $rules[$p['kode_rule']]['premis'][] = $p['kode_item'];
        }
    }

    $rule_terpakai = [];
    $ada_perubahan = true;

    while ($ada_perubahan && $hasil_penyakit === null) {
        $ada_perubahan = false;

        foreach ($rules as $kode_rule => $data) {
            if (in_array($kode_rule, $rule_terpakai)) continue;
            if (empty($data['premis'])) continue;

            $semua_terpenuhi = true;
            foreach ($data['premis'] as $premis) {
                if (!in_array($premis, $working_memory)) {
                    $semua_terpenuhi = false;
                    break;
                }
            }

            if ($semua_terpenuhi) {
                $kesimpulan = $data['kesimpulan'];
                if (!in_array($kesimpulan, $working_memory)) {
                    $working_memory[] = $kesimpulan;
                }
                $rule_terpakai[] = $kode_rule;
                $ada_perubahan = true;

                $log_iterasi[] = [
                    'rule'   => $kode_rule,
                    'premis' => $data['premis'],
                    'hasil'  => $kesimpulan
                ];

                if (strpos($kesimpulan, 'P') === 0) {
                    $hasil_penyakit = $kesimpulan;
                    break;
                }
            }
        }
    }

    return [
        'penyakit'       => $hasil_penyakit,
        'working_memory' => $working_memory,
        'log'            => $log_iterasi
    ];
}

/**
 * ============================================================
 * BAGIAN TAMBAHAN: SESI TANYA-JAWAB (YA/TIDAK) PER GEJALA
 * ============================================================
 * Alur ini menanyakan gejala SATU PER SATU kepada user.
 * - Jika dijawab "Ya"  -> gejala masuk working memory, lalu
 *   langsung dicek apakah ada rule yang sudah terpenuhi
 *   (dihubungkan ke rule yang sesuai).
 * - Jika dijawab "Tidak" -> gejala tsb ditandai tidak terpenuhi,
 *   sehingga semua rule yang membutuhkan gejala itu otomatis
 *   gugur (tidak perlu ditanyakan lagi turunannya).
 * Pertanyaan berikutnya hanya diambil dari rule yang MASIH
 * MUNGKIN tercapai, supaya user tidak ditanyai gejala yang
 * sudah pasti tidak relevan lagi.
 */

/**
 * Ambil seluruh rule beserta premis-premisnya dari database.
 *
 * @param mysqli $koneksi
 * @return array [kode_rule => ['kesimpulan' => ..., 'premis' => [...]]]
 */
function ambilSemuaRule($koneksi) {
    $rules = [];
    $q = mysqli_query($koneksi, "SELECT kode_rule, kesimpulan FROM rule");
    while ($r = mysqli_fetch_assoc($q)) {
        $rules[$r['kode_rule']] = [
            'kesimpulan' => $r['kesimpulan'],
            'premis'     => []
        ];
    }
    $q2 = mysqli_query($koneksi, "SELECT kode_rule, kode_item FROM rule_premis");
    while ($p = mysqli_fetch_assoc($q2)) {
        if (isset($rules[$p['kode_rule']])) {
            $rules[$p['kode_rule']]['premis'][] = $p['kode_item'];
        }
    }
    return $rules;
}

/**
 * Cek apakah sebuah rule secara logika MASIH MUNGKIN terpenuhi,
 * berdasarkan jawaban yang sudah diberikan sejauh ini.
 * Rekursif: kalau premisnya berupa fakta antara (F..) atau
 * penyakit (P..), rule itu mungkin asal SALAH SATU rule yang
 * menyimpulkan fakta tsb masih mungkin.
 *
 * @param string $kode_rule
 * @param array  $rules    hasil ambilSemuaRule()
 * @param array  $jawaban  ['G01' => 'ya'|'tidak', ...]
 * @param array  &$memo    cache hasil per rule (hindari hitung ulang)
 * @return bool
 */
function isRuleMasihMungkin($kode_rule, $rules, $jawaban, &$memo) {
    if (isset($memo[$kode_rule])) return $memo[$kode_rule];
    if (!isset($rules[$kode_rule])) return false;

    // Tandai sementara true untuk mencegah rekursi tak berhenti
    // (jaga-jaga jika suatu saat ada rule yang saling mereferensi).
    $memo[$kode_rule] = true;

    foreach ($rules[$kode_rule]['premis'] as $item) {
        if ($item[0] === 'G') {
            // Premis berupa gejala dasar
            if (isset($jawaban[$item]) && $jawaban[$item] === 'tidak') {
                $memo[$kode_rule] = false;
                return false;
            }
        } else {
            // Premis berupa fakta antara / penyakit (F.. atau P..)
            // Cari semua rule yang menyimpulkan fakta ini
            $ada_yang_mungkin = false;
            foreach ($rules as $kr => $d) {
                if ($d['kesimpulan'] === $item && isRuleMasihMungkin($kr, $rules, $jawaban, $memo)) {
                    $ada_yang_mungkin = true;
                    break;
                }
            }
            if (!$ada_yang_mungkin) {
                $memo[$kode_rule] = false;
                return false;
            }
        }
    }

    $memo[$kode_rule] = true;
    return true;
}

/**
 * Tentukan kode gejala berikutnya yang perlu ditanyakan kepada user.
 * Hanya mengambil gejala dari rule yang MASIH MUNGKIN tercapai dan
 * yang belum dijawab. Mengembalikan null jika tidak ada lagi
 * pertanyaan yang relevan (semua kemungkinan rule sudah gugur,
 * atau semua gejala relevan sudah ditanyakan).
 *
 * @param mysqli $koneksi
 * @param array  $jawaban ['G01' => 'ya'|'tidak', ...]
 * @return string|null kode_gejala berikutnya, atau null jika selesai
 */
function gejalaBerikutnya($koneksi, $jawaban) {
    $rules = ambilSemuaRule($koneksi);
    $memo  = [];
    $kandidat = [];

    foreach ($rules as $kode_rule => $data) {
        if (!isRuleMasihMungkin($kode_rule, $rules, $jawaban, $memo)) continue;
        foreach ($data['premis'] as $item) {
            if ($item[0] === 'G' && !isset($jawaban[$item])) {
                $kandidat[$item] = true;
            }
        }
    }

    if (empty($kandidat)) return null;

    $kode_list = array_keys($kandidat);
    sort($kode_list);
    return $kode_list[0];
}

/**
 * Simpan hasil identifikasi ke tabel riwayat_identifikasi.
 *
 * @param mysqli $koneksi
 * @param int    $id_user
 * @param array  $gejala_ya  daftar kode_gejala yang dijawab "ya"
 * @param array  $hasil      hasil dari jalankanForwardChaining()
 */
function simpanRiwayatIdentifikasi($koneksi, $id_user, $gejala_ya, $hasil) {
    $gejala_str = mysqli_real_escape_string($koneksi, implode(',', $gejala_ya));
    $hasil_penyakit = $hasil['penyakit']
        ? "'" . mysqli_real_escape_string($koneksi, $hasil['penyakit']) . "'"
        : "NULL";
    $id_user = (int) $id_user;

    mysqli_query($koneksi, "INSERT INTO riwayat_identifikasi (id_user, gejala_dipilih, hasil_penyakit)
                             VALUES ($id_user, '$gejala_str', $hasil_penyakit)");
}

/**
 * Bungkus jawaban ['G01'=>'ya','G02'=>'tidak',...] menjadi array
 * kode gejala yang bernilai "ya" saja, siap dipakai sebagai
 * working memory awal untuk jalankanForwardChaining().
 *
 * @param array $jawaban
 * @return array daftar kode_gejala yang dijawab "ya"
 */
function gejalaYangDipilih($jawaban) {
    $hasil = [];
    foreach ($jawaban as $kode => $nilai) {
        if ($nilai === 'ya') $hasil[] = $kode;
    }
    return $hasil;
}
