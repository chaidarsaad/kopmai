@section('title')
    Kebijakan Privasi
@endsection

<div class="mx-auto max-w-screen-xl min-h-screen bg-white pb-[70px] md:px-10 md:pb-10 pt-4 md:pt-[72px] mb-4 px-5">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Kebijakan Privasi</h1>

    <p class="text-sm text-gray-500 mb-8">Tanggal Berlaku:
        {{ $store->created_at->locale('id')->translatedFormat('l, d F Y') }}</p>

    <div class="space-y-6 text-gray-700 leading-relaxed text-justify">
        <p>
            Selamat datang di <strong>KOPMAI STORE</strong>. Kami menghormati privasi Anda dan berkomitmen untuk
            melindungi informasi pribadi yang Anda berikan saat menggunakan situs dan aplikasi kami.
        </p>

        <h2 class="text-xl font-semibold text-gray-800">1. Informasi yang Kami Kumpulkan</h2>
        <ul class="list-disc pl-5">
            <li>Nama, email, nomor telepon</li>
            <li>Data transaksi seperti riwayat pembelian dan metode pembayaran.</li>
            <li>Alamat IP, jenis perangkat, dan sistem operasi.</li>
        </ul>

        <h2 class="text-xl font-semibold text-gray-800">2. Penggunaan Informasi</h2>
        <p>Informasi yang dikumpulkan digunakan untuk:</p>
        <ul class="list-disc pl-5">
            <li>Memproses pesanan dan memberikan layanan terbaik kepada Anda.</li>
            <li>Mengirim informasi penting terkait akun dan pesanan.</li>
            <li>Meningkatkan keamanan dan pengalaman pengguna.</li>
        </ul>

        <h2 class="text-xl font-semibold text-gray-800">3. Berbagi Informasi</h2>
        <p>Kami tidak menjual atau menyewakan informasi Anda. Namun, informasi dapat dibagikan dengan:</p>
        <ul class="list-disc pl-5">
            <li>Mitra pengiriman dan pembayaran.</li>
            <li>Penyedia layanan teknis kami.</li>
            <li>Pihak berwenang sesuai peraturan hukum yang berlaku.</li>
        </ul>

        <h2 class="text-xl font-semibold text-gray-800">4. Keamanan Data</h2>
        <p>
            Kami menggunakan berbagai teknologi dan prosedur keamanan untuk melindungi data pribadi Anda dari akses,
            penggunaan, atau pengungkapan yang tidak sah.
        </p>

        <h2 class="text-xl font-semibold text-gray-800">5. Hak Anda</h2>
        <p>
            Anda berhak untuk mengakses, mengubah, atau menghapus informasi pribadi Anda dengan menghubungi kami.
        </p>

        <h2 class="text-xl font-semibold text-gray-800">6. Perubahan Kebijakan</h2>
        <p>
            Kami dapat memperbarui kebijakan privasi ini dari waktu ke waktu. Perubahan akan diumumkan di halaman ini.
        </p>

        <h2 class="text-xl font-semibold text-gray-800">7. Hubungi Kami</h2>
        <p>Jika Anda memiliki pertanyaan tentang kebijakan privasi ini, silakan hubungi kami:</p>
        <ul class="list-none pl-0">
            <li><strong>No WhatsApp:</strong> {{ $store->whatsapp }}</li>
            <li><strong>Email:</strong> infokopmai@gmail.com</li>
            <li><strong>Alamat:</strong> {{ $store->address }}</li>
        </ul>
    </div>
</div>
