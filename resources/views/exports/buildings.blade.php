<table>
    <thead>
    <tr>
        <th>Tanggal Dibuat</th>
        <th>Dibuat Oleh</th>
        <th>Nomor Izin Bangunan</th>
        <th>Nama</th>
        <th>Fungsi Bangunan</th>
        <th>Kecamatan</th>
        <th>Kelurahan</th>
        <th>Rw</th>
        <th>Rt</th>
        <th>Alamat</th>
        <th>No Bangunan</th>
        <th>Luas Bangunan</th>
        <th>Banyak Lantai</th>
        <th>Ketinggian</th>
        <th>Longitude</th>
        <th>Latitude</th>
        <th>Tanggal Diubah</th>
        <th>Diubah Oleh</th>
    </tr>
    </thead>
    <tbody>
    @foreach($buildings as $building)
        <tr>
            <td>{{ $building->created_at->format('d-m-Y H:i:s') }}</td>
            <td>{{ $building->created_by_user->name }}</td>
            <td>{{ $building->nomor_izin_bangunan }}</td>
            <td>{{ $building->name }}</td>
            <td>{{ $building->function_building->name }}</td>
            <td>{{ $building->kecamatan }}</td>
            <td>{{ $building->kelurahan }}</td>
            <td>{{ $building->rt }}</td>
            <td>{{ $building->rw }}</td>
            <td>{{ $building->alamat }}</td>
            <td>{{ $building->nomor_bangunan }}</td>
            <td>{{ $building->luas_bangunan }}</td>
            <td>{{ $building->banyak_lantai }}</td>
            <td>{{ $building->ketinggian }}</td>
            <td>{{ $building->longitude }}</td>
            <td>{{ $building->latitude }}</td>
            <td>{{ $building->updated_at->format('d-m-Y H:i:s') }}</td>
            <td>{{ $building->updated_by_user->name }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
