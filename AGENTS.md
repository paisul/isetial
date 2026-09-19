# Instruksi Proyek iSetial

## Alur kerja wajib

- Kerjakan permintaan pengguna sampai tuntas dan pertahankan perubahan pengguna yang tidak terkait.
- Setelah mengubah kode, jalankan pengujian yang relevan. Untuk pengujian Laravel lokal, gunakan `.tools/php/php.exe artisan test` karena PHP mungkin tidak tersedia di `PATH`.
- Jalankan `git diff --check` sebelum membuat commit.
- Jika pengujian lulus, langsung commit perubahan yang dibuat untuk permintaan tersebut dengan pesan commit yang singkat dan jelas.
- Push commit ke `origin/master` tanpa meminta konfirmasi tambahan, kecuali sistem secara eksplisit meminta persetujuan akses.
- Setelah push, jalankan deployment production jika mekanisme dan akses deployment tersedia. Verifikasi hasil deployment melalui respons HTTP atau pemeriksaan production yang relevan.
- Jangan menyatakan deployment berhasil hanya karena push berhasil. Jika repository atau lingkungan belum menyediakan mekanisme deployment, jelaskan bahwa push sudah selesai tetapi deployment belum dapat diverifikasi, serta sebutkan konfigurasi yang masih diperlukan.

## Keamanan dan repository

- Jangan commit `.env`, kredensial, password, token, private key, atau data rahasia lainnya.
- Jangan menimpa perubahan pengguna yang tidak terkait dengan tugas aktif.
- Jangan menggunakan operasi Git destruktif seperti `git reset --hard` atau `git checkout --` terhadap perubahan pengguna.
- Aset production Vite di `public/build` disimpan di Git. Jika CSS atau JavaScript berubah, jalankan `npm run build` dan sertakan hasil build yang relevan dalam commit.

## Deployment

- Branch production adalah `master` dan remote utama adalah `origin`.
- Ikuti prosedur deployment yang terdokumentasi di bagian "Update dari GitHub" pada `README.md` ketika akses server tersedia.
- Sesudah deploy, pastikan aplikasi production dapat dibuka dan tidak menghasilkan error server.
