<table>
    <thead>
    <tr>
        <th>Dibuat pada</th>
        <th>Dilaporkan Oleh</th>
        <th>Tipe Laporan</th>
        <th>Fungsi Bangunan</th>
        <th>Pemilik Bangunan</th>
        <th>Deskripsi</th>
        <th>Alamat</th>
        <th>Kecamatan</th>
        <th>Kelurahan</th>
        <th>Longitude</th>
        <th>Latitude</th>
        <th>Warna Bangunan</th>
        <th>Jumlah Lantai</th>
        <th>Material Utama</th>
        <th>Catatan</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($denunciations as $denunciation)
        <tr>
            <td>{{ $denunciation->created_at->format('d-m-Y H:i:s') }}</td>
            <td>{{ $denunciation->user_pelapor->name }}</td>
            <td>{{ $denunciation->type_denunciation->name }}</td>
            <td>{{ $denunciation->function_building->name }}</td>
            <td>{{ $denunciation->pemilik_bangunan }}</td>
            <td>{{ $denunciation->description }}</td>
            <td>{{ $denunciation->alamat }}</td>
            <td>{{ $denunciation->kecamatan }}</td>
            <td>{{ $denunciation->kelurahan }}</td>
            <td>{{ $denunciation->longitude }}</td>
            <td>{{ $denunciation->latitude }}</td>
            <td>{{ $denunciation->warna_bangunan }}</td>
            <td>{{ $denunciation->jumlah_lantai }}</td>
            <td>{{ $denunciation->material_utama }}</td>
            <td>{{ $denunciation->catatan }}</td>
            <td>{{ $denunciation->state }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
